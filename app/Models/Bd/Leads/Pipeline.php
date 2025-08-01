<?php

namespace App\Models\Bd\Leads;

use Illuminate\Database\Eloquent\Model;

class Pipeline extends Model
{
    protected $table='pipelines';
    public $timestamps=false;
    public $primaryKey= "id";
    public $incrementing=true;
    protected $guarded = [];

}
