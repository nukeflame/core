<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ReinsDivision extends Model
{
    use HasFactory;
    // protected $connection = 'mysql';
    protected $table = 'reins_division';
    public $timestamps = false;
    public $primaryKey = 'division_code';
    public $incrementing = false;
    protected $guarded = [];
}
