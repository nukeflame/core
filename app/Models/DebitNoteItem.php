<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class DebitNoteItem extends Model
{
    use HasFactory;

    protected $table = 'debit_note_items';

    protected $fillable = [
        'debit_note_id',
        'line_no',
        'item_code',
        'description',
        'class_group_code',
        'class_code',
        'line_rate',
        'ledger',
        'type',
        'amount',
        'original_amount',
        'original_line_rate',
        'original_currency',
        'reference',
        'remarks',
        'net_amount',
        'item_no',
        'status'
    ];

    protected $casts = [
        'debit_note_id' => 'integer',
        'line_no' => 'integer',
        'line_rate' => 'decimal:4',
        'amount' => 'decimal:2',
        'original_amount' => 'decimal:2',
        'original_line_rate' => 'decimal:4',
    ];

    protected $attributes = [
        'line_no' => 1,
        'ledger' => 'DR',
        'amount' => 0,
    ];

    public const LEDGER_DEBIT = 'DR';
    public const LEDGER_CREDIT = 'CR';

    // Item codes that are typically debits (increase receivable)
    public const DEBIT_CODES = ['IT01', 'IT26'];

    // Item codes that are typically credits (decrease receivable)
    public const CREDIT_CODES = ['IT02', 'IT03', 'IT04', 'IT05', 'IT06', 'IT27', 'IT29'];

    public function debitNote(): BelongsTo
    {
        return $this->belongsTo(DebitNote::class);
    }

    public function itemCodeRef(): BelongsTo
    {
        return $this->belongsTo(TreatyItemCode::class, 'item_code', 'item_code');
    }

    public function businessClassRef(): BelongsTo
    {
        return $this->belongsTo(Classes::class, 'class_code', 'class_code');
    }

    public function classGroupRef(): BelongsTo
    {
        return $this->belongsTo(ClassGroup::class, 'class_group_code', 'class_group_code');
    }

    public function scopeOrdered(Builder $query): Builder
    {
        return $query->orderBy('line_no');
    }

    public function scopeDebits(Builder $query): Builder
    {
        return $query->where('ledger', self::LEDGER_DEBIT);
    }

    public function scopeCredits(Builder $query): Builder
    {
        return $query->where('ledger', self::LEDGER_CREDIT);
    }

    public function scopeOfType(Builder $query, string $itemCode): Builder
    {
        return $query->where('description', $itemCode);
    }

    public function scopeForClass(Builder $query, string $classCode): Builder
    {
        return $query->where('class_code', $classCode);
    }

    public function getDescriptionTextAttribute(): string
    {
        if ($this->relationLoaded('itemCodeRef') && $this->itemCodeRef) {
            return $this->itemCodeRef->description;
        }

        $codes = TreatyItemCode::getForSelect();
        return $codes[$this->description]['description'] ?? $this->description;
    }

    public function getClassNameAttribute(): string
    {
        if (empty($this->class_code)) {
            return '-';
        }

        if ($this->relationLoaded('businessClassRef') && $this->businessClassRef) {
            return $this->businessClassRef->class_name;
        }

        $classes = Classes::getForSelect();
        return $classes[$this->class_code] ?? $this->class_code;
    }

    public function getClassGroupNameAttribute(): string
    {
        if (empty($this->class_group_code)) {
            return '-';
        }

        if ($this->relationLoaded('classGroupRef') && $this->classGroupRef) {
            return $this->classGroupRef->class_group_name;
        }

        $groups = ClassGroup::getForSelect();
        return $groups[$this->class_group_code] ?? $this->class_group_code;
    }

    public function getSignedAmountAttribute(): float
    {
        return $this->isDebit() ? (float) $this->amount : -(float) $this->amount;
    }

    public function getFormattedAmountAttribute(): string
    {
        $prefix = $this->isCredit() ? '-' : '';
        return $prefix . number_format((float) $this->amount, 2);
    }

    public function getFormattedRateAttribute(): string
    {
        if (empty($this->line_rate)) {
            return '-';
        }
        return number_format((float) $this->line_rate, 2) . '%';
    }

    public function getLedgerLabelAttribute(): string
    {
        return $this->ledger === self::LEDGER_DEBIT ? 'Debit' : 'Credit';
    }

    public function isDebit(): bool
    {
        return $this->ledger === self::LEDGER_DEBIT;
    }

    public function isCredit(): bool
    {
        return $this->ledger === self::LEDGER_CREDIT;
    }

    public function isPremiumItem(): bool
    {
        return in_array($this->description, ['IT01', 'IT26']);
    }

    public function isCommissionItem(): bool
    {
        return $this->description === 'IT03';
    }

    public function isTaxItem(): bool
    {
        return in_array($this->description, ['IT04', 'IT05', 'IT29']);
    }

    public function isClaimsItem(): bool
    {
        return in_array($this->description, ['IT02', 'IT27']);
    }

    public function setLedgerFromItemCode(): self
    {
        if (in_array($this->description, self::DEBIT_CODES)) {
            $this->ledger = self::LEDGER_DEBIT;
        } else {
            $this->ledger = self::LEDGER_CREDIT;
        }

        return $this;
    }

    public static function getItemCodesForSelect(): array
    {
        return TreatyItemCode::getForSelect();
    }

    public static function getBusinessClassesGrouped(): array
    {
        return Classes::getGroupedByCategory();
    }

    public static function getValidItemCodes(): array
    {
        return TreatyItemCode::getValidCodes();
    }

    public static function getValidClassCodes(): array
    {
        return Classes::getValidCodes();
    }
}
