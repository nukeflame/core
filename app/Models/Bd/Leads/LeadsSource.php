<?php

namespace App\Models\Bd\Leads;

use Illuminate\Database\Eloquent\Model;

class LeadsSource extends Model
{
    protected $table='leads_source';
    public $timestamps=false;
    public $primaryKey= false;
    public $incrementing=false;
    protected $guarded = [];
}
