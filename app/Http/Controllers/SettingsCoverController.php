<?php

namespace App\Http\Controllers;

use App\Models\Branch;
use App\Models\Classes;
use App\Models\Country;
use App\Models\WhtRate;
use App\Models\PayMethod;
use App\Models\ClassGroup;
use App\Models\ReinsClass;
use App\Models\TreatyType;
use App\Models\BinderCover;
use App\Models\ClauseParam;
use App\Models\BusinessType;
use Illuminate\Http\Request;
use App\Models\CustomerTypes;
use App\Models\FinancePeriod;
use App\Models\ReinsDivision;
use App\Models\TypeOfSumInsured;
use Yajra\DataTables\DataTables;
use App\Models\ReinclassPremtype;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Session;
use Illuminate\Support\Facades\Validator;
use Symfony\Component\HttpFoundation\Response;
use Illuminate\Database\Eloquent\ModelNotFoundException;

class SettingsCoverController extends Controller
{
    // BRANCHES ==================================================================================================
    public function BranchInfo(Request $request)
    {
        return view('settings.cover.branch', [
            'user' => $request->user(),
        ]);
    }

    public function BranchData()
    {
        $Branch = Branch::select(['branch_code', 'branch_name', 'status']);
        return DataTables::Of($Branch)
            ->addColumn('action', function ($row) {
                if ($row->status === 'A') {
                    $status = 'DeActivate';
                } else {
                    $status = 'Activate';
                }
                return '<button class="btn btn-outline-primary btn-sm" id="edit_branch">Edit</button>
                    <button class="btn btn-outline-primary btn-sm" id="activate_branch" value="' . $status . '">' . $status . '</button>';
            })->make(true);
    }

    public function BranchAddData(Request $request)
    {
        try {
            $validator = Validator::make($request->all(), [
                'branch_code' => 'required|int|max:5',
                'branch_name' => 'required|string|max:100'
            ]);
            if ($validator) {
                // If the validation passes, you can proceed to store the data in the database or perform other actions
                Branch::create(
                    [
                        'branch_code' => $request->branch_code,
                        'branch_name' => $request->branch_name,
                        'status' => "A",
                    ]
                );
                // Redirect or return a response as needed
                return redirect('/settings/cover/branch')->with('success', 'Branch information saved successfully');
            } else {
                Session::flash('error', 'some field are missing');
                return [
                    'code' => -1,
                    'msg' => $validator->errors(),
                ];
            }
        } catch (\Throwable $e) {
            throw $e;
        }
    }

    public function BranchEditData(Request $request)
    {
        try {
            $Branch = Branch::findOrFail($request->ed_branch_code);
            $Branch->branch_name = $request->input('ed_branch_name');
            $Branch->save();
            // Redirect or return a response as needed
            return redirect('/settings/cover/branch')->with('success', 'branch information saved successfully');
        } catch (ModelNotFoundException $exception) {
            return redirect('/settings/cover/branch')->with('error', 'Specified code was not found.');
        } catch (\Throwable $e) {
            throw $e;
        }
    }

    public function BranchDeleteData(Request $request)
    {
        try {
            $Branch = Branch::findOrFail($request->del_branch_code);
            if ($Branch->status == "A") {
                $status = "D";
            } else {
                $status = "A";
            }
            $Branch->update([
                'status' => $status,
            ]);
            // Redirect or return a response as needed
            return redirect('/settings/cover/branch')->with('success', 'branch deleted successfully');
        } catch (ModelNotFoundException $exception) {
            return redirect('/settings/cover/branch')->with('error', 'Specified code was not found.');
        } catch (\Throwable $e) {
            throw $e;
        }
    }

    // CLASS GROUPS ==================================================================================================
    public function ClassGroupInfo(Request $request)
    {
        return view('settings.cover.classGroup', [
            'user' => $request->user(),
        ]);
    }

    public function ClassGroupData()
    {
        $ClassGroup = ClassGroup::select(['group_code', 'group_name', 'status']);
        return DataTables::Of($ClassGroup)
            ->addColumn('action', function ($row) {
                if ($row->status === 'A') {
                    $status = 'De Activate';
                } else {
                    $status = 'Activate';
                }
                return '<button class="btn btn-outline-primary btn-sm" id="edit_classGroup">Edit</button>
                    <button class="btn btn-outline-primary btn-sm" id="activate_classGroup" value="' . $status . '">' . $status . '</button>';
            })->make(true);
    }

    public function ClassGroupAddData(Request $request)
    {
        try {
            $validator = Validator::make($request->all(), [
                'group_code' => 'required|int|max:5',
                'group_name' => 'required|string|max:100'
            ]);
            if ($validator) {
                // If the validation passes, you can proceed to store the data in the database or perform other actions
                ClassGroup::create(
                    [
                        'group_code' => $request->group_code,
                        'group_name' => $request->group_name,
                        'status' => "A",
                    ]
                );
                // Redirect or return a response as needed
                return redirect('/settings/cover/classGroup')->with('success', 'class group information saved successfully');
            } else {
                Session::flash('error', 'some field are missing');
                return [
                    'code' => -1,
                    'msg' => $validator->errors(),
                ];
            }
        } catch (\Throwable $e) {
            throw $e;
        }
    }

    public function ClassGroupEditData(Request $request)
    {
        try {
            $ClassGroup = ClassGroup::findOrFail($request->ed_group_code);
            $ClassGroup->group_name = $request->input('ed_group_name');
            $ClassGroup->save();
            // Redirect or return a response as needed
            return redirect('/settings/cover/classGroup')->with('success', 'class group information saved successfully');
        } catch (ModelNotFoundException $exception) {
            return redirect('/settings/cover/classGroup')->with('error', 'Specified code was not found.');
        } catch (\Throwable $e) {
            throw $e;
        }
    }

    public function ClassGroupDeleteData(Request $request)
    {
        try {
            $ClassGroup = ClassGroup::findOrFail($request->del_group_code);
            if ($ClassGroup->status == "A") {
                $status = "D";
            } else {
                $status = "A";
            }
            $ClassGroup->update([
                'status' => $status,
            ]);
            // Redirect or return a response as needed
            return redirect('/settings/cover/classGroup')->with('success', 'class group deleted successfully');
        } catch (ModelNotFoundException $exception) {
            return redirect('/settings/cover/classGroup')->with('error', 'Specified code was not found.');
        } catch (\Throwable $e) {
            throw $e;
        }
    }

    // CLASSES ==================================================================================================
    public function ClassInfo(Request $request)
    {
        return view('settings.cover.class', [
            'user' => $request->user(),
            'clGrps' => ClassGroup::all(),
        ]);
    }

    public function ClassData()
    {
        $Classes = Classes::select(['class_code', 'class_name', 'combined', 'class_group_code', 'status']);
        return DataTables::Of($Classes)
            ->addColumn('action', function ($row) {
                if ($row->status == 'A') {
                    $status = 'De Activate';
                } else {
                    $status = 'Activate';
                }
                return '<button class="btn btn-outline-primary btn-sm" id="edit_class">Edit</button>
                            <button class="btn btn-outline-primary btn-sm" id="activate_class" value="' . $status . '">' . $status . '</button>';
            })->make(true);
    }

    public function ClassAddData(Request $request)
    {
        try {
            $validator = Validator::make($request->all(), [
                'class_code' => 'required|int|max:5',
                'class_name' => 'required|string|max:100'
            ]);
            if ($validator) {
                // If the validation passes, you can proceed to store the data in the database or perform other actions
                Classes::create(
                    [
                        'class_code' => $request->class_code,
                        'class_name' => $request->class_name,
                        'class_group_code' => $request->class_group_code,
                        'status' => "A",
                    ]
                );
                // Redirect or return a response as needed
                return redirect('/settings/cover/class')->with('success', 'class information saved successfully');
            } else {
                Session::flash('error', 'some field are missing');
                return [
                    'code' => -1,
                    'msg' => $validator->errors(),
                ];
            }
        } catch (\Throwable $e) {
            throw $e;
        }
    }

    public function ClassEditData(Request $request)
    {
        try {
            $Classes = Classes::findOrFail($request->ed_class_code);
            $Classes->class_name = $request->input('ed_class_name');
            $Classes->combined = $request->input('ed_combined');
            $Classes->class_group_code = $request->input('ed_class_group_code');
            $Classes->save();
            // Redirect or return a response as needed
            return redirect('/settings/cover/class')->with('success', 'class information saved successfully');
        } catch (ModelNotFoundException $exception) {
            return redirect('/settings/cover/class')->with('error', 'Specified code was not found.');
        } catch (\Throwable $e) {
            throw $e;
        }
    }

    public function ClassDeleteData(Request $request)
    {
        try {
            $Classes = Classes::findOrFail($request->del_class_code);
            if ($Classes->status == "A") {
                $status = "D";
            } else {
                $status = "A";
            }
            $Classes->update([
                'status' => $status,
            ]);
            // Redirect or return a response as needed
            return redirect('/settings/cover/class')->with('success', 'class deleted successfully');
        } catch (ModelNotFoundException $exception) {
            return redirect('/settings/cover/class')->with('error', 'Specified code was not found.');
        } catch (\Throwable $e) {
            throw $e;
        }
    }
    // CLASS CLAUSES ==================================================================================================
    public function classClausesInfo(Request $request)
    {
        return view('settings.cover.classClauses', [
            'user' => $request->user(),
            'classes' => Classes::all(),
        ]);
    }

    public function classClausesData()
    {
        $clauses = ClauseParam::select(['clause_id', 'class_code','clause_title', 'clause_wording', 'status'])->orderBy('clause_id','asc');
        return DataTables::Of($clauses)
            ->addColumn('class_name', function ($row) {
                $class = Classes::where('class_code',$row->class_code)->first();
                return $class->class_name;
            })
            ->addColumn('action', function ($row) {
                if ($row->status == 'A') {
                    $status = 'De Activate';
                } else {
                    $status = 'Activate';
                }
                return '<button class="btn btn-outline-primary btn-sm" id="edit_clause">Edit</button>
                            <button class="btn btn-outline-primary btn-sm" id="activate_clause" value="' . $status . '">' . $status . '</button>';
            })->make(true);
    }

    public function classClausesAddData(Request $request)
    {
        try {
            $validator = Validator::make($request->all(), [
                'clause_id' => 'required|string|max:20',
                'clause_title' => 'required|string|max:300',
                'class_code' => 'required|string|max:20'
            ]);
            if ($validator) {
                $id = ClauseParam::max('clause_id') + 1;
                ClauseParam::create(
                    [
                        'class_code' => $request->class_code,
                        'clause_id' => $id,
                        'clause_title' => $request->clause_title,
                        'clause_wording' => $request->clause_wording,
                        'status' => "A",
                        'created_by' => Auth::user()->user_name,
                        'updated_by' => Auth::user()->user_name,
                    ]
                );
                // Redirect or return a response as needed
                return redirect('/settings/cover/classClauses')->with('success', 'clause information saved successfully');
            } else {
                Session::flash('error', 'some field are missing');
                return [
                    'code' => -1,
                    'msg' => $validator->errors(),
                ];
            }
        } catch (\Throwable $e) {
            throw $e;
        }
    }

    public function classClausesEditData(Request $request)
    {
        try {
            $clauses = ClauseParam::findOrFail($request->ed_clause_id);
            $clauses->class_code = $request->input('ed_class_code');
            $clauses->clause_title = $request->input('ed_clause_title');
            $clauses->clause_wording = $request->input('ed_clause_wording');
            $clauses->updated_by = Auth::user()->user_name;
            $clauses->save();
            // Redirect or return a response as needed
            return redirect('/settings/cover/classClauses')->with('success', 'clause information saved successfully');
        } catch (ModelNotFoundException $exception) {
            return redirect('/settings/cover/classClauses')->with('error', 'Specified clause ID was not found.');
        } catch (\Throwable $e) {
            throw $e;
        }
    }

    public function classClausesDeleteData(Request $request)
    {
        try {
            $clauses = ClauseParam::findOrFail($request->del_clause_id);
            if ($clauses->status == "A") {
                $status = "D";
            } else {
                $status = "A";
            }
            $clauses->update([
                'status' => $status,
            ]);
            // Redirect or return a response as needed
            return redirect('/settings/cover/classClauses')->with('success', 'clause deleted successfully');
        } catch (ModelNotFoundException $exception) {
            return redirect('/settings/cover/classClauses')->with('error', 'Specified clause ID was not found.');
        } catch (\Throwable $e) {
            throw $e;
        }
    }

    // CUSTOMERS TYPE==================================================================================================
    public function CustomerTypeInfo(Request $request)
    {
        return view('settings.cover.customerType', [
            'user' => $request->user(),
        ]);
    }

    public function CustomerTypeData()
    {
        $customer = CustomerTypes::select(['type_id', 'type_name','code', 'status']);
        return DataTables::Of($customer)
            ->addColumn('action', function ($row) {
                if ($row->status === 'A') {
                    $status = 'De Activate';
                } else {
                    $status = 'Activate';
                }
                return '<button class="btn btn-outline-primary btn-sm" id="edit_cust_type">Edit</button>
                <button class="btn btn-outline-primary btn-sm" id="activate_cust_type" value="' . $status . '">' . $status . '</button>';
            })->make(true);
    }

    public function CustomerTypeAddData(Request $request)
    {
        try {
            $validator = Validator::make($request->all(), [
                'code' => 'required|string|max:10',
                'type_name' => 'required|string|max:100'
            ]);
            if ($validator) {
                // If the validation passes, you can proceed to store the data in the database or perform other actions
                CustomerTypes::create(
                    [
                        'code' => $request->code,
                        'code' => $request->code,
                        'type_name' => $request->type_name,
                        'status' => "A",
                    ]
                );
                Session::flash('success', 'Customer Type information saved successfully');
                // Redirect or return a response as needed
                return redirect('/settings/cover/customerType')->with('success', 'Customer Type information saved successfully');
            } else {
                Session::flash('error', 'some field are missing');
                return [
                    'code' => -1,
                    'msg' => $validator->errors(),
                ];
            }
        } catch (\Throwable $e) {
            throw $e;
        }
    }

    public function CustomerTypeEditData(Request $request)
    {
        try {
            $CustomerTypes = CustomerTypes::findOrFail($request->ed_type_id);
            $CustomerTypes->code = $request->input('ed_code');
            $CustomerTypes->type_name = $request->input('ed_type_name');
            $CustomerTypes->save();
            // Redirect or return a response as needed
            return redirect('/settings/cover/customerType')->with('success', 'Customer Type information saved successfully');
        } catch (ModelNotFoundException $exception) {
            return redirect('/settings/cover/customerType')->with('error', 'Specified code was not found.');
        } catch (\Throwable $e) {
            throw $e;
        }
    }

    public function CustomerTypeDeleteData(Request $request)
    {
        try {
            $CustomerTypes = CustomerTypes::findOrFail($request->del_type_id);
            if ($CustomerTypes->status == "A") {
                $status = "D";
            } else {
                $status = "A";
            }
            $CustomerTypes->update([
                'status' => $status,
            ]);
            // Redirect or return a response as needed
            return redirect('/settings/cover/customerType')->with('success', 'Customer type deleted successfully');
        } catch (ModelNotFoundException $exception) {
            return redirect('/settings/cover/customerType')->with('error', 'Specified code was not found.');
        } catch (\Throwable $e) {
            throw $e;
        }
    }

    // COUNTRIES ==================================================================================================
    public function CountryInfo(Request $request)
    {
        return view('settings.cover.country', [
            'user' => $request->user(),
        ]);
    }

    public function CountryData()
    {
        $country = Country::select(['country_iso', 'country_name', 'status']);
        return DataTables::Of($country)
            ->addColumn('action', function ($row) {
                if ($row->status === 'A') {
                    $status = 'De Activate';
                } else {
                    $status = 'Activate';
                }
                return '<button class="btn btn-outline-primary btn-sm" id="edit_country">Edit</button>
                <button class="btn btn-outline-primary btn-sm" id="activate_country" value="' . $status . '">' . $status . '</button>';
            })->make(true);
    }

    public function CountryAddData(Request $request)
    {
        try {
            $validator = Validator::make($request->all(), [
                'country_name' => 'required|string|max:100'
            ]);
            if ($validator) {
                // If the validation passes, you can proceed to store the data in the database or perform other actions
                Country::create(
                    [
                        'country_iso' => $request->country_iso,
                        'country_name' => $request->country_name,
                        'status' => "A",
                    ]
                );
                // Redirect or return a response as needed
                return redirect('/settings/cover/country')->with('success', 'Country information saved successfully');
            } else {
                Session::flash('error', 'some field are missing');
                return [
                    'code' => -1,
                    'msg' => $validator->errors(),
                ];
            }
        } catch (\Throwable $e) {
            throw $e;
        }
    }

    public function CountryEditData(Request $request)
    {
        try {
            $Country = Country::findOrFail($request->ed_country_iso);
            $Country->country_name = $request->input('ed_country_name');
            $Country->save();
            // Redirect or return a response as needed
            return redirect('/settings/cover/country')->with('success', 'Country information saved successfully');
        } catch (ModelNotFoundException $exception) {
            return redirect('/settings/cover/country')->with('error', 'Specified code was not found.');
        } catch (\Throwable $e) {
            throw $e;
        }
    }

    public function CountryDeleteData(Request $request)
    {
        try {
            $Country = Country::findOrFail($request->del_country_iso);
            if ($Country->status == "A") {
                $status = "D";
            } else {
                $status = "A";
            }
            $Country->update([
                'status' => $status,
            ]);
            // Redirect or return a response as needed
            return redirect('/settings/cover/country')->with('success', 'Country deleted successfully');
        } catch (ModelNotFoundException $exception) {
            return redirect('/settings/cover/country')->with('error', 'Specified code was not found.');
        } catch (\Throwable $e) {
            throw $e;
        }
    }

    // BUSINESS TYPES ==================================================================================================
    public function BusinessTypeInfo(Request $request)
    {
        return view('settings.cover.businessType', [
            'user' => $request->user(),
        ]);
    }

    public function BusinessTypeData()
    {
        $BusinessType = BusinessType::select(['bus_type_id', 'bus_type_name']);
        return DataTables::Of($BusinessType)
            ->addColumn('action', function ($row) {
                return '<button class="btn btn-outline-primary btn-sm" id="edit_businessType">Edit</button>';
            })->make(true);
    }

    public function BusinessTypeAddData(Request $request)
    {
        try {
            $validator = Validator::make($request->all(), [
                'bus_type_id' => 'required|int|max:3',
                'bus_type_name' => 'required|string|max:100'
            ]);
            if ($validator) {
                // If the validation passes, you can proceed to store the data in the database or perform other actions
                BusinessType::create(
                    [
                        'bus_type_id' => $request->bus_type_id,
                        'bus_type_name' => $request->bus_type_name
                    ]
                );
                // Redirect or return a response as needed
                return redirect('/settings/cover/businessType')->with('success', 'businessType information saved successfully');
            } else {
                Session::flash('error', 'some field are missing');
                return [
                    'code' => -1,
                    'msg' => $validator->errors(),
                ];
            }
        } catch (\Throwable $e) {
            throw $e;
        }
    }

    public function BusinessTypeEditData(Request $request)
    {
        try {
            $BusinessType = BusinessType::findOrFail($request->ed_bus_type_id);
            $BusinessType->bus_type_name = $request->input('ed_bus_type_name');
            $BusinessType->save();
            // Redirect or return a response as needed
            return redirect('/settings/cover/businessType')->with('success', 'BusinessType information saved successfully');
        } catch (ModelNotFoundException $exception) {
            return redirect('/settings/cover/businessType')->with('error', 'Specified code was not found.');
        } catch (\Throwable $e) {
            throw $e;
        }
    }

    public function BusinessTypeDeleteData(Request $request)
    {
    }

    // BINDERS ==================================================================================================
    public function BinderInfo(Request $request)
    {
        return view('settings.cover.binder', [
            'user' => $request->user(),
        ]);
    }

    public function BinderData()
    {
        $BinderCover = BinderCover::select(['binder_cov_no', 'insured_name', 'agency_name', 'created_at']);
        return DataTables::Of($BinderCover)
            ->addColumn('action', function ($row) {
                return '<button class="btn btn-outline-primary btn-sm" id="edit_binder">Edit</button>';
            })->make(true);
    }

    public function BinderAddData(Request $request)
    {
        try {
            $validator = Validator::make($request->all(), []);
            if ($validator) {
                // If the validation passes, you can proceed to store the data in the database or perform other actions
                BinderCover::create(
                    [
                        'binder_cov_no' => $request->binder_cov_no,
                        'insured_name' => $request->insured_name,
                        'agency_name' => $request->agency_name
                    ]
                );
                // Redirect or return a response as needed
                return redirect('/settings/cover/binder')->with('success', 'binder information saved successfully');
            } else {
                Session::flash('error', 'some field are missing');
                return [
                    'code' => -1,
                    'msg' => $validator->errors(),
                ];
            }
        } catch (\Throwable $e) {
            throw $e;
        }
    }

    public function BinderEditData(Request $request)
    {
        try {
            $BinderCover = BinderCover::findOrFail($request->ed_binder_cov_no);
            $BinderCover->insured_name = $request->input('ed_insured_name');
            $BinderCover->agency_name = $request->input('ed_agency_name');
            $BinderCover->save();
            // Redirect or return a response as needed
            return redirect('/settings/cover/binder')->with('success', 'binder information saved successfully');
        } catch (ModelNotFoundException $exception) {
            return redirect('/settings/cover/binder')->with('error', 'Specified code was not found.');
        } catch (\Throwable $e) {
            throw $e;
        }
    }

    public function BinderDeleteData(Request $request)
    {
    }

    // PAYMENT METHODS ==================================================================================================
    public function PayMethodInfo(Request $request)
    {
        return view('settings.cover.payMethod', [
            'user' => $request->user(),
        ]);
    }

    public function PayMethodData()
    {
        $PayMethod = PayMethod::all();
        return DataTables::Of($PayMethod)
            ->addColumn('action', function ($row) {
                return '<button class="btn btn-outline-primary btn-sm" id="edit_payMethod">Edit</button>';
            })->make(true);
    }

    public function PayMethodAddData(Request $request)
    {
        try {
            $validator = Validator::make($request->all(), []);
            if ($validator) {
                // If the validation passes, you can proceed to store the data in the database or perform other actions
                PayMethod::create(
                    [
                        'pay_method_code' => $request->pay_method_code,
                        'pay_method_name' => $request->pay_method_name,
                        'short_description' => $request->short_description
                    ]
                );
                // Redirect or return a response as needed
                return redirect('/settings/cover/payMethod')->with('success', 'payMethod information saved successfully');
            } else {
                Session::flash('error', 'some field are missing');
                return [
                    'code' => -1,
                    'msg' => $validator->errors(),
                ];
            }
        } catch (\Throwable $e) {
            throw $e;
        }
    }

    public function PayMethodEditData(Request $request)
    {
        try {
            $PayMethod = PayMethod::findOrFail($request->ed_pay_method_code);
            $PayMethod->pay_method_name = $request->input('ed_pay_method_name');
            $PayMethod->short_description = $request->input('ed_short_description');
            $PayMethod->save();
            // Redirect or return a response as needed
            return redirect('/settings/cover/payMethod')->with('success', 'payMethod information saved successfully');
        } catch (ModelNotFoundException $exception) {
            return redirect('/settings/cover/payMethod')->with('error', 'Specified code was not found.');
        } catch (\Throwable $e) {
            throw $e;
        }
    }

    public function PayMethodDeleteData(Request $request)
    {
    }

    // SUM INSURANCE TYPES ==================================================================================================
    public function SumInsTypeInfo(Request $request)
    {
        return view('settings.cover.sumInsType', [
            'user' => $request->user(),
        ]);
    }

    public function SumInsTypeData()
    {
        $TypeOfSumInsured = TypeOfSumInsured::select(['sum_insured_code', 'sum_insured_name', 'status']);
        return DataTables::Of($TypeOfSumInsured)
            ->addColumn('action', function ($row) {
                return '<button class="btn btn-outline-primary btn-sm" id="edit_sumInsType">Edit</button>';
            })->make(true);
    }

    public function SumInsTypeAddData(Request $request)
    {
        try {
            $validator = Validator::make($request->all(), []);
            if ($validator) {
                // If the validation passes, you can proceed to store the data in the database or perform other actions
                TypeOfSumInsured::create(
                    [
                        'sum_insured_code' => $request->sum_insured_code,
                        'sum_insured_name' => $request->sum_insured_name,
                        'status' => 'A'
                    ]
                );
                // Redirect or return a response as needed
                return redirect('/settings/cover/sumInsType')->with('success', 'sumInsType information saved successfully');
            } else {
                Session::flash('error', 'some field are missing');
                return [
                    'code' => -1,
                    'msg' => $validator->errors(),
                ];
            }
        } catch (\Throwable $e) {
            throw $e;
        }
    }

    public function SumInsTypeEditData(Request $request)
    {
        try {
            $TypeOfSumInsured = TypeOfSumInsured::findOrFail($request->ed_sum_insured_code);
            $TypeOfSumInsured->sum_insured_name = $request->input('ed_sum_insured_name');
            $TypeOfSumInsured->save();
            // Redirect or return a response as needed
            return redirect('/settings/cover/sumInsType')->with('success', 'sumInsType information saved successfully');
        } catch (ModelNotFoundException $exception) {
            return redirect('/settings/cover/sumInsType')->with('error', 'Specified code was not found.');
        } catch (\Throwable $e) {
            throw $e;
        }
    }

    public function SumInsTypeDeleteData(Request $request)
    {
    }

    // REINS DIVISION ==================================================================================================
    public function ReinsDivisionInfo(Request $request)
    {
        return view('settings.cover.reinsDivision', [
            'user' => $request->user(),
        ]);
    }

    public function ReinsDivisionData()
    {
        $ReinsDivision = ReinsDivision::select(['division_code', 'division_name', 'status']);
        return DataTables::Of($ReinsDivision)
            ->addColumn('action', function ($row) {
                if ($row->status === 'A') {
                    $status = 'De Activate';
                } else {
                    $status = 'Activate';
                }
                return '<button class="btn btn-outline-primary btn-sm" id="edit_reinsDivision">Edit</button>
                <button class="btn btn-outline-primary btn-sm" id="activate_reinsDivision" value="' . $status . '">' . $status . '</button>';
            })->make(true);
    }

    public function ReinsDivisionAddData(Request $request)
    {
        try {
            $validator = Validator::make($request->all(), []);
            if ($validator) {
                // If the validation passes, you can proceed to store the data in the database or perform other actions
                ReinsDivision::create(
                    [
                        'division_code' => $request->division_code,
                        'division_name' => $request->division_name,
                        'status' => 'A'
                    ]
                );
                // Redirect or return a response as needed
                return redirect('/settings/cover/reinsDivision')->with('success', 'reinsDivision information saved successfully');
            } else {
                Session::flash('error', 'some field are missing');
                return [
                    'code' => -1,
                    'msg' => $validator->errors(),
                ];
            }
        } catch (\Throwable $e) {
            throw $e;
        }
    }

    public function ReinsDivisionEditData(Request $request)
    {
        try {
            $ReinsDivision = ReinsDivision::findOrFail($request->ed_division_code);
            $ReinsDivision->division_name = $request->input('ed_division_name');
            $ReinsDivision->save();
            // Redirect or return a response as needed
            return redirect('/settings/cover/reinsDivision')->with('success', 'reinsDivision information saved successfully');
        } catch (ModelNotFoundException $exception) {
            return redirect('/settings/cover/reinsDivision')->with('error', 'Specified code was not found.');
        } catch (\Throwable $e) {
            throw $e;
        }
    }

    public function ReinsDivisionDeleteData(Request $request)
    {
        try {
            $ReinsDivision = ReinsDivision::findOrFail($request->del_division_code);
            if ($ReinsDivision->status == "A") {
                $status = "D";
            } else {
                $status = "A";
            }
            $ReinsDivision->update(['status' => $status]);
            // Redirect or return a response as needed
            return redirect('/settings/cover/reinsDivision')->with('success', 'reinsDivision deleted successfully');
        } catch (ModelNotFoundException $exception) {
            return redirect('/settings/cover/reinsDivision')->with('error', 'Specified code was not found.');
        } catch (\Throwable $e) {
            throw $e;
        }
    }

    // REINS CLASSES ==================================================================================================
    public function ReinsClassInfo(Request $request)
    {
        return view('settings.cover.reinsClass', [
            'user' => $request->user(),
            'classGroups' => ClassGroup::all(),
        ]);
    }

    public function ReinsClassData()   
    {
        // $ReinsClass = ReinsClass::select(['class_group','class_code', 'class_name', 'status']);
        $ReinsClass = ReinsClass::select('reinsclasses.*', 'class_groups.group_name')
                ->join('class_groups', 'reinsclasses.class_group', '=', 'class_groups.group_code')
                ->get();
        return DataTables::Of($ReinsClass)
            ->addColumn('action', function ($row) {
                if ($row->status === 'A') {
                    $status = 'De Activate';
                } else {
                    $status = 'Activate';
                }
                return '<button class="btn btn-outline-primary btn-sm" id="edit_reinsClass">Edit</button>
                <button class="btn btn-outline-primary btn-sm" id="activate_reinsClass" value="' . $status . '">' . $status . '</button>';
            })->make(true);
    }

    public function ReinsClassAddData(Request $request)
    {
        try {
            $validator = Validator::make($request->all(), []);
            if ($validator) {
                // If the validation passes, you can proceed to store the data in the database or perform other actions
                ReinsClass::create(
                    [
                        'class_group' => $request->class_group,
                        'class_code' => $request->class_code,
                        'class_name' => $request->class_name,
                        'status' => 'A'
                    ]
                );
                // Redirect or return a response as needed
                return redirect('/settings/cover/reinsClass')->with('success', 'reinsClass information saved successfully');
            } else {
                Session::flash('error', 'some field are missing');
                return [
                    'code' => -1,
                    'msg' => $validator->errors(),
                ];
            }
        } catch (\Throwable $e) {
            throw $e;
        }
    }

    public function ReinsClassEditData(Request $request)
    {
        try {
            $ReinsClass = ReinsClass::findOrFail($request->ed_class_code);
            $ReinsClass->class_name = $request->input('ed_class_name');
            $ReinsClass->class_group = $request->input('ed_class_group');
            $ReinsClass->save();
            // Redirect or return a response as needed
            return redirect('/settings/cover/reinsClass')->with('success', 'reinsClass information saved successfully');
        } catch (ModelNotFoundException $exception) {
            return redirect('/settings/cover/reinsClass')->with('error', 'Specified code was not found.');
        } catch (\Throwable $e) {
            throw $e;
        }
    }

    public function ReinsClassDeleteData(Request $request)
    {
        try {
            $ReinsClass = ReinsClass::findOrFail($request->del_class_code);
            if ($ReinsClass->status == "A") {
                $status = "D";
            } else {
                $status = "A";
            }
            $ReinsClass->update(['status' => $status]);
            // Redirect or return a response as needed
            return redirect('/settings/cover/reinsClass')->with('success', 'reinsClass deleted successfully');
        } catch (ModelNotFoundException $exception) {
            return redirect('/settings/cover/reinsClass')->with('error', 'Specified code was not found.');
        } catch (\Throwable $e) {
            throw $e;
        }
    }

    public function ReinsClass(Request $request )
    {
        $ReinsClass = ReinsClass::where('class_group', $request->division)->get();
        return response()->json(['data' => $ReinsClass]);
    }

    // TREATY TYPES ==================================================================================================
    public function TreatyTypeInfo(Request $request)
    {
        return view('settings.cover.treatyType', [
            'user' => $request->user(),
        ]);
    }

    public function TreatyTypeData()
    {
        $TreatyType = TreatyType::select(['type_of_bus','treaty_code', 'treaty_name', 'status']);
        return DataTables::Of($TreatyType)
            ->addColumn('type_of_bus', function ($row) {
                // get reinclass
                $reinClass = BusinessType::where('bus_type_id', $row->type_of_bus)->first();
                return $reinClass->bus_type_name;
            })
            ->addColumn('action', function ($row) {
                if ($row->status === 'A') {
                    $status = 'De Activate';
                } else {
                    $status = 'Activate';
                }
                return '<button class="btn btn-outline-primary btn-sm" id="edit_treatyType">Edit</button>
                    <button class="btn btn-outline-primary btn-sm" id="activate_treatyType" value="' . $status . '">' . $status . '</button>';
            })->make(true);
    }

    public function TreatyTypeAddData(Request $request)
    {
        try {
            $validator = Validator::make($request->all(), []);
            if ($validator) {
                // If the validation passes, you can proceed to store the data in the database or perform other actions
                TreatyType::create(
                    [
                        'type_of_bus' => $request->type_of_bus,
                        'treaty_code' => $request->treaty_code,
                        'treaty_name' => $request->treaty_name,
                        'status' => 'A'
                    ]
                );
                // Redirect or return a response as needed
                return redirect('/settings/cover/treatyType')->with('success', 'treatyType information saved successfully');
            } else {
                Session::flash('error', 'some field are missing');
                return [
                    'code' => -1,
                    'msg' => $validator->errors(),
                ];
            }
        } catch (\Throwable $e) {
            throw $e;
        }
    }

    public function TreatyTypeEditData(Request $request)
    {
        try {
            $TreatyType = TreatyType::findOrFail($request->ed_treaty_code);
            $TreatyType->treaty_name = $request->input('ed_treaty_name');
            $TreatyType->save();
            // Redirect or return a response as needed
            return redirect('/settings/cover/treatyType')->with('success', 'treatyType information saved successfully');
        } catch (ModelNotFoundException $exception) {
            return redirect('/settings/cover/treatyType')->with('error', 'Specified code was not found.');
        } catch (\Throwable $e) {
            throw $e;
        }
    }

    public function TreatyTypeDeleteData(Request $request)
    {
        try {
            $TreatyType = TreatyType::findOrFail($request->del_treaty_code);
            if ($TreatyType->status == "A") {
                $status = "D";
            } else {
                $status = "A";
            }
            $TreatyType->update(['status' => $status]);
            // Redirect or return a response as needed
            return redirect('/settings/cover/treatyType')->with('success', 'treatyType changed status successfully');
        } catch (ModelNotFoundException $exception) {
            return redirect('/settings/cover/treatyType')->with('error', 'Specified code was not found.');
        } catch (\Throwable $e) {
            throw $e;
        }
    }

   

    // REINCLASS PREMTYPES GROUP ==================================================================================================
    public function reinsClassPremtypesInfo(Request $request)
    {
        return view('settings.cover.reinsClassPremtypes', [
            'user' => $request->user(),
            'reinclasses' => ReinsClass::all()
        ]);
    }

    public function reinsClassPremtypesData()
    {
        $ReinclassPremTypes = ReinclassPremtype::all();
        return DataTables::Of($ReinclassPremTypes)
            ->addColumn('reinclass_name', function ($row) {
                // get reinclass
                $reinClass = ReinsClass::where('class_code', $row->reinclass)->first();
                return $reinClass->class_name;
            })
            ->addColumn('action', function ($row) {
                if ($row->status === 'A') {
                    $status = 'DeActivate';
                } else {
                    $status = 'Activate';
                }
                return '<button class="btn btn-outline-primary btn-sm" id="edit_reinsClassPremtypes">Edit</button>
                            <button class="btn btn-outline-primary btn-sm" id="activate_reinsClassPremtypes" value="' . $status . '">' . $status . '</button>';
            })
            ->rawColumns(['action'])
            ->make(true);
    }

    public function reinsClassPremtypesAddData(Request $request)
    {
        try {
            $validator = Validator::make($request->all(), []);
            if ($validator) {
                // If the validation passes, you can proceed to store the data in the database or perform other actions
                ReinclassPremtype::create(
                    [
                        'reinclass' => $request->reinclass,
                        'premtype_code' => $request->premtype_code,
                        'premtype_name' => $request->premtype_name,
                        'status' => 'A'
                    ]
                );
                // Redirect or return a response as needed
                return redirect('/settings/cover/reinsClassPremtypes')->with('success', 'reins class premtype information saved successfully');
            } else {
                Session::flash('error', 'some field are missing');
                return [
                    'code' => -1,
                    'msg' => $validator->errors(),
                ];
            }
        } catch (\Throwable $e) {
            throw $e;
        }
    }

    public function reinsClassPremtypesEditData(Request $request)
    {
        // dd($request);
        try {
            $reinsClassPremtypes = ReinclassPremtype::where('reinclass',$request->ed_reinclass)
                                                    ->where('premtype_code',$request->ed_premtype_code)
                                                    ->update([
                                                        'premtype_name' =>$request->ed_premtype_name
                                                    ]);
            // $reinsClassPremtypes
            // $reinsClassPremtypes->premtype_name = $request->input('ed_premtype_name');
            // $reinsClassPremtypes->save();
            // Redirect or return a response as needed
            return redirect('/settings/cover/reinsClassPremtypes')->with('success', 'reins class premtype edited successfully');
        } catch (ModelNotFoundException $exception) {
            return redirect('/settings/cover/reinsClassPremtypes')->with('error', 'Specified code was not found.');
        } catch (\Throwable $e) {
            throw $e;
        }
    }

    public function reinsClassPremtypesDeleteData(Request $request)
    {
        // dd($request);
        try {
            // $reinsClassPremtypes = ReinclassPremtype::findOrFail($request->del_id);
            $Premtype = ReinclassPremtype::where('reinclass',$request->del_reinclass)
                                                    ->where('premtype_code',$request->del_premtype_code)
                                                    ->first();
            $Premtype_status = $Premtype->status;
            $reinClass = ReinsClass::where('class_code', $request->del_reinclass)->first();
           
            if ($Premtype_status == 'A') {
                $status = 'D';
                $status_name = 'De Activated';
            } else {
                $status = 'A';
                $status_name = 'Activated';
            }

            $reinsClassPremtypes = ReinclassPremtype::where('reinclass',$request->del_reinclass)
                                                    ->where('premtype_code',$request->del_premtype_code)
                                                    ->update([
                                                        'status' =>$status
                                                    ]);

            // Redirect or return a response as needed
            return redirect('/settings/cover/reinsClassPremtypes')->with('success', ''.$reinClass->class_name.'-'.$Premtype->premtype_name.' has been '.$status_name.' successfully');
        } catch (ModelNotFoundException $exception) {
            return redirect('/settings/cover/reinsClassPremtypes')->with('error', 'Specified code was not found.');
        } catch (\Throwable $e) {
            throw $e;
        }
    }

    public function whtRate(Request $request)
    {
        if(!$request->ajax())
        {
            return view('settings.cover.wht_rates');
        }
        else
        {
            $whtRates = WhtRate::all();
            return DataTables::of($whtRates)
                ->addColumn('action', function ($data) {
                    return "<button class='btn btn-outline-primary btn-sm edit' data-data='$data' data-bs-toggle='modal' data-bs-target='#whtModal'>
                                <i class='fa fa-pencil'></i> Edit
                            </button>
                            <button class='btn btn-outline-danger btn-sm delete'  data-data='$data'> <i class='fa fa-trash'></i> Delete</button>";
                })
                ->rawColumns(['action'])
                ->make(true);
        }
    }
    
    public function saveWhtRate(Request $request)
    {
        $request->validate([
            'description' => 'required',
            'rate' => 'required',
        ]);

        try {
            $id = (int)WhtRate::max('id')+1;

            $whtRate = new WhtRate();
            $whtRate->id = $id;
            $whtRate->description = $request->description;
            $whtRate->rate = $request->rate;
            $whtRate->created_by = Auth::user()->user_name;
            $whtRate->updated_by = Auth::user()->user_name;
            $whtRate->save();

            return redirect()->route('settings.whtRate')->with('success', 'Wht Tax added successfully');
        } catch (\Throwable $e) {
            dd($e);
            return redirect()->route('settings.whtRate')->with('error', 'Failed to add Wht Tax');
        }
    }
    
    public function editWhtRate(Request $request)
    {
        // dd($request);
        $request->validate([
            'id' => 'required',
            'description' => 'required',
            'rate' => 'required',
        ]);

        try {
            $whtRate = WhtRate::where('id',$request->id)
                ->update([
                    'description' =>$request->description,
                    'rate' =>$request->rate,
                ]);

            return redirect()->route('settings.whtRate')->with('success', 'Wht Tax edited successfully');
        } catch (\Throwable $e) {
            return redirect()->route('settings.whtRate')->with('error', 'Failed to edit Wht Tax');
        }
    }
    
    public function deleteWhtRate(Request $request)
    {
        // dd($request);
        $request->validate([
            'id' => 'required',
        ]);

        try {
            $whtRate = WhtRate::where('id',$request->id)->first();
            $whtRate->delete();

            return [
                'status' => Response::HTTP_OK,
                'message' => 'Item deleted successfully'
            ];
        } catch (\Throwable $e) {
            return [
                'status' => $e->getCode(),
                'message' => 'Failed to delete item'
            ];
        }
    }
}
