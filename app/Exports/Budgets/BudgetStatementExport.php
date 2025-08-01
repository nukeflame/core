<?php

namespace App\Exports\Budgets;

use Maatwebsite\Excel\Concerns\WithMultipleSheets;

class BudgetStatementExport implements WithMultipleSheets
{
    public function sheets(): array
    {
        return [
            new SummaryBudgetStatementExport(),
            new IncomeBudgetStatementExport(),
            new ExpenseBudgetStatementExport(),
        ];
    }
}
