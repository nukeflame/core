<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class BudgetExpense extends Model
{
    use HasFactory;

    protected $table = 'budget_expenses';
    protected $fillable = [
        'fiscal_year_id',
        'category',
        'subcategory',
        'amount',
        'is_total',
        'created_by',
        'updated_by',
        'company_id'
    ];
    protected $casts = [
        'created_at' => 'datetime',
        'updated_at' => 'datetime',
        'amount' => 'double',
    ];

    public function user()
    {
        return $this->belongsTo(User::class, 'created_by');
    }
}
