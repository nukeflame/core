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
        return Carbon::now()->diffInSeconds($this->stage_updated_at );
    }
}
