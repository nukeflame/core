<?php

namespace App\Http\Controllers;

use App\Http\Requests\FilterCoverRequest;
use App\Models\Classes;
use App\Models\CoverRegister;
use App\Models\Customer;
use App\Models\Region;
use Nukeflame\Core\Services\ReportGeneratorService;
use Nukeflame\Core\Services\ReportService;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Yajra\DataTables\Facades\DataTables;

class CoverReportController extends Controller
{
    protected $reportGenerator;

    public function __construct(ReportGeneratorService $reportGenerator)
    {
        $this->reportGenerator = $reportGenerator;
    }

    public function index(FilterCoverRequest $request)
    {
        $region = $request->input('region', 'all');
        $country = $request->input('country', 'all');
        $period = $request->input('period', date('Y') . '-' . (date('Y') + 1));
        $lineOfBusiness = $request->input('line_of_business', 'all');

        list($startYear, $endYear) = explode('-', $period);
        $startDate = Carbon::createFromDate($startYear, 1, 1);
        $endDate = Carbon::createFromDate($endYear, 12, 31);

        $contracts = $this->reportGenerator->getFilteredContracts(
            $region,
            $startDate,
            $endDate,
            $lineOfBusiness,
            $request->input('tab', 'covers-placement'),
            $request->input('page', 1)
        );

        $summary = $this->reportGenerator->getSummaryStatistics(
            $region,
            $startDate,
            $endDate,
            $lineOfBusiness
        );

        $regions = Region::orderBy('name')->get();
        // $linesOfBusiness = LineOfBusiness::orderBy('name')->get();
        $periodOptions = $this->generatePeriodOptions();

        $linesOfBusiness = [];
        $countries = [];

        // $reinsureds = InsurancePolicy::distinct('reinsurer')
        //     ->orderBy('reinsurer')
        //     ->pluck('reinsurer');

        // $currencies = InsurancePolicy::distinct('currency')
        //     ->orderBy('currency')
        //     ->pluck('currency');

        return view('reports.cover-reports.index', compact(
            'contracts',
            'summary',
            'regions',
            'linesOfBusiness',
            'periodOptions',
            'region',
            'period',
            'countries',
            'lineOfBusiness',
            'country'
        ));
    }

    private function generatePeriodOptions()
    {
        $currentYear = (int) date('Y');
        $options = [];

        for ($i = $currentYear - 2; $i <= $currentYear + 3; $i++) {
            $options[$i . '-' . ($i + 1)] = $i . '-' . ($i + 1);
        }

        return $options;
    }

    public function export(Request $request)
    {
        // Export logic here (would generate Excel/CSV)
        // return response()->download($this->reportGenerator->generateExport($request));
        return null;
    }

    public function print(Request $request)
    {
        // Print logic here (would render a printable view)
        return view('reports.cover-reports.print', [
            // Pass the data needed for printing
        ]);
    }

    public function getCoverPlacementData(Request $request)
    {
        $query = CoverRegister::query();
        // $rein = CoverRegister::with(['reinsurers']);
        // Apply filters based on request parameters
        if ($request->has('cedant') && !empty($request->cedant)) {
            $query->where('cedant', 'like', '%' . $request->cedant . '%');
        }

        if ($request->has('currency') && !empty($request->currency)) {
            $query->where('currency', $request->currency);
        }

        if ($request->has('class') && !empty($request->class)) {
            $query->where('class', $request->class);
        }

        if ($request->has('reinsurer') && !empty($request->reinsurer)) {
            $query->where('reinsurer', 'like', '%' . $request->reinsurer . '%');
        }

        // Date range filter
        if ($request->has('date_from') && !empty($request->date_from)) {
            $query->where('date_offerd', '>=', $request->date_from);
        }

        if ($request->has('date_to') && !empty($request->date_to)) {
            $query->where('date_offerd', '<=', $request->date_to);
        }

        // Premium range filter
        if ($request->has('premium_min') && !empty($request->premium_min)) {
            $query->where('premium', '>=', $request->premium_min);
        }

        if ($request->has('premium_max') && !empty($request->premium_max)) {
            $query->where('premium', '<=', $request->premium_max);
        }

        // Apply DataTables search
        // if ($request->has('search') && !empty($request->search['value'])) {
        //     $searchValue = $request->search['value'];
        //     $query->where(function ($q) use ($searchValue) {
        //         $q->where('cover_no', 'like', '%' . $searchValue . '%')
        //             ->orWhere('cover_title', 'like', '%' . $searchValue . '%')
        //             ->orWhere('cedant', 'like', '%' . $searchValue . '%')
        //             ->orWhere('insured', 'like', '%' . $searchValue . '%')
        //             ->orWhere('currency', 'like', '%' . $searchValue . '%')
        //             ->orWhere('class', 'like', '%' . $searchValue . '%')
        //             ->orWhere('reinsurer', 'like', '%' . $searchValue . '%');
        //     });
        // }

        // Get total count before pagination
        $totalData = $query->count();

        // Apply ordering
        if ($request->has('order')) {
            $columnIndex = $request->order[0]['column'];
            $columnName = $request->columns[$columnIndex]['data'];
            $columnSortOrder = $request->order[0]['dir'];
            $query->orderBy($columnName, $columnSortOrder);
        }

        // Apply pagination
        $start = $request->start ?? 0;
        $length = $request->length ?? 10;
        $data = $query->skip($start)->take($length)->get();

        $columnMapping = [
            'cover_no',
            'cover_title',
            'cedant',
            'insured',
            'biz_type',
            'currency',
            'class',
            'date_offerd',
            'start_date',
            'end_date',
            'our_share',
            'reinsurer',
            'sum_insured',
            'premium',
        ];

        $data = $data->map(function ($item) use ($columnMapping) {
            return $this->transformToNewColumnNames($item, $columnMapping);
        });

        return response()->json([
            'draw' => intval($request->draw),
            'recordsTotal' => $totalData,
            'recordsFiltered' => $totalData,
            'data' => $data
        ]);
    }

    /**
     * Transform model data to use new column names
     */
    private function transformToNewColumnNames($item, $columnMapping)
    {
        $transformed = [];
        foreach ($columnMapping as $title) {
            switch ($title) {
                case 'cover_no':
                    $transformed[$title] = $item['cover_no'];
                    break;
                case 'cover_title':
                    $transformed[$title] = $item['cover_title'];
                    break;
                case 'cedant':
                    $customer = Customer::where('customer_id', $item['customer_id'])->first(['name']) ?? '--';
                    $transformed[$title] = $customer['name'];
                    break;
                case 'insured':
                    $transformed[$title] = $item['insured_name'];
                    break;
                case 'biz_type':
                    $bizType = $item['transaction_type'] === 'REN' ? 'RENEWAL' : 'NEW';
                    $transformed[$title] = $bizType;
                    break;
                case 'currency':
                    $transformed[$title] = $item['currency_code'];
                    break;
                case 'class':
                    $class = Classes::where('class_code', $item['class_code'])->first(['class_name']) ?? '--';
                    $transformed[$title] = $class->class_name;
                    break;
                case 'date_offerd':
                    $transformed[$title] = Carbon::parse($item['date_offered'])->format('d/m/Y') ?? '--';
                    break;
                case 'start_date':
                    $transformed[$title] = Carbon::parse($item['cover_from'])->format('d/m/Y') ?? '--';
                    break;
                case 'end_date':
                    $transformed[$title] = Carbon::parse($item['cover_to'])->format('d/m/Y') ?? '--';
                    break;
                case 'our_share':
                    $transformed[$title] = number_format($item['share_offered'], 4);
                    break;
                case 'reinsurer':
                    $transformed[$title] = $this->buildReinsurerColumn($item);
                    break;
                default:
                    break;
            }
        }

        $transformed['reinsurer_details'] = $this->getReinsurerDetails($item);
        $transformed['DT_RowId'] = 'row_' . $item['cover_no'];

        return $transformed;
    }

    private function getReinsurerDetails($item)
    {
        // $reinsurers = $item->reinsurers ?? collect();
        $reinsurers  = collect([
            [
                'cover_no' => 'C000006',
                'reinsurer_name' => 'CONTINENTAL REINSURANCE LIMITED',
                'share_percentage' => 40.0000,
                'sum_insured' => 96000000.00,
                'premium' => 110000.00,
                'sequence' => 1,
            ],
            [
                'cover_no' => 'C000006',
                'reinsurer_name' => 'MUNICH RE',
                'share_percentage' => 35.0000,
                'sum_insured' => 84000000.00,
                'premium' => 96250.00,
                'sequence' => 2,
            ],
            [
                'cover_no' => 'C000006',
                'reinsurer_name' => 'SWISS RE',
                'share_percentage' => 15.0000,
                'sum_insured' => 36000000.00,
                'premium' => 41250.00,
                'sequence' => 3,
            ],
            [
                'cover_no' => 'C000006',
                'reinsurer_name' => 'HANNOVER RE',
                'share_percentage' => 10.0000,
                'sum_insured' => 24000000.00,
                'premium' => 27500.00,
                'sequence' => 4,
            ],
        ]);

        return $reinsurers->map(function ($reinsurer) {
            return [
                'name' => $reinsurer['reinsurer_name'],
                'share' => $reinsurer['share_percentage'] . '%',
                'sum_insured' => number_format($reinsurer['sum_insured']),
                'premium' => number_format($reinsurer['premium']),
            ];
        })->toArray();
    }

    private function buildReinsurerColumn($item)
    {
        $reinsurers = $item->reinsurers ?? collect();
        if ($reinsurers->count() <= 1) {
            // Single reinsurer - show name directly
            $reinsurer = $reinsurers->first();
            return $reinsurer ? $reinsurer->reinsurer_name : 'CONTINENTAL REINSURANCE LIMITED';
        } else {
            // Multiple reinsurers - show expandable button
            $count = $reinsurers->count();
            return '<button class="btn btn-link btn-sm toggle-reinsurers" data-cover="' . $item['cover_no'] . '">
                        <i class="fas fa-chevron-right toggle-icon"></i>
                        <span class="reinsurer-count">' . $count . ' Reinsurers</span>
                    </button>';
        }
    }

    public function getFilterOptions(Request $request)
    {
        $type = $request->get('type');
        $data = [];
        // switch ($type) {
        //     case 'cedant':
        //         $data = CoverRegister::distinct()
        //             ->whereNotNull('cedant')
        //             ->where('cedant', '!=', '')
        //             ->pluck('cedant')
        //             ->sort()
        //             ->values();
        //         break;

        //     case 'currency':
        //         $data = CoverRegister::distinct()
        //             ->whereNotNull('currency')
        //             ->where('currency', '!=', '')
        //             ->pluck('currency')
        //             ->sort()
        //             ->values();
        //         break;

        //     case 'class':
        //         $data = CoverRegister::distinct()
        //             ->whereNotNull('class')
        //             ->where('class', '!=', '')
        //             ->pluck('class')
        //             ->sort()
        //             ->values();
        //         break;

        //     case 'reinsurer':
        //         $data = CoverRegister::distinct()
        //             ->whereNotNull('reinsurer')
        //             ->where('reinsurer', '!=', '')
        //             ->pluck('reinsurer')
        //             ->sort()
        //             ->values();
        //         break;
        // }

        return response()->json($data);
    }

    public function getCoversByTypeData()
    {
        return DataTables::of([])
            ->addColumn('type_code', function () {
                return '--';
            })
            ->addColumn('cover_type', function () {
                return '--';
            })
            ->addColumn('cover_no', function () {
                return '--';
            })
            ->addColumn('cover_title', function () {
                return '--';
            })
            ->addColumn('group', function () {
                return '--';
            })
            ->addColumn('cedant', function () {
                return '--';
            })
            ->addColumn('insured', function () {
                return '--';
            })
            ->addColumn('currency', function () {
                return '--';
            })
            ->addColumn('class', function () {
                return '--';
            })
            ->addColumn('date_offered', function () {
                return '--';
            })
            ->addColumn('start_date', function () {
                return '--';
            })
            ->addColumn('our_share', function () {
                return '--';
            })
            ->addColumn('action', function () {
                return '--';
            })
            ->make(true);
    }

    public function getCoversEndingData()
    {
        return DataTables::of([])
            ->addColumn('type_code', function () {
                return '--';
            })
            ->addColumn('cover_type', function () {
                return '--';
            })
            ->addColumn('cover_no', function () {
                return '--';
            })
            ->addColumn('cover_title', function () {
                return '--';
            })
            ->addColumn('group', function () {
                return '--';
            })
            ->addColumn('cedant', function () {
                return '--';
            })
            ->addColumn('insured', function () {
                return '--';
            })
            ->addColumn('currency', function () {
                return '--';
            })
            ->addColumn('class', function () {
                return '--';
            })
            ->addColumn('date_offered', function () {
                return '--';
            })
            ->addColumn('start_date', function () {
                return '--';
            })
            ->addColumn('our_share', function () {
                return '--';
            })
            ->addColumn('action', function () {
                return '--';
            })
            ->make(true);
    }

    public function getCoversRenewdData()
    {
        return DataTables::of([])
            ->addColumn('type_code', function () {
                return '--';
            })
            ->addColumn('cover_type', function () {
                return '--';
            })
            ->addColumn('cover_no', function () {
                return '--';
            })
            ->addColumn('cover_title', function () {
                return '--';
            })
            ->addColumn('group', function () {
                return '--';
            })
            ->addColumn('cedant', function () {
                return '--';
            })
            ->addColumn('insured', function () {
                return '--';
            })
            ->addColumn('currency', function () {
                return '--';
            })
            ->addColumn('class', function () {
                return '--';
            })
            ->addColumn('date_offered', function () {
                return '--';
            })
            ->addColumn('start_date', function () {
                return '--';
            })
            ->addColumn('our_share', function () {
                return '--';
            })
            ->addColumn('action', function () {
                return '--';
            })
            ->make(true);
    }
}
