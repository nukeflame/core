<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class ScheduleHeader extends Model
{
    use HasFactory;

    protected $table = 'schedule_headers';
    protected $primaryKey = 'id';
    protected $keyType = 'int';
    public $incrementing = false;
    public $timestamps = true;

    protected $fillable = [
        'name',
        'position',
        'amount_field',
        'sum_insured_type',
        'data_determinant',
        'class',
        'class_group',
        'business_type',
        'opportunity_id',
        'schedule_header_id'
    ];

    protected $casts = [
        'id' => 'integer',
        'position' => 'integer',
        'schedule_header_id' => 'integer',
    ];

    /**
     * Get all of the schedules for the ScheduleHeader
     *
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function schedules(): HasMany
    {
        return $this->hasMany(CoverRisk::class, 'header', 'id');
    }
}
