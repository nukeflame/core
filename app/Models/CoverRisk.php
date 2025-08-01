<?php

namespace App\Models;

use App\Http\Traits\ModelCompositeKey;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class CoverRisk extends Model
{
    use HasFactory, SoftDeletes, ModelCompositeKey;

    protected $table = 'cover_risk';
    public $timestamps = true;
    public $primaryKey = ['endorsement_no', 'id'];
    public $incrementing = false;
    protected $guarded = [];

    protected $casts = [
        // 'sum_insured' => 'float',
        // 'premium' => 'float',
    ];

    /**
     * Get the scheduler_header that owns the CoverRisk
     *
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function schedule_header(): BelongsTo
    {
        return $this->belongsTo(ScheduleHeader::class, 'header', 'id');
    }
}
