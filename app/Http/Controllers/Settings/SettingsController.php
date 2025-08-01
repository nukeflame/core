<?php

namespace App\Http\Controllers\Settings;

use App\Models\Company;
use App\Models\Country;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use App\Http\Controllers\Controller;
use App\Models\Department;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Support\Facades\Session;
use Illuminate\Support\Facades\Validator;
use Yajra\DataTables\Facades\DataTables;

class SettingsController extends Controller
{
    public function companyInfo()
    {
        $company = Company::where('company_id', 1)->firstOrFail();
        $country = Country::where('country_iso', $company->country_code)->first();
        $countries = Country::all();

        return view('settings.company_info', [
            'company' => $company,
            'country' => $country,
            'countries' => $countries,
        ]);
    }

    public function generalConfig(Request $request)
    {
        return view('settings.general_config', []);
    }

    public function departments(Request $request)
    {
        $departments = Department::where('status', 'A')->get();
        $companies = Company::where('active', 'Y')->get();
        $employees = User::whereHas('role', function ($query) {
            $query->whereNot('slug', 'super_admin');
        })->get();

        $locations =  [
            (object)['id' => 1, 'name' => 'Headquarters', 'slug' => 'headquarters']
        ];

        return view('settings.departments', compact('departments', 'companies', 'employees', 'locations'));
    }

    public function departmentsDatatable()
    {
        $departments = Department::query()->orderBy('created_at', 'desc');
        return DataTables::of($departments)
            ->addColumn('status', function ($data) {
                return $data->status === 'A' ? '<span class="badge bg-success">Active</span>' : '<span class="badge bg-danger">Inactive</span>';
            })
            ->addColumn('annual_budget', function ($data) {
                return number_format($data->budget, 2) ?? 0;
            })
            ->addColumn('parent_department', function ($data) {
                return '_';
            })
            ->addColumn('location', function ($data) {
                return 'Headquarters';
            })
            ->addColumn('start_date', function ($data) {
                return $data->start_date ? Carbon::parse($data->start_date)->format('Y-m-d') : '_';
            })
            ->addColumn('email', function ($data) {
                return $data->email ?? '_';
            })
            ->addColumn('action', function ($data) {
                $btn = '';
                $btn .= "<button class='btn btn-primary btn-actions btn-sm edit me-2' data-data='$data' data-bs-toggle='modal' data-bs-target='#departmentModal'>
                            <i class='bi bi-pencil'></i> Edit
                        </button>";

                if ($data?->department_code != 2030) {
                    $btn .= "<button class='btn btn-danger btn-actions btn-sm delete'  data-data='$data'> <i class='bi bi-trash'></i> Delete</button>";
                }

                return $btn;
            })
            ->rawColumns(['action', 'status'])
            ->make(true);
    }

    public function storeDepartment(Request $request)
    {
        $validated = $request->validate([
            'company_id' => 'required|exists:companies,company_id',
            'code' => 'required|string|max:20|unique:company_department,department_code',
            'name' => 'required|string|max:100',
            'description' => 'nullable|string',
            'cost_center' => 'nullable|string|max:20',
            'budget' => 'nullable|string|max:50',
            'parent_id' => 'nullable|exists:company_department,id',
            'location_id' => 'nullable',
            'department_head_id' => 'nullable|exists:users,id',
            'start_date' => 'nullable|date',
            'email' => 'nullable|email|max:100',
            'status' => 'required|in:active,inactive,planned',
        ]);

        try {
            DB::beginTransaction();
            if ($validated) {
                $status = 'A';
                switch ($validated) {
                    case 'active':
                        $status = 'A';
                        break;
                    case 'inactive':
                        $status = 'A';
                        break;
                    case 'planned':
                        $status = 'P';
                        break;
                }

                Department::create(
                    [
                        'company_id' => $request->company_id,
                        'department_code' => $request->code,
                        'department_name' => $request->name,
                        'description' => $request->description,
                        'cost_center' => $request->cost_center,
                        'budget' => (float) str_replace(',', '', $request->budget) ?? 0,
                        'parent_id' => $request->parent_id,
                        'location_id' => $request->location_id,
                        'department_head_id' => $request->department_head_id,
                        'start_date' => $request->start_date,
                        'email' => $request->email,
                        'status' =>  $status
                    ]
                );
            }
            DB::commit();

            return response()->json([
                'status' => 'success',
                'message' => 'Department created successfully'
            ], 200);
        } catch (\Exception $e) {
            DB::rollback();
            return response()->json([
                'status' => 'error',
                'message' => 'Failed to create department: ' . $e->getMessage()
            ], 500);
        }
    }

    public function validateDepartmentCode(Request $request)
    {
        $exists = Department::where('code', $request->code)->exists();
        return response()->json([
            'valid' => !$exists
        ]);
    }

    public function CompanyEdit(Request $request)
    {
        $pip = Company::where('company_id', 1)->first();

        // dd($pip);
        $pip->company_name = $request->get('company_name');
        $pip->postal_address = $request->get('addr1');
        $pip->postal_code = $request->get('addr2');
        $pip->postal_city = $request->get('addr3');
        $pip->email = $request->get('addr4');
        $pip->mobilephone = $request->get('telno');
        $pip->email = $request->get('email');
        $pip->country_code = $request->get('country_code');
        // updated 5.11.09 > end

        DB::beginTransaction();

        try {
            $pip->save();
            DB::commit();


            Session::flash('success', 'Details Editted Successfully');
            return redirect()->route('settings.company_info');
        } catch (\Throwable $e) {
            dd($e);
            DB::rollback();



            Session::flash('error', 'Details Editting Failed');
            return redirect()->route('settings.company_info');
        }
    }
}
