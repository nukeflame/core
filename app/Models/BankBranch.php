<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class BankBranch extends Model
{
    use HasFactory;
    // protected $connection = 'mysql'; 
    protected $table = 'bank_branches';
    public $timestamps = true;
    public $primaryKey = ['bank_code', 'bank_branch_code'];
    public $incrementing = false;
    protected $guarded = [];
}
