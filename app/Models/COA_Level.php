<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class COA_Level extends Model
{
    // use HasFactory;
    // protected $connection = 'mysql'; 
    protected $table='coa_levels';
	public $timestamps=false;
	public $primaryKey=['level_id'];
	public $incrementing=false;
    protected $guarded = [];
}
