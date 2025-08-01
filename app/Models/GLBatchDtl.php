<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class GLBatchDtl extends Model
{
    // use HasFactory;
    // protected $connection = 'mysql'; 
    protected $table='glbatchdtl';
	public $timestamps=true;
	public $primaryKey=['batch_no','item_no'];
	public $incrementing=false;
    protected $guarded = [];
}
