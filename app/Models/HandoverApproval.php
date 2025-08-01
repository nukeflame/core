<?php

namespace App\Models;

use App\Models\Bd\PipelineOpportunity;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class HandoverApproval extends Model
{
    use HasFactory;
    protected $table = 'handover_approvals';
    protected $fillable = [
        'approval_status',
        'approval_comment',
        'intergrate',
        'reason_for_rejection'
    ];

    // public function prospect()
    // {
    //     return $this->belongsTo(PipelineOpportunity::class, 'prospect_id', 'opportunity_id');
    // }
}
