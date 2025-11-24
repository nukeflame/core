<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasOne;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Support\Facades\Auth;


class ApprovalsTracker extends Model
{
    use HasFactory, SoftDeletes;

    protected $table = 'approvals_tracker';

    protected $primaryKey = 'id';

    public $incrementing = false;

    protected $keyType = 'int';

    const STATUS_PENDING = 'P';
    const STATUS_APPROVED = 'A';
    const STATUS_REJECTED = 'R';

    const PRIORITY_CRITICAL = 'critical';
    const PRIORITY_HIGH = 'high';
    const PRIORITY_MEDIUM = 'medium';
    const PRIORITY_LOW = 'low';

    protected $fillable = [
        'id',
        'process_id',
        'process_action',
        'approver',
        'comment',
        'approver_comment',
        'priority',
        'status',
        'data',
        'created_by',
        'updated_by',
        'actioned_at',
    ];


    protected $hidden = [];

    protected $casts = [
        'id' => 'integer',
        'process_id' => 'integer',
        'process_action' => 'integer',
        'approver' => 'integer',
        'data' => 'array',
        'created_at' => 'datetime',
        'updated_at' => 'datetime',
        'actioned_at' => 'datetime',
        'deleted_at' => 'datetime',
    ];

    protected $dates = [
        'created_at',
        'updated_at',
        'actioned_at',
        'deleted_at',
    ];

    protected $appends = [
        'status_label',
        'priority_label',
        'is_pending',
        'is_approved',
        'is_rejected',
        'age_in_hours',
        'is_overdue',
    ];

    protected static function boot()
    {
        parent::boot();

        static::creating(function ($model) {
            if (Auth::check()) {
                $user = Auth::user();
                $model->created_by = $model->created_by ?? ($user->user_name ?? $user->name);
                $model->updated_by = $model->updated_by ?? ($user->user_name ?? $user->name);
            }
        });

        static::updating(function ($model) {
            if (Auth::check()) {
                $user = Auth::user();
                $model->updated_by = $user->user_name ?? $user->name;
            }
        });
    }

    public function notification(): HasOne
    {
        return $this->hasOne(Notification::class, 'approval_tracker_id', 'id');
    }

    public function source(): HasOne
    {
        return $this->hasOne(ApprovalSourceLink::class, 'approval_id', 'id');
    }

    public function sourceLinks(): HasMany
    {
        return $this->hasMany(ApprovalSourceLink::class, 'approval_id', 'id');
    }

    public function processAction(): BelongsTo
    {
        return $this->belongsTo(SystemProcessAction::class, 'process_action', 'id');
    }

    public function process(): BelongsTo
    {
        return $this->belongsTo(SystemProcess::class, 'process_id', 'id');
    }

    public function approverUser(): BelongsTo
    {
        return $this->belongsTo(User::class, 'approver', 'id');
    }

    public function creator(): BelongsTo
    {
        return $this->belongsTo(User::class, 'created_by', 'user_name');
    }

    public function updater(): BelongsTo
    {
        return $this->belongsTo(User::class, 'updated_by', 'user_name');
    }

    public function scopePending($query)
    {
        return $query->where('status', self::STATUS_PENDING);
    }

    public function scopeApproved($query)
    {
        return $query->where('status', self::STATUS_APPROVED);
    }

    public function scopeRejected($query)
    {
        return $query->where('status', self::STATUS_REJECTED);
    }

    public function scopeForApprover($query, $approverId)
    {
        return $query->where('approver', $approverId);
    }

    public function scopeByPriority($query, $priority)
    {
        return $query->where('priority', $priority);
    }

    public function scopeCritical($query)
    {
        return $query->where('priority', self::PRIORITY_CRITICAL);
    }

    public function scopeHighPriority($query)
    {
        return $query->where('priority', self::PRIORITY_HIGH);
    }

    public function scopeOlderThan($query, $hours)
    {
        return $query->where('created_at', '<', now()->subHours($hours));
    }

    public function scopeOverdue($query)
    {
        $sla = config('approvals.sla', [
            'critical' => 4,
            'high' => 24,
            'medium' => 48,
            'low' => 72,
        ]);

        return $query->where(function ($q) use ($sla) {
            foreach ($sla as $priority => $hours) {
                $q->orWhere(function ($subQuery) use ($priority, $hours) {
                    $subQuery->where('priority', $priority)
                        ->where('status', self::STATUS_PENDING)
                        ->where('created_at', '<', now()->subHours($hours));
                });
            }
        });
    }

    public function scopeByNotificationType($query, $type)
    {
        return $query->whereHas('notification', function ($q) use ($type) {
            $q->where('notification_type', $type);
        });
    }

    public function getStatusLabelAttribute(): string
    {
        return match ($this->status) {
            self::STATUS_PENDING => 'Pending',
            self::STATUS_APPROVED => 'Approved',
            self::STATUS_REJECTED => 'Rejected',
            default => 'Unknown',
        };
    }

    public function getPriorityLabelAttribute(): string
    {
        return ucfirst($this->priority);
    }

    public function getIsPendingAttribute(): bool
    {
        return $this->status === self::STATUS_PENDING;
    }

    public function getIsApprovedAttribute(): bool
    {
        return $this->status === self::STATUS_APPROVED;
    }

    public function getIsRejectedAttribute(): bool
    {
        return $this->status === self::STATUS_REJECTED;
    }

    public function getAgeInHoursAttribute(): int
    {
        return (int) now()->diffInHours($this->created_at);
    }

    public function getIsOverdueAttribute(): bool
    {
        if ($this->status !== self::STATUS_PENDING) {
            return false;
        }

        $sla = config('approvals.sla', [
            'critical' => 4,
            'high' => 24,
            'medium' => 48,
            'low' => 72,
        ]);

        $slaHours = $sla[$this->priority] ?? 72;

        return $this->age_in_hours > $slaHours;
    }

    public function getDataAttribute($value): array
    {
        if (is_array($value)) {
            return $value;
        }

        if (is_string($value)) {
            return json_decode($value, true) ?? [];
        }

        return [];
    }

    public function setDataAttribute($value): void
    {
        if (is_array($value)) {
            $this->attributes['data'] = json_encode($value);
        } else {
            $this->attributes['data'] = $value;
        }
    }

    public function canBeActionedBy(?int $userId = null): bool
    {
        $userId = $userId ?? Auth::id();

        return $this->status === self::STATUS_PENDING
            && $this->approver === $userId;
    }

    public function approve(string $comment, ?int $approverId = null): bool
    {
        $approverId = $approverId ?? Auth::id();

        if (!$this->canBeActionedBy($approverId)) {
            return false;
        }

        $this->status = self::STATUS_APPROVED;
        $this->approver_comment = $comment;
        $this->actioned_at = now();

        return $this->save();
    }

    public function reject(string $comment, ?int $approverId = null): bool
    {
        $approverId = $approverId ?? Auth::id();

        if (!$this->canBeActionedBy($approverId)) {
            return false;
        }

        $this->status = self::STATUS_REJECTED;
        $this->approver_comment = $comment;
        $this->actioned_at = now();

        return $this->save();
    }

    public function getApprovalType(): ?string
    {
        return $this->data['type'] ?? null;
    }

    public function getAmount(): float
    {
        if (isset($this->data['amount'])) {
            return (float) $this->data['amount'];
        }

        if ($this->notification) {
            return (float) ($this->notification->amount ?? 0);
        }

        return 0.0;
    }

    public function getClientName(): ?string
    {
        if (isset($this->data['customer'])) {
            return $this->data['customer'];
        }

        if ($this->notification) {
            return $this->notification->client;
        }

        return null;
    }

    public function getRiskLevel(): string
    {
        $amount = $this->getAmount();
        $thresholds = config('approvals.risk_thresholds', [
            'high' => 10000000,  // 10M
            'medium' => 5000000, // 5M
        ]);

        if ($amount >= $thresholds['high']) {
            return 'high';
        } elseif ($amount >= $thresholds['medium']) {
            return 'medium';
        } else {
            return 'low';
        }
    }

    public function getSlaDeadline(): \Illuminate\Support\Carbon
    {
        $sla = config('approvals.sla', [
            'critical' => 4,
            'high' => 24,
            'medium' => 48,
            'low' => 72,
        ]);

        $slaHours = $sla[$this->priority] ?? 72;

        return $this->created_at->addHours($slaHours);
    }

    public function getTimeRemaining(): string
    {
        if ($this->status !== self::STATUS_PENDING) {
            return 'N/A';
        }

        $deadline = $this->getSlaDeadline();

        if (now()->gt($deadline)) {
            return 'Overdue';
        }

        return now()->diffForHumans($deadline, true);
    }

    public function matches(array $criteria): bool
    {
        foreach ($criteria as $key => $value) {
            if ($this->$key !== $value) {
                return false;
            }
        }

        return true;
    }

    public function getSummary(): string
    {
        $type = $this->getApprovalType();
        $amount = number_format($this->getAmount(), 2);
        $client = $this->getClientName() ?? 'N/A';

        return "{$type} approval for {$client} - KES {$amount}";
    }

    public static function getStatuses(): array
    {
        return [
            self::STATUS_PENDING => 'Pending',
            self::STATUS_APPROVED => 'Approved',
            self::STATUS_REJECTED => 'Rejected',
        ];
    }

    public static function getPriorities(): array
    {
        return [
            self::PRIORITY_CRITICAL => 'Critical',
            self::PRIORITY_HIGH => 'High',
            self::PRIORITY_MEDIUM => 'Medium',
            self::PRIORITY_LOW => 'Low',
        ];
    }

    public static function getStatistics(?int $userId = null): array
    {
        $userId = $userId ?? Auth::id();

        return [
            'total' => self::forApprover($userId)->count(),
            'pending' => self::forApprover($userId)->pending()->count(),
            'approved' => self::forApprover($userId)->approved()->count(),
            'rejected' => self::forApprover($userId)->rejected()->count(),
            'overdue' => self::forApprover($userId)->overdue()->count(),
            'critical' => self::forApprover($userId)->critical()->pending()->count(),
        ];
    }

    public static function withFullDetails()
    {
        return self::with([
            'notification',
            'source',
            'sourceLinks',
            'processAction',
            'process',
            'approverUser:id,name,user_name,email',
            'creator:id,name,user_name',
        ]);
    }

    public static function myPending()
    {
        return self::forApprover(Auth::id())
            ->pending()
            ->orderBy('priority')
            ->orderBy('created_at')
            ->get();
    }

    public static function myOverdue()
    {
        return self::forApprover(Auth::id())
            ->overdue()
            ->orderBy('created_at')
            ->get();
    }
}
