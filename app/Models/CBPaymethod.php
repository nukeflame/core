<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class CBPaymethod extends Model
{
    // use HasFactory;
    // protected $connection = 'mysql'; 
    protected $table='cbpay_methods';
	public $timestamps=false;
	public $primaryKey='pay_method_code';
	public $incrementing=false;
    protected $guarded = [];
}
