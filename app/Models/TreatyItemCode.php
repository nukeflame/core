<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class TreatyItemCode extends Model
{
    use HasFactory;

    protected $fillable = [
        'item_code',
        'description',
        'item_type',
        'is_active',
        'sort_order',
    ];

    protected $casts = [
        'is_active' => 'boolean',
        'sort_order' => 'integer',
    ];

    public function scopeActive($query)
    {
        return $query->where('is_active', true);
    }

    public function scopeDebit($query)
    {
        return $query->where('item_type', 'DEBIT');
    }

    public function scopeCredit($query)
    {
        return $query->where('item_type', 'CREDIT');
    }

    public function scopeOrdered($query)
    {
        return $query->orderBy('sort_order');
    }
}
