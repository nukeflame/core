<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ClaimReinNote extends Model
{
    use HasFactory;

    protected $table = 'claim_rein_notes';
    public $timestamps = true;
    // public $primaryKey = ['claim_no', 'tran_no'];
    // public $incrementing = false;
    protected $guarded = [];
}
