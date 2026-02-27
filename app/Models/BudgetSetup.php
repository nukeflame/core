<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class BudgetSetup extends Model
{
    use HasFactory, SoftDeletes;

    protected $table = 'budget_setups';

    protected $fillable = [
        'budget_year',
        'budget_category',
        'status',
        'created_by',
        'updated_by',
    ];

    protected $casts = [
        'budget_year' => 'integer',
        'created_at' => 'datetime',
        'updated_at' => 'datetime',
    ];

    public function creator()
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    public function updater()
    {
        return $this->belongsTo(User::class, 'updated_by');
    }
}
