<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Branch extends Model
{
    use HasFactory;
    // protected $connection = 'mysql';
    protected $table = 'branch';
    public $timestamps = false;
    public $primaryKey = 'branch_code';
    public $incrementing = false;
    protected $guarded = [];
}
