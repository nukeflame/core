<?php

namespace App\Models\Bd\Leads;

use Illuminate\Database\Eloquent\Model;

class LeadStatus extends Model
{
    protected $table='lead_status';
    public $timestamps=false;
    public $primaryKey= 'lead_id';
    public $incrementing=true;
    protected $guarded = [];
}
