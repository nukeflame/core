<?php

namespace App\Models\Bd;

use App\Models\BusinessType;
use App\Models\Classes;
use App\Models\Customer;
use App\Models\Bd\Leads\LeadStatus;
use App\Models\HandoverApproval;
use App\Models\ReinsDivision;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Support\Carbon;

class PipelineOpportunity extends Model
{
    use HasFactory, SoftDeletes;

    protected $table = 'pipeline_opportunities';

    protected $fillable = [
        'opportunity_id',
        'customer_id',
        'insured_name',
        'client_category',
        'contact_name',
        'email',
        'phone',
        'telephone',
        'type_of_bus',
        'classcode',
        'divisions',
        'cede_premium',
        'comm_rate',
        'expected_premium',
        'gross_premium',
        'stage',
        'probability',
        'priority',
        'status',
        'stage_updated_at',
        'next_action',
        'expected_closure_date',
        'effective_date',
        'closing_date',
        'expiry_date',
        'fac_date_offered',
        'quote_deadline',
        'prequalification',
        'pq_status',
        'pq_comments',
        'pipeline_id',
        'lead_owner',
        'pip_year',
        'description',
        'territory_id',
        'account_executive',
        'cr_processed',
        'created_by',
        'updated_by',
    ];

    protected $casts = [
        'cede_premium' => 'decimal:2',
        'expected_premium' => 'decimal:2',
        'gross_premium' => 'decimal:2',
        'comm_rate' => 'decimal:2',
        'stage' => 'integer',
        'probability' => 'integer',
        'effective_date' => 'date',
        'expiry_date' => 'date',
        'closing_date' => 'date',
        'expected_closure_date' => 'date',
        'quote_deadline' => 'date',
        'fac_date_offered' => 'date',
        'stage_updated_at' => 'datetime',
        'contact_name' => 'array',
        'email' => 'array',
        'phone' => 'array',
        'telephone' => 'array',
    ];

    protected $dates = [
        'effective_date',
        'expiry_date',
        'closing_date',
        'quote_deadline',
        'expected_closure_date',
        'fac_date_offered',
        'stage_updated_at',
        'created_at',
        'updated_at',
        'deleted_at',
    ];

    public function customer()
    {
        return $this->belongsTo(Customer::class, 'customer_id', 'customer_id');
    }

    public function leadStatus()
    {
        return $this->belongsTo(LeadStatus::class, 'stage', 'id');
    }

    public function reinsDivision()
    {
        return $this->belongsTo(ReinsDivision::class);
    }

    public function class()
    {
        return $this->belongsTo(Classes::class, 'classcode', 'class_code');
    }

    public function businessType()
    {
        return $this->belongsTo(BusinessType::class, 'type_of_bus', 'bus_type_id');
    }

    public function approval()
    {
        return $this->hasOne(HandoverApproval::class);
    }

    public function handovers()
    {
        return $this->hasMany(HandoverApproval::class, 'prospect_id', 'opportunity_id');
    }

    public function stageTransitions()
    {
        return $this->hasMany(StageTransition::class, 'opportunity_id');
    }

    public function getCurrentStageDurationAttribute()
    {
        return Carbon::now()->diffInSeconds($this->stage_updated_at);
    }

    public static function getStatusOptions(): array
    {
        return [
            'lead' => 'Lead',
            'proposal' => 'Proposal',
            'negotiation' => 'Negotiation',
            'won' => 'Won',
            'lost' => 'Lost',
            'final' => 'Final Stage',
        ];
    }

    public static function getClassOptions(): array
    {
        // return DB::table('classes')
        //     ->pluck('class_name', 'class_code')
        //     ->toArray();
        return [];
    }

    public static function getClassGroupsOptions(): array
    {
        // return DB::table('class_groups')
        //     ->pluck('group_name', 'group_code')
        //     ->toArray();
        return [];
    }

    public static function getPriorityOptions(): array
    {
        return [
            'critical' => 'Critical',
            'high' => 'High',
            'medium' => 'Medium',
            'low' => 'Low'
        ];
    }

    public static function getStageOptions(): array
    {
        return [
            1 => 'Qualification',
            2 => 'Proposal',
            3 => 'Due Diligence',
            4 => 'Negotiation',
            5 => 'Approval'
        ];
    }

    public static function getTreatyTypeOptions(): array
    {
        return [
            'TPR' => 'Treaty Proportional',
            'TNP' => 'Treaty Non-Proportional',
            'FPR' => 'Facultative Proportional',
            'FNP' => 'Facultative Non-Proportional',
        ];
    }

    public function getDaysToEffectiveAttribute(): int
    {
        if (!$this->effective_date) return 0;

        return Carbon::now()->diffInDays(Carbon::parse($this->effective_date), false);
    }

    public function getUrgencyLevelAttribute(): string
    {
        $days = $this->days_to_effective;

        if ($days <= 7) return 'critical';
        if ($days <= 14) return 'urgent';
        if ($days <= 30) return 'upcoming';

        return 'normal';
    }

    public function getWeightedValueAttribute(): float
    {
        if (!$this->cede_premium || !$this->probability) {
            return 0.0;
        }

        return ($this->probability / 100) * $this->cede_premium;
    }

    public function getStageName(): string
    {
        $stages = self::getStageOptions();
        return $stages[$this->stage] ?? 'Unknown';
    }

    public function getTreatyTypeName(): string
    {
        $types = self::getTreatyTypeOptions();
        return $types[$this->type_of_bus] ?? $this->type_of_bus;
    }

    public function getPriorityBadgeClass(): string
    {
        return match (strtolower($this->priority ?? 'medium')) {
            'critical', 'high' => 'priority-high',
            'low' => 'priority-low',
            default => 'priority-medium'
        };
    }

    public function getStageBadgeClass(): string
    {
        return match ($this->stage) {
            1 => 'qualification',
            2 => 'proposal',
            3 => 'due-diligence',
            4 => 'negotiation',
            5 => 'approval',
            default => 'qualification'
        };
    }

    public function isActive(): bool
    {
        return in_array(strtolower($this->status ?? 'active'), ['active', 'pending']);
    }

    public function isClosed(): bool
    {
        return strtolower($this->status ?? '') === 'closed';
    }

    public function isSubmittedToSales(): bool
    {
        return !is_null($this->pipeline_id);
    }

    public function scopeTreaty($query)
    {
        return $query->whereIn('type_of_bus', ['TPR', 'TNP']);
    }

    public function scopeFacultative($query)
    {
        return $query->whereIn('type_of_bus', ['FPR', 'FNP']);
    }

    public function scopeByStage($query, int $stage)
    {
        return $query->where('stage', $stage);
    }

    public function scopeByPriority($query, string $priority)
    {
        return $query->where('priority', $priority);
    }

    public function scopeActive($query)
    {
        return $query->whereIn('status', ['active', 'pending'])
            ->whereNull('pipeline_id');
    }

    public function scopeSubmitted($query)
    {
        return $query->whereNotNull('pipeline_id');
    }
}
