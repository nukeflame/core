<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class ScheduleHeader extends Model
{
    use HasFactory;

    protected $table = 'schedule_headers';
    public $timestamps = true;
    public $primaryKey = 'id';
    public $incrementing = false;
    protected $guarded = [];

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
