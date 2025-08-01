<?php

namespace Database\Seeders;

use App\Models\BudgetExpense;
use App\Models\BudgetIncome;
use App\Models\FiscalYear;
use Illuminate\Database\Seeder;

class FinancialDataSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $financialData = [
            '2025' => [
                'incomes' => [
                    ['category' => 'New Business', 'subcategory' => 'Facultative (Offers & Quotations)', 'amount' => 83782130],
                    ['category' => 'New Business', 'subcategory' => 'Special Lines', 'amount' => 20455994],
                    ['category' => 'New Business', 'subcategory' => 'Treaties', 'amount' => 3359483],
                    ['category' => 'New Business', 'subcategory' => 'International Markets (Total)', 'amount' => 10073220],
                    ['category' => 'New Business', 'subcategory' => 'Total - New Business', 'amount' => 117670827, 'isTotal' => true],
                    ['category' => 'Renewal Business', 'subcategory' => 'Facultative', 'amount' => 66340572],
                    ['category' => 'Renewal Business', 'subcategory' => 'Special Lines', 'amount' => 14273565],
                    ['category' => 'Renewal Business', 'subcategory' => 'Treaties', 'amount' => 5119383],
                    ['category' => 'Renewal Business', 'subcategory' => 'Market Expansion (Total)', 'amount' => 5132352],
                    ['category' => 'Renewal Business', 'subcategory' => 'Total - Renewal Business', 'amount' => 90865872, 'isTotal' => true],
                    ['category' => 'Other Income', 'subcategory' => 'Interest from Investment', 'amount' => 13000000],
                    ['category' => 'Total', 'subcategory' => 'Total Budgeted Income', 'amount' => 221536698, 'isTotal' => true],
                ],
                'expenses' => [
                    ['category' => 'HR', 'subcategory' => 'HR Cost Reinsurance', 'amount' => 42050131],
                    ['category' => 'HR', 'subcategory' => 'Training Costs', 'amount' => 2496543],
                    ['category' => 'Marketing', 'subcategory' => 'Marketing Expenses', 'amount' => 28696102],
                    ['category' => 'Marketing', 'subcategory' => 'Branding Costs', 'amount' => 4010000],
                    ['category' => 'Expansion', 'subcategory' => 'Expansion Cost', 'amount' => 4500000],
                    ['category' => 'Operations', 'subcategory' => 'Office and Administration', 'amount' => 12775337],
                    ['category' => 'Operations', 'subcategory' => 'IT Costs', 'amount' => 5374690],
                    ['category' => 'Operations', 'subcategory' => 'Finance', 'amount' => 4178486],
                    ['category' => 'Operations', 'subcategory' => 'Miscellaneous Costs', 'amount' => 2750000],
                    ['category' => 'Total', 'subcategory' => 'Total Expenses', 'amount' => 106831290, 'isTotal' => true],
                ],
                'summary' => [
                    'grossProfit' => 114705409,
                    'costIncomeRatio' => 48.22,
                    'profitMargin' => 51.78,
                ]
            ],
            '2024' => [
                'incomes' => [
                    ['category' => 'New Business', 'subcategory' => 'Facultative (Offers & Quotations)', 'amount' => 76893405],
                    ['category' => 'New Business', 'subcategory' => 'Special Lines', 'amount' => 17654299],
                    ['category' => 'New Business', 'subcategory' => 'Treaties', 'amount' => 2987654],
                    ['category' => 'New Business', 'subcategory' => 'International Markets (Total)', 'amount' => 9254621],
                    ['category' => 'New Business', 'subcategory' => 'Total - New Business', 'amount' => 106789979, 'isTotal' => true],
                    ['category' => 'Renewal Business', 'subcategory' => 'Facultative', 'amount' => 62134521],
                    ['category' => 'Renewal Business', 'subcategory' => 'Special Lines', 'amount' => 13245687],
                    ['category' => 'Renewal Business', 'subcategory' => 'Treaties', 'amount' => 4789532],
                    ['category' => 'Renewal Business', 'subcategory' => 'Market Expansion (Total)', 'amount' => 4765432],
                    ['category' => 'Renewal Business', 'subcategory' => 'Total - Renewal Business', 'amount' => 84935172, 'isTotal' => true],
                    ['category' => 'Other Income', 'subcategory' => 'Interest from Investment', 'amount' => 11500000],
                    ['category' => 'Total', 'subcategory' => 'Total Budgeted Income', 'amount' => 203225151, 'isTotal' => true],
                ],
                'expenses' => [
                    ['category' => 'HR', 'subcategory' => 'HR Cost Reinsurance', 'amount' => 39654321],
                    ['category' => 'HR', 'subcategory' => 'Training Costs', 'amount' => 2345678],
                    ['category' => 'Marketing', 'subcategory' => 'Marketing Expenses', 'amount' => 25436789],
                    ['category' => 'Marketing', 'subcategory' => 'Branding Costs', 'amount' => 3876500],
                    ['category' => 'Expansion', 'subcategory' => 'Expansion Cost', 'amount' => 4123456],
                    ['category' => 'Operations', 'subcategory' => 'Office and Administration', 'amount' => 11987654],
                    ['category' => 'Operations', 'subcategory' => 'IT Costs', 'amount' => 4987654],
                    ['category' => 'Operations', 'subcategory' => 'Finance', 'amount' => 3876543],
                    ['category' => 'Operations', 'subcategory' => 'Miscellaneous Costs', 'amount' => 2543210],
                    ['category' => 'Total', 'subcategory' => 'Total Expenses', 'amount' => 98831805, 'isTotal' => true],
                ],
                'summary' => [
                    'grossProfit' => 104393346,
                    'costIncomeRatio' => 48.63,
                    'profitMargin' => 51.37
                ]
            ]
        ];

        foreach ($financialData as $year => $data) {
            $financialYear = FiscalYear::create([
                'name' => 'Budget ' . $year,
                'year' => $year,
                'start_date' => now()->startOfYear(),
                'end_date' => now()->endOfYear(),
                'is_current' => $year == now()->year,
                'is_closed' => false,
                'gross_profit' => $data['summary']['grossProfit'],
                'cost_income_ratio' => $data['summary']['costIncomeRatio'],
                'profit_margin' => $data['summary']['profitMargin'],
            ]);

            foreach ($data['incomes'] as $income) {
                BudgetIncome::create([
                    'fiscal_year_id' => $financialYear->id,
                    'category' => $income['category'],
                    'subcategory' => $income['subcategory'],
                    'amount' => $income['amount'],
                    'is_total' => isset($income['isTotal']) ? $income['isTotal'] : false,
                ]);
            }

            foreach ($data['expenses'] as $expense) {
                BudgetExpense::create([
                    'fiscal_year_id' => $financialYear->id,
                    'category' => $expense['category'],
                    'subcategory' => $expense['subcategory'],
                    'amount' => $expense['amount'],
                    'is_total' => isset($expense['isTotal']) ? $expense['isTotal'] : false,
                ]);
            }
        }
    }
}
