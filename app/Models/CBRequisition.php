<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class CBRequisition extends Model
{
    use HasFactory;
    // protected $connection = 'mysql'; 
    protected $table = 'cbrequisitions';
    public $timestamps = false;
    public $primaryKey = ['requisition_no'];
    public $incrementing = false;
    protected $guarded = [];
}
