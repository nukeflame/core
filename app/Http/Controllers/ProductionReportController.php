<?php

namespace App\Http\Controllers;

use App\Repositories\ProductionReportRepository;
use Nukeflame\Core\Services\ReportGeneratorService;
use Illuminate\Http\Request;
use Illuminate\Support\Str;

class ProductionReportController extends Controller
{
    protected $repository;
    protected $reportGenerator;

    public function __construct(ProductionReportRepository $repository, ReportGeneratorService $reportGenerator)
    {
        $this->repository = $repository;
        $this->reportGenerator = $reportGenerator;
    }

    public function summary(Request $request)
    {
        $tab = $request->get('tab', 'debit-type');

        $businessClasses = $this->repository->businessClasses();
        $dateRange = $this->repository->getDateRangeParameters($request);
        $metrics = $this->repository->calculateMetrics($request, $dateRange);

        return view('reports.production-reports.summary', compact(
            'tab',
            'businessClasses',
            'metrics'
        ));
    }

    public function getDebitTypeBusinessData(Request $request)
    {
        // $dateRange = $this->getDateRangeParameters($request);
        // $currency = $request->get('currency', 'KES');
        // $businessClass = $request->get('business_class', 'all');

        $query = $this->repository->select([
            'cover_register.type_of_bus as business_type',
            'cover_register.cedant_premium as premium',
        ])->get();
        // ->select([
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
        //     ->where('productions.company_id', $company->id)debit_types
        //     ->whereBetween('productions.transaction_date', [$dateRange['start_date'], $dateRange['end_date']]);

        // // Apply business class filter if specified
        // if ($businessClass != 'all') {
        //     $query->where('productions.business_class_id', $businessClass);
        // }

        // $data = $query->groupBy('debit_types.id', 'debit_types.code', '.name')
        //     ->orderBy('premium_kes', 'desc')
        //     ->get();

        return response()->json(['data' => []]);
    }

    public function getDebitTypeFinancialData(Request $request)
    {
        // $dateRange = $this->getDateRangeParameters($request);
        // $currency = $request->get('currency', 'KES');
        // $businessClass = $request->get('business_class', 'all');

        // $query = $this->repository->select([
        //     'cover_register.type_of_bus as business_type',
        //     'cover_register.cedant_premium as premium',
        // ])->get();

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
        //     ->where('financial_summaries.company_id', $company->id)
        //     ->whereBetween('financial_summaries.month', [$dateRange['start_date'], $dateRange['end_date']]);

        // // Apply business class filter if specified
        // if ($businessClass != 'all') {
        //     $query->where('financial_summaries.business_class_id', $businessClass);
        // }

        // $data = $query->orderBy('financial_summaries.month')
        //     ->orderBy('financial_summaries.category')
        //     ->orderBy('financial_summaries.subcategory')
        //     ->get();

        return response()->json(['data' => []]);
    }

    public function export(Request $request)
    {
        $tab = $request->get('tab', 'debit-type');
        $report = $request->get('report', 'business-performance');
        $format = $request->get('format', 'excel');

        $dateRange = $this->repository->getDateRangeParameters($request);
        $data = $this->repository->getReportData($tab, $report, $request, $dateRange);

        $filename = ucwords("Production_Summary_{$tab}_{$report}" . "_" . now()->format('Y-m-d'));
        $viewPath =  "reports.production.exports.{$tab}_{$report}";

        return $this->reportGenerator->generateExport($data, $filename, $format, $viewPath, $tab, $report, $request);
    }

    public function detailed()
    {
        return view('reports.production-reports.detailed');
    }


    public function facType()
    {
        return view('reports.production-reports.facultative_type');
    }

    // public function getFinancialData(Request $request)
    // {
    //     // $data = FinancialData::select('id', 'category', 'subcategory', 'type', 'month', 'budgeted', 'actual')
    //     //     ->orderBy('category')
    //     //     ->orderBy('subcategory')
    //     //     ->orderBy('month');

    //     return DataTables::of([])
    //         ->addIndexColumn()
    //         ->addColumn('variance', function ($row) {
    //             $variance = $row->actual - $row->budgeted;
    //             $class = $variance >= 0 ? 'text-success' : 'text-danger';
    //             return '<span class="' . $class . '">' . number_format($variance, 2) . '</span>';
    //         })
    //         ->addColumn('achievement', function ($row) {
    //             if ($row->budgeted == 0) return 'N/A';
    //             $achievement = ($row->actual / $row->budgeted) * 100;
    //             $class = $achievement >= 100 ? 'text-success' : 'text-danger';
    //             return '<span class="' . $class . '">' . number_format($achievement, 2) . '%</span>';
    //         })
    //         ->rawColumns(['variance', 'achievement'])
    //         ->make(true);
    // }
}
