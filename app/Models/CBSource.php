<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class CBSource extends Model
{
    // use HasFactory;
    // protected $connection = 'mysql'; 
    protected $table='cbsources';
	public $timestamps=false;
	public $primaryKey='source_code';
	public $incrementing=false;
    protected $guarded = [];
}
