<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class COA_Config extends Model
{
    // use HasFactory;
    // protected $connection = 'mysql'; 
    protected $table='coa_config';
	public $timestamps=false;
	public $primaryKey=['group_code','account_number'];
	public $incrementing=false;
    protected $guarded = [];
}
