<?php

namespace Nukeflame\Core\Services;

use App\Exports\Reports\Production\DebitType\FinancialSummaryExport;
use App\Exports\Reports\ProductionByDebitTypeBPExport;
use App\Models\CoverContract;
use App\Repositories\ProductionReportRepository;
use Carbon\Carbon;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\DB;
use Maatwebsite\Excel\Facades\Excel;

class ReportGeneratorService
{
    protected $productionRepository;

    /**
     * Create a new export instance.
     * @return void
     */
    public function __construct(ProductionReportRepository $productionRepository)
    {
        $this->productionRepository = $productionRepository;
    }

    public function getFilteredContracts($region, $startDate, $endDate, $lineOfBusiness, $tab, $page = 1)
    {
        $query = CoverContract::with(['cedant']);
        // ->filterByRegion($region)
        // ->filterByPeriod($startDate, $endDate);
        // ->filterByLineOfBusiness($lineOfBusiness);

        switch ($tab) {
            case 'covers-by-type':
                break;
            case 'covers-ending':
                break;
            case 'renewed-covers':
                break;
            case 'covers-placement':
            default:
                $query->orderBy('inception_date', 'desc');
                break;
        }

        return $query->get();
    }

    public function getSummaryStatistics($region, $startDate, $endDate, $lineOfBusiness)
    {
        $contracts = CoverContract::filterByRegion($region)
            ->filterByPeriod($startDate, $endDate);
        // ->filterByLineOfBusiness($lineOfBusiness);

        $totalContracts = $contracts->count();
        $totalPremium = $contracts->sum('premium');

        $avgPlacementTime = DB::table('cover_contracts')
            ->whereNotNull('placement_date')
            ->whereNotNull('completion_date')
            // ->selectRaw('AVG(DATEDIFF(completion_date, placement_date)) as avg_days')
            ->first()
            ->avg_days ?? 0;

        return [
            'totalPlacements' => $totalContracts,
            'totalPremium' => $totalPremium,
            'avgPlacementTime' => round($avgPlacementTime, 1)
        ];
    }

    /**
     * Generate export file based on format
     *
     * @param array $data
     * @param string $filename
     * @param string $format
     * @param string|null $viewPath
     * @return mixed
     */
    public function generateExport($data, $filename, $format = 'excel', $viewPath = null, $tab, $report, $request)
    {
        switch (strtolower($format)) {
            case 'pdf':
                return $this->generatePdf($data, $filename, $viewPath);

            case 'csv':
                // return Excel::download(new GenericDataExport($data, $viewPath), $filename . '.csv', \Maatwebsite\Excel\Excel::CSV);
                return null;

            case 'excel':
                if ($tab === 'debit-type') {
                    if ($report === 'business-performance') {
                        return Excel::download(new ProductionByDebitTypeBPExport($data, $viewPath), $filename . '.xlsx');
                    } elseif ($report === 'financial-summary') {
                        $year = $request->year;
                        $quarter =  $request->quarter == 'all' ? 5 : $request->quarter;
                        return Excel::download(new FinancialSummaryExport($data, $viewPath, $year, $quarter), $filename . '.xlsx');
                    }
                }

            default:
                return Excel::download(new ProductionByDebitTypeBPExport($data, $viewPath), $filename . '.xlsx');
        }
    }

    /**
     * Generate PDF export
     *
     * @param array $data
     * @param string $filename
     * @param string|null $viewPath
     * @return mixed
     */
    protected function generatePdf($data, $filename, $viewPath = null)
    {
        // if ($viewPath) {
        //     $pdf = PDF::loadView($viewPath, ['data' => $data]);
        // } else {
        //     // Generate generic table view if no specific view is provided
        //     $pdf = PDF::loadView('exports.generic-table', [
        //         'data' => $data,
        //         'headers' => count($data) > 0 ? array_keys($data[0]) : []
        //     ]);
        // }

        return null;

        // return $pdf->download($filename . '.pdf');
    }
}
