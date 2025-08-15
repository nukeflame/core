<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Carbon\Carbon;
use Illuminate\Support\Str;
use Exception;
use Illuminate\Support\Facades\Auth;

class SyncOutlookEmails extends Command
{
    /**
     * The name and signature of the console command.
     */
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
                           {--debug : Enable debug output}';

    /**
     * The console command description.
     */
    protected $description = 'Synchronize Outlook emails for users with valid OAuth tokens';

    private $graphApiBase = 'https://graph.microsoft.com/v1.0';
    private $accessToken;
    private $currentUser;
    private $syncStats = [];

    public function handle(): int
    {
        try {
            $this->info('Starting Outlook email synchronization...');

            // Get users to sync
            $users = $this->getUsersToSync();

            if ($users->isEmpty()) {
                $this->warn('No users found with valid OAuth tokens.');
                return Command::SUCCESS;
            }

            $this->info("Found {$users->count()} user(s) to sync");

            if ($this->option('dry-run')) {
                $this->info('DRY RUN MODE - No actual syncing will occur');
                $this->showDryRunResults($users);
                return Command::SUCCESS;
            }

            // Initialize sync stats
            $this->initializeSyncStats();

            // Sync emails for each user
            foreach ($users as $user) {
                $this->syncUserEmails($user);
            }

            // Show final results
            $this->showSyncResults();

            return Command::SUCCESS;
        } catch (Exception $e) {
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
    }

    /**
     * Get users that need email synchronization
     */
    private function getUsersToSync()
    {
        $query = DB::table('oauth_tokens')
            ->where('provider', 'outlook')
            ->where('expires_at', '>', Carbon::now(config('app.timezone'))->timestamp)
            ->whereNotNull('access_token');

        if ($userId = $this->option('user-id')) {
            $query->where('user_id', $userId);
        }

        if ($userEmail = $this->option('user-email')) {
            $query->where('email', $userEmail);
        }

        if (!$this->option('all-users') && !$this->option('user-id') && !$this->option('user-email')) {
            $this->error('Please specify --user-id, --user-email, or --all-users');
            return collect();
        }

        return $query->get();
    }

    /**
     * Show what would be synced in dry-run mode
     */
    private function showDryRunResults($users): void
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

    /**
     * Get token status for display
     */
    private function getTokenStatus($user): string
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

    /**
     * Get last sync time for a user
     */
    private function getLastSyncTime($userId): ?string
    {
        return DB::table('email_sync_logs')
            ->where('user_id', $userId)
            ->where('status', 'success')
            ->max('completed_at');
    }

    /**
     * Initialize sync statistics
     */
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

    /**
     * Sync emails for a specific user
     */
    private function syncUserEmails($user): void
    {
        $this->syncStats['users_processed']++;
        $userInfo = "User {$user->user_id} ({$user->email})";

        try {
            $this->info("Syncing emails for {$userInfo}");
            $this->currentUser = $user;

            $syncLogId = $this->startSyncLog($user->user_id);

            if (!$this->validateAndRefreshToken($user)) {
                throw new Exception('Token validation failed');
            }

            $profile = $this->getUserProfile();
            if (!$profile) {
                throw new Exception('Could not fetch user profile');
            }

            $sinceDate = $this->getSinceDateForUser($user->user_id);
            $this->info("Fetching emails since: {$sinceDate}");

            $emails = $this->fetchUserEmails($sinceDate);
            $this->info("Fetched " . count($emails) . " emails");

            if (!empty($emails)) {
                $processedEmails = $this->processEmails($emails);
                if ($this->option('fetch-attachments')) {
                    $processedEmails = $this->fetchEmailAttachments($processedEmails);
                }

                if ($this->option('fetch-profile-pictures')) {
                    $processedEmails = $this->fetchProfilePictures($processedEmails);
                }

                // Save to database
                $saveResult = $this->saveEmailsToDatabase($processedEmails);
                $this->syncStats['new_emails'] += $saveResult['new'];
                $this->syncStats['updated_emails'] += $saveResult['updated'];
            }

            $this->syncStats['total_emails'] += count($emails);
            $this->syncStats['users_success']++;

            // Complete sync log
            $this->completeSyncLog($syncLogId, 'success', count($emails));
            $this->info("✅ Successfully synced {$userInfo}");
        } catch (Exception $e) {
            $this->syncStats['users_failed']++;
            $this->syncStats['errors'][] = "{$userInfo}: {$e->getMessage()}";

            $this->error("❌ Failed to sync {$userInfo}: {$e->getMessage()}");

            if ($this->option('debug')) {
                $this->error($e->getTraceAsString());
            }

            // Log the error
            if (isset($syncLogId)) {
                $this->completeSyncLog($syncLogId, 'failed', 0, $e->getMessage());
            }

            logger()->error('User email sync failed', [
                'user_id' => $user->user_id,
                'email' => $user->email,
                'error' => $e->getMessage()
            ]);
        }
    }

    /**
     * Validate token and refresh if needed
     */
    private function validateAndRefreshToken($user): bool
    {
        $this->accessToken = decrypt($user->access_token);
        $expiresAt = Carbon::createFromTimestamp($user->expires_at);

        if ($expiresAt->diffInMinutes() < 30) {
            $this->info('Token expires soon, attempting refresh...');

            if ($this->option('force-refresh') || $expiresAt->isPast()) {
                return $this->refreshToken($user);
            }
        }

        try {
            $response = Http::withToken($this->accessToken)->timeout(30)->get($this->graphApiBase . '/me');
            return $response->successful();
        } catch (Exception $e) {
            logger()->info('Initiate refresh Token');
            $this->warn('Token validation failed, attempting refresh...');
            // return $this->refreshToken($user);
        }

        return false;
    }

    /**
     * Refresh OAuth token
     */
    private function refreshToken($user): bool
    {
        if (!$user->refresh_token) {
            throw new Exception('No refresh token available');
        }

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

            $tokenData = $response->json();

            // Update token in database
            DB::table('oauth_tokens')
                ->where('id', $user->id)
                ->update([
                    'access_token' => $tokenData['access_token'],
                    'refresh_token' => $tokenData['refresh_token'] ?? $user->refresh_token,
                    'expires_at' => now()->addSeconds($tokenData['expires_in']),
                    'updated_at' => now()
                ]);

            $this->accessToken = $tokenData['access_token'];
            $this->info('Token refreshed successfully');

            return true;
        } catch (Exception $e) {
            throw new Exception('Token refresh failed: ' . $e->getMessage());
        }
    }

    /**
     * Get user profile from Microsoft Graph
     */
    private function getUserProfile(): ?array
    {
        try {
            $response = Http::withToken($this->accessToken)->timeout(10)->get($this->graphApiBase . '/me');
            return $response->successful() ? $response->json() : null;
        } catch (Exception $e) {
            return null;
        }
    }

    /**
     * Determine since date for email sync
     */
    private function getSinceDateForUser($userId): string
    {
        $lastSync = DB::table('email_sync_logs')
            ->where('user_id', $userId)
            ->where('status', 'success')
            ->max('completed_at');

        if ($lastSync) {
            return Carbon::parse($lastSync)->subHours(1)->toDateTimeString();
        } else {
            $daysBack = (int) $this->option('days-back');
            return now()->subDays($daysBack)->toDateTimeString();
        }
    }

    /**
     * Fetch emails for the current user
     */
    private function fetchUserEmails($sinceDate): array
    {
        $folder = $this->option('folder');
        $limit = min((int) $this->option('limit'), 999);

        $url = $this->buildEmailsUrl($folder, $limit, $sinceDate);

        if ($this->option('debug')) {
            $this->info("API URL: {$url}");
        }

        $response = Http::withToken($this->accessToken)->timeout(60)->get($url);

        if (!$response->successful()) {
            throw new Exception('Failed to fetch emails: HTTP ' . $response->status() . ' - ' . $response->body());
        }

        $data = $response->json();
        return $data['value'] ?? [];
    }

    /**
     * Build Graph API URL for fetching emails
     */
    private function buildEmailsUrl(string $folder, int $limit, string $sinceDate): string
    {
        $folderPath = match ($folder) {
            'inbox' => 'mailFolders/inbox',
            'sent' => 'mailFolders/sentitems',
            'drafts' => 'mailFolders/drafts',
            'deleted' => 'mailFolders/deleteditems',
            default => "mailFolders/{$folder}"
        };

        $params = [
            '$top' => $limit,
            '$select' => implode(',', $this->getDefaultSelectFields()),
            '$orderby' => 'receivedDateTime desc',
        ];

        if ($since = $sinceDate ?? null) {
            $params['$filter'] = "receivedDateTime ge " . Carbon::parse($since)->toISOString();
        } else {
            $params['$filter'] = "receivedDateTime ge " . now()->subDays(30)->toISOString();
        }

        return $this->graphApiBase . "/me/{$folderPath}/messages?" . http_build_query($params, '', '&', PHP_QUERY_RFC3986);
    }

    /**
     * Get default fields to select
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
     * Process raw emails from Graph API
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
                    'attachments' => [],
                    'profile_picture' => null,
                ];
            } catch (Exception $e) {
                if ($this->option('debug')) {
                    $this->warn('Failed to process email: ' . $e->getMessage());
                }
            }
        }

        return $emails;
    }

    /**
     * Fetch email attachments (simplified version)
     */
    private function fetchEmailAttachments(array $emails): array
    {
        $this->info('Fetching attachments...');

        $emailsWithAttachments = array_filter($emails, fn($email) => $email['has_attachments']);

        if (empty($emailsWithAttachments)) {
            return $emails;
        }

        $bar = $this->output->createProgressBar(count($emailsWithAttachments));
        $bar->start();

        foreach ($emails as $index => $email) {
            if (!$email['has_attachments']) {
                continue;
            }

            try {
                $attachments = $this->getEmailAttachments($email['id']);
                $emails[$index]['attachments'] = $attachments;
                $this->syncStats['attachments_downloaded'] += count($attachments);
            } catch (Exception $e) {
                if ($this->option('debug')) {
                    $this->warn("Failed to fetch attachments for email {$email['id']}: " . $e->getMessage());
                }
            }

            $bar->advance();
        }

        $bar->finish();
        $this->line('');

        return $emails;
    }

    /**
     * Get email attachments metadata
     */
    private function getEmailAttachments(string $messageId): array
    {
        $url = $this->graphApiBase . "/me/messages/{$messageId}/attachments";

        $response = Http::withToken($this->accessToken)->timeout(30)->get($url);
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

    /**
     * Fetch profile pictures (simplified)
     */
    private function fetchProfilePictures(array $emails): array
    {
        $this->info('Fetching profile pictures...');

        $senders = array_unique(array_filter(array_column($emails, 'from')));

        if (empty($senders)) {
            return $emails;
        }

        $profileCache = [];
        $bar = $this->output->createProgressBar(count($senders));
        $bar->start();

        foreach ($senders as $sender) {
            try {
                $profileCache[$sender] = $this->getUserProfilePicture($sender);
                if ($profileCache[$sender]['available']) {
                    $this->syncStats['profile_pictures_downloaded']++;
                }
            } catch (Exception $e) {
                $profileCache[$sender] = ['available' => false, 'error' => $e->getMessage()];
            }
            $bar->advance();
        }

        $bar->finish();
        $this->line('');

        foreach ($emails as $index => $email) {
            if ($email['from'] && isset($profileCache[$email['from']])) {
                $emails[$index]['profile_picture'] = $profileCache[$email['from']];
            }
        }

        return $emails;
    }

    /**
     * Get user profile picture
     */
    private function getUserProfilePicture(string $userEmail): array
    {
        $url = $this->graphApiBase . "/users/{$userEmail}/photos/96x96";

        $response = Http::withToken($this->accessToken)->timeout(10)->get($url);
        if ($response->status() === 404) {
            return ['available' => false];
        }

        if (!$response->successful()) {
            throw new Exception('Failed to fetch profile picture: HTTP ' . $response->status());
        }

        return [
            'available' => true,
            'metadata' => $response->json()
        ];
    }

    /**
     * Save emails to database with improved duplicate handling
     */
    private function saveEmailsToDatabase(array $emails): array
    {
        $this->info('Saving emails to database...');
        $newCount = 0;
        $updatedCount = 0;

        foreach ($emails as $email) {
            try {
                // Check if email already exists
                $existingEmail = DB::table('fetched_emails')
                    ->where('message_id', $email['message_id'])
                    ->where('user_id', $this->currentUser->user_id)
                    ->where('user_email', $this->currentUser->email)
                    ->first();

                $emailData = [
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

                if ($existingEmail) {
                    // Update existing email
                    DB::table('fetched_emails')
                        ->where('id', $existingEmail->id)
                        ->update($emailData);
                    $updatedCount++;
                } else {
                    // Insert new email
                    $emailData['message_id'] = $email['message_id'];
                    $emailData['created_at'] = now();

                    DB::table('fetched_emails')->insert($emailData);
                    $newCount++;
                }

                // Save attachments
                if (!empty($email['attachments'])) {
                    $this->saveAttachmentsToDatabase($email['message_id'], $email['attachments']);
                }
            } catch (Exception $e) {
                if ($this->option('debug')) {
                    $this->warn('Failed to save email: ' . $e->getMessage());
                }
            }
        }

        $this->info("Saved {$newCount} new emails, updated {$updatedCount} existing emails");

        return ['new' => $newCount, 'updated' => $updatedCount];
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

    /**
     * Start sync log entry
     */
    private function startSyncLog($userId): int
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

    /**
     * Complete sync log entry
     */
    private function completeSyncLog($syncLogId, $status, $emailCount, $error = null): void
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

    /**
     * Show final sync results
     */
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

    // Helper methods (same as original)
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
