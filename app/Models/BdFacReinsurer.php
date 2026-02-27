<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class BdFacReinsurer extends Model
{
    use HasFactory;

    protected $table = 'bd_fac_reinsurers';

    protected $fillable = [
        'opportunity_id',
        'reinsurer_id',
        'written_share',
        'updated_written_share',
        'signed_share',
        'updated_signed_share',
        'is_declined',
        'decline_reason'
    ];

    protected $casts = [
        'written_share' => 'decimal:2',
        'updated_written_share' => 'decimal:2',
        'signed_share' => 'decimal:2',
        'updated_signed_share' => 'decimal:2',
        'is_declined' => 'boolean'
    ];
}
