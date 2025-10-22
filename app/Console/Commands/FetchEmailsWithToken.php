<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Storage;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;

class FetchEmailsWithToken extends Command
{
    /**
     * The name and signature of the console command.
     */
    protected $signature = 'outlook:fetch-with-token
                           {--token= : Microsoft Graph access token}
                           {--token-file= : Path to file containing the access token}
                           {--user-email= : Email address of the mailbox owner}
                           {--folder=inbox  : Folder to fetch from (inbox, sent, drafts, etc.)}
                           {--limit=50 : Number of emails to fetch (max 999)}
                           {--since= : Fetch emails since date (Y-m-d format)}
                           {--select= : Comma-separated list of fields to select}
                           {--filter= : OData filter expression}
                           {--order-by= : Order by field (default: receivedDateTime desc)}
                           {--save-to-db : Save emails to database}
                           {--output-format=table : Output format (table, json, csv)}
                           {--output-file= : Save output to file}
                           {--show-folders : List available folders}
                           {--show-profile : Show user profile information}
                           {--validate-token : Only validate the token and show user info}
                           {--fetch-attachments : Fetch attachment metadata for emails}
                           {--download-attachments : Download attachment content (requires --fetch-attachments)}
                           {--fetch-profile-pictures : Fetch profile pictures for email senders}
                           {--download-profile-pictures : Download profile picture content (requires --fetch-profile-pictures)}
                           {--profile-picture-size=120x120 : Profile picture size (48x48, 64x64, 96x96, 120x120, 240x240, 360x360, 432x432, 504x504, 648x648)}
                           {--attachment-storage=local : Storage disk for attachments}
                           {--attachment-max-size=10485760 : Maximum attachment size to download (bytes, default 10MB)}
                           {--attachment-types= : Comma-separated list of allowed file extensions (e.g., pdf,docx,xlsx)}
                           {--debug : Enable debug mode}';

    /**
     * The console command description.
     */
    protected $description = 'Fetch emails from Outlook using a Microsoft Graph access token directly with attachment support';

    private $GRAPH_API_BASE;
    private string $accessToken;
    private ?string $userEmail = null;

    public function __construct()
    {
        parent::__construct();
        $this->GRAPH_API_BASE = 'https://graph.microsoft.com/v1.0';
    }

    /**
     * Execute the console command.
     */
    public function handle(): int
    {
        try {
            if (!$this->getAccessToken()) {
                return Command::FAILURE;
            }

            $userInfo = $this->validateTokenAndGetUser();
            if (!$userInfo) {
                $this->error('Failed to validate token or retrieve user information');
                return Command::FAILURE;
            }

            $this->userEmail = $this->option('user-email') ?: ($userInfo['mail'] ?? $userInfo['userPrincipalName'] ?? null);

            if (!$this->userEmail) {
                $this->error('Could not determine user email. Please provide --user-email option');
                return Command::FAILURE;
            }

            $this->info("Authenticated as: {$userInfo['displayName']} ({$this->userEmail})");

            if ($this->option('validate-token')) {
                return $this->showTokenValidation($userInfo);
            }

            if ($this->option('show-profile')) {
                $this->showUserProfile($userInfo);
            }

            if ($this->option('show-folders')) {
                $this->showMailFolders();
            }

            if ($this->option('show-profile') || $this->option('show-folders')) {
                return Command::SUCCESS;
            }

            return $this->fetchEmails();
        } catch (\Exception $e) {
            $this->error('Error: ' . $e->getMessage());

            if ($this->option('debug')) {
                $this->error('Stack trace: ' . $e->getTraceAsString());
            }
            return Command::FAILURE;
        }
    }

    /**
     * Show profile picture summary
     */
    private function showProfilePictureSummary(array $emails): void
    {
        $totalSenders = 0;
        $availablePictures = 0;
        $downloadedPictures = 0;
        $totalSize = 0;
        $uniqueSenders = [];

        foreach ($emails as $email) {
            if ($email['from'] && !in_array($email['from'], $uniqueSenders)) {
                $uniqueSenders[] = $email['from'];
                $totalSenders++;

                if ($email['profile_picture'] && $email['profile_picture']['available']) {
                    $availablePictures++;

                    if ($email['profile_picture']['downloaded']) {
                        $downloadedPictures++;
                        $totalSize += $email['profile_picture']['size'] ?? 0;
                    }
                }
            }
        }

        if ($totalSenders > 0) {
            $this->info("\n=== PROFILE PICTURE SUMMARY ===");
            $this->line("Unique senders: {$totalSenders}");
            $this->line("Available pictures: {$availablePictures}/{$totalSenders}");

            if ($this->option('download-profile-pictures')) {
                $this->line("Downloaded: {$downloadedPictures}/{$availablePictures}");
                $this->line("Total size: " . $this->formatBytes($totalSize));
            }
        }
    }

    /**
     * Get access token from option or file
     */
    private function getAccessToken(): bool
    {
        $token = $this->option('token');
        $tokenFile = $this->option('token-file');

        if ($token) {
            $this->accessToken = trim($token);
            return true;
        }

        if ($tokenFile) {
            if (!file_exists($tokenFile)) {
                $this->error("Token file not found: {$tokenFile}");
                return false;
            }

            $this->accessToken = trim(file_get_contents($tokenFile));
            if (empty($this->accessToken)) {
                $this->error("Token file is empty: {$tokenFile}");
                return false;
            }

            $this->info("Token loaded from file: {$tokenFile}");
            return true;
        }

        // Interactive token input
        $this->accessToken = trim($this->secret('Please paste your Microsoft Graph access token'));

        if (empty($this->accessToken)) {
            $this->error('No access token provided');
            return false;
        }

        return true;
    }

    /**
     * Validate token and get user information
     */
    private function validateTokenAndGetUser(): ?array
    {
        try {
            $response = Http::withHeaders([
                'Authorization' => 'Bearer ' . $this->accessToken,
                'Content-Type' => 'application/json',
            ])->timeout(10)->get($this->GRAPH_API_BASE . '/me');

            if (!$response->successful()) {
                $this->error('Token validation failed: HTTP ' . $response->status());
                if ($this->option('debug')) {
                    $this->error('Response: ' . $response->body());
                }
                return null;
            }

            return $response->json();
        } catch (\Exception $e) {
            $this->error('Token validation exception: ' . $e->getMessage());
            return null;
        }
    }

    /**
     * Show token validation results
     */
    private function showTokenValidation(array $userInfo): int
    {
        $this->info('=== TOKEN VALIDATION SUCCESSFUL ===');
        $this->table(
            ['Property', 'Value'],
            [
                ['Valid', '✓ Yes'],
                ['User', $userInfo['displayName'] ?? 'N/A'],
                ['Email', $userInfo['mail'] ?? $userInfo['userPrincipalName'] ?? 'N/A'],
                ['User ID', $userInfo['id'] ?? 'N/A'],
                ['Token Preview', substr($this->accessToken, 0, 20) . '...'],
            ]
        );

        return Command::SUCCESS;
    }

    /**
     * Show user profile information
     */
    private function showUserProfile(array $profile): void
    {
        $this->info('=== USER PROFILE ===');
        $this->table(
            ['Property', 'Value'],
            [
                ['Display Name', $profile['displayName'] ?? 'N/A'],
                ['Email', $profile['mail'] ?? $profile['userPrincipalName'] ?? 'N/A'],
                ['Job Title', $profile['jobTitle'] ?? 'N/A'],
                ['Department', $profile['department'] ?? 'N/A'],
                ['Office Location', $profile['officeLocation'] ?? 'N/A'],
                ['Mobile Phone', $profile['mobilePhone'] ?? 'N/A'],
                ['Business Phone', $profile['businessPhones'][0] ?? 'N/A'],
                ['Preferred Language', $profile['preferredLanguage'] ?? 'N/A'],
                ['User Principal Name', $profile['userPrincipalName'] ?? 'N/A'],
            ]
        );
    }

    /**
     * Show available mail folders
     */
    private function showMailFolders(): void
    {
        try {
            $response = Http::withHeaders([
                'Authorization' => 'Bearer ' . $this->accessToken,
            ])->get($this->GRAPH_API_BASE . '/me/mailFolders');

            if (!$response->successful()) {
                $this->error('Failed to fetch folders: HTTP ' . $response->status());
                return;
            }

            $folders = $response->json()['value'] ?? [];

            if (empty($folders)) {
                $this->warn('No folders found.');
                return;
            }

            $this->info('=== MAIL FOLDERS ===');
            $this->table(
                ['Display Name', 'ID', 'Total Items', 'Unread Items', 'Child Folders'],
                array_map(function ($folder) {
                    return [
                        $folder['displayName'] ?? 'N/A',
                        isset($folder['id']) ? substr($folder['id'], 0, 20) . '...' : 'N/A',
                        $folder['totalItemCount'] ?? 0,
                        $folder['unreadItemCount'] ?? 0,
                        $folder['childFolderCount'] ?? 0
                    ];
                }, $folders)
            );
        } catch (\Exception $e) {
            $this->error('Failed to fetch mail folders: ' . $e->getMessage());
        }
    }

    /**
     * Fetch emails from Microsoft Graph
     */
    private function fetchEmails(): int
    {
        $folder = $this->option('folder') ?? 'inbox';
        $limit = min((int) $this->option('limit'), 999);
        $since = $this->option('since') ?? now()->subWeek()->format('Y-m-d');
        $select = $this->option('select');
        $filter = $this->option('filter');
        $orderBy = $this->option('order-by') ?: 'receivedDateTime desc';

        $this->info("Fetching emails from folder: {$folder}");
        $this->info("Limit: {$limit}" . ($since ? ", Since: {$since}" : ""));

        try {
            $url = $this->buildEmailsUrl($folder, $limit, $since, $select, $filter, $orderBy);

            if ($this->option('debug')) {
                $this->info("Graph API URL: {$url}");
            }

            $response = Http::withHeaders([
                'Authorization' => 'Bearer ' . $this->accessToken,
                'Content-Type' => 'application/json',
            ])->timeout(60)->get($url);

            if (!$response->successful()) {
                $this->error('Failed to fetch emails: HTTP ' . $response->status());
                if ($this->option('debug')) {
                    $this->error('Response: ' . $response->body());
                }
                return Command::FAILURE;
            }

            $data = $response->json();
            $emails = $data['value'] ?? [];

            $this->info("Successfully fetched " . count($emails) . " emails");

            // Process and display emails
            $processedEmails = $this->processEmails($emails);

            // Fetch attachments if requested
            if ($this->option('fetch-attachments')) {
                $processedEmails = $this->fetchEmailAttachments($processedEmails);
            }

            // Fetch profile pictures if requested
            if ($this->option('fetch-profile-pictures')) {
                $processedEmails = $this->fetchProfilePictures($processedEmails);
            }

            // Save to database if requested
            if ($this->option('save-to-db')) {
                $this->saveEmailsToDatabase($processedEmails);
            }

            // Output emails in requested format
            $this->outputEmails($processedEmails);

            return Command::SUCCESS;
        } catch (\Exception $e) {
            $this->error('Failed to fetch emails: ' . $e->getMessage());
            return Command::FAILURE;
        }
    }

    /**
     * Build the Graph API URL for fetching emails
     */
    private function buildEmailsUrl(string $folder, int $limit, ?string $since, ?string $select, ?string $filter, string $orderBy): string
    {
        $folderPath = $folder === 'inbox' ? 'mailFolders/inbox' : "mailFolders/{$folder}";
        $url = $this->GRAPH_API_BASE . "/me/{$folderPath}/messages";

        $params = [
            '$top' => $limit,
            '$orderby' => $orderBy
        ];

        // Add select fields
        if ($select) {
            $params['$select'] = $select;
        } else {
            $params['$select'] = implode(',', $this->getDefaultSelectFields());
        }

        // Add date filter
        $filters = [];
        if ($since) {
            $sinceDate = Carbon::parse($since)->toISOString();
            $filters[] = "receivedDateTime ge {$sinceDate}";
        }

        // Add custom filter
        if ($filter) {
            $filters[] = $filter;
        }

        if (!empty($filters)) {
            $params['$filter'] = implode(' and ', $filters);
        }

        return $url . '?' . http_build_query($params);
    }

    /**
     * Get default fields to select from Graph API
     */
    private function getDefaultSelectFields(): array
    {
        return [
            'id',
            'subject',
            'from',
            'toRecipients',
            'ccRecipients',
            'receivedDateTime',
            'sentDateTime',
            'body',
            'bodyPreview',
            'importance',
            'isRead',
            'hasAttachments',
            'internetMessageId',
            'conversationId'
        ];
    }

    /**
     * Process raw email data from Graph API
     */
    private function processEmails(array $rawEmails): array
    {
        $emails = [];

        foreach ($rawEmails as $rawEmail) {
            try {
                $emails[] = [
                    'id' => $rawEmail['id'],
                    'subject' => $rawEmail['subject'] ?? '[No Subject]',
                    'from' => $this->extractEmailAddress($rawEmail['from'] ?? null),
                    'from_name' => $this->extractDisplayName($rawEmail['from'] ?? null),
                    'to' => $this->extractRecipients($rawEmail['toRecipients'] ?? []),
                    'cc' => $this->extractRecipients($rawEmail['ccRecipients'] ?? []),
                    'date_received' => $rawEmail['receivedDateTime'] ?? null,
                    'date_sent' => $rawEmail['sentDateTime'] ?? null,
                    'body_preview' => $rawEmail['bodyPreview'] ?? '',
                    'body_content' => $this->extractBodyContent($rawEmail['body'] ?? null),
                    'importance' => $rawEmail['importance'] ?? 'normal',
                    'is_read' => $rawEmail['isRead'] ?? false,
                    'has_attachments' => $rawEmail['hasAttachments'] ?? false,
                    'message_id' => $rawEmail['internetMessageId'] ?? null,
                    'conversation_id' => $rawEmail['conversationId'] ?? null,
                    'attachments' => [], // Will be populated if --fetch-attachments is used
                    'profile_picture' => null, // Will be populated if --fetch-profile-pictures is used
                ];
            } catch (\Exception $e) {
                $this->warn('Failed to process email ID: ' . ($rawEmail['id'] ?? 'unknown'));
                if ($this->option('debug')) {
                    $this->warn('Error: ' . $e->getMessage());
                }
            }
        }

        return $emails;
    }

    /**
     * Fetch attachments for emails that have them
     */
    private function fetchEmailAttachments(array $emails): array
    {
        $this->info('Fetching attachment metadata...');
        $emailsWithAttachments = array_filter($emails, fn($email) => $email['has_attachments']);

        if (empty($emailsWithAttachments)) {
            $this->info('No emails with attachments found.');
            return $emails;
        }

        $this->info('Found ' . count($emailsWithAttachments) . ' emails with attachments');

        $bar = $this->output->createProgressBar(count($emailsWithAttachments));
        $bar->start();

        foreach ($emails as $index => $email) {
            if (!$email['has_attachments']) {
                continue;
            }

            try {
                $attachments = $this->getEmailAttachments($email['id']);
                $emails[$index]['attachments'] = $attachments;

                if ($this->option('download-attachments')) {
                    $emails[$index]['attachments'] = $this->downloadAttachments($email['id'], $attachments);
                }
            } catch (\Exception $e) {
                $this->warn("\nFailed to fetch attachments for email: " . $email['subject']);
                if ($this->option('debug')) {
                    $this->warn('Error: ' . $e->getMessage());
                }
            }

            $bar->advance();
        }

        $bar->finish();
        $this->line('');

        return $emails;
    }

    /**
     * Get attachments metadata for a specific email
     */
    private function getEmailAttachments(string $messageId): array
    {
        $url = $this->GRAPH_API_BASE . "/me/messages/{$messageId}/attachments";

        $response = Http::withHeaders([
            'Authorization' => 'Bearer ' . $this->accessToken,
            'Content-Type' => 'application/json',
        ])->timeout(30)->get($url);

        if (!$response->successful()) {
            throw new \Exception('Failed to fetch attachments: HTTP ' . $response->status());
        }

        $data = $response->json();
        $attachments = [];

        foreach ($data['value'] ?? [] as $attachment) {
            $attachments[] = [
                'id' => $attachment['id'],
                'name' => $attachment['name'] ?? 'unknown',
                'content_type' => $attachment['contentType'] ?? 'application/octet-stream',
                'size' => $attachment['size'] ?? 0,
                'is_inline' => $attachment['isInline'] ?? false,
                'last_modified' => $attachment['lastModifiedDateTime'] ?? null,
                'content_id' => $attachment['contentId'] ?? null,
                'content_location' => $attachment['contentLocation'] ?? null,
                'downloaded' => false,
                'file_path' => null,
                'download_error' => null,
            ];
        }

        return $attachments;
    }

    /**
     * Fetch profile pictures for email senders
     */
    private function fetchProfilePictures(array $emails): array
    {
        $this->info('Fetching profile pictures...');

        // Get unique sender email addresses
        $senderEmails = [];
        foreach ($emails as $email) {
            if ($email['from'] && !in_array($email['from'], $senderEmails)) {
                $senderEmails[] = $email['from'];
            }
        }

        if (empty($senderEmails)) {
            $this->info('No sender emails found for profile pictures.');
            return $emails;
        }

        $this->info('Found ' . count($senderEmails) . ' unique senders');

        // Cache for profile pictures to avoid duplicate requests
        $profilePictureCache = [];

        $bar = $this->output->createProgressBar(count($senderEmails));
        $bar->start();

        foreach ($senderEmails as $senderEmail) {
            try {
                $profilePicture = $this->getUserProfilePicture($senderEmail);
                $profilePictureCache[$senderEmail] = $profilePicture;
            } catch (\Exception $e) {
                $profilePictureCache[$senderEmail] = [
                    'available' => false,
                    'error' => $e->getMessage(),
                    'downloaded' => false,
                    'file_path' => null
                ];

                if ($this->option('debug')) {
                    $this->warn("\nFailed to fetch profile picture for {$senderEmail}: " . $e->getMessage());
                }
            }

            $bar->advance();
        }

        $bar->finish();
        $this->line('');

        // Apply profile pictures to emails
        foreach ($emails as $index => $email) {
            if ($email['from'] && isset($profilePictureCache[$email['from']])) {
                $emails[$index]['profile_picture'] = $profilePictureCache[$email['from']];
            }
        }

        return $emails;
    }

    /**
     * Get user profile picture from Microsoft Graph
     */
    private function getUserProfilePicture(string $userEmail): array
    {
        $size = $this->option('profile-picture-size');
        $url = $this->GRAPH_API_BASE . "/users/{$userEmail}/photos/{$size}";

        // First, check if photo exists by getting metadata
        $metadataResponse = Http::withHeaders([
            'Authorization' => 'Bearer ' . $this->accessToken,
            'Content-Type' => 'application/json',
        ])->timeout(10)->get($url);

        if (!$metadataResponse->successful()) {
            if ($metadataResponse->status() === 404) {
                return [
                    'available' => false,
                    'error' => 'No profile picture available',
                    'downloaded' => false,
                    'file_path' => null
                ];
            }

            throw new \Exception('Failed to fetch profile picture metadata: HTTP ' . $metadataResponse->status());
        }

        $metadata = $metadataResponse->json();

        $profilePicture = [
            'available' => true,
            'width' => $metadata['width'] ?? null,
            'height' => $metadata['height'] ?? null,
            'size' => null, // Will be set when downloading
            'content_type' => 'image/jpeg', // Graph API returns JPEG
            'downloaded' => false,
            'file_path' => null,
            'error' => null
        ];

        // Download the actual image if requested
        if ($this->option('download-profile-pictures')) {
            try {
                $profilePicture = $this->downloadProfilePicture($userEmail, $profilePicture);
            } catch (\Exception $e) {
                $profilePicture['error'] = $e->getMessage();
                if ($this->option('debug')) {
                    $this->warn("Failed to download profile picture for {$userEmail}: " . $e->getMessage());
                }
            }
        }

        return $profilePicture;
    }

    /**
     * Download profile picture content
     */
    private function downloadProfilePicture(string $userEmail, array $profilePicture): array
    {
        $size = $this->option('profile-picture-size');
        $url = $this->GRAPH_API_BASE . "/users/{$userEmail}/photos/{$size}/\$value";
        $storageDisk = $this->option('attachment-storage');

        // Validate storage disk exists
        try {
            Storage::disk($storageDisk);
        } catch (\InvalidArgumentException $e) {
            throw new \Exception("Storage disk '{$storageDisk}' is not configured");
        }

        $response = Http::withHeaders([
            'Authorization' => 'Bearer ' . $this->accessToken,
        ])->timeout(30)->get($url);

        if (!$response->successful()) {
            throw new \Exception('Failed to download profile picture: HTTP ' . $response->status());
        }

        $content = $response->body();
        $profilePicture['size'] = strlen($content);

        // Generate filename
        $filename = $this->generateProfilePictureFilename($userEmail, $size);
        $filepath = "profile_pictures/" . date('Y/m') . "/{$filename}";

        // Ensure directory exists
        $directory = dirname($filepath);
        if (!Storage::disk($storageDisk)->exists($directory)) {
            Storage::disk($storageDisk)->makeDirectory($directory);
        }

        // Save to storage
        Storage::disk($storageDisk)->put($filepath, $content);

        $profilePicture['downloaded'] = true;
        $profilePicture['file_path'] = $filepath;

        if ($this->option('debug')) {
            $this->info("Downloaded profile picture: {$userEmail} -> {$filepath}");
        }

        return $profilePicture;
    }

    /**
     * Generate unique filename for profile picture
     */
    private function generateProfilePictureFilename(string $userEmail, string $size): string
    {
        $emailHash = md5($userEmail);
        $timestamp = time();

        return "profile_{$emailHash}_{$size}_{$timestamp}.jpg";
    }

    /**
     * Download attachments content
     */
    private function downloadAttachments(string $messageId, array $attachments): array
    {
        $maxSize = (int) $this->option('attachment-max-size');
        $allowedTypes = $this->option('attachment-types') ?
            explode(',', strtolower($this->option('attachment-types'))) : null;
        $storageDisk = $this->option('attachment-storage');

        // Validate storage disk exists
        try {
            Storage::disk($storageDisk);
        } catch (\InvalidArgumentException $e) {
            $this->error("Storage disk '{$storageDisk}' is not configured. Please check your config/filesystems.php");
            $this->info("Available disks: " . implode(', ', array_keys(config('filesystems.disks'))));
            return $attachments;
        }

        foreach ($attachments as $index => $attachment) {
            try {
                // Check file size
                if ($attachment['size'] > $maxSize) {
                    $attachments[$index]['download_error'] = "File too large ({$attachment['size']} bytes > {$maxSize} bytes)";
                    continue;
                }

                // Check file type if restricted
                if ($allowedTypes) {
                    $extension = strtolower(pathinfo($attachment['name'], PATHINFO_EXTENSION));
                    if (!in_array($extension, $allowedTypes)) {
                        $attachments[$index]['download_error'] = "File type not allowed: {$extension}";
                        continue;
                    }
                }

                // Download attachment content
                $content = $this->downloadAttachmentContent($messageId, $attachment['id']);

                if ($content) {
                    // Generate unique filename
                    $filename = $this->generateUniqueFilename($attachment['name'], $messageId);
                    $filepath = "emails/{$this->userEmail}/" . date('Y/m/d') . "/{$filename}";

                    // Ensure directory exists
                    $directory = dirname($filepath);
                    if (!Storage::disk($storageDisk)->exists($directory)) {
                        Storage::disk($storageDisk)->makeDirectory($directory);
                    }

                    // Save to storage
                    Storage::disk($storageDisk)->put($filepath, $content);

                    $attachments[$index]['downloaded'] = true;
                    $attachments[$index]['file_path'] = $filepath;

                    if ($this->option('debug')) {
                        $this->info("Downloaded: {$attachment['name']} -> {$filepath}");
                    }
                }
            } catch (\Exception $e) {
                $attachments[$index]['download_error'] = $e->getMessage();
                if ($this->option('debug')) {
                    $this->warn("Failed to download {$attachment['name']}: " . $e->getMessage());
                }
            }
        }

        return $attachments;
    }

    /**
     * Download attachment content from Graph API
     */
    private function downloadAttachmentContent(string $messageId, string $attachmentId): ?string
    {
        $url = $this->GRAPH_API_BASE . "/me/messages/{$messageId}/attachments/{$attachmentId}/\$value";

        $response = Http::withHeaders([
            'Authorization' => 'Bearer ' . $this->accessToken,
        ])->timeout(120)->get($url);

        if (!$response->successful()) {
            throw new \Exception('Failed to download attachment content: HTTP ' . $response->status());
        }

        return $response->body();
    }

    /**
     * Generate unique filename to avoid conflicts
     */
    private function generateUniqueFilename(string $originalName, string $messageId): string
    {
        $extension = pathinfo($originalName, PATHINFO_EXTENSION);
        $basename = pathinfo($originalName, PATHINFO_FILENAME);
        $timestamp = time();
        $messagePrefix = substr(md5($messageId), 0, 8);

        return Str::slug($basename) . "_{$messagePrefix}_{$timestamp}" . ($extension ? ".{$extension}" : '');
    }

    /**
     * Extract email address from Graph API email object
     */
    private function extractEmailAddress(?array $emailObject): ?string
    {
        return $emailObject['emailAddress']['address'] ?? null;
    }

    /**
     * Extract display name from Graph API email object
     */
    private function extractDisplayName(?array $emailObject): ?string
    {
        return $emailObject['emailAddress']['name'] ?? null;
    }

    /**
     * Extract recipients from Graph API recipients array
     */
    private function extractRecipients(array $recipients): array
    {
        return array_map(function ($recipient) {
            return [
                'email' => $recipient['emailAddress']['address'] ?? null,
                'name' => $recipient['emailAddress']['name'] ?? null
            ];
        }, $recipients);
    }

    /**
     * Extract body content
     */
    private function extractBodyContent(?array $body): ?string
    {
        if (!$body || !isset($body['content'])) {
            return null;
        }

        return $body['content'];
    }

    /**
     * Save emails to database (enhanced with attachments)
     */
    private function saveEmailsToDatabase(array $emails): void
    {
        $this->info('Saving emails to database...');
        $saved = 0;

        foreach ($emails as $email) {
            try {
                $emailId = DB::table('fetched_emails')->updateOrInsert(
                    [
                        'message_id' => $email['message_id'],
                        'user_email' => $this->userEmail
                    ],
                    [
                        'uid' => $email['id'],
                        'subject' => $email['subject'],
                        'from_email' => $email['from'],
                        'from_name' => $email['from_name'],
                        'to_recipients' => json_encode($email['to']),
                        'cc_recipients' => json_encode($email['cc']),
                        'date_received' => $email['date_received'],
                        'date_sent' => $email['date_sent'],
                        'body_text' => strip_tags($email['body_content'] ?? ''),
                        'body_html' => $email['body_content'],
                        'body_preview' => $email['body_preview'],
                        'importance' => $email['importance'],
                        'is_read' => $email['is_read'],
                        'has_attachments' => $email['has_attachments'],
                        'conversation_id' => $email['conversation_id'],
                        'folder' => $this->option('folder'),
                        'created_at' => now(),
                        'updated_at' => now()
                    ]
                );

                // Save attachments if present
                if (!empty($email['attachments'])) {
                    $this->saveAttachmentsToDatabase($email['message_id'], $email['attachments']);
                }

                // Save profile picture if present
                if (!empty($email['profile_picture']) && $email['profile_picture']['available']) {
                    $this->saveProfilePictureToDatabase($email['message_id'], $email['from'], $email['profile_picture']);
                }

                $saved++;
            } catch (\Exception $e) {
                $this->warn('Failed to save email: ' . $email['subject']);
                if ($this->option('debug')) {
                    $this->warn('Error: ' . $e->getMessage());
                }
            }
        }

        $this->info("Saved {$saved} emails to database");
    }

    /**
     * Save attachments to database
     */
    private function saveAttachmentsToDatabase(string $messageId, array $attachments): void
    {
        foreach ($attachments as $attachment) {
            try {
                DB::table('email_attachments')->updateOrInsert(
                    [
                        'message_id' => $messageId,
                        'attachment_id' => $attachment['id'],
                        'user_email' => $this->userEmail
                    ],
                    [
                        'name' => $attachment['name'],
                        'content_type' => $attachment['content_type'],
                        'size' => $attachment['size'],
                        'is_inline' => $attachment['is_inline'],
                        'last_modified' => $attachment['last_modified'],
                        'content_id' => $attachment['content_id'],
                        'content_location' => $attachment['content_location'],
                        'downloaded' => $attachment['downloaded'],
                        'file_path' => $attachment['file_path'],
                        'download_error' => $attachment['download_error'],
                        'created_at' => now(),
                        'updated_at' => now()
                    ]
                );
            } catch (\Exception $e) {
                if ($this->option('debug')) {
                    $this->warn("Failed to save attachment {$attachment['name']}: " . $e->getMessage());
                }
            }
        }
    }

    /**
     * Save profile picture to database
     */
    private function saveProfilePictureToDatabase(string $messageId, string $senderEmail, array $profilePicture): void
    {
        try {
            DB::table('email_profile_pictures')->updateOrInsert(
                [
                    'sender_email' => $senderEmail,
                    'user_email' => $this->userEmail
                ],
                [
                    'width' => $profilePicture['width'],
                    'height' => $profilePicture['height'],
                    'size' => $profilePicture['size'],
                    'content_type' => $profilePicture['content_type'],
                    'downloaded' => $profilePicture['downloaded'],
                    'file_path' => $profilePicture['file_path'],
                    'error' => $profilePicture['error'],
                    'last_fetched' => now(),
                    'created_at' => now(),
                    'updated_at' => now()
                ]
            );

            // Link profile picture to this email
            DB::table('email_profile_picture_links')->updateOrInsert(
                [
                    'message_id' => $messageId,
                    'sender_email' => $senderEmail
                ],
                [
                    'created_at' => now(),
                    'updated_at' => now()
                ]
            );
        } catch (\Exception $e) {
            if ($this->option('debug')) {
                $this->warn("Failed to save profile picture for {$senderEmail}: " . $e->getMessage());
            }
        }
    }

    /**
     * Output emails in requested format (enhanced with attachments)
     */
    private function outputEmails(array $emails): void
    {
        $format = $this->option('output-format');
        $outputFile = $this->option('output-file');

        switch ($format) {
            case 'json':
                $output = json_encode($emails, JSON_PRETTY_PRINT);
                break;
            case 'csv':
                $output = $this->convertToCsv($emails);
                break;
            case 'table':
            default:
                $this->displayEmailsTable($emails);
                return;
        }

        if ($outputFile) {
            file_put_contents($outputFile, $output);
            $this->info("Output saved to: {$outputFile}");
        } else {
            $this->line($output);
        }
    }

    /**
     * Display emails as table (enhanced with attachment info)
     */
    private function displayEmailsTable(array $emails): void
    {
        if (empty($emails)) {
            $this->warn('No emails to display');
            return;
        }

        $this->info('=== FETCHED EMAILS ===');

        $tableData = array_map(function ($email) {
            $attachmentInfo = '';
            if ($email['has_attachments'] && !empty($email['attachments'])) {
                $totalAttachments = count($email['attachments']);
                $downloadedCount = count(array_filter($email['attachments'], fn($a) => $a['downloaded']));
                $attachmentInfo = $this->option('download-attachments')
                    ? "{$downloadedCount}/{$totalAttachments}"
                    : $totalAttachments;
            } elseif ($email['has_attachments']) {
                $attachmentInfo = 'Yes';
            } else {
                $attachmentInfo = 'No';
            }

            return [
                substr($email['subject'], 0, 40),
                $email['from_name'] ?: $email['from'],
                $email['date_received'] ? Carbon::parse($email['date_received'])->format('Y-m-d H:i') : 'N/A',
                $email['is_read'] ? 'Read' : 'Unread',
                $attachmentInfo,
                substr($email['body_preview'], 0, 50) . '...'
            ];
        }, array_slice($emails, 0, 20)); // Limit table to 20 emails

        $headers = ['Subject', 'From', 'Date', 'Status', 'Attachments', 'Preview'];
        if ($this->option('download-attachments')) {
            $headers[4] = 'Attachments (DL/Total)';
        }

        $this->table($headers, $tableData);

        if (count($emails) > 20) {
            $this->info('Showing first 20 emails. Total: ' . count($emails));
        }

        // Show attachment summary if fetched
        if ($this->option('fetch-attachments')) {
            $this->showAttachmentSummary($emails);
        }

        // Show profile picture summary if fetched
        if ($this->option('fetch-profile-pictures')) {
            $this->showProfilePictureSummary($emails);
        }
    }

    /**
     * Show attachment summary
     */
    private function showAttachmentSummary(array $emails): void
    {
        $totalAttachments = 0;
        $downloadedAttachments = 0;
        $totalSize = 0;
        $downloadedSize = 0;
        $attachmentTypes = [];

        foreach ($emails as $email) {
            foreach ($email['attachments'] ?? [] as $attachment) {
                $totalAttachments++;
                $totalSize += $attachment['size'];

                $extension = strtolower(pathinfo($attachment['name'], PATHINFO_EXTENSION) ?: 'no-ext');
                $attachmentTypes[$extension] = ($attachmentTypes[$extension] ?? 0) + 1;

                if ($attachment['downloaded']) {
                    $downloadedAttachments++;
                    $downloadedSize += $attachment['size'];
                }
            }
        }

        if ($totalAttachments > 0) {
            $this->info("\n=== ATTACHMENT SUMMARY ===");
            $this->line("Total attachments: {$totalAttachments}");
            $this->line("Total size: " . $this->formatBytes($totalSize));

            if ($this->option('download-attachments')) {
                $this->line("Downloaded: {$downloadedAttachments}/{$totalAttachments}");
                $this->line("Downloaded size: " . $this->formatBytes($downloadedSize));
            }

            if (!empty($attachmentTypes)) {
                $this->line("\nFile types:");
                foreach ($attachmentTypes as $type => $count) {
                    $this->line("  .{$type}: {$count}");
                }
            }
        }
    }

    /**
     * Format bytes to human readable format
     */
    private function formatBytes(int $bytes): string
    {
        $units = ['B', 'KB', 'MB', 'GB'];
        $bytes = max($bytes, 0);
        $pow = floor(($bytes ? log($bytes) : 0) / log(1024));
        $pow = min($pow, count($units) - 1);

        $bytes /= (1 << (10 * $pow));

        return round($bytes, 2) . ' ' . $units[$pow];
    }

    /**
     * Convert emails to CSV format (enhanced with attachments)
     */
    private function convertToCsv(array $emails): string
    {
        if (empty($emails)) {
            return '';
        }

        $csv = "Subject,From Email,From Name,Date Received,Is Read,Has Attachments,Attachment Count,Has Profile Picture,Body Preview\n";

        foreach ($emails as $email) {
            $attachmentCount = count($email['attachments'] ?? []);
            $hasProfilePicture = !empty($email['profile_picture']) && $email['profile_picture']['available'];

            $csv .= implode(',', [
                '"' . str_replace('"', '""', $email['subject']) . '"',
                '"' . str_replace('"', '""', $email['from'] ?? '') . '"',
                '"' . str_replace('"', '""', $email['from_name'] ?? '') . '"',
                '"' . ($email['date_received'] ?? '') . '"',
                $email['is_read'] ? 'true' : 'false',
                $email['has_attachments'] ? 'true' : 'false',
                $attachmentCount,
                $hasProfilePicture ? 'true' : 'false',
                '"' . str_replace('"', '""', substr($email['body_preview'], 0, 100)) . '"'
            ]) . "\n";
        }

        return $csv;
    }
}
