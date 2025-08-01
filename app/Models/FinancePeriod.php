<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class FinancePeriod extends Model
{
    // use HasFactory;
    // protected $connection = 'mysql'; 
    protected $table='finance_period';
	public $timestamps=false;
	public $primaryKey=['account_year','account_month'];
	public $incrementing=false;
    protected $guarded = [];
}
