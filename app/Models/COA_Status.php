<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class COA_Status extends Model
{
    // use HasFactory;
    // protected $connection = 'mysql'; 
    protected $table='coa_status';
	public $timestamps=false;
	public $primaryKey='status_code';
	public $incrementing=false;
    protected $guarded = [];
}
