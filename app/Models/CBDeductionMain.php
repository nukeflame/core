<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class CBDeductionMain extends Model
{
    use HasFactory;
    // protected $connection = 'mysql'; 
    protected $table = 'cbdeductions';
    public $timestamps = false;
    // public $primaryKey = ['reference_no','cb_source_code','deduction_code'];
    public $primaryKey = 'reference_no';
    public $incrementing = false;
    protected $guarded = [];
}
