<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class GLMastBal extends Model
{
    // use HasFactory;
    // protected $connection = 'mysql'; 
    protected $table='glmastbal';
	public $timestamps=true;
	public $primaryKey=['account_number','account_year','account_month'];
	public $incrementing=false;
    protected $guarded = [];
}
