<?php

namespace App\Models\Bd;

use Illuminate\Database\Eloquent\Model;

class Intermediary extends Model
{
    protected $table='intermediaries';
    public $timestamps=false;
    public $primaryKey= false;
    public $incrementing=false;
    protected $guarded = [];
}
