<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Carbon\Carbon;

class GraphSubscription extends Model
{
    use HasFactory;

    protected $fillable = [
        'subscription_id',
        'user_id',
        'user_email',
        'resource',
        'change_type',
        'notification_url',
        'client_state',
        'expiration_date',
        'status',
        'last_notification_at',
        'notification_count',
        'last_renewal_at',
        'renewal_attempts',
        'last_error',
    ];

    protected $casts = [
        'expiration_date' => 'datetime',
        'last_notification_at' => 'datetime',
        'last_renewal_at' => 'datetime',
        'notification_count' => 'integer',
        'renewal_attempts' => 'integer',
    ];

    /**
     * Get the user that owns the subscription
     */
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    /**
     * Get webhook deliveries for this subscription
     */
    public function webhookDeliveries(): HasMany
    {
        return $this->hasMany(WebhookDelivery::class, 'subscription_id', 'subscription_id');
    }

    /**
     * Check if subscription needs renewal
     */
    public function needsRenewal(): bool
    {
        if ($this->status !== 'active') {
            return false;
        }

        // Renew 24 hours before expiration
        return $this->expiration_date &&
               $this->expiration_date->subHours(24)->isPast();
    }

    /**
     * Check if subscription is expired
     */
    public function isExpired(): bool
    {
        return $this->expiration_date && $this->expiration_date->isPast();
    }

    /**
     * Check if subscription is active
     */
    public function isActive(): bool
    {
        return $this->status === 'active' && !$this->isExpired();
    }

    /**
     * Mark subscription as renewed
     */
    public function markAsRenewed(string $newExpirationDate): void
    {
        $this->update([
            'expiration_date' => Carbon::parse($newExpirationDate),
            'last_renewal_at' => now(),
            'renewal_attempts' => 0,
            'status' => 'active',
            'last_error' => null,
        ]);
    }

    /**
     * Increment notification counter
     */
    public function recordNotification(): void
    {
        $this->increment('notification_count');
        $this->update(['last_notification_at' => now()]);
    }

    /**
     * Mark subscription as failed
     */
    public function markAsFailed(string $error): void
    {
        $this->update([
            'status' => 'failed',
            'last_error' => $error,
        ]);
    }

    /**
     * Scope for active subscriptions
     */
    public function scopeActive($query)
    {
        return $query->where('status', 'active')
                    ->where('expiration_date', '>', now());
    }

    /**
     * Scope for expiring subscriptions
     */
    public function scopeExpiringSoon($query, int $hoursThreshold = 24)
    {
        return $query->where('status', 'active')
                    ->whereBetween('expiration_date', [
                        now(),
                        now()->addHours($hoursThreshold)
                    ]);
    }

    /**
     * Scope for expired subscriptions
     */
    public function scopeExpired($query)
    {
        return $query->where('expiration_date', '<', now());
    }
}
