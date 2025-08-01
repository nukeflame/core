<?php

namespace App\Models\Leads;

use Illuminate\Database\Eloquent\Model;

class Industry extends Model
{
    protected $table='industries';
    public $timestamps=false;
    public $primaryKey= false;
    public $incrementing=false;
    protected $guarded = [];
}
