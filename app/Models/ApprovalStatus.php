<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ApprovalStatus extends Model
{
    use HasFactory;
    
    // protected $connection = 'mysql'; 
    protected $table='approval_status';
	public $timestamps=true;
	public $primaryKey='id';
	public $incrementing=false;
    protected $guarded = [];
}
