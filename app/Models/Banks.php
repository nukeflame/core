<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Banks extends Model
{
    use HasFactory;
    // protected $connection = 'mysql'; 
    protected $table = 'banks';
    public $timestamps = false;
    // public $primaryKey = ['bank_code'];
    public $primaryKey = 'bank_code';
    public $incrementing = false;
    protected $guarded = [];
}
