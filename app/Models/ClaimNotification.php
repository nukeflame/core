<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class ClaimNotification extends Model
{
    use HasFactory;

    /**
     * The attributes that are mass assignable.
     */
    protected $fillable = [
        'claim_id',
        'user_id',
        'status',
        'is_resend',
        'data',
        'recipients',
        'message_id',
        'sent_at',
        'failed_at',
        'error_message',
        'scheduled_for'
    ];

    /**
     * The attributes that should be cast.
     */
    protected $casts = [
        'data' => 'array',
        'recipients' => 'array',
        'is_resend' => 'boolean',
        'sent_at' => 'datetime',
        'failed_at' => 'datetime',
        'scheduled_for' => 'datetime'
    ];

    /**
     * Get the claim that owns the notification.
     */
    public function claim(): BelongsTo
    {
        return $this->belongsTo(ClaimNtfRegister::class);
    }

    /**
     * Get the user who sent the notification.
     */
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    /**
     * Scope a query to only include sent notifications.
     */
    public function scopeSent($query)
    {
        return $query->where('status', 'sent');
    }

    /**
     * Scope a query to only include failed notifications.
     */
    public function scopeFailed($query)
    {
        return $query->where('status', 'failed');
    }

    /**
     * Scope a query to only include resend notifications.
     */
    public function scopeResends($query)
    {
        return $query->where('is_resend', true);
    }

    /**
     * Get the formatted recipients string.
     */
    public function getFormattedRecipientsAttribute(): string
    {
        if (empty($this->recipients)) {
            return 'No recipients';
        }

        return implode(', ', $this->recipients);
    }

    /**
     * Check if the notification was sent successfully.
     */
    public function wasSent(): bool
    {
        return $this->status === 'sent' && !is_null($this->sent_at);
    }

    /**
     * Check if the notification failed.
     */
    public function hasFailed(): bool
    {
        return $this->status === 'failed';
    }

    /**
     * Check if the notification is pending.
     */
    public function isPending(): bool
    {
        return $this->status === 'pending';
    }
}
