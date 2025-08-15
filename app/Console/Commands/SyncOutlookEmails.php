<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;
use Exception;
use Illuminate\Support\Collection;

class SyncOutlookEmails extends Command
{
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
                           {--debug : Enable debug output}';

    protected $description = 'Synchronize Outlook emails for users with valid OAuth tokens';

    public const GRAPH_API_BASE = 'https://graph.microsoft.com/v1.0';
    private const TOKEN_REFRESH_THRESHOLD_MINUTES = 30;
    private const MAX_EMAILS_PER_REQUEST = 999;
    private const HTTP_TIMEOUT_SECONDS = 60;

    public string $accessToken = '';
    public object $currentUser;
    public array $syncStats = [];

    public function handle(): int
    {
        try {
            $this->displayHeader();
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

        logger()->error('Outlook sync failed', [
            'error' => $e->getMessage(),
            'trace' => $e->getTraceAsString()
        ]);

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

        return $query->get();
    }

    private function validateSyncOptions(): bool
    {
        if (!$this->option('all-users') && !$this->option('user-id') && !$this->option('user-email')) {
            $this->error('Please specify --user-id, --user-email, or --all-users');
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
            $this->info("✅ Successfully synced {$userInfo}");

            $this->syncStats['users_success']++;
        } catch (Exception $e) {
            $this->handleUserSyncError($user, $userInfo, $e, $syncLogId ?? null);
        }
    }

    private function authenticateUser(object $user): void
    {
        if (!$this->validateAndRefreshToken($user)) {
            throw new Exception('Token validation failed');
        }

        if (!$this->getUserProfile()) {
            throw new Exception('Could not fetch user profile');
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
            $this->error($e->getTraceAsString());
        }

        if ($syncLogId) {
            $this->completeSyncLog($syncLogId, 'failed', 0, $e->getMessage());
        }

        logger()->error('User email sync failed', [
            'user_id' => $user->user_id,
            'email' => $user->email,
            'error' => $e->getMessage()
        ]);
    }

    // ===== Authentication & Token Management =====

    private function validateAndRefreshToken(object $user): bool
    {
        $this->accessToken = decrypt($user->access_token);
        $expiresAt = Carbon::createFromTimestamp($user->expires_at);

        if ($this->shouldRefreshToken($expiresAt)) {
            return $this->refreshToken($user);
        }

        return $this->validateToken();
    }

    private function shouldRefreshToken(Carbon $expiresAt): bool
    {
        return $expiresAt->diffInMinutes() < self::TOKEN_REFRESH_THRESHOLD_MINUTES
            || $this->option('force-refresh')
            || $expiresAt->isPast();
    }

    private function validateToken(): bool
    {
        try {
            $response = Http::withToken($this->accessToken)
                ->timeout(30)
                ->get(self::GRAPH_API_BASE . '/me');

            return $response->successful();
        } catch (Exception $e) {
            $this->warn('Token validation failed');
            return false;
        }
    }

    private function refreshToken(object $user): bool
    {
        if (!$user->refresh_token) {
            throw new Exception('No refresh token available');
        }

        $this->info('Token expires soon, attempting refresh...');

        try {
            $response = Http::asForm()->post('https://login.microsoftonline.com/common/oauth2/v2.0/token', [
                'client_id' => config('services.microsoft.client_id'),
                'client_secret' => config('services.microsoft.client_secret'),
                'refresh_token' => $user->refresh_token,
                'grant_type' => 'refresh_token',
                'scope' => 'https://graph.microsoft.com/Mail.Read https://graph.microsoft.com/User.Read'
            ]);

            if (!$response->successful()) {
                throw new Exception('Token refresh failed: ' . $response->body());
            }

            $this->updateTokenInDatabase($user, $response->json());
            $this->info('Token refreshed successfully');

            return true;
        } catch (Exception $e) {
            throw new Exception('Token refresh failed: ' . $e->getMessage());
        }
    }

    private function updateTokenInDatabase(object $user, array $tokenData): void
    {
        DB::table('oauth_tokens')
            ->where('id', $user->id)
            ->update([
                'access_token' => $tokenData['access_token'],
                'refresh_token' => $tokenData['refresh_token'] ?? $user->refresh_token,
                'expires_at' => now()->addSeconds($tokenData['expires_in']),
                'updated_at' => now()
            ]);

        $this->accessToken = $tokenData['access_token'];
    }

    private function fetchUserEmails(string $sinceDate): array
    {
        $url = $this->buildEmailsUrl($sinceDate);

        if ($this->option('debug')) {
            $this->info("API URL: {$url}");
        }

        $response = Http::withToken($this->accessToken)
            ->timeout(self::HTTP_TIMEOUT_SECONDS)
            ->get($url);

        if (!$response->successful()) {
            throw new Exception('Failed to fetch emails: HTTP ' . $response->status() . ' - ' . $response->body());
        }

        return $response->json()['value'] ?? [];
    }

    private function buildEmailsUrl(string $sinceDate): string
    {
        $folder = $this->option('folder');
        $limit = min((int) $this->option('limit'), self::MAX_EMAILS_PER_REQUEST);

        $folderPath = $this->getFolderPath($folder);
        $params = $this->buildUrlParams($limit, $sinceDate);

        return self::GRAPH_API_BASE . "/me/{$folderPath}/messages?" . http_build_query($params, '', '&', PHP_QUERY_RFC3986);
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
        $params['$filter'] = "receivedDateTime ge " . Carbon::parse($filterDate)->toISOString();

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

    private function processEmails(array $rawEmails): array
    {
        $emails = [];

        foreach ($rawEmails as $rawEmail) {
            try {
                $emails[] = $this->transformEmailData($rawEmail);
            } catch (Exception $e) {
                if ($this->option('debug')) {
                    $this->warn('Failed to process email: ' . $e->getMessage());
                }
            }
        }

        return $emails;
    }

    private function transformEmailData(array $rawEmail): array
    {
        return [
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
            'attachments' => [],
            'profile_picture' => null,
        ];
    }

    private function attachmentService(): object
    {
        return new class($this) {
            private $command;

            public function __construct($command)
            {
                $this->command = $command;
            }

            public function fetchEmailAttachments(array $emails): array
            {
                $this->command->info('Fetching attachments...');
                $emailsWithAttachments = array_filter($emails, fn($email) => $email['has_attachments']);

                if (empty($emailsWithAttachments)) {
                    return $emails;
                }

                $bar = $this->command->getOutput()->createProgressBar(count($emailsWithAttachments));
                $bar->start();

                foreach ($emails as $index => $email) {
                    if (!$email['has_attachments']) {
                        continue;
                    }

                    try {
                        $attachments = $this->getEmailAttachments($email['id']);
                        $emails[$index]['attachments'] = $attachments;
                        $this->command->syncStats['attachments_downloaded'] += count($attachments);
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

            private function getEmailAttachments(string $messageId): array
            {
                $url = SyncOutlookEmails::GRAPH_API_BASE . "/me/messages/{$messageId}/attachments";

                $response = Http::withToken($this->command->accessToken)->timeout(30)->get($url);
                if (!$response->successful()) {
                    throw new Exception('Failed to fetch attachments: HTTP ' . $response->status());
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
                    ];
                }

                return $attachments;
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
                return $senders;
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
            }

            private function fetchProfilePictureMetadata(string $userEmail, string $size): array
            {
                $url = SyncOutlookEmails::GRAPH_API_BASE . "/users/{$userEmail}/photos/{$size}";

                $response = Http::withToken($this->command->accessToken)->timeout(10)->get($url);
                if (!$response->successful()) {
                    if ($response->status() === 404) {
                        return $this->createErrorProfilePicture('No profile picture available');
                    }

                    throw new Exception('Failed to fetch profile picture metadata: HTTP ' . $response->status());
                }

                $data = $response->json();
                return array_merge($response->json(), ['available' => true]);
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
                    $this->command->info("Downloaded profile picture: {$userEmail} -> {$filepath}");
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

                $response = Http::withToken($this->command->accessToken)->timeout(30)->get($url);

                if (!$response->successful()) {
                    throw new Exception('Failed to download profile picture: HTTP ' . $response->status());
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
                return "profile_{$emailHash}_{$size}.jpg";
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
        };
    }

    private function saveEmailsToDatabase(array $emails): array
    {
        $this->info('Saving emails to database...');
        $newCount = 0;
        $updatedCount = 0;

        foreach ($emails as $email) {
            try {
                $result = $this->saveOrUpdateEmail($email);
                $result['isNew'] ? $newCount++ : $updatedCount++;

                $this->saveRelatedData($email);
            } catch (Exception $e) {
                if ($this->option('debug')) {
                    $this->warn('Failed to save email: ' . $e->getMessage());
                }
            }
        }

        $this->info("Saved {$newCount} new emails, updated {$updatedCount} existing emails");
        return ['new' => $newCount, 'updated' => $updatedCount];
    }

    private function saveOrUpdateEmail(array $email): array
    {
        $existingEmail = $this->findExistingEmail($email['message_id']);
        $emailData = $this->buildEmailData($email);

        if ($existingEmail) {
            DB::table('fetched_emails')->where('id', $existingEmail->id)->update($emailData);
            return ['isNew' => false];
        } else {
            $emailData['message_id'] = $email['message_id'];
            $emailData['created_at'] = now();
            DB::table('fetched_emails')->insert($emailData);
            return ['isNew' => true];
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
            'updated_at' => now()
        ];
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
        foreach ($attachments as $attachment) {
            try {
                DB::table('email_attachments')->updateOrInsert(
                    [
                        'message_id' => $messageId,
                        'attachment_id' => $attachment['id'],
                        'user_id' => $this->currentUser->user_id,
                        'user_email' => $this->currentUser->email
                    ],
                    [
                        'name' => $attachment['name'],
                        'content_type' => $attachment['content_type'],
                        'size' => $attachment['size'],
                        'is_inline' => $attachment['is_inline'],
                        'created_at' => now(),
                        'updated_at' => now()
                    ]
                );
            } catch (Exception $e) {
                if ($this->option('debug')) {
                    $this->warn("Failed to save attachment: " . $e->getMessage());
                }
            }
        }
    }

    private function saveProfilePictureToDatabase(string $messageId, string $senderEmail, array $profilePicture): void
    {
        try {
            DB::table('email_profile_pictures')->updateOrInsert(
                [
                    'sender_email' => $senderEmail,
                    'user_id' => $this->currentUser->id,
                    'user_email' => $this->currentUser->email
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

    // ===== Sync Logging =====

    private function startSyncLog(int $userId): int
    {
        return DB::table('email_sync_logs')->insertGetId([
            'user_id' => $userId,
            'folder' => $this->option('folder'),
            'status' => 'running',
            'started_at' => now(),
            'created_at' => now(),
            'updated_at' => now()
        ]);
    }

    private function completeSyncLog(int $syncLogId, string $status, int $emailCount, ?string $error = null): void
    {
        DB::table('email_sync_logs')
            ->where('id', $syncLogId)
            ->update([
                'status' => $status,
                'emails_processed' => $emailCount,
                'error_message' => $error,
                'completed_at' => now(),
                'updated_at' => now()
            ]);
    }

    private function getUserProfile(): ?array
    {
        try {
            $response = Http::withToken($this->accessToken)
                ->timeout(10)
                ->get(self::GRAPH_API_BASE . '/me');

            return $response->successful() ? $response->json() : null;
        } catch (Exception $e) {
            return null;
        }
    }

    private function getSinceDateForUser(int $userId): string
    {
        $lastSync = DB::table('email_sync_logs')
            ->where('user_id', $userId)
            ->where('status', 'success')
            ->max('completed_at');

        if ($lastSync) {
            return Carbon::parse($lastSync)->subHours(1)->toDateTimeString();
        }

        $daysBack = (int) $this->option('days-back');
        return now()->subDays($daysBack)->toDateTimeString();
    }

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
        $this->table(
            ['User ID', 'Email', 'Token Status', 'Last Sync', 'Days Back'],
            $users->map(function ($user) {
                $lastSync = $this->getLastSyncTime($user->user_id);
                return [
                    $user->user_id,
                    $user->email ?? 'N/A',
                    $this->getTokenStatus($user),
                    $lastSync ? Carbon::parse($lastSync)->diffForHumans() : 'Never',
                    $this->option('days-back')
                ];
            })->toArray()
        );
    }

    private function getTokenStatus(object $user): string
    {
        if (!$user->access_token) {
            return '❌ No token';
        }

        $expiresAt = Carbon::parse($user->expires_at);

        if ($expiresAt->isPast()) {
            return '⚠️ Expired';
        }

        if ($expiresAt->diffInHours() < 24) {
            return '⚠️ Expires soon';
        }

        return '✅ Valid';
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
                ['Users Success', $this->syncStats['users_success']],
                ['Users Failed', $this->syncStats['users_failed']],
                ['Total Emails', $this->syncStats['total_emails']],
                ['New Emails', $this->syncStats['new_emails']],
                ['Updated Emails', $this->syncStats['updated_emails']],
                ['Attachments', $this->syncStats['attachments_downloaded']],
                ['Profile Pictures', $this->syncStats['profile_pictures_downloaded']],
            ]
        );

        if (!empty($this->syncStats['errors'])) {
            $this->error("\nErrors encountered:");
            foreach ($this->syncStats['errors'] as $error) {
                $this->line("  • {$error}");
            }
        }
    }

    private function extractEmailAddress(?array $emailObject): ?string
    {
        return $emailObject['emailAddress']['address'] ?? null;
    }

    private function extractDisplayName(?array $emailObject): ?string
    {
        return $emailObject['emailAddress']['name'] ?? null;
    }

    private function extractRecipients(array $recipients): array
    {
        return array_map(function ($recipient) {
            return [
                'email' => $recipient['emailAddress']['address'] ?? null,
                'name' => $recipient['emailAddress']['name'] ?? null
            ];
        }, $recipients);
    }

    private function extractBodyContent(?array $body): ?string
    {
        return $body['content'] ?? null;
    }
}
