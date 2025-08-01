<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ClaimPeril extends Model
{
    // use HasFactory;
    // protected $connection = 'mysql';    //devgbdata
    protected $table = 'claim_perils';
    public $timestamps = true;
    public $primaryKey = ['claim_no', 'tran_no'];
    public $incrementing = false;
    protected $guarded = [];
}
