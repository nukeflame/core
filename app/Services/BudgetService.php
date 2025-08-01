<?php

namespace App\Services;

use App\Models\Budget;
use App\Models\BudgetAllocation;
use App\Models\BudgetIncome;
use App\Models\FiscalYear;
use App\Models\PerformanceRecord;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class BudgetService
{
    /**
     * Check if the new performance record exceeds budget constraints
     *
     * @param string $period
     * @param array $data
     * @param int $staffCount
     * @return array
     */
    public function checkBudgetConstraint(string $period, array $data, int $staffCount): array
    {
        $budget = BudgetAllocation::getActiveBudget($period);

        if (!$budget) {
            return [
                'status' => true,
                'message' => 'No budget constraints defined for this period'
            ];
        }

        $utilization = $this->getPeriodUtilization($period);

        // Calculate totals from the new data (multiplied by staff count)
        $newBusinessGwp = ($data['new_fac_gwp'] + $data['new_special_gwp'] +
            $data['new_treaty_gwp'] + $data['new_market_gwp']) * $staffCount;

        $newBusinessIncome = ($data['new_fac_income'] + $data['new_special_income'] +
            $data['new_treaty_income'] + $data['new_market_income']) * $staffCount;

        $renewalBusinessGwp = ($data['renewal_fac_gwp'] + $data['renewal_special_gwp'] +
            $data['renewal_treaty_gwp'] + $data['renewal_market_gwp']) * $staffCount;

        $renewalBusinessIncome = ($data['renewal_fac_income'] + $data['renewal_special_income'] +
            $data['renewal_treaty_income'] + $data['renewal_market_income']) * $staffCount;

        $totalGwp = $newBusinessGwp + $renewalBusinessGwp;
        $totalIncome = $newBusinessIncome + $renewalBusinessIncome;

        $messages = [];

        // // Check new business GWP
        // if (
        //     $budget->new_business_gwp_budget > 0 &&
        //     ($utilization['new_business_gwp'] + $newBusinessGwp) > $budget->new_business_gwp_budget
        // ) {
        //     $messages[] = "New Business GWP will exceed budget by " .
        //         number_format(($utilization['new_business_gwp'] + $newBusinessGwp) - $budget->new_business_gwp_budget, 2) .
        //         " KES";
        // }

        // // Check new business income
        // if (
        //     $budget->new_business_income_budget > 0 &&
        //     ($utilization['new_business_income'] + $newBusinessIncome) > $budget->new_business_income_budget
        // ) {
        //     $messages[] = "New Business Income will exceed budget by " .
        //         number_format(($utilization['new_business_income'] + $newBusinessIncome) - $budget->new_business_income_budget, 2) .
        //         " KES";
        // }

        // // Check renewal business GWP
        // if (
        //     $budget->renewal_business_gwp_budget > 0 &&
        //     ($utilization['renewal_business_gwp'] + $renewalBusinessGwp) > $budget->renewal_business_gwp_budget
        // ) {
        //     $messages[] = "Renewal Business GWP will exceed budget by " .
        //         number_format(($utilization['renewal_business_gwp'] + $renewalBusinessGwp) - $budget->renewal_business_gwp_budget, 2) .
        //         " KES";
        // }

        // // Check renewal business income
        // if (
        //     $budget->renewal_business_income_budget > 0 &&
        //     ($utilization['renewal_business_income'] + $renewalBusinessIncome) > $budget->renewal_business_income_budget
        // ) {
        //     $messages[] = "Renewal Business Income will exceed budget by " .
        //         number_format(($utilization['renewal_business_income'] + $renewalBusinessIncome) - $budget->renewal_business_income_budget, 2) .
        //         " KES";
        // }

        // // Check total GWP
        // if (
        //     $budget->total_gwp_budget > 0 &&
        //     ($utilization['total_gwp'] + $totalGwp) > $budget->total_gwp_budget
        // ) {
        //     $messages[] = "Total GWP will exceed budget by " .
        //         number_format(($utilization['total_gwp'] + $totalGwp) - $budget->total_gwp_budget, 2) .
        //         " KES";
        // }

        // Check total income
        if (
            $budget->total_income_budget > 0 &&
            ($utilization['total_income'] + $totalIncome) > $budget->total_income_budget
        ) {
            $messages[] = "Total Income will exceed budget by " .
                number_format(($utilization['total_income'] + $totalIncome) - $budget->total_income_budget, 2) .
                " KES";
        }

        if (count($messages) > 0) {
            return [
                'status' => false,
                'message' => implode('. ', $messages)
            ];
        }

        return [
            'status' => true,
            'message' => 'Budget constraints satisfied'
        ];
    }

    /**
     * Get current period utilization
     *
     * @param string $period
     * @return array
     */
    public function getPeriodUtilization(string $period): array
    {
        $records = PerformanceRecord::where('account_period', $period)->get();

        $utilization = [
            'new_business_gwp' => 0,
            'new_business_income' => 0,
            'renewal_business_gwp' => 0,
            'renewal_business_income' => 0,
            'total_gwp' => 0,
            'total_income' => 0
        ];

        foreach ($records as $record) {
            // Sum up new business GWP
            $utilization['new_business_gwp'] +=
                $record->new_fac_gwp +
                $record->new_special_gwp +
                $record->new_treaty_gwp +
                $record->new_market_gwp;

            // Sum up new business income
            $utilization['new_business_income'] +=
                $record->new_fac_income +
                $record->new_special_income +
                $record->new_treaty_income +
                $record->new_market_income;

            // Sum up renewal business GWP
            $utilization['renewal_business_gwp'] +=
                $record->renewal_fac_gwp +
                $record->renewal_special_gwp +
                $record->renewal_treaty_gwp +
                $record->renewal_market_gwp;

            // Sum up renewal business income
            $utilization['renewal_business_income'] +=
                $record->renewal_fac_income +
                $record->renewal_special_income +
                $record->renewal_treaty_income +
                $record->renewal_market_income;
        }

        // Calculate totals
        $utilization['total_gwp'] = $utilization['new_business_gwp'] + $utilization['renewal_business_gwp'];
        $utilization['total_income'] = $utilization['new_business_income'] + $utilization['renewal_business_income'];

        return $utilization;
    }

    /**
     * Get budget utilization status for a period
     *
     * @param string $period
     * @return array
     */
    public function getBudgetUtilizationStatus(string $period): array
    {
        $budget = BudgetAllocation::getActiveBudget($period);

        if (!$budget) {
            return [
                'has_budget' => false,
                'message' => 'No budget defined for this period'
            ];
        }

        $utilization = $this->getPeriodUtilization($period);

        // Calculate percentages
        $status = [
            'has_budget' => true,
            // 'new_business_gwp' => [
            //     'budget' => $budget->new_business_gwp_budget,
            //     'utilized' => $utilization['new_business_gwp'],
            //     'remaining' => $budget->new_business_gwp_budget - $utilization['new_business_gwp'],
            //     'percentage' => $budget->new_business_gwp_budget > 0
            //         ? round(($utilization['new_business_gwp'] / $budget->new_business_gwp_budget) * 100, 2)
            //         : 0
            // ],
            // 'new_business_income' => [
            //     'budget' => $budget->new_business_income_budget,
            //     'utilized' => $utilization['new_business_income'],
            //     'remaining' => $budget->new_business_income_budget - $utilization['new_business_income'],
            //     'percentage' => $budget->new_business_income_budget > 0
            //         ? round(($utilization['new_business_income'] / $budget->new_business_income_budget) * 100, 2)
            //         : 0
            // ],
            // 'renewal_business_gwp' => [
            //     'budget' => $budget->renewal_business_gwp_budget,
            //     'utilized' => $utilization['renewal_business_gwp'],
            //     'remaining' => $budget->renewal_business_gwp_budget - $utilization['renewal_business_gwp'],
            //     'percentage' => $budget->renewal_business_gwp_budget > 0
            //         ? round(($utilization['renewal_business_gwp'] / $budget->renewal_business_gwp_budget) * 100, 2)
            //         : 0
            // ],
            // 'renewal_business_income' => [
            //     'budget' => $budget->renewal_business_income_budget,
            //     'utilized' => $utilization['renewal_business_income'],
            //     'remaining' => $budget->renewal_business_income_budget - $utilization['renewal_business_income'],
            //     'percentage' => $budget->renewal_business_income_budget > 0
            //         ? round(($utilization['renewal_business_income'] / $budget->renewal_business_income_budget) * 100, 2)
            //         : 0
            // ],
            // 'total_gwp' => [
            //     'budget' => $budget->total_gwp_budget,
            //     'utilized' => $utilization['total_gwp'],
            //     'remaining' => $budget->total_gwp_budget - $utilization['total_gwp'],
            //     'percentage' => $budget->total_gwp_budget > 0
            //         ? round(($utilization['total_gwp'] / $budget->total_gwp_budget) * 100, 2)
            //         : 0
            // ],
            'total_income' => [
                'budget' => $budget->total_income_budget,
                'utilized' => $utilization['total_income'],
                'remaining' => $budget->total_income_budget - $utilization['total_income'],
                'percentage' => $budget->total_income_budget > 0
                    ? round(($utilization['total_income'] / $budget->total_income_budget) * 100, 2)
                    : 0
            ]
        ];

        return $status;
    }

    /**
     * Get budget data
     *
     * @return array
     */
    public function getBudgetData()
    {
        $currentPeriod = Carbon::now();
        $fiscalYear = FiscalYear::where(['is_current' => true, 'year' => $currentPeriod->year])->firstOrFail();

        $budgetIncome = BudgetIncome::where('fiscal_year_id', $fiscalYear->id)
            ->where('is_total', true)
            ->where('subcategory', '!=', 'Total Budgeted Income')
            ->sum('amount');

        $budgetIncome = is_numeric($budgetIncome) ? (float)$budgetIncome : 0;

        $periodTotal = PerformanceRecord::where('account_period', $currentPeriod->format('Y/m'))
            ->selectRaw('
                SUM(new_fac_gwp) as new_fac_gwp,
                SUM(new_special_gwp) as new_special_gwp,
                SUM(new_treaty_gwp) as new_treaty_gwp,
                SUM(new_market_gwp) as new_market_gwp,
                SUM(new_fac_income) as new_fac_income,
                SUM(new_special_income) as new_special_income,
                SUM(new_treaty_income) as new_treaty_income,
                SUM(new_market_income) as new_market_income,
                SUM(renewal_fac_gwp) as renewal_fac_gwp,
                SUM(renewal_special_gwp) as renewal_special_gwp,
                SUM(renewal_treaty_gwp) as renewal_treaty_gwp,
                SUM(renewal_market_gwp) as renewal_market_gwp,
                SUM(renewal_fac_income) as renewal_fac_income,
                SUM(renewal_special_income) as renewal_special_income,
                SUM(renewal_treaty_income) as renewal_treaty_income,
                SUM(renewal_market_income) as renewal_market_income
            ')
            ->first();

        $allocated = $periodTotal ? collect($periodTotal->toArray())->sum() : 0;
        $allocated = is_numeric($allocated) ? (float)$allocated : 0;
        $unallocated = $budgetIncome - $allocated;
        $unallocated = is_numeric($unallocated) ? (float)$unallocated : 0;

        return [
            'totalBudget' => 'KES ' . number_format($budgetIncome, 2),
            'allocated' => 'KES ' . number_format($allocated, 2),
            'unallocated' => 'KES ' . number_format($unallocated, 2),
        ];
    }

    /**
     * Get all staff without performance records
     *
     * @return array
     */
    public function getStaffPerfomanceData()
    {
        $allActiveStaff = User::where('status', 'A')
            // ->where('is_staff', true)
            ->select('id', 'name')
            ->orderBy('name')
            ->get();

        $staffWithPerformanceRecords = DB::table('performance_records')
            ->where('deleted_at', null)
            ->select('user_id')
            ->distinct()
            ->pluck('user_id')
            ->toArray();


        $staffWithoutPerformanceRecords = $allActiveStaff->filter(function ($staff) use ($staffWithPerformanceRecords) {
            return !in_array($staff->id, $staffWithPerformanceRecords);
        });

        return $staffWithoutPerformanceRecords->values()->all();
    }

    /**
     * Calculate total income from the request data
     *
     * @param Request $request
     * @return float
     */
    public function calculateTotalIncome(Request $request): float
    {
        $fields = [
            'new_fac_gwp',
            'new_special_gwp',
            'new_treaty_gwp',
            'new_market_gwp',
            'new_fac_income',
            'new_special_income',
            'new_treaty_income',
            'new_market_income',
            'renewal_fac_gwp',
            'renewal_special_gwp',
            'renewal_treaty_gwp',
            'renewal_market_gwp',
            'renewal_fac_income',
            'renewal_special_income',
            'renewal_treaty_income',
            'renewal_market_income'
        ];

        $newTotal = 0;
        foreach ($fields as $field) {
            $value = $request->input($field);
            if ($value !== null && $value !== '') {
                $cleanValue = (float) str_replace(',', '', $value);
                $newTotal += $cleanValue;
            }
        }

        $periodTotal = PerformanceRecord::where('account_period', $request->account_period)
            ->selectRaw('
                SUM(new_fac_gwp) as new_fac_gwp,
                SUM(new_special_gwp) as new_special_gwp,
                SUM(new_treaty_gwp) as new_treaty_gwp,
                SUM(new_market_gwp) as new_market_gwp,
                SUM(new_fac_income) as new_fac_income,
                SUM(new_special_income) as new_special_income,
                SUM(new_treaty_income) as new_treaty_income,
                SUM(new_market_income) as new_market_income,
                SUM(renewal_fac_gwp) as renewal_fac_gwp,
                SUM(renewal_special_gwp) as renewal_special_gwp,
                SUM(renewal_treaty_gwp) as renewal_treaty_gwp,
                SUM(renewal_market_gwp) as renewal_market_gwp,
                SUM(renewal_fac_income) as renewal_fac_income,
                SUM(renewal_special_income) as renewal_special_income,
                SUM(renewal_treaty_income) as renewal_treaty_income,
                SUM(renewal_market_income) as renewal_market_income
            ')
            ->first();

        $existingTotal = $periodTotal ? collect($periodTotal->toArray())->sum() : 0;

        return $newTotal + $existingTotal;
    }
}
