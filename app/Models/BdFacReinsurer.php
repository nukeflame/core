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
        'customer_id',
        'written_share',
        'is_declined',
        'decline_reason'
    ];

    protected $casts = [
        'written_share' => 'decimal:2',
        'is_declined' => 'boolean'
    ];
}
