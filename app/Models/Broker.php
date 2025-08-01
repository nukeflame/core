<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Broker extends Model
{
    use HasFactory;
    // protected $connection = 'mysql'; 
    protected $table='brokers';
	public $timestamps=false;
	public $primaryKey=['broker_code'];
	public $incrementing=false;
    protected $guarded = [];
}
