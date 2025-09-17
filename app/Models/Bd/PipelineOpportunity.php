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
use Illuminate\Support\Carbon;

class PipelineOpportunity extends Model
{
    use HasFactory;
    protected $table = 'pipeline_opportunities';
    protected $guarded = [];

    // protected $dates = [
    //     'effective_date',
    //     'expiry_date',
    //     'quote_deadline'
    // ];

    // protected $casts = [
    //     'gross_premium' => 'decimal:2',
    //     'expected_premium' => 'decimal:2',
    //     'commission_percentage' => 'decimal:2'
    // ];

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
            'inquiry' => 'Inquiry',
            'quoted' => 'Quoted',
            'negotiation' => 'Under Negotiation',
            'bound' => 'Bound',
            'declined' => 'Declined'
        ];
    }

    public static function getClassOptions(): array
    {
        return [
            'property' => 'Property',
            'casualty' => 'Casualty',
            'marine' => 'Marine',
            'aviation' => 'Aviation',
            'energy' => 'Energy'
        ];
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

    public function getDaysToEffectiveAttribute(): int
    {
        if (!$this->effective_date) return 0;

        return Carbon::now()->diffInDays(Carbon::parse($this->effective_date), false);
    }

    public function getUrgencyLevelAttribute(): string
    {
        $days = $this->expected_closure_date; // $this->days_to_effective;

        if ($days <= 7) return 'critical';
        if ($days <= 14) return 'urgent';
        if ($days <= 30) return 'upcoming';

        return 'normal';
    }
}
