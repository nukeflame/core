<?php

namespace App\Repositories;

use App\Models\CoverRegister;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Collection;
use Prettus\Repository\Criteria\RequestCriteria;
use Prettus\Repository\Exceptions\RepositoryException;

class ProductionReportRepository extends BaseRepository
{
    /**
     * Specify Model class name
     *
     * @return string
     */
    public function model()
    {
        return CoverRegister::class;
    }

    /**
     * Boot up the repository, pushing criteria
     */
    public function boot()
    {
        try {
            $this->pushCriteria(app(RequestCriteria::class));
        } catch (RepositoryException $e) {
        }
    }

    /**
     * Calculate metrics for summary cards
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  array  $dateRange
     * @return array
     */
    public function calculateMetrics(Request $request, array $dateRange)
    {
        $currency = $request->get('currency', 'KES');
        $businessClass = $request->get('business_class', 'all');

        $currentPeriodMetrics = $this->calculateProductionMetrics(
            $dateRange['start_date'],
            $dateRange['end_date'],
            $currency,
            $businessClass
        );

        $periodDays = Carbon::parse($dateRange['start_date'])->diffInDays(Carbon::parse($dateRange['end_date']));
        $previousPeriodEnd = Carbon::parse($dateRange['start_date'])->subDay();
        $previousPeriodStart = Carbon::parse($previousPeriodEnd)->subDays($periodDays);

        $previousPeriodMetrics = $this->calculateProductionMetrics(
            $previousPeriodStart,
            $previousPeriodEnd,
            $currency,
            $businessClass
        );

        $metrics = [];

        $metrics['total_premium'] = [
            'value' => $currentPeriodMetrics['total_premium'],
            'currency' => $currency,
            'trend' => $this->calculateTrendPercentage(
                $previousPeriodMetrics['total_premium'],
                $currentPeriodMetrics['total_premium']
            ),
            'trend_direction' => $currentPeriodMetrics['total_premium'] >= $previousPeriodMetrics['total_premium'] ? 'up' : 'down'
        ];

        $metrics['commission_earned'] = [
            'value' => $currentPeriodMetrics['commission_earned'],
            'trend' => $this->calculateTrendPercentage(
                $previousPeriodMetrics['commission_earned'],
                $currentPeriodMetrics['commission_earned']
            ),
            'trend_direction' => $currentPeriodMetrics['commission_earned'] >= $previousPeriodMetrics['commission_earned'] ? 'up' : 'down'
        ];

        $metrics['claims_ratio'] = [
            'value' => $currentPeriodMetrics['claims_ratio'],
            'trend' => $previousPeriodMetrics['claims_ratio'] - $currentPeriodMetrics['claims_ratio'],
            'trend_direction' => $currentPeriodMetrics['claims_ratio'] <= $previousPeriodMetrics['claims_ratio'] ? 'down' : 'up'
        ];

        $metrics['active_policies'] = [
            'value' => $currentPeriodMetrics['active_policies'],
            'trend' => $currentPeriodMetrics['active_policies'] - $previousPeriodMetrics['active_policies'],
            'trend_direction' => $currentPeriodMetrics['active_policies'] >= $previousPeriodMetrics['active_policies'] ? 'up' : 'down'
        ];

        return $metrics;
    }

    public function getDateRangeParameters(Request $request)
    {
        $dateRange = $request->get('date_range', 'year-to-date');
        $now = Carbon::now();

        switch ($dateRange) {
            case 'year-to-date':
                $startDate = Carbon::createFromDate($now->year, 1, 1)->startOfDay();
                $endDate = $now->copy()->endOfDay();
                break;

            case 'last-quarter':
                $startDate = $now->copy()->subMonths(3)->startOfMonth()->startOfDay();
                $endDate = $now->copy()->subMonth()->endOfMonth()->endOfDay();
                break;

            case 'last-6-months':
                $startDate = $now->copy()->subMonths(6)->startOfDay();
                $endDate = $now->copy()->endOfDay();
                break;

            case 'last-12-months':
                $startDate = $now->copy()->subYear()->startOfDay();
                $endDate = $now->copy()->endOfDay();
                break;

            case 'custom-range':
                $startDate = $request->filled('start_date')
                    ? Carbon::parse($request->get('start_date'))->startOfDay()
                    : $now->copy()->subYear()->startOfDay();

                $endDate = $request->filled('end_date')
                    ? Carbon::parse($request->get('end_date'))->endOfDay()
                    : $now->copy()->endOfDay();
                break;

            default:
                $startDate = Carbon::createFromDate($now->year, 1, 1)->startOfDay();
                $endDate = $now->copy()->endOfDay();
        }

        return [
            'start_date' => $startDate,
            'end_date' => $endDate
        ];
    }

    protected function calculateTrendPercentage($previousValue, $currentValue)
    {
        if ($previousValue == 0) {
            return $currentValue > 0 ? 100 : 0;
        }

        return (($currentValue - $previousValue) / $previousValue) * 100;
    }

    public function getReportData($tab, $report, Request $request, array $dateRange)
    {
        $currency = $request->get('currency', 'KES');
        $businessClass = $request->get('business_class', 'all');

        if ($tab === 'debit-type') {
            if ($report === 'business-performance') {
                return $this->getDebitTypeBusinessPerformanceData($dateRange, $currency, $businessClass);
            } elseif ($report === 'financial-summary') {
                return $this->getDebitTypeFinancialSummaryData($dateRange, $currency, $businessClass);
            }
        }

        return [];
    }

    protected function getDebitTypeBusinessPerformanceData($dateRange, $currency, $businessClass, array $filters = []): ?Collection
    {
        // In a real application, this would query your database
        // For example:

        /*
        return DebitType::with(['reserves'])
            ->when(isset($filters['date_from']), function ($query) use ($filters) {
                return $query->where('created_at', '>=', $filters['date_from']);
            })
            ->when(isset($filters['date_to']), function ($query) use ($filters) {
                return $query->where('created_at', '<=', $filters['date_to']);
            })
            ->get()
            ->map(function ($debitType) {
                return [
                    $debitType->code,
                    $debitType->name,
                    $debitType->mium_res_gwp ?? '-',
                    $debitType->on_res_cedant ?? '-',
                    $debitType->prm_tax_res ?? '-',
                    $debitType->rein_tax_res ?? '-',
                    $debitType->w_tax_res ?? '-',
                    $debitType->vat_res ?? '-',
                    $debitType->claim_res ?? '-',
                    $debitType->ue_res_income ?? '-',
                ];
            });
        */

        // $query = app($this->model());
        // $query = DebitType::select([
        //     'debit_types.code as type_code',
        //     'debit_types.name as type_name',
        //     DB::raw('SUM(productions.gross_premium_' . strtolower($currency) . ') as premium_kes'),
        //     DB::raw('SUM(productions.commission_' . strtolower($currency) . ') as commission_kes'),
        //     DB::raw('SUM(productions.prm_tax_' . strtolower($currency) . ') as prm_tax_kes'),
        //     DB::raw('SUM(productions.rein_tax_' . strtolower($currency) . ') as rein_tax_kes'),
        //     DB::raw('SUM(productions.w_tax_' . strtolower($currency) . ') as w_tax_kes'),
        //     DB::raw('SUM(productions.vat_' . strtolower($currency) . ') as vat_kes'),
        //     DB::raw('SUM(productions.claims_paid_' . strtolower($currency) . ') as claims_kes'),
        //     DB::raw('SUM(productions.underwriting_income_' . strtolower($currency) . ') as ue_kes'),
        //     DB::raw('CASE WHEN SUM(productions.gross_premium_' . strtolower($currency) . ') = 0 THEN 0
        //                 ELSE (SUM(productions.commission_' . strtolower($currency) . ') /
        //                 SUM(productions.gross_premium_' . strtolower($currency) . ')) * 100 END as comm_percent')
        // ])
        //     ->join('productions', 'debit_types.id', '=', 'productions.debit_type_id')
        //     ->where('productions.company_id', $companyId)
        //     ->whereBetween('productions.transaction_date', [$dateRange['start_date'], $dateRange['end_date']]);

        // // Apply business class filter if specified
        // if ($businessClass != 'all') {
        //     $query->where('productions.business_class_id', $businessClass);
        // }

        // return $query->groupBy('debit_types.id', 'debit_types.code', 'debit_types.name')
        //     ->orderBy('premium_kes', 'desc')
        //     ->get()
        //     ->toArray();

        return collect([
            ['Facultative(Quotations & Offers)', 'Facultative', '-', '-', '-', '-', '-', '-', '-', '-'],
            ['Special Lines', 'Facultative', '-', '-', '-', '-', '-', '-', '-', '-'],
            ['External Markets', 'Facultative', '-', '-', '-', '-', '-', '-', '-', '-'],
            ['MinDeps', 'MDP', '-', '-', '-', '-', '-', '-', '-', '-'],
            ['TreatyProp', 'Proportional', '-', '-', '-', '-', '-', '-', '-', '-'],
        ]);
    }

    /**
     * Get debit type financial summary data for export
     *
     * @param  int  $companyId
     * @param  array  $dateRange
     * @param  string  $currency
     * @param  string|int  $businessClass
     * @return array
     */
    protected function getDebitTypeFinancialSummaryData($dateRange, $currency, $businessClass): array
    {
        // $query = FinancialSummary::select([
        //     'financial_summaries.category',
        //     'financial_summaries.subcategory',
        //     'debit_types.name as type',
        //     DB::raw('DATE_FORMAT(financial_summaries.month, "%b %Y") as month'),
        //     DB::raw('financial_summaries.budgeted_amount_' . strtolower($currency) . ' as budgeted'),
        //     DB::raw('financial_summaries.actual_amount_' . strtolower($currency) . ' as actual'),
        //     DB::raw('(financial_summaries.actual_amount_' . strtolower($currency) . ' -
        //                 financial_summaries.budgeted_amount_' . strtolower($currency) . ') as variance'),
        //     DB::raw('CASE WHEN financial_summaries.budgeted_amount_' . strtolower($currency) . ' = 0 THEN 0
        //                 ELSE (financial_summaries.actual_amount_' . strtolower($currency) . ' /
        //                 financial_summaries.budgeted_amount_' . strtolower($currency) . ') * 100 END as achievement_percent')
        // ])
        //     ->join('debit_types', 'financial_summaries.debit_type_id', '=', 'debit_types.id')
        //     ->where('financial_summaries.company_id', $companyId)
        //     ->whereBetween('financial_summaries.month', [$dateRange['start_date'], $dateRange['end_date']]);

        // // Apply business class filter if specified
        // if ($businessClass != 'all') {
        //     $query->where('financial_summaries.business_class_id', $businessClass);
        // }

        // return $query->orderBy('financial_summaries.month')
        //     ->orderBy('financial_summaries.category')
        //     ->orderBy('financial_summaries.subcategory')
        //     ->get()
        //     ->toArray();

        return [];
    }

    public function calculateProductionMetrics($startDate, $endDate, $currency = 'KES', $businessClass = 'all')
    {
        // Base query for production data
        // $query = Production::wheProductionReportRepositoryre('company_id', $companyId)
        //     ->whereBetween('transaction_date', [$startDate, $endDate]);

        // // Apply business class filter if specified
        // if ($businessClass != 'all') {
        //     $query->where('business_class_id', $businessClass);
        // }

        // // Get production summary
        // $production = $query->select([
        //     DB::raw('SUM(gross_premium_' . strtolower($currency) . ') as total_premium'),
        //     DB::raw('SUM(commission_' . strtolower($currency) . ') as commission_earned'),
        //     DB::raw('SUM(claims_paid_' . strtolower($currency) . ') as claims_paid')
        // ])->first();

        // // Calculate claims ratio
        // $claimsRatio = 0;
        // if ($production->total_premium > 0) {
        //     $claimsRatio = ($production->claims_paid / $production->total_premium) * 100;
        // }

        // // Get active policies count
        // $policyQuery = Policy::where('company_id', $companyId)
        //     ->where('status', 'active')
        //     ->where('expiry_date', '>=', $endDate);

        // // Apply business class filter if specified
        // if ($businessClass != 'all') {
        //     $policyQuery->where('business_class_id', $businessClass);
        // }

        // $activePolicies = $policyQuery->count();

        return [
            'total_premium' => 0,
            'commission_earned' => 0,
            'claims_ratio' => 0,
            'active_policies' => 0
        ];
    }



    public function businessClasses()
    {
        $data = [
            (object) [
                'id' => 1,
                'name' => 'Facultative (Quotations & Offers)'
            ],
            (object) [
                'id' => 2,
                'name' => 'Special Lines'
            ],
            (object) [
                'id' => 3,
                'name' => 'External Markets'
            ],
            (object) [
                'id' => 4,
                'name' => 'MinDeps'
            ],
            (object) [
                'id' => 5,
                'name' => 'Treaty Proportional'
            ],
        ];

        return $data;
    }
}
