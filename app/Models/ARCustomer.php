<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ARCustomer extends Model
{
    // use HasFactory;
    // protected $connection = 'mysql'; 
    protected $table='ar_customers';
	public $timestamps=false;
	public $primaryKey=['customer_id'];
	public $incrementing=false;
    protected $guarded = [];
}
