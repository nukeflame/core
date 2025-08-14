<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\SoftDeletes;

class Email extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'sender_email',
        'sender_name',
        'recipient_email',
        'recipient_name',
        'subject',
        'body',
        'recipients',
        'cc_emails',
        'bcc_emails',
        'claim_id',
        'claim_no',
        'priority',
        'category',
        'reference',
        'attachments',
        'status',
        'folder',
        'reply_to_id',
        'outlook_message_id',
        'conversation_id',
        'internet_message_id',
        'sent_at',
        'failed_at',
        'error_message',
        'sent_by'
    ];

    protected $casts = [
        'recipients' => 'array',
        'cc_emails' => 'array',
        'bcc_emails' => 'array',
        'attachments' => 'array',
        'sent_at' => 'datetime',
        'failed_at' => 'datetime',
        'created_at' => 'datetime',
        'updated_at' => 'datetime'
    ];

    protected $dates = [
        'sent_at',
        'failed_at',
        'created_at',
        'updated_at',
        'deleted_at'
    ];

    /**
     * Relationship with ClaimRegister
     */
    public function claim(): BelongsTo
    {
        return $this->belongsTo(ClaimRegister::class, 'claim_serial_no', 'claim_id');
    }

    /**
     * Relationship with User (sender)
     */
    public function sender(): BelongsTo
    {
        return $this->belongsTo(User::class, 'sent_by');
    }

    /**
     * Get the original message (for replies)
     */
    public function originalMessage(): BelongsTo
    {
        return $this->belongsTo(Email::class, 'reply_to_id', 'outlook_message_id');
    }

    /**
     * Get replies to this message
     */
    public function replies()
    {
        return $this->hasMany(Email::class, 'reply_to_id', 'outlook_message_id');
    }

    /**
     * Scope for filtering by status
     */
    public function scopeWithStatus($query, $status)
    {
        return $query->where('status', $status);
    }

    /**
     * Scope for filtering by claim
     */
    public function scopeForClaim($query, $claimNo)
    {
        return $query->where('claim_no', $claimNo);
    }

    /**
     * Scope for filtering by date range
     */
    public function scopeDateRange($query, $from, $to = null)
    {
        $query->where('created_at', '>=', $from);
        if ($to) {
            $query->where('created_at', '<=', $to);
        }
        return $query;
    }

    /**
     * Scope for filtering by priority
     */
    public function scopeWithPriority($query, $priority)
    {
        return $query->where('priority', $priority);
    }

    /**
     * Scope for filtering by category
     */
    public function scopeWithCategory($query, $category)
    {
        return $query->where('category', $category);
    }

    /**
     * Scope for conversation emails
     */
    public function scopeInConversation($query, $conversationId)
    {
        return $query->where('conversation_id', $conversationId);
    }

    /**
     * Scope for sent emails
     */
    public function scopeSent($query)
    {
        return $query->where('status', 'sent');
    }

    /**
     * Scope for failed emails
     */
    public function scopeFailed($query)
    {
        return $query->whereIn('status', ['failed', 'permanently_failed']);
    }

    /**
     * Scope for pending emails
     */
    public function scopePending($query)
    {
        return $query->where('status', 'queued');
    }

    /**
     * Check if email is sent
     */
    public function isSent(): bool
    {
        return $this->status === 'sent';
    }

    /**
     * Check if email failed
     */
    public function hasFailed(): bool
    {
        return in_array($this->status, ['failed', 'permanently_failed']);
    }

    /**
     * Check if email is pending
     */
    public function isPending(): bool
    {
        return $this->status === 'queued';
    }

    /**
     * Check if email is a reply
     */
    public function isReply(): bool
    {
        return !empty($this->reply_to_id);
    }

    /**
     * Check if email has attachments
     */
    public function hasAttachments(): bool
    {
        return !empty($this->attachments) && count($this->attachments) > 0;
    }

    /**
     * Get attachment count
     */
    public function getAttachmentCount(): int
    {
        return !empty($this->attachments) ? count($this->attachments) : 0;
    }

    /**
     * Get total attachment size in bytes
     */
    public function getTotalAttachmentSize(): int
    {
        if (empty($this->attachments)) {
            return 0;
        }

        return array_sum(array_column($this->attachments, 'size'));
    }

    /**
     * Get formatted attachment size
     */
    public function getFormattedAttachmentSize(): string
    {
        $bytes = $this->getTotalAttachmentSize();

        if ($bytes === 0) {
            return '0 B';
        }

        $units = ['B', 'KB', 'MB', 'GB'];
        $base = log($bytes, 1024);

        return round(pow(1024, $base - floor($base)), 2) . ' ' . $units[floor($base)];
    }

    /**
     * Get all recipients (TO + CC + BCC)
     */
    public function getAllRecipients(): array
    {
        $allRecipients = [];

        if (!empty($this->recipients)) {
            $allRecipients = array_merge($allRecipients, $this->recipients);
        }

        if (!empty($this->cc_emails)) {
            $allRecipients = array_merge($allRecipients, $this->cc_emails);
        }

        if (!empty($this->bcc_emails)) {
            $allRecipients = array_merge($allRecipients, $this->bcc_emails);
        }

        return array_unique($allRecipients);
    }

    /**
     * Get recipient count
     */
    public function getRecipientCount(): int
    {
        return count($this->getAllRecipients());
    }

    /**
     * Mark as sent
     */
    public function markAsSent($messageId = null, $conversationId = null): void
    {
        $updateData = [
            'status' => 'sent',
            'sent_at' => now(),
            'error_message' => null,
            'failed_at' => null
        ];

        if ($messageId) {
            $updateData['outlook_message_id'] = $messageId;
        }

        if ($conversationId) {
            $updateData['conversation_id'] = $conversationId;
        }

        $this->update($updateData);
    }

    /**
     * Mark as failed
     */
    public function markAsFailed($errorMessage, $permanent = false): void
    {
        $this->update([
            'status' => $permanent ? 'permanently_failed' : 'failed',
            'failed_at' => now(),
            'error_message' => $errorMessage
        ]);
    }

    /**
     * Reset for retry
     */
    public function resetForRetry(): void
    {
        $this->update([
            'status' => 'queued',
            'error_message' => null,
            'failed_at' => null
        ]);
    }

    /**
     * Get status badge color for UI
     */
    public function getStatusBadgeColor(): string
    {
        return match ($this->status) {
            'sent' => 'success',
            'queued' => 'warning',
            'failed' => 'danger',
            'permanently_failed' => 'dark',
            default => 'secondary'
        };
    }

    /**
     * Get priority badge color for UI
     */
    public function getPriorityBadgeColor(): string
    {
        return match ($this->priority) {
            'high' => 'danger',
            'normal' => 'primary',
            'low' => 'secondary',
            default => 'primary'
        };
    }

    /**
     * Get formatted subject with prefix
     */
    public function getFormattedSubject(): string
    {
        $subject = $this->subject;

        if ($this->isReply() && !str_starts_with($subject, 'RE:')) {
            $subject = 'RE: ' . $subject;
        }

        return $subject;
    }

    /**
     * Get email thread (conversation)
     */
    public function getThread()
    {
        if (empty($this->conversation_id)) {
            return collect([$this]);
        }

        return self::inConversation($this->conversation_id)
            ->orderBy('created_at', 'asc')
            ->get();
    }

    /**
     * Scope for recent emails
     */
    public function scopeRecent($query, $days = 7)
    {
        return $query->where('created_at', '>=', now()->subDays($days));
    }

    /**
     * Get email body preview (first 150 characters)
     */
    public function getBodyPreview($length = 150): string
    {
        $body = strip_tags($this->body);
        return strlen($body) > $length ? substr($body, 0, $length) . '...' : $body;
    }
}
