<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class CBDeductions extends Model
{
    use HasFactory;
    // protected $connection = 'mysql'; 
    protected $table = 'cbdeductions_param';
    public $timestamps = false;
    // public $primaryKey = ['doc_type', 'deduction_code'];
    public $primaryKey = 'deduction_code';
    public $incrementing = false;
    protected $guarded = [];
}
