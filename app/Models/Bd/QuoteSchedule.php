<?php

namespace App\Models\Bd;

use App\Models\ScheduleHeader;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class QuoteSchedule extends Model
{
    use HasFactory;
    protected $table = 'quote_schedules';
    protected $primaryKey = 'id';
    protected $fillable = [
        'schedule_id',
        'name',
        'details',
    ];
    // public function schedules()
    // // {
    //     return $this->belongsTo(ScheduleHeader::class, 'schedule_id', 'id');
    // }
    // public function quotes()
    // {
    //     return $this->belongsTo(Quote::class,'quote_id','id');
    // }

}
