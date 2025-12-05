<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class TransactionLog extends Model
{
    protected $table = 'transaction_logs';

    protected $fillable = [
        'entity_type',
        'entity_id',
        'action',
        'old_values',
        'new_values',
        'user_id',
        'ip_address',
        'user_agent',
    ];

    protected $casts = [
        'old_values' => 'array',
        'new_values' => 'array',
    ];

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function scopeForEntity($query, string $type, int $id)
    {
        return $query->where('entity_type', $type)->where('entity_id', $id);
    }

    public function scopeByAction($query, string $action)
    {
        return $query->where('action', $action);
    }

    public function scopeByUser($query, int $userId)
    {
        return $query->where('user_id', $userId);
    }

    public function scopeRecent($query, int $days = 30)
    {
        return $query->where('created_at', '>=', now()->subDays($days));
    }


    public static function logDebitNote(
        DebitNote $debitNote,
        string $action,
        ?array $oldValues = null
    ): self {
        return self::create([
            'entity_type' => 'debit_note',
            'entity_id' => $debitNote->id,
            'action' => $action,
            'old_values' => $oldValues,
            'new_values' => $debitNote->toArray(),
            'user_id' => auth()->id(),
            'ip_address' => request()->ip(),
            'user_agent' => request()->userAgent(),
        ]);
    }

    public static function getHistory(string $entityType, int $entityId)
    {
        return self::forEntity($entityType, $entityId)
            ->with('user:id,name')
            ->orderBy('created_at', 'desc')
            ->get();
    }
}
