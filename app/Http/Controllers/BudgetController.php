<?php

namespace App\Http\Controllers;

use App\Exports\Budgets\BudgetStatementExport;
use App\Http\Requests\PerformanceRecordRequest;
use App\Models\BudgetAllocation;
use App\Models\BudgetExpense;
use App\Models\BudgetIncome;
use App\Models\Company;
use App\Models\FiscalYear;
use App\Models\PerformanceRecord;
use App\Models\User;
use App\Services\BudgetService;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Validator;
use Maatwebsite\Excel\Facades\Excel;
use PhpOffice\PhpSpreadsheet\IOFactory;

class BudgetController extends Controller
{
    protected $budgetService;

    /**
     * Constructor for PerformanceRecordController
     *
     * @param BudgetService $budgetService
     */
    public function __construct(BudgetService $budgetService)
    {
        $this->budgetService = $budgetService;
    }

    public function index()
    {
        $currentPeriod = Carbon::now()->format('Y/m');

        $fiscalYears = FiscalYear::all();
        $defaultYear = FiscalYear::where('is_current', true)->pluck('year')->toArray();

        $budgetData = $this->budgetService->getBudgetData();
        $staff = $this->budgetService->getStaffPerfomanceData();

        return view('admin.budget_allocation', [
            'filters' => [
                'fiscalYears' => $fiscalYears,
                'defaultYear' => $defaultYear,
            ],
            'staff' => $staff,
            'currentPeriod' => $currentPeriod,
            'budgetData' => json_encode($budgetData)
        ]);
    }

    public function budgetForm()
    {
        return view('admin.budget_allocation');
    }

    public function getBudgetAllocationData(Request $request)
    {
        $requestedYear = $request->input('year', date('Y'));
        if ($request->filled('year')) {
            $fiscalYears = FiscalYear::where('year', $request->year)->get();
        } else {
            $fiscalYears = FiscalYear::all();
        }

        $financialData = [];
        if ($fiscalYears->count() > 0) {
            foreach ($fiscalYears as $year) {
                $incomes = BudgetIncome::where('fiscal_year_id', $year->id)->get();
                $expenses = BudgetExpense::where('fiscal_year_id', $year->id)->get();

                $totalIncome =  is_numeric($incomes->where('is_total', true)->where('subcategory', '!=', 'Total Budgeted Income')->sum('amount')) ? $incomes->where('is_total', true)->where('subcategory', '!=', 'Total Budgeted Income')->sum('amount') : 0;
                $totalExpense =  is_numeric($expenses->where('is_total', false)->where('subcategory', '!=', 'Total Expenses')->sum('amount')) ? $expenses->where('is_total', false)->where('subcategory', '!=', 'Total Expenses')->sum('amount') : 0;

                $grossProfit = $totalIncome - $totalExpense;

                $costIncomeRatio = $totalIncome > 0 ? ($totalExpense / $totalIncome) * 100 : 0;
                $profitMargin = $totalIncome > 0 ? ($grossProfit / $totalIncome) * 100 : 0;

                $financialData[$year->year] = [
                    'incomes' => $incomes->map(function ($income) {
                        $amount = is_numeric($income->amount) ? $income->amount : 0;
                        return [
                            'category' => $income->category,
                            'subcategory' => $income->subcategory,
                            'amount' => number_format($amount, 2),
                            'isTotal' => $income->is_total ?? false
                        ];
                    })->toArray(),
                    'expenses' => $expenses->map(function ($expense) {
                        $amount = is_numeric($expense->amount) ? $expense->amount : 0;
                        return [
                            'category' => $expense->category,
                            'subcategory' => $expense->subcategory,
                            'amount' => number_format($amount, 2),
                            'isTotal' => $expense->is_total ?? false
                        ];
                    })->toArray(),
                    'summary' => [
                        'grossProfit' => number_format(max($grossProfit, 0), 2),
                        'costIncomeRatio' => number_format(max($costIncomeRatio, 0), 2),
                        'profitMargin' => number_format(max($profitMargin, 0), 2)
                    ]
                ];
            }
        }

        if (isset($financialData[$requestedYear])) {
            return response()->json($financialData[$requestedYear]);
        } else {
            $firstYear = array_key_first($financialData);
            return response()->json($financialData[$firstYear]);
        }
    }

    public function getStaffData()
    {
        $results = PerformanceRecord::all();
        $data = collect($results)->map(function ($query) {
            $user = User::find($query->user_id);

            return (object)[
                'id'                              => $query->id,
                'handler'                         => $user?->name,
                'new_gwp_fac'                     => $query->new_fac_gwp,
                'new_gwp_special'                 => $query->new_special_gwp,
                'new_gwp_treaty'                  => $query->new_treaty_gwp,
                'new_gwp_market_expansion'        => $query->new_market_gwp,
                'new_income_fac'                  => $query->new_fac_income,
                'new_income_special'              => $query->new_special_income,
                'new_income_treaty'               => $query->new_treaty_income,
                'new_income_market_expansion'     => $query->new_market_income,
                'renewal_gwp_fac'                 => $query->renewal_fac_gwp,
                'renewal_gwp_special'             => $query->renewal_special_gwp,
                'renewal_gwp_treaty'              => $query->renewal_treaty_gwp,
                'renewal_gwp_market_expansion'    => $query->renewal_market_gwp,
                'renewal_income_fac'              => $query->renewal_fac_income,
                'renewal_income_special'          => $query->renewal_special_income,
                'renewal_income_treaty'           => $query->renewal_treaty_income,
                'renewal_income_market_expansion' => $query->renewal_market_income,
                'actions'                         => [
                    'handlerId' => $user?->id,
                    'accountPeriod' => $query?->account_period,
                    'recordDate' => Carbon::parse($query?->record_date)->format('Y-m-d')
                ],
            ];
        });

        $formattedData = [];
        foreach ($data as $row) {
            $formattedData[] = [
                'id' => $row->id,
                'handler' => $row->handler,
                'actions' => $row->actions,
                'newGWP' => [
                    'fac' => $row->new_gwp_fac,
                    'special' => $row->new_gwp_special,
                    'treaty' => $row->new_gwp_treaty,
                    'market_expansion' => $row->new_gwp_market_expansion,
                    'total' => $row->new_gwp_fac + $row->new_gwp_special + $row->new_gwp_treaty
                ],
                'newIncome' => [
                    'fac' => $row->new_income_fac,
                    'special' => $row->new_income_special,
                    'treaty' => $row->new_income_treaty,
                    'market_expansion' => $row->new_income_market_expansion,
                    'total' => $row->new_income_fac + $row->new_income_special + $row->new_income_treaty
                ],
                'renewalGWP' => [
                    'fac' => $row->renewal_gwp_fac,
                    'special' => $row->renewal_gwp_special,
                    'treaty' => $row->renewal_gwp_treaty,
                    'market_expansion' => $row->renewal_gwp_market_expansion,
                    'total' => $row->renewal_gwp_fac + $row->renewal_gwp_special + $row->renewal_gwp_treaty
                ],
                'renewalIncome' => [
                    'fac' => $row->renewal_income_fac,
                    'special' => $row->renewal_income_special,
                    'treaty' => $row->renewal_income_treaty,
                    'market_expansion' => $row->renewal_income_market_expansion,
                    'total' => $row->renewal_income_fac + $row->renewal_income_special + $row->renewal_income_treaty
                ]
            ];
        }

        $totals = [
            'newGWP' => ['fac' => 0, 'special' => 0, 'treaty' => 0, 'market_expansion' => 0, 'total' => 0],
            'newIncome' => ['fac' => 0, 'special' => 0, 'treaty' => 0, 'market_expansion' => 0, 'total' => 0],
            'renewalGWP' => ['fac' => 0, 'special' => 0, 'treaty' => 0, 'market_expansion' => 0, 'total' => 0],
            'renewalIncome' => ['fac' => 0, 'special' => 0, 'treaty' => 0, 'market_expansion' => 0, 'total' => 0]
        ];

        foreach ($formattedData as $row) {
            foreach (['newGWP', 'newIncome', 'renewalGWP', 'renewalIncome'] as $category) {
                foreach (['fac', 'special', 'treaty', 'market_expansion', 'total'] as $type) {
                    $totals[$category][$type] += $row[$category][$type];
                }
            }
        }

        $combinedTotalGWP = $totals['newGWP']['total'] + $totals['renewalGWP']['total'];
        $combinedTotalIncome = $totals['newIncome']['total'] + $totals['renewalIncome']['total'];

        return response()->json([
            'data' => $formattedData,
            'totals' => $totals,
            'combinedTotalGWP' => $combinedTotalGWP,
            'combinedTotalIncome' => $combinedTotalIncome
        ]);
    }

    public function changeStatus(Request $request, BudgetAllocation $budget)
    {
        $request->validate([
            'status' => 'required|in:Draft,Active,On Hold,Completed'
        ]);

        try {
            $budget->status = $request->status;
            $budget->updated_by = auth()->id();
            $budget->save();

            return response()->json([
                'success' => true,
                'message' => 'Budget status updated successfully',
                'budget' => $budget
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to update budget status. Please try again.'
            ], 500);
        }
    }

    public function getStaff()
    {
        $staff = User::where('status', 'A')
            ->select('id', 'name')
            ->orderBy('name')
            ->get();

        return response()->json($staff);
    }

    public function storePerformanceRecords(PerformanceRecordRequest $request)
    {
        DB::beginTransaction();
        try {
            $total = $this->budgetService->calculateTotalIncome($request);
            $accountPeriod = $request->input('account_period');
            if (!preg_match('/^\d{4}\/\d{1,2}$/', $accountPeriod)) {
                return response()->json([
                    'status' => 'error',
                    'message' => 'Invalid account period format',
                    'errors' => [
                        'account_period' => ['Account period should be in format YYYY/MM']
                    ]
                ], 422);
            }

            list($year, $month) = explode('/', $accountPeriod);

            $fiscalYear = FiscalYear::where(['is_current' => true, 'year' => $year])->first();
            if (!$fiscalYear) {
                return response()->json([
                    'status' => 'error',
                    'message' => 'Fiscal year not found',
                    'errors' => [
                        'fiscal_year' => ["No current fiscal year found for {$year}"]
                    ]
                ], 422);
            }

            $budgetIncome = BudgetIncome::where('fiscal_year_id', $fiscalYear->id)
                ->where('is_total', true)
                ->where('subcategory', '!=', 'Total Budgeted Income')
                ->sum('amount');

            $budgetIncome = is_numeric($budgetIncome) ? (float)$budgetIncome : 0;

            if ($total > $budgetIncome) {
                $formattedTotal = number_format($total, 2, '.', ',');
                $formattedBudget = number_format($budgetIncome, 2, '.', ',');
                return response()->json([
                    'status' => 'error',
                    'message' => 'Budget limit exceeded',
                    'errors' => [
                        'budget' => ["The total income and GWP <strong>({$formattedTotal})</strong> cannot exceed the budget limit <strong>({$formattedBudget})</strong> for the selected period {$year}/{$month}"]
                    ]
                ], 422);
            }

            $cleanedData = $this->cleanNumericInputs($request);

            $budgetResult = $this->budgetService->checkBudgetConstraint(
                $request->account_period,
                $cleanedData,
                count($request->account_handler)
            );

            if (!$budgetResult['status']) {
                return response()->json([
                    'status' => 'error',
                    'message' => 'Budget constraint exceeded',
                    'errors' => [
                        'budget' => [$budgetResult['message']]
                    ]
                ], 422);
            }

            // $periodUtilization = $this->budgetService->getPeriodUtilization($request->account_period);

            foreach ($request->account_handler as $staffId) {
                PerformanceRecord::create([
                    'user_id' => $staffId,
                    'account_period' => $request->account_period,
                    'record_date' => $request->record_date,

                    'new_fac_gwp' => $cleanedData['new_fac_gwp'] ?? 0,
                    'new_special_gwp' => $cleanedData['new_special_gwp'] ?? 0,
                    'new_treaty_gwp' => $cleanedData['new_treaty_gwp'] ?? 0,
                    'new_market_gwp' => $cleanedData['new_market_gwp'] ?? 0,
                    'new_fac_income' => $cleanedData['new_fac_income'] ?? 0,
                    'new_special_income' => $cleanedData['new_special_income'] ?? 0,
                    'new_treaty_income' => $cleanedData['new_treaty_income'] ?? 0,
                    'new_market_income' => $cleanedData['new_market_income'] ?? 0,

                    'renewal_fac_gwp' => $cleanedData['renewal_fac_gwp'] ?? 0,
                    'renewal_special_gwp' => $cleanedData['renewal_special_gwp'] ?? 0,
                    'renewal_treaty_gwp' => $cleanedData['renewal_treaty_gwp'] ?? 0,
                    'renewal_market_gwp' => $cleanedData['renewal_market_gwp'] ?? 0,
                    'renewal_fac_income' => $cleanedData['renewal_fac_income'] ?? 0,
                    'renewal_special_income' => $cleanedData['renewal_special_income'] ?? 0,
                    'renewal_treaty_income' => $cleanedData['renewal_treaty_income'] ?? 0,
                    'renewal_market_income' => $cleanedData['renewal_market_income'] ?? 0,
                ]);
            }

            DB::commit();

            $updatedBudgetStatus = $this->budgetService->getBudgetUtilizationStatus($request->account_period);
            return response()->json([
                'status' => 'success',
                'message' => 'Performance record(s) saved successfully',
                'budget_status' => $updatedBudgetStatus
            ]);
        } catch (\Exception $e) {
            DB::rollBack();

            return response()->json([
                'status' => 'error',
                'message' => 'Failed to save performance record',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    private function cleanNumericInputs(Request $request)
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

        $cleanedData = [];

        foreach ($fields as $field) {
            $value = $request->input($field);
            if ($value) {
                $cleanedData[$field] = (float) str_replace(',', '', $value);
            } else {
                $cleanedData[$field] = 0;
            }
        }

        return $cleanedData;
    }

    public function getRecords(Request $request)
    {
        $period = $request->input('period', Carbon::now()->format('Y/m'));
        $userId = $request->input('user_id');

        $query = PerformanceRecord::with('user')
            ->where('account_period', $period);

        if ($userId) {
            $query->where('user_id', $userId);
        }

        $records = $query->orderBy('record_date', 'desc')->get();
        $budgetStatus = $this->budgetService->getBudgetUtilizationStatus($period);

        return response()->json([
            'status' => 'success',
            'data' => $records,
            'budget_status' => $budgetStatus
        ]);
    }

    public function budgets(Request $request)
    {
        // logger(json_encode($request->all(), JSON_PRETTY_PRINT));
        return response()->json(['success' => true]);
    }

    public function destroyBudgetAllocation(Request $request)
    {
        $performanceRecord = PerformanceRecord::find($request->id);
        if (!$performanceRecord) {
            return response()->json(['success' => false, 'message' => 'Budget allocation record not found'], 404);
        }
        $performanceRecord->forceDelete();

        $budgetStatus = $this->budgetService->getBudgetUtilizationStatus($performanceRecord->account_period);
        return response()->json([
            'success' => true,
            'message' => 'Performance record deleted successfully',
            'budget_status' => $budgetStatus
        ]);
    }

    public function updatePerformanceRecords(PerformanceRecordRequest $request, $id)
    {
        try {
            DB::beginTransaction();
            $performanceRecord = PerformanceRecord::findOrFail($id);
            $cleanedData = $this->cleanNumericInputs($request);

            $budgetResult = $this->budgetService->checkBudgetConstraint(
                $request->account_period,
                $cleanedData,
                count($request->account_handler)
            );

            if (!$budgetResult['status']) {
                return response()->json([
                    'status' => 'error',
                    'message' => 'Budget constraint exceeded',
                    'errors' => [
                        'budget' => [$budgetResult['message']]
                    ]
                ], 422);
            }

            foreach ($request->account_handler as $staffId) {
                $performanceRecord->update([
                    'user_id' => $staffId,
                    'account_period' => $request->account_period,
                    'record_date' => $request->record_date,
                    ...$cleanedData
                ]);
            }

            DB::commit();

            $updatedBudgetStatus = $this->budgetService->getBudgetUtilizationStatus($request->account_period);
            return response()->json([
                'status' => 'success',
                'message' => 'Performance record(s) updated successfully',
                'budget_status' => $updatedBudgetStatus
            ]);
        } catch (\Exception $e) {
            DB::rollBack();
            return response()->json([
                'success' => false,
                'message' => 'Failed to update performance record',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    public function getFiscalYears(Request $request)
    {
        $search = $request->input('search', '');
        $query = FiscalYear::query();

        if (!empty($search)) {
            $query->where('year', 'like', "%{$search}%");
        }

        $totalCount = $query->count();

        $page = $request->input('page', 1);
        $perPage = 30;
        $fiscalYears = $query->orderBy('year', 'desc')
            ->skip(($page - 1) * $perPage)
            ->take($perPage)
            ->pluck('year');

        $currentYear = date('Y');
        $defaultYear = FiscalYear::where('year', $currentYear)->exists() ? $currentYear : $fiscalYears->first();

        return response()->json([
            'years' => $fiscalYears,
            'total_count' => $totalCount,
            'default_year' => $defaultYear
        ]);
    }

    public function createIncomeBudget()
    {
        $fiscalYears = FiscalYear::orderBy('year', 'desc')->get();

        return view('admin.budgets.create_income_budget', [
            'fiscalYears' => $fiscalYears,
            'type' => 'income'
        ]);
    }

    public function createExpenseBudget()
    {
        $fiscalYears = FiscalYear::orderBy('year', 'desc')->get();

        return view('admin.budgets.create_expense_budget', [
            'fiscalYears' => $fiscalYears,
            'type' => 'income'
        ]);
    }

    public function storeIncomeBudget(Request $request)
    {
        try {
            $request->validate([
                'fiscal_year_id' => 'required|exists:fiscal_years,id',
                'items' => 'required|array|min:1',
                'items.*.category' => 'required|string|max:255',
                'items.*.subcategory' => 'required|string|max:255',
                'items.*.amount' => 'required|numeric|min:0',
                'items.*.is_total' => 'sometimes|nullable|boolean',
            ]);

            // $company = auth()->user()->company;
            $fiscalYearId = $request->fiscal_year_id;
            $company = Company::first();

            DB::beginTransaction();
            foreach ($request->items as $item) {
                BudgetIncome::create([
                    'company_id' => $company->company_id,
                    'fiscal_year_id' => $fiscalYearId,
                    'category' => $item['category'],
                    'subcategory' => $item['subcategory'],
                    'amount' => $item['amount'],
                    'is_total' => isset($item['is_total']) ? 1 : 0,
                    'created_by' => auth()->id(),
                ]);
            }

            DB::commit();
            return response()->json([
                'success' => true,
                'message' => 'Budget income created successfully',
            ]);
        } catch (\Illuminate\Validation\ValidationException $e) {
            return response()->json([
                'success' => false,
                'message' => 'Validation failed',
                'errors' => $e->errors()
            ], 422);
        } catch (\Exception $e) {
            DB::rollBack();
            return response()->json([
                'success' => false,
                'message' => 'Failed to create budget income',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    public function storeExpenseBudget(Request $request)
    {
        try {
            $request->validate([
                'fiscal_year_id' => 'required|exists:fiscal_years,id',
                'items' => 'required|array|min:1',
                'items.*.category' => 'required|string|max:255',
                'items.*.subcategory' => 'required|string|max:255',
                'items.*.amount' => 'required|numeric|min:0',
                'items.*.is_total' => 'sometimes|nullable|boolean',
            ]);

            // $company = auth()->user()->company;
            $fiscalYearId = $request->fiscal_year_id;
            $company = Company::first();

            DB::beginTransaction();
            foreach ($request->items as $item) {
                BudgetExpense::create([
                    'company_id' => $company->company_id,
                    'fiscal_year_id' => $fiscalYearId,
                    'category' => $item['category'],
                    'subcategory' => $item['subcategory'],
                    'amount' => $item['amount'],
                    'is_total' => isset($item['is_total']) ? 1 : 0,
                    'created_by' => auth()->id(),
                ]);
            }

            DB::commit();
            return response()->json([
                'success' => true,
                'message' => 'Budget expense created successfully',
            ]);
        } catch (\Illuminate\Validation\ValidationException $e) {
            return response()->json([
                'success' => false,
                'message' => 'Validation failed',
                'errors' => $e->errors()
            ], 422);
        } catch (\Exception $e) {
            DB::rollBack();
            return response()->json([
                'success' => false,
                'message' => 'Failed to create budget expense',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    public function viewImport()
    {
        $fiscalYears = FiscalYear::orderBy('year', 'desc')->get();

        return view('admin.budgets.import_budget', [
            'fiscalYears' => $fiscalYears,
            'type' => 'import'
        ]);
    }

    public function showImportForm()
    {
        $fiscalYears = FiscalYear::orderBy('year', 'desc')->get();

        return view('admin.budgets.import_budget', [
            'fiscalYears' => $fiscalYears,
            'type' => 'import'
        ]);
    }

    public function downloadTemplate()
    {
        return Excel::download(new BudgetStatementExport(), 'budget_allocation_statement_template.xlsx');
    }

    /**
     * Validate data from Excel file
     */
    public function validateImport(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'fiscal_year_id' => 'required|exists:fiscal_years,id',
            'excel_file' => 'required|file|mimes:xlsx,xls|max:5120', // Max 5MB
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'errors' => $validator->errors()->all()
            ]);
        }

        logger($request->all());
        return response()->json([
            'success' => true,
            'rows' => 12
        ]);
        // try {
        //     // Read the Excel file
        //     $excelFile = $request->file('excel_file');
        //     $spreadsheet = IOFactory::load($excelFile);
        //     $worksheet = $spreadsheet->getActiveSheet();
        //     $rows = $worksheet->toArray();

        //     // Remove header row
        //     $header = array_shift($rows);

        //     // Validate headers
        //     $expectedHeaders = ['Category', 'Subcategory', 'Amount (KES)'];
        //     $headerValid = count(array_intersect($header, $expectedHeaders)) === count($expectedHeaders);

        //     if (!$headerValid) {
        //         return response()->json([
        //             'success' => false,
        //             'errors' => ['The Excel file does not have the expected column headers. Please download and use the provided template.']
        //         ]);
        //     }

        //     // Validate and format data
        //     $validatedRows = [];
        //     $errors = [];

        //     foreach ($rows as $index => $row) {
        //         $rowNumber = $index + 2; // +2 because of 0-indexed array and header row

        //         // Skip empty rows
        //         if (empty($row[0]) && empty($row[1]) && empty($row[2])) {
        //             continue;
        //         }

        //         // Check required fields
        //         if (empty($row[0])) {
        //             $errors[] = "Row {$rowNumber}: Category is required";
        //             continue;
        //         }

        //         if (empty($row[1])) {
        //             $errors[] = "Row {$rowNumber}: Subcategory is required";
        //             continue;
        //         }

        //         // Validate amount
        //         if (!is_numeric($row[2])) {
        //             $errors[] = "Row {$rowNumber}: Amount must be a number";
        //             continue;
        //         }

        //         $validatedRows[] = [
        //             'category' => $row[0],
        //             'subcategory' => $row[1],
        //             'amount' => (float) $row[2],
        //             'valid' => true,
        //             'error' => null
        //         ];
        //     }

        //     if (count($errors) > 0) {
        //         return response()->json([
        //             'success' => false,
        //             'errors' => $errors
        //         ]);
        //     }

        //     return response()->json([
        //         'success' => true,
        //         'rows' => $validatedRows
        //     ]);
        // } catch (\Exception $e) {
        //     return response()->json([
        //         'success' => false,
        //         'errors' => ['An error occurred while processing the Excel file: ' . $e->getMessage()]
        //     ]);
        // }
    }

    /**
     * Start the import process
     */
    public function startImport(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'fiscal_year_id' => 'required|exists:fiscal_years,id',
            'excel_file' => 'required|file|mimes:xlsx,xls|max:5120', // Max 5MB
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'message' => 'Validation failed',
                'errors' => $validator->errors()->all()
            ]);
        }

        try {
            $excelFile = $request->file('excel_file');
            $fileName = 'income_import_' . Str::random(10) . '.' . $excelFile->getClientOriginalExtension();
            $filePath = $excelFile->storeAs('temp', $fileName);

            $jobId = Str::uuid()->toString();

            $spreadsheet = IOFactory::load(Storage::path($filePath));
            $worksheet = $spreadsheet->getActiveSheet();
            $totalRows = $worksheet->getHighestRow() - 1; // Subtract header row

            Cache::put("import_job_{$jobId}_status", [
                'status' => 'pending',
                'status_message' => 'Import job queued.',
                'processed_rows' => 0,
                'total_rows' => $totalRows,
                'messages' => [],
                'file_path' => $filePath,
                'fiscal_year_id' => $request->input('fiscal_year_id'),
                'overwrite_existing' => $request->has('overwrite_existing'),
                'started_at' => now()
            ], 3600);

            // Dispatch the import job to the queue
            // ProcessIncomeImport::dispatch($jobId, $filePath, $request->input('fiscal_year_id'), $request->has('overwrite_existing'));

            return response()->json([
                'success' => true,
                'job_id' => $jobId,
                'message' => 'Import job started',
                'total_rows' => $totalRows
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'An error occurred while starting the import: ' . $e->getMessage()
            ]);
        }
    }

    /**
     * Get import progress
     */
    public function getImportProgress($jobId)
    {
        $progressData = Cache::get("import_job_{$jobId}_status");

        logger($progressData);

        if (!$progressData) {
            return response()->json([
                'success' => false,
                'message' => 'Import job not found'
            ], 404);
        }

        return response()->json($progressData);
    }

    /**
     * Cancel an ongoing import
     */
    public function cancelImport(Request $request, $jobId)
    {
        $progressData = Cache::get("import_job_{$jobId}_status");

        if (!$progressData) {
            return response()->json([
                'success' => false,
                'message' => 'Import job not found'
            ], 404);
        }

        // Update status in cache
        $progressData['status'] = 'cancelled';
        $progressData['status_message'] = 'Import cancelled by user.';
        Cache::put("import_job_{$jobId}_status", $progressData, 3600);

        // Clean up stored file if exists
        if (!empty($progressData['file_path']) && Storage::exists($progressData['file_path'])) {
            Storage::delete($progressData['file_path']);
        }

        return response()->json([
            'success' => true,
            'message' => 'Import cancelled successfully'
        ]);
    }

    /**
     * Resume a paused import
     */
    public function resumeImport(Request $request, $jobId)
    {
        $progressData = Cache::get("import_job_{$jobId}_status");

        if (!$progressData) {
            return response()->json([
                'success' => false,
                'message' => 'Import job not found'
            ], 404);
        }

        // Only resume if paused
        if ($progressData['status'] !== 'paused') {
            return response()->json([
                'success' => false,
                'message' => 'Import job is not paused'
            ], 400);
        }

        // Update status and re-dispatch job
        $progressData['status'] = 'processing';
        $progressData['status_message'] = 'Import resumed.';
        Cache::put("import_job_{$jobId}_status", $progressData, 3600);

        // Re-dispatch the job from the current position
        // ProcessIncomeImport::dispatch(
        //     $jobId,
        //     $progressData['file_path'],
        //     $progressData['fiscal_year_id'],
        //     $progressData['overwrite_existing'],
        //     $progressData['processed_rows']
        // );

        return response()->json([
            'success' => true,
            'message' => 'Import resumed successfully'
        ]);
    }
}
