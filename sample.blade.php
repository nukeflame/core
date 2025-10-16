<?php

// ============================================
// 1. ENVIRONMENT CONFIGURATION (.env)
// ============================================
/*
MICROSOFT_CLIENT_ID=your_client_id
MICROSOFT_CLIENT_SECRET=your_client_secret
MICROSOFT_REDIRECT_URI=https://yourapp.com/auth/microsoft/callback
MICROSOFT_TENANT_ID=common

WEBHOOK_URL=https://yourapp.com/webhooks/microsoft
WEBHOOK_CLIENT_STATE=your_random_secret_string

QUEUE_CONNECTION=redis
REDIS_HOST=127.0.0.1
REDIS_PASSWORD=null
REDIS_PORT=6379
*/

// ============================================
// 2. DATABASE MIGRATIONS
// ============================================

// database/migrations/xxxx_create_microsoft_tokens_table.php
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateMicrosoftTokensTable extends Migration
{
    public function up()
    {
        Schema::create('microsoft_tokens', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->onDelete('cascade');
            $table->text('access_token');
            $table->text('refresh_token');
            $table->timestamp('expires_at');
            $table->string('scope')->nullable();
            $table->timestamps();

            $table->index('user_id');
            $table->index('expires_at');
        });
    }

    public function down()
    {
        Schema::dropIfExists('microsoft_tokens');
    }
}

// database/migrations/xxxx_create_email_sync_states_table.php
class CreateEmailSyncStatesTable extends Migration
{
    public function up()
    {
        Schema::create('email_sync_states', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->onDelete('cascade');
            $table->text('delta_token')->nullable();
            $table->timestamp('last_synced_at')->nullable();
            $table->integer('sync_attempts')->default(0);
            $table->timestamp('last_attempt_at')->nullable();
            $table->text('last_error')->nullable();
            $table->string('subscription_id')->nullable();
            $table->timestamp('subscription_expires_at')->nullable();
            $table->timestamps();

            $table->unique('user_id');
            $table->index('subscription_expires_at');
        });
    }

    public function down()
    {
        Schema::dropIfExists('email_sync_states');
    }
}

// database/migrations/xxxx_create_emails_table.php
class CreateEmailsTable extends Migration
{
    public function up()
    {
        Schema::create('emails', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->onDelete('cascade');
            $table->string('message_id')->unique();
            $table->string('subject')->nullable();
            $table->text('body_preview')->nullable();
            $table->text('from_address')->nullable();
            $table->string('from_name')->nullable();
            $table->boolean('is_read')->default(false);
            $table->boolean('has_attachments')->default(false);
            $table->timestamp('received_at')->nullable();
            $table->json('metadata')->nullable();
            $table->softDeletes();
            $table->timestamps();

            $table->index('user_id');
            $table->index('message_id');
            $table->index('received_at');
            $table->index(['user_id', 'is_read']);
        });
    }

    public function down()
    {
        Schema::dropIfExists('emails');
    }
}

// ============================================
// 3. MODELS
// ============================================

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Crypt;

class MicrosoftToken extends Model
{
    protected $fillable = ['user_id', 'access_token', 'refresh_token', 'expires_at', 'scope'];

    protected $casts = [
        'expires_at' => 'datetime',
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function setAccessTokenAttribute($value)
    {
        $this->attributes['access_token'] = Crypt::encryptString($value);
    }

    public function getAccessTokenAttribute($value)
    {
        return Crypt::decryptString($value);
    }

    public function setRefreshTokenAttribute($value)
    {
        $this->attributes['refresh_token'] = Crypt::encryptString($value);
    }

    public function getRefreshTokenAttribute($value)
    {
        return Crypt::decryptString($value);
    }

    public function isExpired(): bool
    {
        return $this->expires_at->isPast();
    }
}

class EmailSyncState extends Model
{
    protected $fillable = ['user_id', 'delta_token', 'last_synced_at', 'sync_attempts', 'last_attempt_at', 'last_error', 'subscription_id', 'subscription_expires_at'];

    protected $casts = [
        'last_synced_at' => 'datetime',
        'last_attempt_at' => 'datetime',
        'subscription_expires_at' => 'datetime',
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function needsSubscriptionRenewal(): bool
    {
        return !$this->subscription_expires_at || $this->subscription_expires_at->subDay()->isPast();
    }
}

class Email extends Model
{
    use SoftDeletes;

    protected $fillable = ['user_id', 'message_id', 'subject', 'body_preview', 'from_address', 'from_name', 'is_read', 'has_attachments', 'received_at', 'metadata'];

    protected $casts = [
        'is_read' => 'boolean',
        'has_attachments' => 'boolean',
        'received_at' => 'datetime',
        'metadata' => 'array',
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }
}

// ============================================
// 4. SERVICES
// ============================================

namespace App\Services;

use App\Models\MicrosoftToken;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;
use Carbon\Carbon;

class MicrosoftGraphService
{
    private const GRAPH_API_BASE = 'https://graph.microsoft.com/v1.0';
    private const TOKEN_URL = 'https://login.microsoftonline.com/common/oauth2/v2.0/token';

    public function refreshToken(MicrosoftToken $token): MicrosoftToken
    {
        $response = Http::asForm()->post(self::TOKEN_URL, [
            'client_id' => config('services.microsoft.client_id'),
            'client_secret' => config('services.microsoft.client_secret'),
            'refresh_token' => $token->refresh_token,
            'grant_type' => 'refresh_token',
            'scope' => 'https://graph.microsoft.com/Mail.Read offline_access',
        ]);

        if ($response->failed()) {
            Log::error('Token refresh failed', [
                'user_id' => $token->user_id,
                'error' => $response->json(),
            ]);
            throw new \Exception('Failed to refresh token');
        }

        $data = $response->json();

        $token->update([
            'access_token' => $data['access_token'],
            'refresh_token' => $data['refresh_token'] ?? $token->refresh_token,
            'expires_at' => Carbon::now()->addSeconds($data['expires_in']),
        ]);

        return $token->fresh();
    }

    public function getValidToken(int $userId): string
    {
        $token = MicrosoftToken::where('user_id', $userId)->firstOrFail();

        if ($token->isExpired()) {
            $token = $this->refreshToken($token);
        }

        return $token->access_token;
    }

    public function getDeltaMessages(int $userId, ?string $deltaToken = null): array
    {
        $accessToken = $this->getValidToken($userId);

        $url = $deltaToken ? $deltaToken : self::GRAPH_API_BASE . '/me/mailFolders/inbox/messages/delta?$select=id,subject,bodyPreview,from,isRead,hasAttachments,receivedDateTime';

        $response = Http::withToken($accessToken)
            ->retry(3, 100, function ($exception, $request) {
                if ($exception->response->status() === 429) {
                    $retryAfter = $exception->response->header('Retry-After') ?? 60;
                    sleep($retryAfter);
                    return true;
                }
                return false;
            })
            ->get($url);

        if ($response->failed()) {
            Log::error('Delta query failed', [
                'user_id' => $userId,
                'status' => $response->status(),
                'error' => $response->json(),
            ]);
            throw new \Exception('Failed to fetch delta messages');
        }

        return $response->json();
    }

    public function createSubscription(int $userId): array
    {
        $accessToken = $this->getValidToken($userId);

        $expirationDateTime = Carbon::now()->addHours(71)->toIso8601String();

        $response = Http::withToken($accessToken)->post(self::GRAPH_API_BASE . '/subscriptions', [
            'changeType' => 'created,updated',
            'notificationUrl' => config('services.microsoft.webhook_url'),
            'resource' => 'me/mailFolders/inbox/messages',
            'expirationDateTime' => $expirationDateTime,
            'clientState' => config('services.microsoft.webhook_client_state'),
        ]);

        if ($response->failed()) {
            Log::error('Subscription creation failed', [
                'user_id' => $userId,
                'error' => $response->json(),
            ]);
            throw new \Exception('Failed to create subscription');
        }

        return $response->json();
    }

    public function renewSubscription(string $subscriptionId, int $userId): array
    {
        $accessToken = $this->getValidToken($userId);

        $expirationDateTime = Carbon::now()->addHours(71)->toIso8601String();

        $response = Http::withToken($accessToken)->patch(self::GRAPH_API_BASE . "/subscriptions/{$subscriptionId}", ['expirationDateTime' => $expirationDateTime]);

        if ($response->failed()) {
            throw new \Exception('Failed to renew subscription');
        }

        return $response->json();
    }
}

// ============================================
// 5. JOBS
// ============================================

namespace App\Jobs;

use App\Models\Email;
use App\Models\EmailSyncState;
use App\Services\MicrosoftGraphService;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Log;
use Carbon\Carbon;

class SyncUserEmails implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    public $tries = 3;
    public $backoff = [60, 300, 900]; // 1min, 5min, 15min

    protected int $userId;

    public function __construct(int $userId)
    {
        $this->userId = $userId;
    }

    public function handle(MicrosoftGraphService $graphService)
    {
        $syncState = EmailSyncState::firstOrCreate(['user_id' => $this->userId], ['sync_attempts' => 0]);

        try {
            $syncState->update([
                'sync_attempts' => $syncState->sync_attempts + 1,
                'last_attempt_at' => now(),
            ]);

            // Renew subscription if needed
            if ($syncState->needsSubscriptionRenewal()) {
                if ($syncState->subscription_id) {
                    $subscription = $graphService->renewSubscription($syncState->subscription_id, $this->userId);
                } else {
                    $subscription = $graphService->createSubscription($this->userId);
                }

                $syncState->update([
                    'subscription_id' => $subscription['id'],
                    'subscription_expires_at' => Carbon::parse($subscription['expirationDateTime']),
                ]);
            }

            // Fetch delta messages
            $result = $graphService->getDeltaMessages($this->userId, $syncState->delta_token);

            $this->processMessages($result['value'] ?? []);

            // Store new delta token
            $deltaLink = $result['@odata.deltaLink'] ?? null;
            if ($deltaLink) {
                $syncState->update([
                    'delta_token' => $deltaLink,
                    'last_synced_at' => now(),
                    'sync_attempts' => 0,
                    'last_error' => null,
                ]);
            }

            Log::info('Email sync completed', [
                'user_id' => $this->userId,
                'messages_processed' => count($result['value'] ?? []),
            ]);
        } catch (\Exception $e) {
            $syncState->update([
                'last_error' => $e->getMessage(),
            ]);

            Log::error('Email sync failed', [
                'user_id' => $this->userId,
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString(),
            ]);

            throw $e;
        }
    }

    protected function processMessages(array $messages): void
    {
        foreach ($messages as $message) {
            // Handle deletions
            if (isset($message['@removed'])) {
                Email::where('message_id', $message['id'])->delete();
                continue;
            }

            // Upsert email
            Email::updateOrCreate(
                [
                    'user_id' => $this->userId,
                    'message_id' => $message['id'],
                ],
                [
                    'subject' => $message['subject'] ?? null,
                    'body_preview' => $message['bodyPreview'] ?? null,
                    'from_address' => $message['from']['emailAddress']['address'] ?? null,
                    'from_name' => $message['from']['emailAddress']['name'] ?? null,
                    'is_read' => $message['isRead'] ?? false,
                    'has_attachments' => $message['hasAttachments'] ?? false,
                    'received_at' => isset($message['receivedDateTime']) ? Carbon::parse($message['receivedDateTime']) : null,
                    'metadata' => $message,
                ],
            );
        }
    }

    public function failed(\Throwable $exception)
    {
        Log::error('Email sync job failed permanently', [
            'user_id' => $this->userId,
            'error' => $exception->getMessage(),
        ]);
    }
}

class RenewAllSubscriptions implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    public function handle()
    {
        $states = EmailSyncState::whereNotNull('subscription_id')
            ->where('subscription_expires_at', '<', now()->addDay())
            ->get();

        foreach ($states as $state) {
            SyncUserEmails::dispatch($state->user_id);
        }

        Log::info('Renewed subscriptions', ['count' => $states->count()]);
    }
}

// ============================================
// 6. CONTROLLERS
// ============================================

namespace App\Http\Controllers;

use App\Jobs\SyncUserEmails;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;

class MicrosoftWebhookController extends Controller
{
    public function handle(Request $request)
    {
        // Validation token response
        if ($request->has('validationToken')) {
            return response($request->input('validationToken'), 200)->header('Content-Type', 'text/plain');
        }

        // Verify client state
        $notifications = $request->input('value', []);

        foreach ($notifications as $notification) {
            if ($notification['clientState'] !== config('services.microsoft.webhook_client_state')) {
                Log::warning('Invalid client state in webhook', [
                    'notification' => $notification,
                ]);
                continue;
            }

            // Extract user from subscription or resource
            $userId = $this->extractUserId($notification);

            if ($userId) {
                SyncUserEmails::dispatch($userId);
            }
        }

        return response()->json(['status' => 'accepted'], 202);
    }

    protected function extractUserId(array $notification): ?int
    {
        $subscriptionId = $notification['subscriptionId'] ?? null;

        if (!$subscriptionId) {
            return null;
        }

        $syncState = \App\Models\EmailSyncState::where('subscription_id', $subscriptionId)->first();

        return $syncState?->user_id;
    }
}

class EmailController extends Controller
{
    public function index(Request $request)
    {
        $emails = Email::where('user_id', $request->user()->id)
            ->orderBy('received_at', 'desc')
            ->paginate(50);

        return response()->json($emails);
    }

    public function sync(Request $request)
    {
        SyncUserEmails::dispatch($request->user()->id);

        return response()->json([
            'message' => 'Sync initiated',
            'status' => 'processing',
        ]);
    }

    public function status(Request $request)
    {
        $syncState = EmailSyncState::where('user_id', $request->user()->id)->first();

        return response()->json([
            'last_synced_at' => $syncState?->last_synced_at,
            'subscription_expires_at' => $syncState?->subscription_expires_at,
            'last_error' => $syncState?->last_error,
        ]);
    }
}

// ============================================
// 7. ROUTES
// ============================================

// routes/web.php or routes/api.php
use App\Http\Controllers\EmailController;
use App\Http\Controllers\MicrosoftWebhookController;

Route::post('/webhooks/microsoft', [MicrosoftWebhookController::class, 'handle'])->name('microsoft.webhook');

Route::middleware(['auth:sanctum'])->group(function () {
    Route::get('/emails', [EmailController::class, 'index']);
    Route::post('/emails/sync', [EmailController::class, 'sync']);
    Route::get('/emails/sync/status', [EmailController::class, 'status']);
});

// ============================================
// 8. SCHEDULER (app/Console/Kernel.php)
// ============================================

namespace App\Console;

use App\Jobs\RenewAllSubscriptions;
use Illuminate\Console\Scheduling\Schedule;
use Illuminate\Foundation\Console\Kernel as ConsoleKernel;

class Kernel extends ConsoleKernel
{
    protected function schedule(Schedule $schedule)
    {
        // Renew subscriptions daily
        $schedule->job(new RenewAllSubscriptions())->dailyAt('03:00')->onOneServer();
    }
}

// ============================================
// 9. CONFIG (config/services.php)
// ============================================

return [
    'microsoft' => [
        'client_id' => env('MICROSOFT_CLIENT_ID'),
        'client_secret' => env('MICROSOFT_CLIENT_SECRET'),
        'redirect' => env('MICROSOFT_REDIRECT_URI'),
        'tenant' => env('MICROSOFT_TENANT_ID', 'common'),
        'webhook_url' => env('WEBHOOK_URL'),
        'webhook_client_state' => env('WEBHOOK_CLIENT_STATE'),
    ],
];

// ============================================
// 10. SETUP COMMANDS
// ============================================

/*
# Installation steps:

composer require guzzlehttp/guzzle
composer require predis/predis

php artisan migrate
php artisan queue:work --queue=default --tries=3
php artisan schedule:work

# Webhook setup:
# 1. Ensure your webhook URL is publicly accessible via HTTPS
# 2. Microsoft will send a validation request first
# 3. The controller automatically handles validation

# Monitoring:
php artisan queue:failed
php artisan queue:retry all
*/

<?php

namespace App\Jobs;

use App\Models\Email;
use App\Models\EmailSyncState;
use App\Models\EmailAttachment;
use App\Models\User;
use App\Services\EmailSyncService;
use App\Services\MicrosoftGraphService;
use App\Exceptions\SyncLockedException;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Queue\Middleware\WithoutOverlapping;
use Illuminate\Queue\Middleware\RateLimited;
use Illuminate\Queue\Middleware\ThrottlesExceptions;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\DB;

// ============================================
// Sync User Emails Job
// ============================================

class SyncUserEmails implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    public $tries = 3;
    public $maxExceptions = 2;
    public $backoff = [60, 300, 900]; // 1min, 5min, 15min
    public $timeout = 300; // 5 minutes
    public $failOnTimeout = true;

    protected int $userId;
    protected string $syncType;

    /**
     * Create a new job instance
     */
    public function __construct(int $userId, string $syncType = 'delta')
    {
        $this->userId = $userId;
        $this->syncType = in_array($syncType, ['delta', 'full', 'webhook', 'manual'])
            ? $syncType
            : 'delta';

        $this->onQueue('email-sync');
    }

    /**
     * Get middleware for job
     */
    public function middleware(): array
    {
        return [
            new WithoutOverlapping($this->userId),
            new RateLimited('email-sync'),
            (new ThrottlesExceptions(3, 5))->backoff(5),
        ];
    }

    /**
     * Execute the job
     */
    public function handle(EmailSyncService $syncService): void
    {
        $startTime = microtime(true);

        try {
            Log::info('Email sync started', [
                'user_id' => $this->userId,
                'sync_type' => $this->syncType,
                'attempt' => $this->attempts(),
                'job_id' => $this->job->getJobId()
            ]);

            // Verify user exists and has valid token
            $user = User::find($this->userId);
            if (!$user) {
                Log::error('User not found for sync', ['user_id' => $this->userId]);
                $this->delete();
                return;
            }

            if (!$user->hasMicrosoftAccount()) {
                Log::warning('User has no Microsoft account', ['user_id' => $this->userId]);
                $this->delete();
                return;
            }

            // Perform sync
            $stats = $syncService->syncUser($this->userId, $this->syncType);

            $duration = round((microtime(true) - $startTime) * 1000);

            Log::info('Email sync completed successfully', [
                'user_id' => $this->userId,
                'sync_type' => $this->syncType,
                'duration_ms' => $duration,
                'stats' => $stats
            ]);

        } catch (SyncLockedException $e) {
            // Don't retry if sync is locked
            Log::info('Sync already in progress, skipping', [
                'user_id' => $this->userId,
                'lock_owner' => $e->getMessage()
            ]);

            $this->delete();
            return;

        } catch (\App\Exceptions\TokenRefreshException $e) {
            // Token is invalid, notify user and don't retry
            Log::error('Token refresh failed permanently', [
                'user_id' => $this->userId,
                'error' => $e->getMessage()
            ]);

            // Optionally notify user to reconnect
            dispatch(new NotifyUserToReconnect($this->userId));

            $this->fail($e);
            return;

        } catch (\Exception $e) {
            $duration = round((microtime(true) - $startTime) * 1000);

            Log::error('Email sync failed', [
                'user_id' => $this->userId,
                'sync_type' => $this->syncType,
                'attempt' => $this->attempts(),
                'duration_ms' => $duration,
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);

            throw $e;
        }
    }

    /**
     * Handle job failure
     */
    public function failed(\Throwable $exception): void
    {
        Log::error('Email sync job failed permanently', [
            'user_id' => $this->userId,
            'sync_type' => $this->syncType,
            'attempts' => $this->attempts(),
            'error' => $exception->getMessage(),
            'exception_class' => get_class($exception)
        ]);

        // Release lock if stuck
        $syncState = EmailSyncState::where('user_id', $this->userId)->first();
        if ($syncState && $syncState->is_locked) {
            $syncState->releaseLock();
            Log::info('Released stuck lock after job failure', [
                'user_id' => $this->userId
            ]);
        }

        // Notify admins if critical
        if ($syncState && $syncState->consecutive_failures >= 5) {
            dispatch(new NotifyAdminSyncFailure($this->userId, $exception));
        }
    }

    /**
     * Calculate backoff time
     */
    public function backoff(): array
    {
        return $this->backoff;
    }

    /**
     * Determine retry deadline
     */
    public function retryUntil(): \DateTime
    {
        return now()->addHours(2);
    }

    /**
     * Get job tags for monitoring
     */
    public function tags(): array
    {
        return [
            'sync',
            'user:' . $this->userId,
            'type:' . $this->syncType
        ];
    }
}

// ============================================
// Renew All Subscriptions Job
// ============================================

class RenewAllSubscriptions implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    public $tries = 1;
    public $timeout = 600; // 10 minutes

    public function __construct()
    {
        $this->onQueue('subscriptions');
    }

    /**
     * Execute the job
     */
    public function handle(): void
    {
        $renewalDays = config('mail.sync.subscription_renewal_days', 2);

        Log::info('Starting subscription renewal process', [
            'renewal_threshold_days' => $renewalDays
        ]);

        $states = EmailSyncState::active()
            ->needsRenewal($renewalDays)
            ->with('user')
            ->get();

        $stats = [
            'total' => $states->count(),
            'dispatched' => 0,
            'skipped' => 0,
            'failed' => 0
        ];

        foreach ($states as $state) {
            try {
                // Check if user still has valid token
                if (!$state->user || !$state->user->hasValidMicrosoftToken()) {
                    Log::warning('Skipping renewal - invalid token', [
                        'user_id' => $state->user_id
                    ]);
                    $stats['skipped']++;
                    continue;
                }

                // Dispatch sync job with random delay to prevent thundering herd
                SyncUserEmails::dispatch($state->user_id, 'delta')
                    ->delay(now()->addSeconds(rand(1, 60)));

                $stats['dispatched']++;

            } catch (\Exception $e) {
                $stats['failed']++;
                Log::error('Failed to dispatch renewal sync', [
                    'user_id' => $state->user_id,
                    'error' => $e->getMessage()
                ]);
            }
        }

        Log::info('Subscription renewal process completed', $stats);
    }

    /**
     * Handle job failure
     */
    public function failed(\Throwable $exception): void
    {
        Log::critical('Subscription renewal job failed', [
            'error' => $exception->getMessage(),
            'trace' => $exception->getTraceAsString()
        ]);

        // Notify admins
        dispatch(new NotifyAdminCriticalFailure('subscription_renewal', $exception));
    }
}

// ============================================
// Clean Stale Locks Job
// ============================================

class CleanStaleLocks implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    public $tries = 1;
    public $timeout = 60;

    public function __construct()
    {
        $this->onQueue('maintenance');
    }

    /**
     * Execute the job
     */
    public function handle(): void
    {
        $timeout = config('mail.sync.lock_timeout', 300);

        $staleLocks = EmailSyncState::stuckLocks(ceil($timeout / 60))
            ->get();

        if ($staleLocks->isEmpty()) {
            Log::debug('No stale locks found');
            return;
        }

        Log::info('Cleaning stale locks', [
            'count' => $staleLocks->count(),
            'timeout_seconds' => $timeout
        ]);

        $cleaned = 0;

        foreach ($staleLocks as $state) {
            try {
                $state->releaseLock();
                $cleaned++;

                Log::warning('Released stale lock', [
                    'user_id' => $state->user_id,
                    'lock_owner' => $state->lock_owner,
                    'locked_at' => $state->locked_at,
                    'minutes_locked' => $state->locked_at->diffInMinutes(now())
                ]);

            } catch (\Exception $e) {
                Log::error('Failed to release stale lock', [
                    'user_id' => $state->user_id,
                    'error' => $e->getMessage()
                ]);
            }
        }

        Log::info('Stale lock cleanup completed', [
            'cleaned' => $cleaned,
            'failed' => $staleLocks->count() - $cleaned
        ]);
    }
}

// ============================================
// Process Attachment Download Job
// ============================================

class DownloadEmailAttachment implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    public $tries = 3;
    public $backoff = [30, 120, 300];
    public $timeout = 120;

    protected int $attachmentId;

    public function __construct(int $attachmentId)
    {
        $this->attachmentId = $attachmentId;
        $this->onQueue('attachments');
    }

    /**
     * Execute the job
     */
    public function handle(MicrosoftGraphService $graphService): void
    {
        $attachment = EmailAttachment::find($this->attachmentId);

        if (!$attachment) {
            Log::warning('Attachment not found', [
                'attachment_id' => $this->attachmentId
            ]);
            $this->delete();
            return;
        }

        if ($attachment->is_downloaded) {
            Log::info('Attachment already downloaded', [
                'attachment_id' => $this->attachmentId
            ]);
            return;
        }

        try {
            $email = $attachment->email;
            $userId = $email->user_id;

            Log::info('Downloading attachment', [
                'attachment_id' => $this->attachmentId,
                'email_id' => $email->id,
                'user_id' => $userId,
                'size_bytes' => $attachment->size_bytes
            ]);

            // Check size limit
            $maxSizeMB = config('mail.sync.max_attachment_size_mb', 25);
            $sizeMB = $attachment->size_bytes / (1024 * 1024);

            if ($sizeMB > $maxSizeMB) {
                Log::warning('Attachment exceeds size limit', [
                    'attachment_id' => $this->attachmentId,
                    'size_mb' => round($sizeMB, 2),
                    'limit_mb' => $maxSizeMB
                ]);
                $this->fail(new \Exception("Attachment size ({$sizeMB}MB) exceeds limit ({$maxSizeMB}MB)"));
                return;
            }

            // Download attachment content
            $attachmentData = $graphService->downloadAttachment(
                $userId,
                $email->message_id,
                $attachment->attachment_id
            );

            // Store attachment
            $path = $this->storeAttachment($attachment, $attachmentData);

            $attachment->update([
                'storage_path' => $path,
                'is_downloaded' => true,
                'downloaded_at' => now()
            ]);

            Log::info('Attachment downloaded successfully', [
                'attachment_id' => $this->attachmentId,
                'path' => $path,
                'size_bytes' => $attachment->size_bytes
            ]);

        } catch (\Exception $e) {
            Log::error('Failed to download attachment', [
                'attachment_id' => $this->attachmentId,
                'attempt' => $this->attempts(),
                'error' => $e->getMessage()
            ]);

            throw $e;
        }
    }

    /**
     * Store attachment to disk
     */
    private function storeAttachment(EmailAttachment $attachment, array $attachmentData): string
    {
        $content = base64_decode($attachmentData['contentBytes']);

        // Create safe filename
        $safeFilename = $this->sanitizeFilename($attachment->name);

        $path = sprintf(
            '%s/%s/%s_%s',
            $attachment->email->user_id,
            $attachment->email_id,
            time(),
            $safeFilename
        );

        $disk = config('mail.sync.attachment_storage_disk', 'attachments');
        \Storage::disk($disk)->put($path, $content);

        return $path;
    }

    /**
     * Sanitize filename
     */
    private function sanitizeFilename(string $filename): string
    {
        // Remove any path components
        $filename = basename($filename);

        // Replace unsafe characters
        $filename = preg_replace('/[^a-zA-Z0-9._-]/', '_', $filename);

        // Limit length
        if (strlen($filename) > 200) {
            $extension = pathinfo($filename, PATHINFO_EXTENSION);
            $name = pathinfo($filename, PATHINFO_FILENAME);
            $filename = substr($name, 0, 195) . '.' . $extension;
        }

        return $filename;
    }

    /**
     * Handle job failure
     */
    public function failed(\Throwable $exception): void
    {
        Log::error('Attachment download failed permanently', [
            'attachment_id' => $this->attachmentId,
            'error' => $exception->getMessage()
        ]);

        // Mark as failed but keep record
        EmailAttachment::where('id', $this->attachmentId)
            ->update(['is_downloaded' => false]);
    }

    /**
     * Get job tags
     */
    public function tags(): array
    {
        return ['attachment', 'attachment:' . $this->attachmentId];
    }
}

// ============================================
// Cleanup Old Emails Job
// ============================================

class CleanupOldEmails implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    public $tries = 1;
    public $timeout = 300;

    protected int $daysToKeep;

    public function __construct(int $daysToKeep = 90)
    {
        $this->daysToKeep = $daysToKeep;
        $this->onQueue('maintenance');
    }

    /**
     * Execute the job
     */
    public function handle(): void
    {
        $cutoffDate = now()->subDays($this->daysToKeep);

        Log::info('Starting email cleanup', [
            'cutoff_date' => $cutoffDate->toDateString(),
            'days_to_keep' => $this->daysToKeep
        ]);

        try {
            // Delete old soft-deleted emails permanently
            $deleted = DB::transaction(function () use ($cutoffDate) {
                // First, delete associated attachments
                $attachmentCount = DB::table('email_attachments')
                    ->whereIn('email_id', function ($query) use ($cutoffDate) {
                        $query->select('id')
                            ->from('emails')
                            ->whereNotNull('deleted_at')
                            ->where('deleted_at', '<', $cutoffDate);
                    })
                    ->delete();

                // Then delete emails
                $emailCount = Email::onlyTrashed()
                    ->where('deleted_at', '<', $cutoffDate)
                    ->forceDelete();

                return [
                    'emails' => $emailCount,
                    'attachments' => $attachmentCount
                ];
            });

            Log::info('Email cleanup completed', $deleted);

            // Clean up orphaned attachment files
            $this->cleanupOrphanedFiles();

        } catch (\Exception $e) {
            Log::error('Email cleanup failed', [
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);

            throw $e;
        }
    }

    /**
     * Clean up orphaned attachment files
     */
    private function cleanupOrphanedFiles(): void
    {
        try {
            $disk = \Storage::disk(config('mail.sync.attachment_storage_disk'));
            $allFiles = $disk->allFiles();
            $orphaned = 0;

            foreach ($allFiles as $file) {
                // Check if attachment record exists
                $exists = EmailAttachment::where('storage_path', $file)
                    ->where('is_downloaded', true)
                    ->exists();

                if (!$exists) {
                    $disk->delete($file);
                    $orphaned++;
                }
            }

            if ($orphaned > 0) {
                Log::info('Cleaned up orphaned attachment files', [
                    'count' => $orphaned
                ]);
            }

        } catch (\Exception $e) {
            Log::warning('Failed to cleanup orphaned files', [
                'error' => $e->getMessage()
            ]);
        }
    }
}

// ============================================
// Retry Failed Syncs Job
// ============================================

class RetryFailedSyncs implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    public $tries = 1;
    public $timeout = 300;

    public function __construct()
    {
        $this->onQueue('email-sync');
    }

    /**
     * Execute the job
     */
    public function handle(): void
    {
        $retryAfterHours = config('mail.sync.retry_failed_after_hours', 1);
        $maxFailures = config('mail.sync.max_consecutive_failures', 5);

        // Get users with failed syncs but not permanently failed
        $states = EmailSyncState::where('status', 'failed')
            ->where('consecutive_failures', '<', $maxFailures)
            ->where('last_attempt_at', '<', now()->subHours($retryAfterHours))
            ->with('user')
            ->get();

        Log::info('Retrying failed syncs', [
            'count' => $states->count(),
            'retry_after_hours' => $retryAfterHours
        ]);

        $retried = 0;
        $skipped = 0;

        foreach ($states as $state) {
            try {
                // Verify user still has valid setup
                if (!$state->user || !$state->user->hasMicrosoftAccount()) {
                    $skipped++;
                    continue;
                }

                // Reset status to active
                $state->update(['status' => 'active']);

                // Dispatch sync with delay
                SyncUserEmails::dispatch($state->user_id, 'delta')
                    ->delay(now()->addSeconds(rand(5, 120)));

                $retried++;

            } catch (\Exception $e) {
                Log::error('Failed to retry sync', [
                    'user_id' => $state->user_id,
                    'error' => $e->getMessage()
                ]);
            }
        }

        Log::info('Failed sync retry completed', [
            'retried' => $retried,
            'skipped' => $skipped
        ]);
    }
}

// ============================================
// Notification Jobs (Stubs)
// ============================================

class NotifyUserToReconnect implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    protected int $userId;

    public function __construct(int $userId)
    {
        $this->userId = $userId;
        $this->onQueue('notifications');
    }

    public function handle(): void
    {
        $user = User::find($this->userId);

        if ($user) {
            // Send notification to user
            // $user->notify(new MicrosoftAccountExpired());

            Log::info('Sent reconnection notification', [
                'user_id' => $this->userId
            ]);
        }
    }
}

class NotifyAdminSyncFailure implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    protected int $userId;
    protected \Throwable $exception;

    public function __construct(int $userId, \Throwable $exception)
    {
        $this->userId = $userId;
        $this->exception = $exception;
        $this->onQueue('notifications');
    }

    public function handle(): void
    {
        // Send alert to admins
        Log::critical('Admin notification: Persistent sync failure', [
            'user_id' => $this->userId,
            'error' => $this->exception->getMessage()
        ]);

        // Send to Slack, email, etc.
    }
}

class NotifyAdminCriticalFailure implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    protected string $type;
    protected \Throwable $exception;

    public function __construct(string $type, \Throwable $exception)
    {
        $this->type = $type;
        $this->exception = $exception;
        $this->onQueue('notifications');
    }

    public function handle(): void
    {
        Log::critical('Critical system failure', [
            'type' => $this->type,
            'error' => $this->exception->getMessage(),
            'trace' => $this->exception->getTraceAsString()
        ]);

        // Send urgent alert
    }
}
