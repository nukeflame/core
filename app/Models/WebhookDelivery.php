<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class WebhookDelivery extends Model
{
    use HasFactory;

    protected $fillable = [
        'subscription_id',
        'user_id',
        'change_type',
        'resource',
        'resource_data',
        'client_state',
        'is_valid',
        'is_processed',
        'processed_at',
        'payload',
        'source_ip',
        'processing_error',
    ];

    protected $casts = [
        'is_valid' => 'boolean',
        'is_processed' => 'boolean',
        'processed_at' => 'datetime',
        'payload' => 'array',
        'resource_data' => 'array',
    ];

    /**
     * Get the subscription that owns the webhook delivery
     */
    public function subscription(): BelongsTo
    {
        return $this->belongsTo(GraphSubscription::class, 'subscription_id', 'subscription_id');
    }

    /**
     * Get the user that owns the webhook delivery
     */
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    /**
     * Mark as processed
     */
    public function markAsProcessed(): void
    {
        $this->update([
            'is_processed' => true,
            'processed_at' => now(),
        ]);
    }

    /**
     * Mark as failed
     */
    public function markAsFailed(string $error): void
    {
        $this->update([
            'is_processed' => true,
            'processed_at' => now(),
            'processing_error' => $error,
        ]);
    }

    /**
     * Scope for unprocessed deliveries
     */
    public function scopeUnprocessed($query)
    {
        return $query->where('is_processed', false)
                    ->where('is_valid', true);
    }

    /**
     * Scope for processed deliveries
     */
    public function scopeProcessed($query)
    {
        return $query->where('is_processed', true);
    }

    /**
     * Scope for valid deliveries
     */
    public function scopeValid($query)
    {
        return $query->where('is_valid', true);
    }

    /**
     * Scope for failed deliveries
     */
    public function scopeFailed($query)
    {
        return $query->whereNotNull('processing_error');
    }
}
