<?php

namespace App\Http\Controllers;

use App\Models\BudgetSetup;
use App\Models\FiscalYear;
use App\Models\User;
use App\Models\UserBudgetSetup;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Validator;
use Symfony\Component\HttpFoundation\Response;
use Yajra\DataTables\Facades\DataTables;
use Illuminate\Database\Eloquent\ModelNotFoundException;

class BudgetSetupController extends Controller
{
    /**
     * Display the budget setup settings page.
     */
    public function index(Request $request)
    {
        $fiscalYears = FiscalYear::orderByDesc('year')->get();

        return view('settings.finance.budget_setup', [
            'user' => $request->user(),
            'fiscalYears' => $fiscalYears,
        ]);
    }

    /**
     * Return DataTable JSON for budget setups.
     */
    public function data()
    {
        $budgetSetups = BudgetSetup::select(['id', 'budget_year', 'budget_category', 'status']);

        return DataTables::of($budgetSetups)
            ->addColumn('action', function ($row) {
                return '<button class="btn btn-outline-primary btn-sm" id="edit_budget_setup">Edit</button>
                    <button class="btn btn-outline-danger btn-sm" id="delete_budget_setup">Delete</button>';
            })
            ->rawColumns(['action'])
            ->make(true);
    }

    /**
     * Store a new budget setup.
     */
    public function store(Request $request)
    {
        try {
            $validator = Validator::make($request->all(), [
                'budget_year' => 'required|integer|min:2000|max:2100',
                'budget_category' => 'required|string|max:150',
                'status' => 'required|in:A,D',
            ]);

            if ($validator->fails()) {
                if ($request->ajax() || $request->wantsJson()) {
                    return response()->json([
                        'success' => false,
                        'message' => 'Validation failed',
                        'errors' => $validator->errors(),
                    ], Response::HTTP_UNPROCESSABLE_ENTITY);
                }

                return redirect()->back()
                    ->withErrors($validator)
                    ->withInput();
            }

            // Check for duplicate
            $exists = BudgetSetup::where('budget_year', $request->budget_year)
                ->where('budget_category', $request->budget_category)
                ->exists();

            if ($exists) {
                return response()->json([
                    'success' => false,
                    'message' => 'A budget setup with this year and category already exists.',
                ], Response::HTTP_CONFLICT);
            }

            BudgetSetup::create([
                'budget_year' => $request->budget_year,
                'budget_category' => $request->budget_category,
                'status' => $request->status,
                'created_by' => Auth::id(),
            ]);

            if ($request->ajax() || $request->wantsJson()) {
                return response()->json([
                    'success' => true,
                    'message' => 'Budget setup saved successfully',
                ]);
            }

            return redirect()->back()->with('success', 'Budget setup saved successfully');
        } catch (\Throwable $e) {
            if ($request->ajax() || $request->wantsJson()) {
                return response()->json([
                    'success' => false,
                    'message' => 'An error occurred while saving the budget setup.',
                ], Response::HTTP_INTERNAL_SERVER_ERROR);
            }
            throw $e;
        }
    }

    /**
     * Update an existing budget setup.
     */
    public function update(Request $request)
    {
        try {
            $validator = Validator::make($request->all(), [
                'ed_id' => 'required|integer|exists:budget_setups,id',
                'ed_budget_year' => 'required|integer|min:2000|max:2100',
                'ed_budget_category' => 'required|string|max:150',
                'ed_status' => 'required|in:A,D',
            ]);

            if ($validator->fails()) {
                if ($request->ajax() || $request->wantsJson()) {
                    return response()->json([
                        'success' => false,
                        'message' => 'Validation failed',
                        'errors' => $validator->errors(),
                    ], Response::HTTP_UNPROCESSABLE_ENTITY);
                }

                return redirect()->back()
                    ->withErrors($validator)
                    ->withInput();
            }

            // Check for duplicate (excluding current record)
            $exists = BudgetSetup::where('budget_year', $request->ed_budget_year)
                ->where('budget_category', $request->ed_budget_category)
                ->where('id', '!=', $request->ed_id)
                ->exists();

            if ($exists) {
                return response()->json([
                    'success' => false,
                    'message' => 'A budget setup with this year and category already exists.',
                ], Response::HTTP_CONFLICT);
            }

            $budgetSetup = BudgetSetup::findOrFail($request->ed_id);
            $budgetSetup->budget_year = $request->ed_budget_year;
            $budgetSetup->budget_category = $request->ed_budget_category;
            $budgetSetup->status = $request->ed_status;
            $budgetSetup->updated_by = Auth::id();
            $budgetSetup->save();

            if ($request->ajax() || $request->wantsJson()) {
                return response()->json([
                    'success' => true,
                    'message' => 'Budget setup updated successfully',
                ]);
            }

            return redirect()->back()->with('success', 'Budget setup updated successfully');
        } catch (ModelNotFoundException $exception) {
            if ($request->ajax() || $request->wantsJson()) {
                return response()->json([
                    'success' => false,
                    'message' => 'Budget setup not found.',
                ], Response::HTTP_NOT_FOUND);
            }

            return redirect()->back()->with('error', 'Budget setup not found.');
        } catch (\Throwable $e) {
            if ($request->ajax() || $request->wantsJson()) {
                return response()->json([
                    'success' => false,
                    'message' => 'An error occurred while updating the budget setup.',
                ], Response::HTTP_INTERNAL_SERVER_ERROR);
            }
            throw $e;
        }
    }

    /**
     * Soft-delete a budget setup.
     */
    public function destroy(Request $request)
    {
        try {
            $validator = Validator::make($request->all(), [
                'del_id' => 'required|integer|exists:budget_setups,id',
            ]);

            if ($validator->fails()) {
                return response()->json([
                    'success' => false,
                    'message' => 'Invalid budget setup specified.',
                ], Response::HTTP_UNPROCESSABLE_ENTITY);
            }

            $budgetSetup = BudgetSetup::findOrFail($request->del_id);
            $budgetSetup->delete();

            return response()->json([
                'success' => true,
                'message' => 'Budget setup deleted successfully',
            ]);
        } catch (ModelNotFoundException $exception) {
            return response()->json([
                'success' => false,
                'message' => 'Budget setup not found.',
            ], Response::HTTP_NOT_FOUND);
        } catch (\Throwable $e) {
            return response()->json([
                'success' => false,
                'message' => 'An error occurred while deleting the budget setup.',
            ], Response::HTTP_INTERNAL_SERVER_ERROR);
        }
    }

    /**
     * Copy budget setups from a source fiscal year to a target year.
     */
    public function copyFromFiscalYear(Request $request)
    {
        try {
            $validator = Validator::make($request->all(), [
                'source_year' => 'required|integer|min:2000|max:2100',
                'target_year' => 'required|integer|min:2000|max:2100',
            ]);

            if ($validator->fails()) {
                return response()->json([
                    'success' => false,
                    'message' => 'Validation failed',
                    'errors' => $validator->errors(),
                ], Response::HTTP_UNPROCESSABLE_ENTITY);
            }

            $sourceYear = $request->source_year;
            $targetYear = $request->target_year;

            if ($sourceYear == $targetYear) {
                return response()->json([
                    'success' => false,
                    'message' => 'Source and target years must be different.',
                ], Response::HTTP_UNPROCESSABLE_ENTITY);
            }

            $sourceSetups = BudgetSetup::where('budget_year', $sourceYear)->get();

            if ($sourceSetups->isEmpty()) {
                return response()->json([
                    'success' => false,
                    'message' => "No budget setups found for year {$sourceYear}.",
                ], Response::HTTP_NOT_FOUND);
            }

            $copied = 0;
            $skipped = 0;

            foreach ($sourceSetups as $setup) {
                $exists = BudgetSetup::where('budget_year', $targetYear)
                    ->where('budget_category', $setup->budget_category)
                    ->exists();

                if ($exists) {
                    $skipped++;
                    continue;
                }

                BudgetSetup::create([
                    'budget_year' => $targetYear,
                    'budget_category' => $setup->budget_category,
                    'status' => $setup->status,
                    'created_by' => Auth::id(),
                ]);

                $copied++;
            }

            $message = "{$copied} budget setup(s) copied to {$targetYear}.";
            if ($skipped > 0) {
                $message .= " {$skipped} duplicate(s) skipped.";
            }

            return response()->json([
                'success' => true,
                'message' => $message,
                'copied' => $copied,
                'skipped' => $skipped,
            ]);
        } catch (\Throwable $e) {
            return response()->json([
                'success' => false,
                'message' => 'An error occurred while copying budget setups.',
            ], Response::HTTP_INTERNAL_SERVER_ERROR);
        }
    }

    // =====================================================
    // User Budget Setup Methods
    // =====================================================

    /**
     * Display the user budget setup page.
     */
    public function userSetupIndex(Request $request)
    {
        $budgetCategories = BudgetSetup::where('status', 'A')
            ->orderByDesc('budget_year')
            ->get(['id', 'budget_year', 'budget_category']);

        return view('settings.finance.user_budget_setup', [
            'user' => $request->user(),
            'budgetCategories' => $budgetCategories,
        ]);
    }

    /**
     * DataTable JSON for users with budget setup status.
     */
    public function userSetupData()
    {
        $users = User::where('users.status', 'A')
            ->leftJoin('company_department', DB::raw('CAST(users.department_id AS BIGINT)'), '=', 'company_department.id')
            ->select(['users.id', 'users.name', 'company_department.department_name', 'users.designation'])
            ->orderBy('users.name');

        return DataTables::of($users)
            ->addColumn('department', function ($row) {
                return $row->department_name ?? '—';
            })
            ->addColumn('user_title', function ($row) {
                return $row->designation ?? '—';
            })
            ->addColumn('setup_status', function ($row) {
                $setup = UserBudgetSetup::where('user_id', $row->id)->first();
                if ($setup) {
                    return '<span class="badge bg-success-transparent text-success">Configured</span>';
                }
                return '<span class="badge bg-warning-transparent text-warning">Pending</span>';
            })
            ->addColumn('action', function ($row) {
                return '<button class="btn btn-primary btn-sm btn-wave" id="user_budget_details" data-user-id="' . $row->id . '">
                            <i class="bx bx-detail me-1"></i>Details
                        </button>';
            })
            ->rawColumns(['setup_status', 'action'])
            ->make(true);
    }

    /**
     * Get a user's budget setup details.
     */
    public function userSetupShow($id)
    {
        try {
            $user = User::leftJoin('company_department', DB::raw('CAST(users.department_id AS BIGINT)'), '=', 'company_department.id')
                ->select(['users.id', 'users.name', 'company_department.department_name', 'users.designation'])
                ->findOrFail($id);
            $setup = UserBudgetSetup::where('user_id', $id)->first();

            return response()->json([
                'success' => true,
                'user' => [
                    'id' => $user->id,
                    'name' => $user->name,
                    'department' => $user->department_name,
                    'title' => $user->designation,
                ],
                'setup' => $setup,
            ]);
        } catch (ModelNotFoundException $e) {
            return response()->json([
                'success' => false,
                'message' => 'User not found.',
            ], Response::HTTP_NOT_FOUND);
        }
    }

    /**
     * Store a new user budget setup.
     */
    public function userSetupStore(Request $request)
    {
        try {
            $validator = Validator::make($request->all(), [
                'user_id' => 'required|integer|exists:users,id',
                'est_production' => 'nullable|numeric|min:0',
                'return_on_investment' => 'nullable|numeric|min:0',
                'sectors' => 'nullable|array',
                'sectors.*' => 'string|max:100',
                'policies' => 'nullable|array',
                'policies.*' => 'string|max:100',
                'categories' => 'nullable|array',
                'categories.*' => 'string|max:150',
            ]);

            if ($validator->fails()) {
                return response()->json([
                    'success' => false,
                    'message' => 'Validation failed',
                    'errors' => $validator->errors(),
                ], Response::HTTP_UNPROCESSABLE_ENTITY);
            }

            // Check if user already has a setup
            $exists = UserBudgetSetup::where('user_id', $request->user_id)->exists();
            if ($exists) {
                return response()->json([
                    'success' => false,
                    'message' => 'User already has a budget setup. Use update instead.',
                ], Response::HTTP_CONFLICT);
            }

            UserBudgetSetup::create([
                'user_id' => $request->user_id,
                'est_production' => $request->est_production ?? 0,
                'return_on_investment' => $request->return_on_investment ?? 0,
                'sectors' => $request->sectors ?? [],
                'policies' => $request->policies ?? [],
                'categories' => $request->categories ?? [],
                'status' => 'A',
                'created_by' => Auth::id(),
            ]);

            return response()->json([
                'success' => true,
                'message' => 'User budget setup saved successfully.',
            ]);
        } catch (\Throwable $e) {
            return response()->json([
                'success' => false,
                'message' => 'An error occurred while saving the user budget setup.',
            ], Response::HTTP_INTERNAL_SERVER_ERROR);
        }
    }

    /**
     * Update an existing user budget setup.
     */
    public function userSetupUpdate(Request $request)
    {
        try {
            $validator = Validator::make($request->all(), [
                'user_id' => 'required|integer|exists:users,id',
                'est_production' => 'nullable|numeric|min:0',
                'return_on_investment' => 'nullable|numeric|min:0',
                'sectors' => 'nullable|array',
                'sectors.*' => 'string|max:100',
                'policies' => 'nullable|array',
                'policies.*' => 'string|max:100',
                'categories' => 'nullable|array',
                'categories.*' => 'string|max:150',
            ]);

            if ($validator->fails()) {
                return response()->json([
                    'success' => false,
                    'message' => 'Validation failed',
                    'errors' => $validator->errors(),
                ], Response::HTTP_UNPROCESSABLE_ENTITY);
            }

            $setup = UserBudgetSetup::where('user_id', $request->user_id)->first();

            if (!$setup) {
                // Auto-create if it doesn't exist
                UserBudgetSetup::create([
                    'user_id' => $request->user_id,
                    'est_production' => $request->est_production ?? 0,
                    'return_on_investment' => $request->return_on_investment ?? 0,
                    'sectors' => $request->sectors ?? [],
                    'policies' => $request->policies ?? [],
                    'categories' => $request->categories ?? [],
                    'status' => 'A',
                    'created_by' => Auth::id(),
                ]);
            } else {
                $setup->est_production = $request->est_production ?? 0;
                $setup->return_on_investment = $request->return_on_investment ?? 0;
                $setup->sectors = $request->sectors ?? [];
                $setup->policies = $request->policies ?? [];
                $setup->categories = $request->categories ?? [];
                $setup->updated_by = Auth::id();
                $setup->save();
            }

            return response()->json([
                'success' => true,
                'message' => 'User budget setup updated successfully.',
            ]);
        } catch (\Throwable $e) {
            return response()->json([
                'success' => false,
                'message' => 'An error occurred while updating the user budget setup.',
            ], Response::HTTP_INTERNAL_SERVER_ERROR);
        }
    }
}
