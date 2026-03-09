<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;

class CreditNote extends Model
{
    use HasFactory, SoftDeletes;

    protected $table = 'credit_notes';

    protected $fillable = [
        'credit_note_no',
        'cover_no',
        'endorsement_no',
        'type_of_bus',
        'type',
        'installment_no',
        'posting_year',
        'posting_quarter',
        'posting_date',
        'currency',
        'exchange_rate',
        'gross_amount',
        'commission_rate',
        'commission_amount',
        'brokerage_rate',
        'brokerage_amount',
        'premium_levy',
        'reinsurance_levy',
        'withholding_tax',
        'other_deductions',
        'net_amount',
        'compute_premium_tax',
        'compute_reinsurance_tax',
        'compute_withholding_tax',
        'loss_participation',
        'sliding_commission',
        'show_cedant',
        'show_reinsurer',
        'comments',
        'internal_notes',
        'status',
        'submitted_at',
        'submitted_by',
        'approved_at',
        'approved_by',
        'rejected_at',
        'rejected_by',
        'rejection_reason',
        'cancelled_at',
        'cancelled_by',
        'cancellation_reason',
        'posted_at',
        'posted_by',
        'reinsurer_id',
        'created_by',
        'updated_by',
    ];

    protected $casts = [
        'posting_year' => 'integer',
        'posting_date' => 'date',
        'installment_no' => 'integer',
        'exchange_rate' => 'decimal:6',
        'gross_amount' => 'decimal:2',
        'commission_rate' => 'decimal:4',
        'commission_amount' => 'decimal:2',
        'brokerage_rate' => 'decimal:4',
        'brokerage_amount' => 'decimal:2',
        'premium_levy' => 'decimal:2',
        'reinsurance_levy' => 'decimal:2',
        'withholding_tax' => 'decimal:2',
        'other_deductions' => 'decimal:2',
        'net_amount' => 'decimal:2',
        'compute_premium_tax' => 'boolean',
        'compute_reinsurance_tax' => 'boolean',
        'compute_withholding_tax' => 'boolean',
        'loss_participation' => 'boolean',
        'sliding_commission' => 'boolean',
        'show_cedant' => 'boolean',
        'show_reinsurer' => 'boolean',
        'submitted_at' => 'datetime',
        'approved_at' => 'datetime',
        'rejected_at' => 'datetime',
        'cancelled_at' => 'datetime',
        'posted_at' => 'datetime',
    ];

    protected $attributes = [
        'status' => 'DRAFT',
        'currency' => 'KES',
        'exchange_rate' => 1.000000,
        'installment_no' => 1,
        'gross_amount' => 0,
        'commission_amount' => 0,
        'brokerage_amount' => 0,
        'premium_levy' => 0,
        'reinsurance_levy' => 0,
        'withholding_tax' => 0,
        'other_deductions' => 0,
        'net_amount' => 0,
        'compute_premium_tax' => false,
        'compute_reinsurance_tax' => false,
        'compute_withholding_tax' => false,
        'loss_participation' => false,
        'sliding_commission' => false,
        'show_cedant' => true,
        'show_reinsurer' => true,
    ];

    public const STATUS_DRAFT = 'DRAFT';
    public const STATUS_PENDING = 'PENDING';
    public const STATUS_APPROVED = 'APPROVED';
    public const STATUS_REJECTED = 'REJECTED';
    public const STATUS_CANCELLED = 'CANCELLED';
    public const STATUS_POSTED = 'POSTED';
    public const STATUS_REVERSED = 'REVERSED';

    public const STATUSES = [
        self::STATUS_DRAFT,
        self::STATUS_PENDING,
        self::STATUS_APPROVED,
        self::STATUS_REJECTED,
        self::STATUS_CANCELLED,
        self::STATUS_POSTED,
        self::STATUS_REVERSED,
    ];

    public const EDITABLE_STATUSES = [
        self::STATUS_DRAFT,
        self::STATUS_REJECTED,
    ];

    public const CANCELLABLE_STATUSES = [
        self::STATUS_DRAFT,
        self::STATUS_PENDING,
        self::STATUS_APPROVED,
    ];

    public const TYPE_FAC = 'FAC';
    public const TYPE_TREATY = 'TREATY';
    public const TYPE_RETRO = 'RETRO';

    public const BUSINESS_TYPES = [
        self::TYPE_FAC => 'Facultative',
        self::TYPE_TREATY => 'Treaty',
        self::TYPE_RETRO => 'Retrocession',
    ];

    public const QUARTERS = ['Q1', 'Q2', 'Q3', 'Q4'];

    public const LEDGER_DEBIT = 'DR';
    public const LEDGER_CREDIT = 'CR';

    public function items(): HasMany
    {
        return $this->hasMany(CreditNoteItem::class)->orderBy('line_no');
    }

    public function debitItems(): HasMany
    {
        return $this->items()->where('ledger', self::LEDGER_DEBIT);
    }

    public function creditItems(): HasMany
    {
        return $this->items()->where('ledger', self::LEDGER_CREDIT);
    }

    public function cover(): BelongsTo
    {
        return $this->belongsTo(CoverRegister::class, 'cover_no', 'cover_no');
    }

    public function createdBy(): BelongsTo
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    public function updatedBy(): BelongsTo
    {
        return $this->belongsTo(User::class, 'updated_by');
    }

    public function submittedBy(): BelongsTo
    {
        return $this->belongsTo(User::class, 'submitted_by');
    }

    public function approvedBy(): BelongsTo
    {
        return $this->belongsTo(User::class, 'approved_by');
    }

    public function rejectedBy(): BelongsTo
    {
        return $this->belongsTo(User::class, 'rejected_by');
    }

    public function cancelledBy(): BelongsTo
    {
        return $this->belongsTo(User::class, 'cancelled_by');
    }

    public function postedBy(): BelongsTo
    {
        return $this->belongsTo(User::class, 'posted_by');
    }

    public function logs(): HasMany
    {
        return $this->hasMany(TransactionLog::class, 'entity_id')
            ->where('entity_type', 'credit_note')
            ->orderByDesc('created_at');
    }

    public function scopeDraft(Builder $query): Builder
    {
        return $query->where('status', self::STATUS_DRAFT);
    }

    public function scopePending(Builder $query): Builder
    {
        return $query->where('status', self::STATUS_PENDING);
    }

    public function scopeApproved(Builder $query): Builder
    {
        return $query->where('status', self::STATUS_APPROVED);
    }

    public function scopeRejected(Builder $query): Builder
    {
        return $query->where('status', self::STATUS_REJECTED);
    }

    public function scopeCancelled(Builder $query): Builder
    {
        return $query->where('status', self::STATUS_CANCELLED);
    }

    public function scopePosted(Builder $query): Builder
    {
        return $query->where('status', self::STATUS_POSTED);
    }

    public function scopeActive(Builder $query): Builder
    {
        return $query->whereNotIn('status', [
            self::STATUS_CANCELLED,
            self::STATUS_REVERSED,
        ]);
    }

    public function scopeEditable(Builder $query): Builder
    {
        return $query->whereIn('status', self::EDITABLE_STATUSES);
    }

    public function scopeForCover(Builder $query, string $coverNo): Builder
    {
        return $query->where('cover_no', $coverNo);
    }

    public function scopeForEndorsement(Builder $query, string $endorsementNo): Builder
    {
        return $query->where('endorsement_no', $endorsementNo);
    }

    public function scopeForPeriod(Builder $query, int $year, ?string $quarter = null): Builder
    {
        $query->where('posting_year', $year);

        if ($quarter) {
            $query->where('posting_quarter', $quarter);
        }

        return $query;
    }

    public function scopeDateRange(Builder $query, ?string $from, ?string $to): Builder
    {
        if ($from) {
            $query->whereDate('posting_date', '>=', $from);
        }
        if ($to) {
            $query->whereDate('posting_date', '<=', $to);
        }
        return $query;
    }

    public function scopeOfType(Builder $query, string $type): Builder
    {
        return $query->where('type_of_bus', $type);
    }

    public function scopeAmountRange(Builder $query, ?float $min, ?float $max, string $field = 'gross_amount'): Builder
    {
        if ($min !== null) {
            $query->where($field, '>=', $min);
        }
        if ($max !== null) {
            $query->where($field, '<=', $max);
        }
        return $query;
    }

    public function scopeSearch(Builder $query, string $term): Builder
    {
        return $query->where(function ($q) use ($term) {
            $q->where('credit_note_no', 'like', "%{$term}%")
                ->orWhere('cover_no', 'like', "%{$term}%")
                ->orWhere('endorsement_no', 'like', "%{$term}%");
        });
    }

    public function scopeCreatedByUser(Builder $query, int $userId): Builder
    {
        return $query->where('created_by', $userId);
    }

    public function getTotalDeductionsAttribute(): float
    {
        return (float) $this->commission_amount
            + (float) $this->brokerage_amount
            + (float) $this->premium_levy
            + (float) $this->reinsurance_levy
            + (float) $this->withholding_tax
            + (float) $this->other_deductions;
    }

    public function getFormattedNumberAttribute(): string
    {
        return $this->credit_note_no ?? '';
    }

    public function getFormattedGrossAttribute(): string
    {
        return number_format((float) $this->gross_amount, 2);
    }

    public function getFormattedNetAttribute(): string
    {
        return number_format((float) $this->net_amount, 2);
    }

    public function getFormattedCommissionAttribute(): string
    {
        return number_format((float) $this->commission_amount, 2);
    }

    public function getFormattedBrokerageAttribute(): string
    {
        return number_format((float) $this->brokerage_amount, 2);
    }

    public function getFormattedDeductionsAttribute(): string
    {
        return number_format($this->total_deductions, 2);
    }

    public function getDisplayAmountAttribute(): string
    {
        return $this->currency . ' ' . $this->formatted_net;
    }

    public function getStatusBadgeClassAttribute(): string
    {
        return match ($this->status) {
            self::STATUS_DRAFT => 'bg-secondary',
            self::STATUS_PENDING => 'bg-warning text-dark',
            self::STATUS_APPROVED => 'bg-success',
            self::STATUS_REJECTED => 'bg-danger',
            self::STATUS_CANCELLED => 'bg-dark',
            self::STATUS_POSTED => 'bg-info',
            self::STATUS_REVERSED => 'bg-secondary',
            default => 'bg-light text-dark',
        };
    }

    public function getStatusLabelAttribute(): string
    {
        return match ($this->status) {
            self::STATUS_DRAFT => 'Draft',
            self::STATUS_PENDING => 'Pending Approval',
            self::STATUS_APPROVED => 'Approved',
            self::STATUS_REJECTED => 'Rejected',
            self::STATUS_CANCELLED => 'Cancelled',
            self::STATUS_POSTED => 'Posted',
            self::STATUS_REVERSED => 'Reversed',
            default => $this->status ?? 'Unknown',
        };
    }

    public function getBusinessTypeLabelAttribute(): string
    {
        return self::BUSINESS_TYPES[$this->type_of_bus] ?? $this->type_of_bus ?? '';
    }

    public function getPeriodDisplayAttribute(): string
    {
        return "{$this->posting_year} {$this->posting_quarter}";
    }

    public function getFormattedPostingDateAttribute(): string
    {
        return $this->posting_date?->format('d M Y') ?? '';
    }

    public function getItemsCountAttribute(): int
    {
        return $this->items()->count();
    }

    public function getDebitTotalAttribute(): float
    {
        return (float) $this->debitItems()->sum('amount');
    }

    public function getCreditTotalAttribute(): float
    {
        return (float) $this->creditItems()->sum('amount');
    }

    public function isDraft(): bool
    {
        return $this->status === self::STATUS_DRAFT;
    }

    public function isPending(): bool
    {
        return $this->status === self::STATUS_PENDING;
    }

    public function isApproved(): bool
    {
        return $this->status === self::STATUS_APPROVED;
    }

    public function isRejected(): bool
    {
        return $this->status === self::STATUS_REJECTED;
    }

    public function isCancelled(): bool
    {
        return $this->status === self::STATUS_CANCELLED;
    }

    public function isPosted(): bool
    {
        return $this->status === self::STATUS_POSTED;
    }

    public function isReversed(): bool
    {
        return $this->status === self::STATUS_REVERSED;
    }

    public function isActive(): bool
    {
        return !in_array($this->status, [self::STATUS_CANCELLED, self::STATUS_REVERSED]);
    }

    public function isEditable(): bool
    {
        return in_array($this->status, self::EDITABLE_STATUSES);
    }

    public function canSubmit(): bool
    {
        return $this->status === self::STATUS_DRAFT;
    }

    public function canApprove(): bool
    {
        return $this->status === self::STATUS_PENDING;
    }

    public function canCancel(): bool
    {
        return in_array($this->status, self::CANCELLABLE_STATUSES);
    }

    public function canPost(): bool
    {
        return $this->status === self::STATUS_APPROVED;
    }

    public function canReverse(): bool
    {
        return $this->status === self::STATUS_POSTED;
    }

    public function canRevertToDraft(): bool
    {
        return $this->status === self::STATUS_REJECTED;
    }

    public function canDelete(): bool
    {
        return $this->isEditable();
    }

    public function canDuplicate(): bool
    {
        return true;
    }

    public static function getStatusOptions(): array
    {
        return [
            self::STATUS_DRAFT => 'Draft',
            self::STATUS_PENDING => 'Pending Approval',
            self::STATUS_APPROVED => 'Approved',
            self::STATUS_REJECTED => 'Rejected',
            self::STATUS_CANCELLED => 'Cancelled',
            self::STATUS_POSTED => 'Posted',
            self::STATUS_REVERSED => 'Reversed',
        ];
    }

    public static function getBusinessTypeOptions(): array
    {
        return self::BUSINESS_TYPES;
    }

    public static function getQuarterOptions(): array
    {
        return array_combine(self::QUARTERS, self::QUARTERS);
    }

    public static function getYearOptions(): array
    {
        $currentYear = now()->year;
        $years = range($currentYear - 2, $currentYear + 1);
        return array_combine($years, $years);
    }

    protected static function boot()
    {
        parent::boot();

        static::creating(function (self $model) {
            if (!$model->created_by && auth()->check()) {
                $model->created_by = auth()->id();
            }

            if (!$model->status) {
                $model->status = self::STATUS_DRAFT;
            }
        });

        static::updating(function (self $model) {
            if (auth()->check()) {
                $model->updated_by = auth()->id();
            }
        });
    }
}
