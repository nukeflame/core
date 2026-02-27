<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class UserBudgetSetup extends Model
{
    use HasFactory, SoftDeletes;

    protected $table = 'user_budget_setups';

    protected $fillable = [
        'user_id',
        'budget_setup_id',
        'est_production',
        'return_on_investment',
        'sectors',
        'policies',
        'categories',
        'status',
        'created_by',
        'updated_by',
    ];

    protected $casts = [
        'est_production' => 'decimal:2',
        'return_on_investment' => 'decimal:2',
        'sectors' => 'array',
        'policies' => 'array',
        'categories' => 'array',
        'created_at' => 'datetime',
        'updated_at' => 'datetime',
    ];

    public function user()
    {
        return $this->belongsTo(User::class, 'user_id');
    }

    public function budgetSetup()
    {
        return $this->belongsTo(BudgetSetup::class, 'budget_setup_id');
    }

    public function creator()
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    public function updater()
    {
        return $this->belongsTo(User::class, 'updated_by');
    }
}
