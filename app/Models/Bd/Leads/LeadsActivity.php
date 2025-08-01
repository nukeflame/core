<?php

namespace App\Models\Leads;

use Illuminate\Database\Eloquent\Model;
use App\Models\Leads\ActivityAttendees;

class LeadsActivity extends Model
{
    protected $table='leads_activity';
    public $timestamps=false;
    public $primaryKey= false;
    public $incrementing=false;
    protected $guarded = [];
}
