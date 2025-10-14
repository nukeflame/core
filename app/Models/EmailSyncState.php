<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class EmailSyncState extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'delta_token',
        'delta_token_expires_at',
        'last_synced_at',
        'last_successful_sync_at',
        'sync_attempts',
        'consecutive_failures',
        'last_attempt_at',
        'last_error',
        'last_error_code',
        'subscription_id',
        'subscription_expires_at',
        'subscription_created_at',
        'is_locked',
        'locked_at',
        'lock_owner',
        'status',
        'total_emails_synced',
        'emails_synced_this_session',
        'sync_statistics'
    ];

    protected $casts = [
        'delta_token_expires_at' => 'datetime',
        'last_synced_at' => 'datetime',
        'last_successful_sync_at' => 'datetime',
        'last_attempt_at' => 'datetime',
        'subscription_expires_at' => 'datetime',
        'subscription_created_at' => 'datetime',
        'locked_at' => 'datetime',
        'is_locked' => 'boolean',
        'sync_attempts' => 'integer',
        'consecutive_failures' => 'integer',
        'total_emails_synced' => 'integer',
        'emails_synced_this_session' => 'integer',
        'sync_statistics' => 'array',
    ];

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function getLastSyncDurationAttribute(): ?int
    {
        if (!$this->sync_statistics || !isset($this->sync_statistics['last_sync'])) {
            return null;
        }

        return $this->sync_statistics['last_sync']['duration_ms'] ?? null;
    }

    public function getAverageSyncDurationAttribute(): ?float
    {
        if (!$this->sync_statistics) {
            return null;
        }

        $durations = collect($this->sync_statistics)
            ->where('duration_ms', '>', 0)
            ->pluck('duration_ms');

        return $durations->isEmpty() ? null : $durations->avg();
    }

    public function getSuccessRateAttribute(): ?float
    {
        $totalAttempts = $this->sync_attempts;

        if ($totalAttempts === 0) {
            return null;
        }

        $successful = $totalAttempts - $this->consecutive_failures;
        return ($successful / $totalAttempts) * 100;
    }

    public function getHealthScoreAttribute(): int
    {
        $score = 100;
        $score -= min($this->consecutive_failures * 10, 50);
        if ($this->last_synced_at) {
            $hoursSinceSync = $this->last_synced_at->diffInHours(now());
            if ($hoursSinceSync > 24) {
                $score -= min(($hoursSinceSync - 24) * 2, 30);
            }
        } else {
            $score -= 30;
        }

        if ($this->is_locked && $this->locked_at) {
            $minutesLocked = $this->locked_at->diffInMinutes(now());
            if ($minutesLocked > 10) {
                $score -= min($minutesLocked - 10, 20);
            }
        }

        return max(0, $score);
    }

    public function needsSubscriptionRenewal(int $daysBeforeExpiry = 2): bool
    {
        if (!$this->subscription_expires_at) {
            return true;
        }

        return $this->subscription_expires_at
            ->subDays($daysBeforeExpiry)
            ->isPast();
    }

    public function isSubscriptionExpired(): bool
    {
        return !$this->subscription_expires_at ||
            $this->subscription_expires_at->isPast();
    }

    public function isDeltaTokenExpired(): bool
    {
        return !$this->delta_token_expires_at ||
            $this->delta_token_expires_at->isPast();
    }

    public function shouldPauseSync(): bool
    {
        return $this->consecutive_failures >= config('mail.sync.max_consecutive_failures', 5);
    }

    public function acquireLock(string $owner, int $timeoutSeconds = 300): bool
    {
        if (
            $this->is_locked &&
            $this->locked_at &&
            $this->locked_at->addSeconds($timeoutSeconds)->isFuture()
        ) {
            return false;
        }

        return $this->update([
            'is_locked' => true,
            'locked_at' => now(),
            'lock_owner' => $owner
        ]);
    }

    public function releaseLock(): bool
    {
        return $this->update([
            'is_locked' => false,
            'locked_at' => null,
            'lock_owner' => null
        ]);
    }

    public function markSyncStarted(): void
    {
        $this->update([
            'sync_attempts' => $this->sync_attempts + 1,
            'last_attempt_at' => now(),
            'emails_synced_this_session' => 0
        ]);
    }

    public function markSyncSuccess(int $emailsProcessed = 0): void
    {
        $this->update([
            'last_synced_at' => now(),
            'last_successful_sync_at' => now(),
            'consecutive_failures' => 0,
            'last_error' => null,
            'last_error_code' => null,
            'total_emails_synced' => $this->total_emails_synced + $emailsProcessed,
            'emails_synced_this_session' => $emailsProcessed,
            'status' => 'active'
        ]);
    }

    public function markSyncFailed(string $error, $errorCode = null): void
    {
        $consecutiveFailures = $this->consecutive_failures + 1;
        $maxFailures = config('mail.sync.max_consecutive_failures', 5);

        $this->update([
            'consecutive_failures' => $consecutiveFailures,
            'last_error' => substr($error, 0, 1000), // Limit error message length
            'last_error_code' => $errorCode,
            'status' => $consecutiveFailures >= $maxFailures ? 'failed' : $this->status
        ]);
    }

    public function resetDeltaToken(): void
    {
        $this->update([
            'delta_token' => null,
            'delta_token_expires_at' => null
        ]);
    }

    public function updateStatistics(array $stats): void
    {
        $currentStats = $this->sync_statistics ?? [];

        $this->update([
            'sync_statistics' => array_merge($currentStats, [
                'last_sync' => array_merge($stats, [
                    'timestamp' => now()->toIso8601String()
                ])
            ])
        ]);
    }

    public function reset(): void
    {
        $this->update([
            'consecutive_failures' => 0,
            'last_error' => null,
            'last_error_code' => null,
            'status' => 'active',
            'is_locked' => false,
            'locked_at' => null,
            'lock_owner' => null
        ]);
    }

    public function scopeActive($query)
    {
        return $query->where('status', 'active');
    }

    public function scopeFailed($query)
    {
        return $query->where('status', 'failed');
    }

    public function scopeNeedsRenewal($query, int $days = 2)
    {
        return $query->whereNotNull('subscription_id')
            ->where('subscription_expires_at', '<', now()->addDays($days));
    }

    public function scopeStale($query, int $hours = 24)
    {
        return $query->where(function ($q) use ($hours) {
            $q->where('last_synced_at', '<', now()->subHours($hours))
                ->orWhereNull('last_synced_at');
        });
    }

    public function scopeLocked($query)
    {
        return $query->where('is_locked', true);
    }

    public function scopeStuckLocks($query, int $minutes = 30)
    {
        return $query->where('is_locked', true)
            ->where('locked_at', '<', now()->subMinutes($minutes));
    }
}
