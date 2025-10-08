<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;
use Exception;
use Illuminate\Support\Str;
use Illuminate\Support\Collection;

class SyncOutlookEmails extends Command
{
    use EmailImageLinkingTrait;

    protected $signature = 'outlook:sync
                           {--user-id= : Specific user ID to sync}
                           {--user-email= : Specific user email to sync}
                           {--all-users : Sync all users with valid tokens}
                           {--folder=inbox : Folder to sync (inbox, sent, drafts, etc.)}
                           {--days-back=7 : Number of days to look back for new emails}
                           {--limit=100 : Maximum emails to fetch per user}
                           {--force-refresh : Force token refresh if needed}
                           {--dry-run : Show what would be synced without actually doing it}
                           {--fetch-attachments : Download email attachments}
                           {--fetch-profile-pictures : Download sender profile pictures}
                           {--download-profile-pictures : Download profile picture content (requires --fetch-profile-pictures)}
                           {--profile-picture-size=120x120 : Profile picture size (48x48, 64x64, 96x96, 120x120, 240x240, 360x360, 432x432, 504x504, 648x648)}
                           {--attachment-storage=local : Storage disk for attachments and profile pictures}
                           {--debug : Enable debug output}
                           {--download-attachments : Download attachment content (requires --fetch-attachments)}
                           {--attachment-max-size=10485760 : Maximum attachment size to download (bytes, default 10MB)}
                           {--attachment-types= : Comma-separated list of allowed file extensions (e.g., pdf,docx,xlsx)}
                           {--max-retries=3 : Maximum retry attempts for failed requests}';

    protected $description = 'Synchronize Outlook emails for users with valid OAuth tokens';

    public const GRAPH_API_BASE = 'https://graph.microsoft.com/v1.0';
    private const TOKEN_REFRESH_THRESHOLD_MINUTES = 30;
    private const MAX_EMAILS_PER_REQUEST = 999;
    private const HTTP_TIMEOUT_SECONDS = 60;
    private const REQUIRED_SCOPES = ['Mail.Read', 'User.Read', 'offline_access'];

    public string $accessToken = '';
    public object $currentUser;
    public array $syncStats = [];

    public function handle(): int
    {
        try {
            $this->displayHeader();
            $this->validateConfiguration();
            $users = $this->getUsersToSync();

            if ($users->isEmpty()) {
                $this->warn('No users found with valid OAuth tokens.');
                return Command::SUCCESS;
            }

            if ($this->option('dry-run')) {
                return $this->handleDryRun($users);
            }

            return $this->executeSync($users);
        } catch (Exception $e) {
            return $this->handleError($e);
        }
    }

    private function displayHeader(): void
    {
        $this->info('Starting Outlook email synchronization...');

        if ($this->option('debug')) {
            $this->info('Debug mode enabled');
            $this->info('Configuration check:');
            $this->info('- Azure Client ID: ' . (config('services.azure.client_id') ? 'Set' : 'Missing'));
            $this->info('- Azure Client Secret: ' . (config('services.azure.client_secret') ? 'Set' : 'Missing'));
            $this->info('- Azure Tenant ID: ' . (config('services.azure.tenant_id') ? 'Set' : 'Missing'));
        }
    }

    private function validateConfiguration(): void
    {
        $requiredConfigs = [
            'services.azure.client_id',
            'services.azure.client_secret',
            'services.azure.tenant_id'
        ];

        foreach ($requiredConfigs as $config) {
            if (!config($config)) {
                throw new Exception("Missing required configuration: {$config}");
            }
        }

        // Validate storage disk
        $storageDisk = $this->option('attachment-storage');
        try {
            Storage::disk($storageDisk);
        } catch (\InvalidArgumentException $e) {
            throw new Exception("Storage disk '{$storageDisk}' is not configured");
        }
    }

    private function handleDryRun(Collection $users): int
    {
        $this->info('DRY RUN MODE - No actual syncing will occur');
        $this->showDryRunResults($users);
        return Command::SUCCESS;
    }

    private function executeSync(Collection $users): int
    {
        $this->info("Found {$users->count()} user(s) to sync");
        $this->initializeSyncStats();

        foreach ($users as $user) {
            $this->syncUserEmails($user);
        }

        $this->showSyncResults();
        return Command::SUCCESS;
    }

    private function handleError(Exception $e): int
    {
        $this->error('Sync failed: ' . $e->getMessage());

        if ($this->option('debug')) {
            $this->error('Stack trace: ' . $e->getTraceAsString());
        }

        // logger()->error('Outlook sync failed', [
        //     'error' => $e->getMessage(),
        //     'trace' => $e->getTraceAsString(),
        //     'options' => $this->options()
        // ]);

        return Command::FAILURE;
    }

    private function getUsersToSync(): Collection
    {
        if (!$this->validateSyncOptions()) {
            return collect();
        }

        $query = DB::table('oauth_tokens')
            ->where('provider', 'outlook')
            ->where('expires_at', '>', now()->timestamp)
            ->whereNotNull('access_token');

        $this->applyUserFilters($query);

        $users = $query->get();

        if ($this->option('debug')) {
            $this->info("Query found {$users->count()} users with valid tokens");
            foreach ($users as $user) {
                $expiresAt = Carbon::createFromTimestamp($user->expires_at);
                $this->info("- User {$user->user_id} ({$user->email}): expires {$expiresAt->diffForHumans()}");
            }
        }

        return $users;
    }

    private function validateSyncOptions(): bool
    {
        if (!$this->option('all-users') && !$this->option('user-id') && !$this->option('user-email')) {
            $this->error('Please specify --user-id, --user-email, or --all-users');
            return false;
        }

        $limit = (int) $this->option('limit');
        if ($limit <= 0 || $limit > self::MAX_EMAILS_PER_REQUEST) {
            $this->error("Limit must be between 1 and " . self::MAX_EMAILS_PER_REQUEST);
            return false;
        }

        return true;
    }

    private function applyUserFilters($query): void
    {
        if ($userId = $this->option('user-id')) {
            $query->where('user_id', $userId);
        }

        if ($userEmail = $this->option('user-email')) {
            $query->where('email', $userEmail);
        }
    }

    private function syncUserEmails(object $user): void
    {
        $this->syncStats['users_processed']++;
        $userInfo = "User {$user->user_id} ({$user->email})";
        $syncLogId = null;

        try {
            $this->info("Syncing emails for {$userInfo}");
            $this->currentUser = $user;

            $syncLogId = $this->startSyncLog($user->user_id);
            $this->authenticateUser($user);

            $sinceDate = $this->getSinceDateForUser($user->user_id);
            $this->info("Fetching emails since: {$sinceDate}");

            $emails = $this->fetchAndProcessEmails($sinceDate);
            $saveResult = $this->saveEmailsToDatabase($emails);

            $this->updateSyncStats($emails, $saveResult);
            $this->completeSyncLog($syncLogId, 'success', count($emails));
            $this->info("✅ Successfully synced {$userInfo} - {$saveResult['new']} new, {$saveResult['updated']} updated");

            $this->syncStats['users_success']++;
        } catch (Exception $e) {
            $this->handleUserSyncError($user, $userInfo, $e, $syncLogId);
        }
    }

    private function authenticateUser(object $user): void
    {
        if ($this->option('debug')) {
            $this->info("Authenticating user {$user->user_id}");
        }

        if (!$this->validateAndRefreshToken($user)) {
            throw new Exception('Token validation failed');
        }

        if (!$this->getUserProfile()) {
            throw new Exception('Could not fetch user profile');
        }

        if ($this->option('debug')) {
            $this->info("User authenticated successfully");
        }
    }

    private function fetchAndProcessEmails(string $sinceDate): array
    {
        $rawEmails = $this->fetchUserEmails($sinceDate);
        $this->info("Fetched " . count($rawEmails) . " emails");

        if (empty($rawEmails)) {
            return [];
        }

        $emails = $this->processEmails($rawEmails);

        if ($this->option('fetch-attachments')) {
            $emails = $this->attachmentService()->fetchEmailAttachments($emails);
        }

        if ($this->option('fetch-profile-pictures')) {
            $emails = $this->profilePictureService()->fetchProfilePictures($emails);
        }

        return $emails;
    }

    private function updateSyncStats(array $emails, array $saveResult): void
    {
        $this->syncStats['total_emails'] += count($emails);
        $this->syncStats['new_emails'] += $saveResult['new'];
        $this->syncStats['updated_emails'] += $saveResult['updated'];
    }

    private function handleUserSyncError(object $user, string $userInfo, Exception $e, ?int $syncLogId): void
    {
        $this->syncStats['users_failed']++;
        $this->syncStats['errors'][] = "{$userInfo}: {$e->getMessage()}";

        $this->error("❌ Failed to sync {$userInfo}: {$e->getMessage()}");

        if ($this->option('debug')) {
            $this->error("Full error trace:");
            $this->error($e->getTraceAsString());
        }

        if ($syncLogId) {
            $this->completeSyncLog($syncLogId, 'failed', 0, $e->getMessage());
        }

        // 'f05b4f'

        // logger()->error('User email sync failed', [
        //     'user_id' => $user->user_id,
        //     'email' => $user->email,
        //     'error' => $e->getMessage(),
        //     'trace' => $e->getTraceAsString()
        // ]);
    }

    private function validateAndRefreshToken(object $user): bool
    {
        $this->accessToken = decrypt($user->access_token);
        $expiresAt = Carbon::createFromTimestamp($user->expires_at);

        if ($this->option('debug')) {
            $this->info("Token expires: {$expiresAt->toDateTimeString()} ({$expiresAt->diffForHumans()})");
        }

        if ($this->shouldRefreshToken($expiresAt)) {
            return $this->refreshToken($user);
        }

        return $this->validateToken();
    }

    private function shouldRefreshToken(Carbon $expiresAt): bool
    {
        $shouldRefresh = $expiresAt->diffInMinutes() < self::TOKEN_REFRESH_THRESHOLD_MINUTES
            || $this->option('force-refresh')
            || $expiresAt->isPast();

        if ($this->option('debug')) {
            $this->info("Should refresh token: " . ($shouldRefresh ? 'Yes' : 'No'));
        }

        return $shouldRefresh;
    }

    private function validateToken(): bool
    {
        try {
            if ($this->option('debug')) {
                $this->info("Validating access token...");
                $this->info("Token (first 20 chars): " . substr($this->accessToken, 0, 20) . "...");
            }

            $response = Http::withToken($this->accessToken)
                ->timeout(30)
                ->get(self::GRAPH_API_BASE . '/me');

            if ($this->option('debug')) {
                $this->info("Token validation response: HTTP {$response->status()}");
                if (!$response->successful()) {
                    $this->error("Token validation failed with response: " . $response->body());
                }
            }

            return $response->successful();
        } catch (Exception $e) {
            if ($this->option('debug')) {
                $this->error("Token validation exception: " . $e->getMessage());
            }
            $this->warn('Token validation failed: ' . $e->getMessage());
            return false;
        }
    }

    private function refreshToken(object $user): bool
    {
        if (!$user->refresh_token) {
            throw new Exception('No refresh token available');
        }

        $this->info("Token expires soon, attempting refresh for user {$user->user_id}...");

        try {
            $refreshToken = decrypt($user->refresh_token);
            $payload = [
                'client_id' => config('services.azure.client_id'),
                'client_secret' => config('services.azure.client_secret'),
                'grant_type' => 'refresh_token',
                'refresh_token' => $refreshToken,
                'scope' => implode(' ', self::REQUIRED_SCOPES)
            ];

            if (config('services.azure.redirect_uri')) {
                $payload['redirect_uri'] = config('services.azure.redirect_uri');
            }

            if ($this->option('debug')) {
                $this->info("Refreshing token with payload (excluding secrets):");
                $debugPayload = $payload;
                $debugPayload['client_secret'] = '[REDACTED]';
                $debugPayload['refresh_token'] = '[REDACTED]';
                $this->info(json_encode($debugPayload, JSON_PRETTY_PRINT));
            }

            $response = Http::asForm()
                ->timeout(30)
                ->post(
                    "https://login.microsoftonline.com/" . config('services.azure.tenant_id') . "/oauth2/v2.0/token",
                    $payload
                );

            if ($this->option('debug')) {
                $this->info("Token refresh response: HTTP {$response->status()}");
            }

            if (!$response->successful()) {
                $errorResponse = $response->json();
                if ($this->option('debug')) {
                    $this->error("Token refresh failed with response: " . json_encode($errorResponse, JSON_PRETTY_PRINT));
                }

                // logger()->error('Token refresh failed', [
                //     'user_id' => $user->user_id,
                //     'response_status' => $response->status(),
                //     'response_body' => $errorResponse,
                // ]);

                throw new Exception('Token refresh failed: ' . ($errorResponse['error_description'] ?? 'Unknown error'));
            }

            $data = $response->json();
            $this->updateTokenInDatabase($user, $data);
            $this->info("Token refreshed successfully for user {$user->user_id}");

            return true;
        } catch (Exception $e) {
            throw new Exception("Token refresh failed for user {$user->user_id}: " . $e->getMessage());
        }
    }

    private function updateTokenInDatabase(object $user, array $tokenData): void
    {
        $expiresAt = Carbon::now('UTC')->addSeconds($tokenData['expires_in']);

        if ($this->option('debug')) {
            $this->info("Updating token in database, new expiry: {$expiresAt->toDateTimeString()}");
        }

        DB::table('oauth_tokens')
            ->where([
                'email' => $user->email,
                'provider' => 'outlook'
            ])
            ->update([
                'access_token' => encrypt($tokenData['access_token']),
                'refresh_token' => encrypt($tokenData['refresh_token'] ?? $user->refresh_token),
                'expires_at' => $expiresAt->setTimezone(config('app.timezone'))->timestamp,
                'scope' => $tokenData['scope'] ?? implode(' ', self::REQUIRED_SCOPES),
                'updated_at' => now(),
            ]);

        $this->accessToken = $tokenData['access_token'];
    }

    private function fetchUserEmails(string $sinceDate): array
    {
        $url = $this->buildEmailsUrl($sinceDate);

        if ($this->option('debug')) {
            $this->info("Fetching emails from API URL: {$url}");
            $this->info("Access token (first 20 chars): " . substr($this->accessToken, 0, 20) . "...");
        }

        return $this->executeWithRetry(function () use ($url) {
            $response = Http::withToken($this->accessToken)
                ->timeout(self::HTTP_TIMEOUT_SECONDS)
                ->get($url);

            if ($this->option('debug')) {
                $this->info("API Response Status: HTTP {$response->status()}");
                $this->info("Response Headers: " . json_encode($response->headers()));
            }

            if (!$response->successful()) {
                $errorBody = $response->body();
                if ($this->option('debug')) {
                    $this->error("API Error Response: " . $errorBody);
                }
                throw new Exception('Failed to fetch emails: HTTP ' . $response->status() . ' - ' . $errorBody);
            }

            $data = $response->json();
            $emails = $data['value'] ?? [];

            if ($this->option('debug')) {
                $this->info("Successfully fetched " . count($emails) . " emails from API");
                if (!empty($emails)) {
                    $this->info("First email subject: " . ($emails[0]['subject'] ?? 'No subject'));
                    $this->info("Last email subject: " . (end($emails)['subject'] ?? 'No subject'));
                }
            }

            return $emails;
        });
    }

    public function executeWithRetry(callable $callback): mixed
    {
        $maxRetries = (int) $this->option('max-retries');
        $lastException = null;

        for ($attempt = 1; $attempt <= $maxRetries; $attempt++) {
            try {
                return $callback();
            } catch (Exception $e) {
                $lastException = $e;

                if ($attempt < $maxRetries) {
                    $delay = min(2 ** ($attempt - 1), 30);
                    if ($this->option('debug')) {
                        $this->warn("Attempt {$attempt} failed: {$e->getMessage()}. Retrying in {$delay} seconds...");
                    }
                    sleep($delay);
                } else {
                    if ($this->option('debug')) {
                        $this->error("All {$maxRetries} attempts failed. Last error: {$e->getMessage()}");
                    }
                }
            }
        }

        throw $lastException;
    }

    private function buildEmailsUrl(string $sinceDate): string
    {
        $folder = $this->option('folder');
        $limit = min((int) $this->option('limit'), self::MAX_EMAILS_PER_REQUEST);

        $folderPath = $this->getFolderPath($folder);
        $params = $this->buildUrlParams($limit, $sinceDate);

        $url = self::GRAPH_API_BASE . "/me/{$folderPath}/messages?" . http_build_query($params, '', '&', PHP_QUERY_RFC3986);

        if ($this->option('debug')) {
            $this->info("Built URL parameters:");
            foreach ($params as $key => $value) {
                $this->info("  {$key}: {$value}");
            }
        }

        return $url;
    }

    private function getFolderPath(string $folder): string
    {
        return match ($folder) {
            'inbox' => 'mailFolders/inbox',
            'sent' => 'mailFolders/sentitems',
            'drafts' => 'mailFolders/drafts',
            'deleted' => 'mailFolders/deleteditems',
            default => "mailFolders/{$folder}"
        };
    }

    private function buildUrlParams(int $limit, string $sinceDate): array
    {
        $params = [
            '$top' => $limit,
            '$select' => implode(',', $this->getDefaultSelectFields()),
            '$orderby' => 'receivedDateTime desc',
        ];

        $filterDate = $sinceDate ?: now()->subDays(30)->toDateTimeString();
        // Use proper ISO 8601 format for Graph API
        $params['$filter'] = "receivedDateTime ge " . Carbon::parse($filterDate)->format('Y-m-d\TH:i:s\Z');

        return $params;
    }

    private function getDefaultSelectFields(): array
    {
        return [
            'id',
            'subject',
            'from',
            'toRecipients',
            'ccRecipients',
            'bccRecipients',
            'receivedDateTime',
            'sentDateTime',
            'body',
            'bodyPreview',
            'importance',
            'isRead',
            'hasAttachments',
            'internetMessageId',
            'conversationId',
            'categories'
        ];
    }

    private function processEmails(array $rawEmails): array
    {
        return $this->processEmailsWithImageLinking($rawEmails);
    }

    private function attachmentService(): object
    {
        return new class($this) {
            private $command;
            private $user;

            public function __construct($command)
            {
                $this->command = $command;
                $this->user = $this->command->currentUser;
            }

            public function fetchEmailAttachments(array $emails): array
            {
                $this->command->info('Fetching attachments...');
                $emailsWithAttachments = array_filter($emails, fn($email) => $email['has_attachments']);

                if (empty($emailsWithAttachments)) {
                    $this->command->info('No emails with attachments found.');
                    return $emails;
                }

                $this->command->info('Found ' . count($emailsWithAttachments) . ' emails with attachments');
                $bar = $this->command->getOutput()->createProgressBar(count($emailsWithAttachments));
                $bar->start();

                foreach ($emails as $index => $email) {
                    if (!$email['has_attachments']) {
                        continue;
                    }

                    try {
                        $attachments = $this->getEmailAttachments($email['id']);
                        // $emails[$index]['attachments'] = $attachments;

                        // if ($this->command->option('download-attachments')) {
                        $emails[$index]['attachments'] = $this->downloadAttachments($email['id'], $attachments);
                        // }

                        $this->command->syncStats['attachments_downloaded'] += count($attachments);


                        if ($this->command->option('debug')) {
                            $this->command->info("Fetched " . count($attachments) . " attachments for email: " . ($email['subject'] ?? 'No Subject'));
                        }
                    } catch (Exception $e) {
                        if ($this->command->option('debug')) {
                            $this->command->warn("Failed to fetch attachments for email {$email['id']}: " . $e->getMessage());
                        }
                    }

                    $bar->advance();
                }

                $bar->finish();
                $this->command->line('');
                return $emails;
            }

            /**
             * Download attachments content
             */
            private function downloadAttachments(string $messageId, array $attachments): array
            {
                $maxSize = (int) $this->command->option('attachment-max-size');
                $allowedTypes = $this->command->option('attachment-types') ?
                    explode(',', strtolower($this->command->option('attachment-types'))) : null;
                $storageDisk = $this->command->option('attachment-storage');

                try {
                    Storage::disk($storageDisk);
                } catch (\InvalidArgumentException $e) {
                    $this->command->error("Storage disk '{$storageDisk}' is not configured. Please check your config/filesystems.php");
                    $this->command->info("Available disks: " . implode(', ', array_keys(config('filesystems.disks'))));
                    return $attachments;
                }

                foreach ($attachments as $index => $attachment) {
                    try {
                        if ($attachment['size'] > $maxSize) {
                            $attachments[$index]['download_error'] = "File too large ({$attachment['size']} bytes > {$maxSize} bytes)";
                            continue;
                        }

                        if ($allowedTypes) {
                            $extension = strtolower(pathinfo($attachment['name'], PATHINFO_EXTENSION));
                            if (!in_array($extension, $allowedTypes)) {
                                $attachments[$index]['download_error'] = "File type not allowed: {$extension}";
                                continue;
                            }
                        }

                        $content = $this->downloadAttachmentContent($messageId, $attachment['id']);

                        if ($content) {
                            $filename = $this->generateUniqueFilename($attachment['name'], $messageId);
                            $filepath = "public/emails/{$this->user->email}/{$filename}";

                            $directory = dirname($filepath);
                            if (!Storage::disk($storageDisk)->exists($directory)) {
                                Storage::disk($storageDisk)->makeDirectory($directory);
                            }

                            $fileExists = Storage::disk($storageDisk)->exists($filepath);

                            Storage::disk($storageDisk)->put($filepath, $content);

                            $attachments[$index]['downloaded'] = true;
                            $attachments[$index]['file_path'] = $filepath;

                            if ($this->command->option('debug')) {
                                if ($fileExists) {
                                    $this->command->info("File already existed, overwritten: {$attachment['name']} -> {$filepath}");
                                } else {
                                    $this->command->info("Downloaded: {$attachment['name']} -> {$filepath}");
                                }
                            }
                        }
                    } catch (\Exception $e) {
                        $attachments[$index]['download_error'] = $e->getMessage();
                        if ($this->command->option('debug')) {
                            $this->command->warn("Failed to download {$attachment['name']}: " . $e->getMessage());
                        }
                    }
                }

                return $attachments;
            }

            private function downloadAttachmentContent(string $messageId, string $attachmentId): ?string
            {
                $url = SyncOutlookEmails::GRAPH_API_BASE . "/me/messages/{$messageId}/attachments/{$attachmentId}/\$value";

                $response = Http::withToken($this->command->accessToken)->timeout(120)->get($url);

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

                $slugged = Str::slug($basename);
                $underscored = str_replace('-', '_', $slugged);

                return $underscored . ($extension ? ".{$extension}" : '');
            }

            private function getEmailAttachments(string $messageId): array
            {
                return $this->command->executeWithRetry(function () use ($messageId) {
                    $url = SyncOutlookEmails::GRAPH_API_BASE . "/me/messages/{$messageId}/attachments";

                    if ($this->command->option('debug')) {
                        $this->command->info("Fetching attachments from: {$url}");
                    }

                    $response = Http::withToken($this->command->accessToken)->timeout(30)->get($url);

                    if (!$response->successful()) {
                        throw new Exception('Failed to fetch attachments: HTTP ' . $response->status() . ' - ' . $response->body());
                    }

                    $data = $response->json();
                    $attachments = [];

                    foreach ($data['value'] ?? [] as $attachment) {
                        $attachments[] = [
                            'id' => $attachment['id'] ?? null,
                            'name' => $attachment['name'] ?? 'unknown',
                            'content_type' => $attachment['contentType'] ?? 'application/octet-stream',
                            'size' => $attachment['size'] ?? 0,
                            'is_inline' => $attachment['isInline'] ?? false,
                        ];
                    }

                    return $attachments;
                });
            }
        };
    }

    private function profilePictureService(): object
    {
        return new class($this) {
            private $command;

            public function __construct($command)
            {
                $this->command = $command;
            }

            public function fetchProfilePictures(array $emails): array
            {
                $this->command->info('Fetching profile pictures...');
                $senderEmails = $this->getUniqueSenders($emails);

                if (empty($senderEmails)) {
                    $this->command->info('No sender emails found for profile pictures.');
                    return $emails;
                }

                $this->command->info('Found ' . count($senderEmails) . ' unique senders');
                $profilePictureCache = $this->buildProfilePictureCache($senderEmails);

                return $this->applyProfilePicturesToEmails($emails, $profilePictureCache);
            }

            private function getUniqueSenders(array $emails): array
            {
                $senders = [];
                foreach ($emails as $email) {
                    if ($email['from'] && !in_array($email['from'], $senders)) {
                        $senders[] = $email['from'];
                    }
                }
                return array_unique($senders);
            }

            private function buildProfilePictureCache(array $senderEmails): array
            {
                $cache = [];
                $bar = $this->command->getOutput()->createProgressBar(count($senderEmails));
                $bar->start();

                foreach ($senderEmails as $senderEmail) {
                    try {
                        $profilePicture = $this->getUserProfilePicture($senderEmail);
                        $cache[$senderEmail] = $profilePicture;

                        if ($profilePicture['available']) {
                            $this->command->syncStats['profile_pictures_downloaded']++;
                        }
                    } catch (Exception $e) {
                        $cache[$senderEmail] = $this->createErrorProfilePicture($e->getMessage());

                        if ($this->command->option('debug')) {
                            $this->command->warn("Failed to fetch profile picture for {$senderEmail}: " . $e->getMessage());
                        }
                    }

                    $bar->advance();
                }

                $bar->finish();
                $this->command->line('');
                return $cache;
            }

            private function applyProfilePicturesToEmails(array $emails, array $cache): array
            {
                foreach ($emails as $index => $email) {
                    if ($email['from'] && isset($cache[$email['from']])) {
                        $emails[$index]['profile_picture'] = $cache[$email['from']];
                    }
                }
                return $emails;
            }

            private function getUserProfilePicture(string $userEmail): array
            {
                $size = $this->command->option('profile-picture-size');

                return $this->command->executeWithRetry(function () use ($userEmail, $size) {
                    $metadata = $this->fetchProfilePictureMetadata($userEmail, $size);
                    if (!$metadata['available']) {
                        return $metadata;
                    }

                    $profilePicture = [
                        'available' => true,
                        'width' => $metadata['width'] ?? null,
                        'height' => $metadata['height'] ?? null,
                        'size' => null,
                        'content_type' => 'image/jpeg',
                        'downloaded' => false,
                        'file_path' => null,
                        'error' => null
                    ];

                    if ($this->command->option('download-profile-pictures')) {
                        try {
                            $profilePicture = $this->downloadProfilePicture($userEmail, $profilePicture);
                        } catch (Exception $e) {
                            $profilePicture['error'] = $e->getMessage();
                            if ($this->command->option('debug')) {
                                $this->command->warn("Failed to download profile picture for {$userEmail}: " . $e->getMessage());
                            }
                        }
                    }

                    return $profilePicture;
                });
            }

            private function fetchProfilePictureMetadata(string $userEmail, string $size): array
            {
                $url = SyncOutlookEmails::GRAPH_API_BASE . "/users/{$userEmail}/photos/{$size}";

                if ($this->command->option('debug')) {
                    $this->command->info("Fetching profile picture metadata: {$url}");
                }

                $response = Http::withToken($this->command->accessToken)->timeout(10)->get($url);

                if (!$response->successful()) {
                    if ($response->status() === 404) {
                        return $this->createErrorProfilePicture('No profile picture available');
                    }

                    throw new Exception('Failed to fetch profile picture metadata: HTTP ' . $response->status() . ' - ' . $response->body());
                }

                $data = $response->json();
                return array_merge($data, ['available' => true]);
            }

            private function downloadProfilePicture(string $userEmail, array $profilePicture): array
            {
                $size = $this->command->option('profile-picture-size');
                $storageDisk = $this->command->option('attachment-storage');

                $this->validateStorageDisk($storageDisk);

                $content = $this->downloadProfilePictureContent($userEmail, $size);
                $filepath = $this->saveProfilePictureToStorage($userEmail, $size, $content, $storageDisk);

                $profilePicture['size'] = strlen($content);
                $profilePicture['downloaded'] = true;
                $profilePicture['file_path'] = $filepath;

                if ($this->command->option('debug')) {
                    $this->command->info("Downloaded profile picture: {$userEmail} -> {$filepath} (" . $this->formatBytes($profilePicture['size']) . ")");
                }

                return $profilePicture;
            }

            private function validateStorageDisk(string $storageDisk): void
            {
                try {
                    Storage::disk($storageDisk);
                } catch (\InvalidArgumentException $e) {
                    throw new Exception("Storage disk '{$storageDisk}' is not configured");
                }
            }

            private function downloadProfilePictureContent(string $userEmail, string $size): string
            {
                $url = SyncOutlookEmails::GRAPH_API_BASE . "/users/{$userEmail}/photos/{$size}/\$value";

                if ($this->command->option('debug')) {
                    $this->command->info("Downloading profile picture content: {$url}");
                }

                $response = Http::withToken($this->command->accessToken)->timeout(30)->get($url);

                if (!$response->successful()) {
                    throw new Exception('Failed to download profile picture: HTTP ' . $response->status() . ' - ' . $response->body());
                }

                return $response->body();
            }

            private function saveProfilePictureToStorage(string $userEmail, string $size, string $content, string $storageDisk): string
            {
                $filename = $this->generateProfilePictureFilename($userEmail, $size);
                $filepath = "profile_pictures/{$filename}";

                if (!Storage::disk($storageDisk)->exists('profile_pictures')) {
                    Storage::disk($storageDisk)->makeDirectory('profile_pictures');
                }

                Storage::disk($storageDisk)->put($filepath, $content);
                return $filepath;
            }

            private function generateProfilePictureFilename(string $userEmail, string $size): string
            {
                $emailHash = md5(strtolower(trim($userEmail)));
                $timestamp = now()->format('Y-m-d_H-i-s');
                return "profile_{$emailHash}_{$size}_{$timestamp}.jpg";
            }

            private function createErrorProfilePicture(string $error): array
            {
                return [
                    'available' => false,
                    'error' => $error,
                    'downloaded' => false,
                    'file_path' => null
                ];
            }

            private function formatBytes(int $bytes): string
            {
                if ($bytes >= 1024 * 1024) {
                    return round($bytes / (1024 * 1024), 2) . ' MB';
                } elseif ($bytes >= 1024) {
                    return round($bytes / 1024, 2) . ' KB';
                }
                return $bytes . ' B';
            }
        };
    }

    private function saveEmailsToDatabase(array $emails): array
    {
        $this->info('Saving emails with image links to database...');
        $newCount = 0;
        $updatedCount = 0;

        foreach ($emails as $email) {
            try {
                $result = $this->saveEmailWithImageLinks($email);
                $result['isNew'] ? $newCount++ : $updatedCount++;
            } catch (Exception $e) {
                if ($this->option('debug')) {
                    $this->warn('Failed to save email with images: ' . $e->getMessage());
                }
            }
        }

        return ['new' => $newCount, 'updated' => $updatedCount];
    }

    private function saveOrUpdateEmail(array $email): array
    {
        if (!$email['message_id']) {
            throw new Exception('Email missing message_id, cannot save');
        }

        if ($this->option('debug')) {
            $this->info("Saving email: " . ($email['subject'] ?? '[No Subject]'));
            $this->info("Message ID: " . $email['message_id']);
        }

        $existingEmail = $this->findExistingEmail($email['message_id']);
        $emailData = $this->buildEmailData($email);

        if ($this->option('debug')) {
            $this->info("Existing email found: " . ($existingEmail ? 'Yes (ID: ' . $existingEmail->id . ')' : 'No'));
        }

        if ($existingEmail) {
            DB::table('fetched_emails')->where('id', $existingEmail->id)->update($emailData);
            return ['isNew' => false, 'id' => $existingEmail->id];
        } else {
            $emailData['message_id'] = $email['message_id'];
            $emailData['created_at'] = now();

            $id = DB::table('fetched_emails')->insertGetId($emailData);
            return ['isNew' => true, 'id' => $id];
        }
    }

    private function findExistingEmail(string $messageId): ?object
    {
        return DB::table('fetched_emails')
            ->where('message_id', $messageId)
            ->where('user_id', $this->currentUser->user_id)
            ->where('user_email', $this->currentUser->email)
            ->first();
    }

    private function buildEmailData(array $email): array
    {
        return [
            'user_id' => $this->currentUser->user_id,
            'user_email' => $this->currentUser->email,
            'uid' => $email['id'],
            'subject' => substr($email['subject'], 0, 255),
            'from_email' => $email['from'],
            'from_name' => $email['from_name'] ? substr($email['from_name'], 0, 255) : null,
            'to_recipients' => json_encode($email['to']),
            'cc_recipients' => json_encode($email['cc']),
            'bcc_recipients' => json_encode($email['bcc'] ?? []),
            'date_received' => $email['date_received'],
            'date_sent' => $email['date_sent'],
            'body_text' => $this->stripAndTruncateHtml($email['body_content'] ?? ''),
            'body_html' => $email['body_content'],
            'body_preview' => substr($email['body_preview'], 0, 500),
            'importance' => $email['importance'],
            'is_read' => $email['is_read'],
            'has_attachments' => $email['has_attachments'],
            'conversation_id' => $email['conversation_id'],
            'categories' => json_encode($email['categories'] ?? []),
            'folder' => $this->option('folder'),
            'updated_at' => now()
        ];
    }

    private function stripAndTruncateHtml(?string $html, int $maxLength = 10000): ?string
    {
        if (!$html) {
            return null;
        }

        $text = strip_tags($html);
        return substr($text, 0, $maxLength);
    }

    private function saveRelatedData(array $email): void
    {
        if (!empty($email['attachments'])) {
            $this->saveAttachmentsToDatabase($email['message_id'], $email['attachments']);
        }

        if (!empty($email['profile_picture']) && $email['profile_picture']['available']) {
            $this->saveProfilePictureToDatabase($email['message_id'], $email['from'], $email['profile_picture']);
        }
    }

    private function saveAttachmentsToDatabase(string $messageId, array $attachments): void
    {

        // foreach ($attachments as $attachment) {
        //     try {
        //         DB::table('email_attachments')->updateOrInsert(
        //             [
        //                 'message_id' => $messageId,
        //                 'attachment_id' => $attachment['id'],
        //                 'user_id' => $this->currentUser->user_id,
        //                 'user_email' => $this->currentUser->email
        //             ],
        //             [
        //                 'name' => substr($attachment['name'], 0, 255),
        //                 'content_type' => $attachment['content_type'],
        //                 'size' => $attachment['size'],
        //                 'is_inline' => $attachment['is_inline'],
        //                 'created_at' => now(),
        //                 'updated_at' => now()
        //             ]
        //         );
        //     } catch (Exception $e) {
        //         if ($this->option('debug')) {
        //             $this->warn("Failed to save attachment {$attachment['name']}: " . $e->getMessage());
        //         }
        //     }
        // }
    }

    private function saveProfilePictureToDatabase(string $messageId, string $senderEmail, array $profilePicture): void
    {
        try {
            DB::table('email_profile_pictures')->updateOrInsert(
                [
                    'sender_email' => $senderEmail,
                    'user_id' => $this->currentUser->user_id,
                ],
                [
                    'width' => $profilePicture['width'],
                    'height' => $profilePicture['height'],
                    'size' => $profilePicture['size'],
                    'content_type' => $profilePicture['content_type'],
                    'downloaded' => $profilePicture['downloaded'],
                    'file_path' => $profilePicture['file_path'],
                    'error' => $profilePicture['error'],
                    'user_email' => $this->currentUser->email,
                    'last_fetched' => now(),
                    'created_at' => now(),
                    'updated_at' => now()
                ]
            );

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
        } catch (Exception $e) {
            if ($this->option('debug')) {
                $this->warn("Failed to save profile picture for {$senderEmail}: " . $e->getMessage());
            }
        }
    }

    private function startSyncLog(int $userId): int
    {
        // email_sync_logs
        DB::table('email_sync_logs')->where('user_id', $userId)->delete();

        return DB::table('email_sync_logs')->insertGetId([
            'user_id' => $userId,
            'folder' => $this->option('folder'),
            'status' => 'running',
            'started_at' => now()->utc(),
            'options' => json_encode($this->options()),
            'created_at' => now()->utc(),
            'updated_at' => now()->utc()
        ]);
    }

    // private function startSyncLog(int $userId): int
    // {
    //     return DB::table('email_sync_logs')->insertGetId([
    //         'user_id' => $userId,
    //         'folder' => $this->option('folder'),
    //         'status' => 'running',
    //         'started_at' => now(),
    //         'options' => json_encode($this->options()),
    //         'created_at' => now(),
    //         'updated_at' => now()
    //     ]);
    // }

    private function completeSyncLog(int $syncLogId, string $status, int $emailCount, ?string $error = null): void
    {
        $updateData = [
            'status' => $status,
            'emails_processed' => $emailCount,
            'error_message' => $error,
            'completed_at' => now()->utc(), // Ensure UTC timezone for consistency
            'updated_at' => now()->utc()
        ];

        if ($this->option('debug')) {
            $this->info("Completing sync log {$syncLogId} with status: {$status}, emails: {$emailCount}");
            if ($error) {
                $this->error("Sync error: {$error}");
            }
        }

        $updated = DB::table('email_sync_logs')
            ->where('id', $syncLogId)
            ->update($updateData);

        if ($this->option('debug')) {
            $this->info("Sync log update result: " . ($updated ? 'Success' : 'Failed'));
        }
    }

    // private function completeSyncLog(int $syncLogId, string $status, int $emailCount, ?string $error = null): void
    // {
    //     DB::table('email_sync_logs')
    //         ->where('id', $syncLogId)
    //         ->update([
    //             'status' => $status,
    //             'emails_processed' => $emailCount,
    //             'error_message' => $error,
    //             'completed_at' => now(),
    //             'updated_at' => now()
    //         ]);
    // }


    private function getUserProfile(): ?array
    {
        try {
            $response = Http::withToken($this->accessToken)
                ->timeout(10)
                ->get(self::GRAPH_API_BASE . '/me');

            if ($response->successful()) {
                $profile = $response->json();
                if ($this->option('debug')) {
                    $this->info("User profile: " . ($profile['displayName'] ?? 'Unknown') . " (" . ($profile['mail'] ?? $profile['userPrincipalName'] ?? 'No email') . ")");
                }
                return $profile;
            }

            if ($this->option('debug')) {
                $this->error("Failed to get user profile: HTTP {$response->status()} - " . $response->body());
            }

            return null;
        } catch (Exception $e) {
            if ($this->option('debug')) {
                $this->error("Exception getting user profile: " . $e->getMessage());
            }
            return null;
        }
    }

    private function getSinceDateForUser(int $userId): string
    {
        $folder = $this->option('folder');

        // Get the last successful sync for this specific user AND folder combination
        $lastSync = DB::table('email_sync_logs')
            ->where('user_id', $userId)
            ->where('folder', $folder) // Add folder filter
            ->where('status', 'success')
            ->orderBy('completed_at', 'desc') // Ensure we get the most recent
            ->value('completed_at'); // Use value() instead of max() for better performance

        if ($lastSync) {
            // Ensure we're working with Carbon instance and proper timezone
            $lastSyncCarbon = Carbon::parse($lastSync)->setTimezone(config('app.timezone'));
            $sinceDate = $lastSyncCarbon->subHours(1)->utc()->toDateTimeString();

            if ($this->option('debug')) {
                $this->info("Found previous successful sync for folder '{$folder}' at {$lastSyncCarbon->toDateTimeString()}, syncing since {$sinceDate}");
            }
            return $sinceDate;
        }

        $daysBack = (int) $this->option('days-back');
        $sinceDate = now()->utc()->subDays($daysBack)->toDateTimeString();

        if ($this->option('debug')) {
            $this->info("No previous sync found for folder '{$folder}', syncing emails from {$daysBack} days back: {$sinceDate}");
        }

        return $sinceDate;
    }

    // private function getSinceDateForUser(int $userId): string
    // {
    //     $lastSync = DB::table('email_sync_logs')
    //         ->where('user_id', $userId)
    //         ->where('status', 'success')
    //         ->max('completed_at');

    //     if ($lastSync) {
    //         $sinceDate = Carbon::parse($lastSync)->subHours(1)->toDateTimeString();
    //         if ($this->option('debug')) {
    //             $this->info("Found previous successful sync at {$lastSync}, syncing since {$sinceDate}");
    //         }
    //         return $sinceDate;
    //     }

    //     $daysBack = (int) $this->option('days-back');
    //     $sinceDate = now()->subDays($daysBack)->toDateTimeString();

    //     if ($this->option('debug')) {
    //         $this->info("No previous sync found, syncing emails from {$daysBack} days back: {$sinceDate}");
    //     }

    //     return $sinceDate;
    // }

    private function initializeSyncStats(): void
    {
        $this->syncStats = [
            'users_processed' => 0,
            'users_success' => 0,
            'users_failed' => 0,
            'total_emails' => 0,
            'new_emails' => 0,
            'updated_emails' => 0,
            'attachments_downloaded' => 0,
            'profile_pictures_downloaded' => 0,
            'errors' => []
        ];
    }

    private function showDryRunResults(Collection $users): void
    {
        $this->info("DRY RUN: Would sync {$users->count()} user(s)");

        $this->table(
            ['User ID', 'Email', 'Token Status', 'Last Sync', 'Days Back', 'Folder'],
            $users->map(function ($user) {
                $lastSync = $this->getLastSyncTime($user->user_id);
                return [
                    $user->user_id,
                    $user->email ?? 'N/A',
                    $this->getTokenStatus($user),
                    $lastSync ? Carbon::parse($lastSync)->diffForHumans() : 'Never',
                    $this->option('days-back'),
                    $this->option('folder')
                ];
            })->toArray()
        );

        $this->info("\nOptions that would be used:");
        $this->info("- Folder: " . $this->option('folder'));
        $this->info("- Limit per user: " . $this->option('limit'));
        $this->info("- Fetch attachments: " . ($this->option('fetch-attachments') ? 'Yes' : 'No'));
        $this->info("- Fetch profile pictures: " . ($this->option('fetch-profile-pictures') ? 'Yes' : 'No'));
        $this->info("- Download profile pictures: " . ($this->option('download-profile-pictures') ? 'Yes' : 'No'));
        $this->info("- Storage disk: " . $this->option('attachment-storage'));
        $this->info("- Max retries: " . $this->option('max-retries'));
    }

    private function getTokenStatus(object $user): string
    {
        if (!$user->access_token) {
            return '❌ No token';
        }

        $expiresAt = Carbon::createFromTimestamp($user->expires_at);

        if ($expiresAt->isPast()) {
            return '⚠️ Expired';
        }

        if ($expiresAt->diffInHours() < 24) {
            return '⚠️ Expires soon (' . $expiresAt->diffForHumans() . ')';
        }

        return '✅ Valid (' . $expiresAt->diffForHumans() . ')';
    }

    private function getLastSyncTime(int $userId): ?string
    {
        return DB::table('email_sync_logs')
            ->where('user_id', $userId)
            ->where('status', 'success')
            ->max('completed_at');
    }

    private function showSyncResults(): void
    {
        $this->info("\n=== SYNC RESULTS ===");

        $this->table(
            ['Metric', 'Count'],
            [
                ['Users Processed', $this->syncStats['users_processed']],
                ['Users Success', $this->syncStats['users_success'] . ' / ' . $this->syncStats['users_processed']],
                ['Users Failed', $this->syncStats['users_failed']],
                ['Total Emails Fetched', $this->syncStats['total_emails']],
                ['New Emails Saved', $this->syncStats['new_emails']],
                ['Updated Emails', $this->syncStats['updated_emails']],
                ['Attachments Downloaded', $this->syncStats['attachments_downloaded']],
                ['Profile Pictures Downloaded', $this->syncStats['profile_pictures_downloaded']],
            ]
        );

        if (!empty($this->syncStats['errors'])) {
            $this->error("\nErrors encountered:");
            foreach ($this->syncStats['errors'] as $error) {
                $this->line("  • {$error}");
            }
        }

        // Summary
        if ($this->syncStats['users_success'] > 0) {
            $this->info("\n✅ Sync completed successfully for {$this->syncStats['users_success']} user(s)");
        }

        if ($this->syncStats['users_failed'] > 0) {
            $this->warn("❌ Sync failed for {$this->syncStats['users_failed']} user(s)");
        }

        if ($this->syncStats['new_emails'] > 0 || $this->syncStats['updated_emails'] > 0) {
            $this->info("📧 Total emails processed: {$this->syncStats['total_emails']} (New: {$this->syncStats['new_emails']}, Updated: {$this->syncStats['updated_emails']})");
        }
    }

    private function preventDuplicateRunningSyncs(int $userId): bool
    {
        $folder = $this->option('folder');

        $existingRunning = DB::table('email_sync_logs')
            ->where('user_id', $userId)
            ->where('folder', $folder)
            ->where('status', 'running')
            ->where('started_at', '>', now()->subHours(2)) // Only consider recent running logs
            ->exists();

        if ($existingRunning) {
            $this->warn("Sync already running for user {$userId}, folder '{$folder}'. Skipping...");
            return false;
        }

        return true;
    }
}
