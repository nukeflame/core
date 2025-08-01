<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class COA_LevelCateg extends Model
{
    // use HasFactory;
    // protected $connection = 'mysql'; 
    protected $table='coa_level_categories';
	public $timestamps=false;
	public $primaryKey=['level_categ_id '];
	public $incrementing=false;
    protected $guarded = [];
}
