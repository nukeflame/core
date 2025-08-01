<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class TypeOfSumInsured extends Model
{
    use HasFactory;
    // protected $connection = 'mysql'; 
    protected $table='type_of_sum_insured';
	public $timestamps=false;
	public $primaryKey=['sum_insured_code'];
	public $incrementing=false;
    protected $guarded = [];
}
