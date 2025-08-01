<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class StaffNotice extends Model
{
    use HasFactory;

    protected $fillable = [
        'notice',
        'description',
        'type',
        'effective_from',
        'expired_at',
        'issued_by',
        'priority'
    ];

    protected $casts = [
        'effective_from' => 'datetime',
        'expired_at' => 'datetime',
    ];
}
