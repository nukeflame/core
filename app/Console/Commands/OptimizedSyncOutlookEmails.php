<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Log;
use Carbon\Carbon;
use Exception;
use Illuminate\Support\Collection;

class OptimizedSyncOutlookEmails extends Command
{
    protected $signature = 'outlook:sync-optimized
                           {--user-id= : Specific user ID to sync}
                           {--user-email= : Specific user email to sync}
                           {--all-users : Sync all users with valid tokens}
                           {--folder=inbox : Folder to sync (inbox, sent, drafts, etc.)}
                           {--limit=25 : Maximum emails to fetch per user (reduced for frequent syncs)}
                           {--skip-recent=60 : Skip users synced within X seconds}
                           {--batch-size=5 : Maximum users to process per run}
                           {--debug : Enable debug output}';

    protected $description = 'Optimized Outlook email sync for minute-by-minute execution';

    // Performance constants
    public const GRAPH_API_BASE = 'https://graph.microsoft.com/v1.0';
    private const TOKEN_CACHE_TTL = 300; // 5 minutes
    private const HTTP_TIMEOUT = 15; // Reduced timeout
    private const MAX_LOOKBACK_MINUTES = 5; // Only look back 5 minutes for frequent syncs
    private const BATCH_INSERT_SIZE = 50;

    private array $syncStats = [];
    private array $userTokenCache = [];

    public function handle(): int
    {
        $startTime = microtime(true);

        try {
            $this->initializeSyncStats();
            $users = $this->getOptimizedUsersToSync();

            if ($users->isEmpty()) {
                $this->logQuiet('No users need syncing');
                return Command::SUCCESS;
            }

            $this->processUsersBatch($users);
            $this->logExecutionTime($startTime);

            return Command::SUCCESS;
        } catch (Exception $e) {
            $this->error('Optimized sync failed: ' . $e->getMessage());
            Log::error('Optimized email sync failed', ['error' => $e->getMessage()]);
            return Command::FAILURE;
        }
    }

    /**
     * Get users optimized for frequent syncing
     */
    private function getOptimizedUsersToSync(): Collection
    {
        $batchSize = (int) $this->option('batch-size');
        $skipRecentSeconds = (int) $this->option('skip-recent');

        $query = DB::table('oauth_tokens as ot')
            ->select('ot.*')
            ->where('ot.provider', 'outlook')
            ->where('ot.expires_at', '>', now()->timestamp)
            ->whereNotNull('ot.access_token');

        // Apply user filters
        $this->applyUserFilters($query);

        // Skip recently synced users for efficiency
        if ($skipRecentSeconds > 0) {
            $query->leftJoin('email_sync_logs as recent', function ($join) use ($skipRecentSeconds) {
                $join->on('ot.user_id', '=', 'recent.user_id')
                    ->where('recent.status', '=', 'success')
                    ->where('recent.completed_at', '>=', now()->subSeconds($skipRecentSeconds));
            })->whereNull('recent.user_id');
        }

        // Prioritize users who haven't been synced recently
        $query->leftJoin('email_sync_logs as last_sync', function ($join) {
            $join->on('ot.user_id', '=', 'last_sync.user_id')
                ->whereRaw('last_sync.id = (SELECT MAX(id) FROM email_sync_logs WHERE user_id = ot.user_id)');
        })
            ->orderByRaw('COALESCE(last_sync.completed_at, "1970-01-01") ASC')
            ->limit($batchSize);

        return $query->get();
    }

    private function applyUserFilters($query): void
    {
        if ($userId = $this->option('user-id')) {
            $query->where('ot.user_id', $userId);
        }

        if ($userEmail = $this->option('user-email')) {
            $query->where('ot.email', $userEmail);
        }

        if (!$this->option('all-users') && !$this->option('user-id') && !$this->option('user-email')) {
            // Default to no users if no filter specified (safety for cron)
            $query->whereRaw('1 = 0');
        }
    }

    /**
     * Process users in optimized batch
     */
    private function processUsersBatch(Collection $users): void
    {
        $this->logQuiet("Processing {$users->count()} users");

        foreach ($users as $user) {
            $this->syncUserOptimized($user);
        }

        $this->logQuiet($this->getCompactSummary());
    }

    /**
     * Optimized sync for single user
     */
    private function syncUserOptimized(object $user): void
    {
        $this->syncStats['users_processed']++;

        try {
            // Quick token validation with caching
            $accessToken = $this->getCachedValidToken($user);
            if (!$accessToken) {
                $this->syncStats['users_failed']++;
                return;
            }

            // Get optimized date range (very recent only)
            $sinceDate = $this->getOptimizedSinceDate($user->user_id);

            // Fetch minimal email data
            $emails = $this->fetchEmailsMinimal($user, $accessToken, $sinceDate);

            if (empty($emails)) {
                $this->syncStats['users_success']++;
                $this->logSuccessfulSync($user->user_id, 0);
                return;
            }

            // Process and save efficiently
            $processedEmails = $this->processEmailsMinimal($emails);
            $saved = $this->saveEmailsBatch($user, $processedEmails);

            $this->updateSyncStats($saved);
            $this->logSuccessfulSync($user->user_id, count($emails));
            $this->syncStats['users_success']++;

            if ($this->option('debug')) {
                $this->line("✓ {$user->email}: {$saved['new']} new");
            }
        } catch (Exception $e) {
            $this->syncStats['users_failed']++;
            $this->logFailedSync($user->user_id, $e->getMessage());

            if ($this->option('debug')) {
                $this->line("✗ {$user->email}: {$e->getMessage()}");
            }
        }
    }

    /**
     * Get cached valid token with automatic refresh
     */
    private function getCachedValidToken(object $user): ?string
    {
        $cacheKey = "outlook_token_{$user->user_id}";

        // Try cache first
        if (Cache::has($cacheKey)) {
            return Cache::get($cacheKey);
        }

        try {
            $token = decrypt($user->access_token);
            $expiresAt = Carbon::createFromTimestamp($user->expires_at);

            // Refresh if expires within 30 minutes
            if ($expiresAt->diffInMinutes() < 30) {
                $token = $this->refreshTokenQuick($user);
                if (!$token) {
                    return null;
                }
            }

            // Quick validation
            if ($this->validateTokenQuick($token)) {
                Cache::put($cacheKey, $token, self::TOKEN_CACHE_TTL);
                return $token;
            }

            return null;
        } catch (Exception $e) {
            return null;
        }
    }

    /**
     * Quick token refresh without extensive error handling
     */
    private function refreshTokenQuick(object $user): ?string
    {
        if (!$user->refresh_token) {
            return null;
        }

        try {
            $response = Http::timeout(10)->asForm()->post(
                'https://login.microsoftonline.com/common/oauth2/v2.0/token',
                [
                    'client_id' => config('services.microsoft.client_id'),
                    'client_secret' => config('services.microsoft.client_secret'),
                    'refresh_token' => $user->refresh_token,
                    'grant_type' => 'refresh_token',
                    'scope' => 'https://graph.microsoft.com/Mail.Read'
                ]
            );

            if (!$response->successful()) {
                return null;
            }

            $tokenData = $response->json();

            // Update database asynchronously to avoid blocking
            $this->updateTokenAsync($user->id, $tokenData);

            return $tokenData['access_token'];
        } catch (Exception $e) {
            return null;
        }
    }

    /**
     * Quick token validation
     */
    private function validateTokenQuick(string $token): bool
    {
        try {
            $response = Http::withToken($token)
                ->timeout(5)
                ->get(self::GRAPH_API_BASE . '/me?$select=id');

            return $response->successful();
        } catch (Exception $e) {
            return false;
        }
    }

    /**
     * Get optimized since date for frequent syncing
     */
    private function getOptimizedSinceDate(int $userId): string
    {
        // For minute-by-minute sync, only look back a few minutes
        $lastSync = Cache::remember("last_sync_{$userId}", 300, function () use ($userId) {
            return DB::table('email_sync_logs')
                ->where('user_id', $userId)
                ->where('status', 'success')
                ->max('completed_at');
        });

        if ($lastSync) {
            // Look back 2 minutes from last sync to ensure no gaps
            return Carbon::parse($lastSync)->subMinutes(2)->toISOString();
        }

        // First sync - only look back a short period for speed
        return now()->subMinutes(self::MAX_LOOKBACK_MINUTES)->toISOString();
    }

    /**
     * Fetch emails with minimal data for speed
     */
    private function fetchEmailsMinimal(object $user, string $accessToken, string $sinceDate): array
    {
        $limit = min((int) $this->option('limit'), 25);
        $folder = $this->option('folder');

        $folderPath = match ($folder) {
            'inbox' => 'mailFolders/inbox',
            'sent' => 'mailFolders/sentitems',
            'drafts' => 'mailFolders/drafts',
            default => "mailFolders/{$folder}"
        };

        // Minimal select fields for speed
        $selectFields = [
            'id',
            'subject',
            'from',
            'receivedDateTime',
            'sentDateTime',
            'bodyPreview',
            'isRead',
            'hasAttachments',
            'internetMessageId',
            'conversationId'
        ];

        $params = [
            '$top' => $limit,
            '$select' => implode(',', $selectFields),
            '$orderby' => 'receivedDateTime desc',
            '$filter' => "receivedDateTime ge {$sinceDate}"
        ];

        $url = self::GRAPH_API_BASE . "/me/{$folderPath}/messages?" .
            http_build_query($params, '', '&', PHP_QUERY_RFC3986);

        $response = Http::withToken($accessToken)
            ->timeout(self::HTTP_TIMEOUT)
            ->get($url);

        if (!$response->successful()) {
            throw new Exception("API request failed: HTTP {$response->status()}");
        }

        return $response->json()['value'] ?? [];
    }

    /**
     * Process emails with minimal transformation
     */
    private function processEmailsMinimal(array $rawEmails): array
    {
        $emails = [];

        foreach ($rawEmails as $email) {
            $emails[] = [
                'id' => $email['id'],
                'subject' => $email['subject'] ?? '[No Subject]',
                'from' => $email['from']['emailAddress']['address'] ?? null,
                'from_name' => $email['from']['emailAddress']['name'] ?? null,
                'date_received' => $email['receivedDateTime'] ?? null,
                'date_sent' => $email['sentDateTime'] ?? null,
                'body_preview' => $email['bodyPreview'] ?? '',
                'is_read' => $email['isRead'] ?? false,
                'has_attachments' => $email['hasAttachments'] ?? false,
                'message_id' => $email['internetMessageId'] ?? null,
                'conversation_id' => $email['conversationId'] ?? null,
            ];
        }

        return $emails;
    }

    /**
     * Save emails in optimized batches
     */
    private function saveEmailsBatch(object $user, array $emails): array
    {
        if (empty($emails)) {
            return ['new' => 0, 'updated' => 0];
        }

        // Get existing message IDs in batch
        $messageIds = array_filter(array_column($emails, 'message_id'));
        $existingIds = [];

        if (!empty($messageIds)) {
            $existingIds = DB::table('fetched_emails')
                ->where('user_id', $user->user_id)
                ->whereIn('message_id', $messageIds)
                ->pluck('message_id')
                ->toArray();
        }

        // Prepare batch insert data for new emails only
        $newEmails = [];
        $now = now();

        foreach ($emails as $email) {
            if (in_array($email['message_id'], $existingIds)) {
                continue; // Skip existing emails
            }

            $newEmails[] = [
                'user_id' => $user->user_id,
                'user_email' => $user->email,
                'uid' => $email['id'],
                'subject' => $email['subject'],
                'from_email' => $email['from'],
                'from_name' => $email['from_name'],
                'to_recipients' => '[]', // Skip for performance
                'cc_recipients' => '[]', // Skip for performance
                'date_received' => $email['date_received'],
                'date_sent' => $email['date_sent'],
                'body_text' => '', // Skip for performance
                'body_html' => '', // Skip for performance
                'body_preview' => substr($email['body_preview'], 0, 500), // Truncate
                'importance' => 'normal',
                'is_read' => $email['is_read'],
                'has_attachments' => $email['has_attachments'],
                'message_id' => $email['message_id'],
                'conversation_id' => $email['conversation_id'],
                'folder' => $this->option('folder'),
                'created_at' => $now,
                'updated_at' => $now
            ];
        }

        // Batch insert new emails
        $newCount = 0;
        if (!empty($newEmails)) {
            // Insert in chunks to avoid memory issues
            $chunks = array_chunk($newEmails, self::BATCH_INSERT_SIZE);
            foreach ($chunks as $chunk) {
                DB::table('fetched_emails')->insert($chunk);
                $newCount += count($chunk);
            }
        }

        return ['new' => $newCount, 'updated' => 0];
    }

    /**
     * Async token update to avoid blocking
     */
    private function updateTokenAsync(int $tokenId, array $tokenData): void
    {
        // Queue this update or use a separate process
        dispatch(function () use ($tokenId, $tokenData) {
            DB::table('oauth_tokens')
                ->where('id', $tokenId)
                ->update([
                    'access_token' => encrypt($tokenData['access_token']),
                    'refresh_token' => $tokenData['refresh_token'] ?? null,
                    'expires_at' => now()->addSeconds($tokenData['expires_in'])->timestamp,
                    'updated_at' => now()
                ]);
        })->afterResponse();
    }

    /**
     * Log successful sync efficiently
     */
    private function logSuccessfulSync(int $userId, int $emailCount): void
    {
        // Use async logging to avoid blocking
        dispatch(function () use ($userId, $emailCount) {
            DB::table('email_sync_logs')->insert([
                'user_id' => $userId,
                'folder' => $this->option('folder'),
                'status' => 'success',
                'emails_processed' => $emailCount,
                'started_at' => now(),
                'completed_at' => now(),
                'created_at' => now(),
                'updated_at' => now()
            ]);
        })->afterResponse();
    }

    /**
     * Log failed sync efficiently
     */
    private function logFailedSync(int $userId, string $error): void
    {
        dispatch(function () use ($userId, $error) {
            DB::table('email_sync_logs')->insert([
                'user_id' => $userId,
                'folder' => $this->option('folder'),
                'status' => 'failed',
                'emails_processed' => 0,
                'error_message' => substr($error, 0, 500), // Truncate long errors
                'started_at' => now(),
                'completed_at' => now(),
                'created_at' => now(),
                'updated_at' => now()
            ]);
        })->afterResponse();
    }

    private function initializeSyncStats(): void
    {
        $this->syncStats = [
            'users_processed' => 0,
            'users_success' => 0,
            'users_failed' => 0,
            'total_emails' => 0,
            'new_emails' => 0,
        ];
    }

    private function updateSyncStats(array $saved): void
    {
        $this->syncStats['total_emails'] += $saved['new'];
        $this->syncStats['new_emails'] += $saved['new'];
    }

    private function getCompactSummary(): string
    {
        return sprintf(
            '✓ %d/%d users, %d new emails',
            $this->syncStats['users_success'],
            $this->syncStats['users_processed'],
            $this->syncStats['new_emails']
        );
    }

    private function logQuiet(string $message): void
    {
        if ($this->option('debug')) {
            $this->info($message);
        }
    }

    private function logExecutionTime(float $startTime): void
    {
        $executionTime = round((microtime(true) - $startTime) * 1000);

        if ($this->option('debug')) {
            $this->info("Execution time: {$executionTime}ms");
        }

        // Log slow executions
        if ($executionTime > 30000) { // 30 seconds
            Log::warning('Slow email sync execution', [
                'execution_time_ms' => $executionTime,
                'stats' => $this->syncStats
            ]);
        }
    }
}
