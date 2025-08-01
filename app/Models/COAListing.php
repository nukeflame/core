<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class COAListing extends Model
{
    // use HasFactory;
    // protected $connection = 'mysql'; 
    protected $table='coa_listing';
	public $timestamps=false;
	// public $primaryKey='status_code';
	public $incrementing=false;
    protected $guarded = [];
}
