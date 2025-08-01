<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class ClaimDocs extends Model
{
    use HasFactory, SoftDeletes;

    protected $table = 'claim_docs';
    protected $guarded = [];

    protected $casts = [
        'file_base64' => 'string'
    ];
}
