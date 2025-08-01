<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class BudgetAllocation extends Model
{
    use HasFactory, SoftDeletes;

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'period_year',
        'period_month',
        'total_income_budget',
        'total_expense_budget',
        'status',
        'created_by',
        'updated_by'
    ];

    protected $casts = [
        'total_income_budget' => 'float',
        'total_expense_budget' => 'float',
    ];

    public function getFormattedPeriodAttribute(): string
    {
        return $this->period_year . '/' . str_pad($this->period_month, 2, '0', STR_PAD_LEFT);
    }

    public static function getActiveBudget(string $period): ?BudgetAllocation
    {
        list($year, $month) = explode('/', $period);

        return self::where('period_year', $year)
            ->where('period_month', $month)
            ->where('status', 'Active')
            ->first();
    }
}
