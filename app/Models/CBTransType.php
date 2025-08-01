<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class CBTransType extends Model
{
    // use HasFactory;
    // protected $connection = 'mysql'; 
    protected $table='cbtrans_types';
	public $timestamps=false;
	public $primaryKey=['doc_type','type_code','source_code'];
	// public $primaryKey='type_code';
	public $incrementing=false;
    protected $guarded = [];
}
