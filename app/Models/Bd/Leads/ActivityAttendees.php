<?php

namespace App\Models\Leads;

use Illuminate\Database\Eloquent\Model;

class ActivityAttendees extends Model
{
    protected $table='activity_attendees';
    public $timestamps=false;
    public $primaryKey= false;
    public $incrementing=false;
    protected $guarded = [];
}
