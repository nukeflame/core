<?php

namespace App\Http\Controllers;

use Throwable;
use Carbon\Carbon;
use App\Models\Banks;
use App\Models\Company;
use App\Models\Country;
use App\Models\TaxRate;
use App\Models\TaxType;
use App\Models\CBSource;
use App\Models\Currency;
use App\Models\TaxGroup;
use App\Models\COA_Level;
use App\Models\GLMastBal;
use App\Models\ARCustomer;
use App\Models\BankBranch;
use App\Models\COA_Config;
use App\Models\COA_Status;
use App\Models\CBPaymethod;
use App\Models\CBTransType;
use App\Models\CBDeductions;
use App\Models\CurrencyRate;
use Illuminate\Http\Request;
use App\Models\FinancePeriod;
use App\Models\GLTransaction;
use App\Models\COA_LevelCateg;
use App\Models\ARCustomerGroup;
use App\Models\COA_CompanySegment;
use App\Models\Department;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Session;
use Yajra\DataTables\Facades\DataTables;
use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\ValidationException;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Support\Facades\Response;
use Illuminate\Support\Facades\View;

class SettingsFinanceController extends Controller
{

    private $_year;
    private $_month;
    private $_quarter;
    private $_endorsement_no;

    public function __construct()
    {
        $this->_year = Carbon::now()->year;
        $this->_month = Carbon::now()->month;
        $this->_quarter = Carbon::now()->quarter;
    }

    //

    function COA_Levels(Request $request)
    {
        return view('settings.finance.coa_levels', [
            'user' => $request->user(),
        ]);
    }

    function COA_NewLevel(Request $request)
    {
        try {
            //code...
            // Validation rules
            $validator = Validator::make($request->all(), [
                'level_id' => 'required|number',
                'level_name' => 'required|string|max:255',
            ]);

            if ($validator) {
                // If the validation passes, you can proceed to store the data in the database or perform other actions
                COA_Level::create(
                    [
                        'level_id' => $request->level_id,
                        'name' => $request->level_name,
                        'status' => 'N'
                    ]
                );

                // Redirect or return a response as needed
                return redirect('/settings/finance/coa-levels')->with('success', 'New Leve information saved successfully');
            } else {
                Session::flash('error', 'some field are missing');
                return [
                    'code' => -1,
                    'msg' => $validator->errors(),
                ];
            }
        } catch (\Throwable $th) {
            //throw $th;
        }
    }

    function COA_LevelDatatable(Request $request)
    {

        $coa_level = COA_Level::select(['level_id', 'name', 'status']);
        return DataTables::of($coa_level)
            ->editColumn('amend', function ($fn) {
                return '<button class="amend_coa_level btn btn-primary  datatable-btn "
						onclick="amendCOALevel(`' . $fn->level_id . '`)">
						Amend
					</button>';
            })

            ->rawColumns(['amend'])
            ->make(true);
    }


    function COA_LeveslDtl(Request $request)
    {
        $levels = COA_Level::all();

        return view::make('settings.finance.coa_levels_categ', compact('levels'));
    }

    function COA_LeveslDtlData(Request $request)
    {
        $leveldtl = COA_LevelCateg::all();

        return Datatables::Of($leveldtl)
            ->addColumn('action', function ($leveldtl) {
                return '<button type="button" class="btn btn-outline-primary btn-sm" id="edit_categ">Edit</button>';
            })

            ->addColumn('level_descr', function ($leveldtl) {
                $level = trim($leveldtl->level_id);
                $categlev = COA_Level::where('level_id', $level)->first();
                return $categlev->name;
            })

            ->addColumn('parent_descr', function ($leveldtl) {
                if ($leveldtl->parent_id == 0) {
                    return 'NO PARENT';
                }

                $parent = $leveldtl->parent_id;
                $par_descr = COA_LevelCateg::where('level_categ_id', $parent)->first();
                return $par_descr->level_categ_name;
            })
            ->make(true);
    }


    function getCOALevelParent(Request $request)
    {

        $request_lev_id = trim($request->level_id);
        $parent_levels = COA_Level::where('level_id', '<', $request_lev_id)->orderBy('level_id', 'desc')->get();


        if ($parent_levels->isNotEmpty()) {
            $parent_level = $parent_levels->first(); // Access the first element
            $level_id = $parent_level->level_id;
            $parent_category = COA_LevelCateg::where('level_id', $level_id)->get();
            return $parent_category;
        } else {
            $parent_id = 0;
            $result = array('status' => $parent_id);
            return $result;
        }
    }

    function postCOALevelCateg(Request $request)
    {
        $max_id = COA_LevelCateg::max('level_categ_id');
        // dd($max_id);

        $attributes = COA_LevelCateg::create([
            'level_categ_id' => $max_id + 1,
            'level_id' => trim($request->level_id),
            'level_categ_name' => trim($request->description),
            'parent_id' => trim($request->parent_id)
        ]);

        Session::Flash('success', 'Level Category Added Successfully');
    }

    function updCOALevelCateg(Request $request)
    {
        $categ_id = trim($request->old_category_id);

        $level_id = trim($request->edit_level_id);

        $parent_id = trim($request->edit_parent_id);

        $description = trim($request->edit_description);

        $update_category = COA_LevelCateg::where('level_categ_id ', $categ_id)
            ->update([
                'level_id' => $level_id,
                'level_categ_name' => $description,
                'parent_id' => $parent_id
            ]);

        Session::Flash('success', 'Level Category Updated Successfully');
    }

    function COAConfig(Request $request)
    {
        $segments = COA_CompanySegment::all();

        $categ_levels = COA_Level::where('status', '==', 'A')->orderBy('level_id', 'ASC')->get();
        // dd($categ_levels);


        return View::make('settings.finance.coa_config', compact('segments'))
            ->with('categ_levels', $categ_levels);
    }

    function COAData(Request $request)
    {
        $prid = $request->acc_prid;
        $prsno = $request->acc_prsno;

        $chartconfig = COA_Config::where('prid', $prid)
            ->where('prsno', $prsno)
            ->get()[0];

        $finance_period = FinancePeriod::get()[0];

        $acc_years = GLMastBal::where('account_number', $prsno)
            ->where('account_year', $finance_period->account_year)
            ->where('account_month', $finance_period->account_month)
            ->selectRaw('distinct(account_year) as acount_year,ytd_bal,account_month')
            ->orderBy('account_year', 'DESC')
            ->get();
        //dd($acc_years);

        $sql = "select level.name as categ_level,categ.level_categ_name as category from coa_level_categories categ join coa_levels level on categ.level_id=level.level_id order by categ.level_id";

        $acc_categs = DB::select(DB::raw($sql));

        // $acc_status = GLAccountStatus::where('status_code',$chartconfig->status)->get()[0];

        // return view::make('settings.finance.gl_nlchartdetails',compact('chartconfig','acc_categs','acc_years'));
    }

    function getCOASegments(Request $request)
    {
        $coa_segments = COA_CompanySegment::orderBy('segment_position', 'ASC')->get();

        return DataTables::Of($coa_segments)
            ->make(true);
    }

    function getAccountGrp(Request $request)
    {
        $grpsegments = COA_Config::where('segment_code', 'GRP');


        return Datatables::Of($grpsegments)
            ->addColumn('action', function ($leveldtl) {
                if ($leveldtl->status == 'A') {
                    $act = 'DeActivate';
                } else {
                    $act = 'Activate';
                }
                return '<button class="btn btn-outline-primary btn-sm" id="view_grp_dtl">Edit</button>
                        <button class="btn btn-outline-primary btn-sm" id="activate_grp">' . $act . '</button>';
            })
            ->make(true);
    }

    function getAccountGrpDtl(Request $request)
    {
        $grpsegments = COA_Config::where('segment_code', 'GRP')->get();

        echo json_encode($grpsegments);
    }

    function getAccountSEC(Request $request)
    {
        $glh = COA_CompanySegment::where('segment_code', 'SEC')->first();


        $accounts = COA_Config::where('segment_code', $glh->segment_code);
        $segments = COA_CompanySegment::where('segment_code', '<>', $glh->segment_code)
            ->orderBy('segment_position', 'ASC')
            ->get();

        //  dd('wewew',$segments);


        return Datatables::Of($accounts)

            ->addColumn('seg_' . $segments[0]->segment_position, function ($grp) use ($segments) {

                $prsno = substr($grp->account_number, 0, (int) $segments[0]->segment_length);

                return $prsno;
            })
            ->addColumn('seg_desc_' . $segments[0]->segment_position, function ($grp) use ($segments) {

                $prsno = substr($grp->account_number, 0, $segments[0]->segment_length);

                $nlparam = COA_Config::where('segment_code', $segments[0]->segment_code)
                    ->whereRaw("trim(account_number) = '" . $prsno . "'")
                    ->get()[0];

                return $nlparam->description;
            })
            ->addColumn('action', function ($leveldtl) {
                if ($leveldtl->status == 'A') {
                    $act = 'DeActivate';
                } else {
                    $act = 'Activate';
                }
                return '<button class="btn btn-outline-primary btn-sm" id="view_grp_dtl">Edit</button>
                <button class="btn btn-outline-primary btn-sm" id="activate_grp">' . $act . '</button>';
            })

            ->make(true);
    }
    function postAccountGrp(Request $request)
    {
        // dd($request);
        try {
            $validatedData = $request->validate([
                'group_code' => 'required|numeric',
                'group_name' => 'required|string',
            ]);

            COA_Config::create([
                'segment_code' => 'GRP',
                'account_number' => trim($request->group_code),
                'description' => trim($request->group_name),
                'created_by' => Auth::user()->user_name,
                'dr_cr' => ' ',
                'level_categ_id_1' => 0,
                'level_categ_id_2' => 0,
                'level_categ_id_3' => 0,
                'level_categ_id_4' => 0,
                'status' => 'A',

            ]);

            return response()->json([
                'status' => '200',
                'message' => 'Data saved successfully'
            ]);
        } catch (ValidationException $e) {
            // If validation fails, return a JSON response with errors
            // dd($e);
            return response()->json([
                'status' => '504',
                'message' => $e->errors()
            ], 422);
        } catch (Throwable $e) {
            // dd($e);
            return response()->json([
                'status' => $e->getCode(),
                'message' => $e->getMessage()
            ]);
        }
    }

    function postAccountSec(Request $request)
    {
        // dd($request);
        try {
            $validatedData = $request->validate([
                'section_code' => 'required|numeric',
                'section_name' => 'required|string',
                'acc_grp_code' => 'required|numeric',
            ]);

            COA_Config::create([
                'segment_code' => 'SEC',
                'account_number' => trim($request->acc_grp_code . $request->section_code),
                'description' => trim($request->section_name),
                'created_by' => Auth::user()->user_name,
                'dr_cr' => ' ',
                'level_categ_id_1' => 0,
                'level_categ_id_2' => 0,
                'level_categ_id_3' => 0,
                'level_categ_id_4' => 0,
                'status' => 'A',

            ]);

            return response()->json([
                'status' => '200',
                'message' => 'Data saved successfully'
            ]);
        } catch (ValidationException $e) {
            // If validation fails, return a JSON response with errors
            // dd($e);
            return response()->json([
                'status' => '504',
                'message' => $e->errors()
            ], 422);
        } catch (Throwable $e) {
            // dd($e);
            return response()->json([
                'status' => $e->getCode(),
                'message' => $e->getMessage()
            ]);
        }
    }

    function postAccountCode(Request $request)
    {
        // dd($request->all());
        DB::beginTransaction();
        try {
            $validatedData = $request->validate([
                'parent' => 'required|numeric',
                'account_code_field' => 'required|numeric',
                'account_name' => 'required|string',
                'categ_type' => 'required|numeric',
                'categ_group' => 'required|numeric',
                'categ_sub_group' => 'required|numeric',
                // 'categ_final' => 'required|numeric',
                'currency' => 'required|string',
            ]);
            $account_number = trim($request->parent . $request->account_code_field);
            $dr_cr = $request->normal_bal;
            $current_bal = (float) str_replace(',', '', $request->current_bal);
            $local_cr_amount = 0;
            $local_dr_amount = 0;


            COA_Config::create([
                'segment_code' => $request->prid,
                'account_number' => $account_number,
                'description' => trim($request->account_name),
                'created_by' => Auth::user()->user_name,
                'dr_cr' => $dr_cr,
                'bank_flag' => $request->bank_flag,
                'level_categ_id_1' => $request->categ_type,
                'level_categ_id_2' => $request->categ_group,
                'level_categ_id_3' => $request->categ_sub_group,
                'level_categ_id_4' => 0, //$request->categ_final,
                'status' => $request->account_status,

            ]);

            if ($current_bal != 0) {
                if ($dr_cr == 'C') {
                    $local_cr_amount = $current_bal;
                } else {
                    $local_dr_amount = $current_bal;
                }

                $transaction_no = GLTransaction::where('doc_type', 'OPB')->count() + 1;
                $transaction_no = str_pad($transaction_no, 6, '0', STR_PAD_LEFT);
                $reference_no = $transaction_no . $this->_year;
                // --insert account gl transactions table if there is current balance
                $create_gltransaction = GLTransaction::create([
                    'source_code' => 'GL',
                    'offcd' => '000',
                    'doc_type' => 'OPB',
                    'entry_type_descr' => 'OPB',
                    'reference_no' => $reference_no,
                    'line_no' => 1,
                    'cover_no' => ' ',
                    'endorsement_no' => ' ',
                    'claim_no' => ' ',
                    'item_no' => 1,
                    'dr_cr' => $dr_cr,
                    'pay_request_no' => ' ',
                    'gl_account' => $account_number,
                    'period_year' => $this->_year,
                    'period_month' => $this->_month,
                    'foreign_amount' => $current_bal,
                    'local_amount' => $current_bal,
                    'branch' => '000',
                    'currency_code' => $request->currency,
                    'currency_rate' => 1,
                    'debit_credit_no' => ' ',
                    'customer_id' => ' ',
                    'created_date' => Carbon::now(),
                    'created_by' => Auth::user()->user_name,
                    'created_time' => Carbon::now(),
                    'updated_date' => Carbon::now(),
                    'updated_by' => Auth::user()->user_name,
                    'updated_time' => Carbon::now(),
                    'narration' => 'OPENING BALANCE',
                ]);
            }
            //Create the account to GLMast Table
            $glmastbal = GLMastBal::where([
                'account_number' => $account_number,
                'offcd' => '000',
                'account_year' => $this->_year,
                'account_month' => $this->_month
            ])->first();

            if ($glmastbal) {
                // Update the existing record
                $glmastbal->update([
                    'ytd_bal' => DB::raw('ytd_bal + ' . $local_dr_amount . ' - ' . $local_cr_amount),
                    'ytd_debits' => DB::raw('ytd_debits + ' . $local_dr_amount),
                    'ytd_credits' => DB::raw('ytd_credits + ' . $local_cr_amount),
                    'period_debits' => DB::raw('period_debits + ' . $local_dr_amount),
                    'period_credits' => DB::raw('period_credits + ' . $local_cr_amount),
                    'period_closing_bal' => DB::raw('period_closing_bal + ' . $local_cr_amount . ' + ' . $local_dr_amount . ' + ' . $local_cr_amount),
                    'ytd_closing_bal' => DB::raw('ytd_closing_bal + ' . $local_cr_amount . ' + ' . $local_dr_amount . ' + ' . $local_cr_amount),
                ]);
            } else {
                // Create a new record
                GLMastBal::create([
                    'account_number' => $account_number,
                    'offcd' => '000',
                    'account_year' => $this->_year,
                    'account_month' => $this->_month,
                    'ytd_bal' => $local_dr_amount - $local_cr_amount,
                    'ytd_debits' => $local_dr_amount,
                    'ytd_credits' => $local_cr_amount,
                    'period_debits' => $local_dr_amount,
                    'period_credits' => $local_cr_amount,
                    'year_opening_bal' => 0,
                    'period_opening_bal' => 0,
                    'period_closing_bal' => $local_dr_amount + $local_cr_amount,
                    'ytd_closing_bal' => $local_dr_amount + $local_cr_amount,
                ]);
            }


            DB::commit();
            return response()->json([
                'status' => '200',
                'message' => 'Data saved successfully'
            ]);
        } catch (ValidationException $e) {
            DB::rollback();
            // If validation fails, return a JSON response with errors
            // dd($e);
            return response()->json([
                'status' => '504',
                'message' => $e->errors()
            ], 422);
        } catch (Throwable $e) {
            DB::rollback();
            dd($e);
            return response()->json([
                'status' => $e->getCode(),
                'message' => $e->getMessage()
            ]);
        }
    }

    function getChartofAccounts(Request $request)
    {
        $glh = COA_CompanySegment::orderBy('segment_position', 'DESC')->get()[0];


        $accounts = COA_Config::where('segment_code', $glh->segment_code);
        $segments = COA_CompanySegment::where('segment_code', '<>', $glh->segment_code)
            ->orderBy('segment_position', 'ASC')
            ->get();

        // dd($segments[1]->segment_position,$segments[1]->segment_length);


        return Datatables::Of($accounts)
            //foreach($segments as $seg){
            ->addColumn('seg_' . $segments[0]->segment_position, function ($grp) use ($segments) {

                $prsno = substr($grp->account_number, 0, (int) $segments[0]->segment_length);
                // dd($prsno);
                return $prsno;
            })
            ->addColumn('seg_desc_' . $segments[0]->segment_position, function ($grp) use ($segments) {

                $prsno = substr($grp->account_number, 0, $segments[0]->segment_length);

                $nlparam = COA_Config::where('segment_code', $segments[0]->segment_code)
                    ->whereRaw("trim(account_number) = '" . $prsno . "'")
                    ->get()[0];
                // dd($nlparam);
                return $nlparam->description;
            })
            ->addColumn('seg_' . $segments[1]->segment_position, function ($grp) use ($segments) {

                $prsno = substr($grp->account_number, $segments[1]->segment_length, $segments[1]->segment_length);

                return $prsno;
            })
            ->addColumn('seg_desc_' . $segments[1]->segment_position, function ($grp) use ($segments) {

                $prsno = substr($grp->account_number, 0, $segments[0]->segment_length + $segments[1]->segment_length);

                $nlparam = COA_Config::where('segment_code', $segments[1]->segment_code)
                    ->whereRaw("trim(account_number) = '" . $prsno . "'")
                    ->get()[0];

                return $nlparam->description;
            })
            ->addColumn('action', function ($leveldtl) {
                return '<button class="btn btn-outline-primary btn-sm" id="edit_code_dtl">Edit</button>';
            })
            ->make(true);
    }

    function getParentDetails(Request $request)
    {
        // dd($request->all());
        $segment = $request->get('segment_code');

        //get the previous segment
        $segments = COA_CompanySegment::where('segment_code', $segment)->get()[0];

        $prev_segment = COA_CompanySegment::where('segment_position', '<', $segments->segment_position)
            ->orderBy('segment_position', 'DESC')->first();

        $prsno = $request->get('parent');
        //dd($prev_segment->segment_code);

        $nlparams = COA_Config::whereRaw("segment_code = '" . $prev_segment->segment_code . "' and trim(account_number)='" . $prsno . "'")
            //->where('prsno',$prsno)
            ->get()[0];
        return $nlparams;
    }

    function getSegmentDetails(Request $request)
    {
        return COA_CompanySegment::where('segment_code', $request->segment_code)->get()[0];
    }

    function getSegmentParents(Request $request)
    {
        $segment = COA_CompanySegment::where('segment_code', $request->segment)->get()[0];

        $othersabove = COA_CompanySegment::where('segment_position', '<', $segment->segment_position)->orderBy('segment_position', 'DESC')->get()[0];


        $parents = COA_Config::where('segment_code', $othersabove->segment_code)->get();
        if (count($parents) > 0) {
            return $parents;
        } else {
            return null;
        }
    }

    function getSegmentStatus(Request $request)
    {
        //CHECK IF LAST
        $segment = COA_CompanySegment::where('segment_code', $request->segment_code)->get();

        $others = COA_CompanySegment::where('segment_position', '>', $segment[0]->segment_position)->get();
        if (count($others) > 0) {
            $results[] = ['status' => 'isnotlast'];
        } else {
            $results[] = ['status' => 'islast'];
        }
        return Response::Json($results);
    }

    function getParentCategories(Request $request)
    {
        return COA_LevelCateg::where('parent_id', $request->get('parent_id'))->get();
    }
    function fetchCategoriesDropdown(Request $request)
    {
        return COA_LevelCateg::where('parent_id', 0)->get();
    }

    function fetchCategoryItem(Request $request)
    {
        return COA_LevelCateg::where('parent_id', $request->category_id)->get();
    }

    public function getAccountStatuses(Request $request)
    {
        return COA_Status::all();
    }

    function getBaseCurrency(Request $request)
    {
        return Currency::where('base_currency', 'Y')->first();
        // echo json_encode($currency);
    }

    // CURRENCY ==================================================================================================
    public function CurrencyInfo(Request $request)
    {
        return view('settings.finance.currency', [
            'user' => $request->user(),
            'currencies' => Currency::all(),
            'countries' => Country::all(),
        ]);
    }

    public function CurrencyData()
    {
        $Currency = Currency::select(['country_iso', 'currency_code', 'currency_name', 'base_currency', 'status']);
        return DataTables::Of($Currency)
            ->addColumn('action', function ($row) {
                if ($row->status === 'A') {
                    $status = 'Activate';
                } else {
                    $status = 'De Activate';
                }
                return '<button class="btn btn-outline-primary btn-sm" id="edit_currency">Edit</button>
                <button class="btn btn-outline-primary btn-sm" id="activate_currency" value="' . $status . '">' . $status . '</button>';
            })->make(true);
    }

    public function CurrencyAddData(Request $request)
    {
        try {
            $validator = Validator::make($request->all(), []);
            if ($validator) {
                // If the validation passes, you can proceed to store the data in the database or perform other actions
                Currency::create(
                    [
                        'country_iso' => $request->country_iso,
                        'currency_code' => $request->currency_code,
                        'currency_name' => $request->currency_name
                    ]
                );
                Session::flash('success', 'Currency information saved successfully');
                // Redirect or return a response as needed
                return redirect('/settings/finance/currency')->with('success', 'Currency information saved successfully');
            } else {
                Session::flash('error', 'some field are missing');
                return [
                    'code' => -1,
                    'msg' => $validator->errors(),
                ];
            }
        } catch (\Throwable $e) {
            throw $e;
            // dd($e);
        }
    }

    public function CurrencyEditData(Request $request)
    {
        try {
            $Currency = Currency::findOrFail($request->ed_currency_code);
            $Currency->currency_name = $request->input('ed_currency_name');
            $Currency->base_currency = $request->input('ed_base_currency');
            $Currency->save();
            // Redirect or return a response as needed
            return redirect('/settings/finance/currency')->with('success', 'currency information saved successfully');
        } catch (ModelNotFoundException $exception) {
            return redirect('/settings/finance/currency')->with('error', 'Specified code was not found.');
        } catch (\Throwable $e) {
            throw $e;
            // dd($e);
        }
    }

    public function CurrencyDeleteData(Request $request)
    {
        try {
            $Currency = Currency::findOrFail($request->del_currency_code);
            if ($Currency->status == "A") {
                $status = "D";
            } else {
                $status = "A";
            }
            $Currency->update([
                'status' => $status,
            ]);
            // Redirect or return a response as needed
            return redirect('/settings/finance/currency')->with('success', 'currency deleted successfully');
        } catch (ModelNotFoundException $exception) {
            return redirect('/settings/finance/currency')->with('error', 'Specified code was not found.');
        } catch (\Throwable $e) {
            throw $e;
            // dd($e);
        }
    }

    // CURRENCY RATES==================================================================================================
    public function CurrencyRateInfo(Request $request)
    {
        return view('settings.finance.currencyRate', [
            'user' => $request->user(),
            'currencies' => Currency::all(),
        ]);
    }

    public function CurrencyRateData()
    {
        $currencyRate = CurrencyRate::select(['currency_code', 'currency_date', 'currency_rate']);
        return DataTables::Of($currencyRate)
            ->addColumn('action', function ($row) {
                return '<button class="btn btn-outline-primary btn-sm" id="edit_currency_rate">Edit</button>';
            })->make(true);
    }

    public function CurrencyRateAddData(Request $request)
    {
        try {
            $validator = Validator::make($request->all(), [
                'currency_rate' => 'required|string|max:100'
            ]);
            if ($validator) {
                // If the validation passes, you can proceed to store the data in the database or perform other actions
                CurrencyRate::create(
                    [
                        'currency_code' => $request->currency_code,
                        'currency_date' => $request->currency_date,
                        'currency_rate' => $request->currency_rate
                    ]
                );
                Session::flash('success', 'Currency rate information saved successfully');
                // Redirect or return a response as needed
                return redirect('/settings/finance/currencyRate')->with('success', 'Currency rate information saved successfully');
            } else {
                Session::flash('error', 'some field are missing');
                return [
                    'code' => -1,
                    'msg' => $validator->errors(),
                ];
            }
        } catch (\Throwable $e) {
            throw $e;
            // dd($e);
        }
    }

    public function CurrencyRateEditData(Request $request)
    {
        try {
            $CurrencyRate = CurrencyRate::findOrFail($request->ed_currency_code);
            $CurrencyRate->currency_date = $request->input('ed_currency_date');
            $CurrencyRate->currency_rate = $request->input('ed_currency_rate');
            $CurrencyRate->save();
            // Redirect or return a response as needed
            return redirect('/settings/finance/currencyRate')->with('success', 'Currency rate information saved successfully');
        } catch (ModelNotFoundException $exception) {
            return redirect('/settings/finance/currencyRate')->with('error', 'Specified code was not found.');
        } catch (\Throwable $e) {
            throw $e;
            // dd($e);
        }
    }

    // COA STATUS ==================================================================================================
    public function CoaStatusInfo(Request $request)
    {
        return view('settings.finance.coaStatus', [
            'user' => $request->user(),
        ]);
    }

    public function CoaStatusData()
    {
        $COA_Status = COA_Status::select(['status_code', 'status_name', 'status']);
        return DataTables::Of($COA_Status)
            ->addColumn('action', function ($row) {
                if ($row->status === 'A') {
                    $status = 'Activate';
                } else {
                    $status = 'De Activate';
                }
                return '<button class="btn btn-outline-primary btn-sm" id="edit_coaStatus">Edit</button>
                    <button class="btn btn-outline-primary btn-sm" id="activate_coaStatus" value="' . $status . '">' . $status . '</button>';
            })->make(true);
    }

    public function CoaStatusAddData(Request $request)
    {
        try {
            $validator = Validator::make($request->all(), []);
            if ($validator) {
                // If the validation passes, you can proceed to store the data in the database or perform other actions
                COA_Status::create(
                    [
                        'status_code' => $request->status_code,
                        'status_name' => $request->status_name,
                        'status' => 'A'
                    ]
                );
                Session::flash('success', 'COA status information saved successfully');
                // Redirect or return a response as needed
                return redirect('/settings/finance/coaStatus')->with('success', 'COA status information saved successfully');
            } else {
                Session::flash('error', 'some field are missing');
                return [
                    'code' => -1,
                    'msg' => $validator->errors(),
                ];
            }
        } catch (\Throwable $e) {
            throw $e;
            // dd($e);
        }
    }

    public function CoaStatusEditData(Request $request)
    {
        try {
            $COA_Status = COA_Status::findOrFail($request->ed_status_code);
            $COA_Status->status_name = $request->input('ed_status_name');
            $COA_Status->save();
            // Redirect or return a response as needed
            return redirect('/settings/finance/coaStatus')->with('success', 'coaStatus information saved successfully');
        } catch (ModelNotFoundException $exception) {
            return redirect('/settings/finance/coaStatus')->with('error', 'Specified code was not found.');
        } catch (\Throwable $e) {
            throw $e;
        }
    }

    public function CoaStatusDeleteData(Request $request)
    {
        try {
            $COA_Status = COA_Status::findOrFail($request->del_status_code);
            if ($COA_Status->status == "A") {
                $status = "D";
            } else {
                $status = "A";
            }
            $COA_Status->update([
                'status' => $status,
            ]);
            // Redirect or return a response as needed
            return redirect('/settings/finance/coaStatus')->with('success', 'coaStatus deleted successfully');
        } catch (ModelNotFoundException $exception) {
            return redirect('/settings/finance/coaStatus')->with('error', 'Specified code was not found.');
        } catch (\Throwable $e) {
            throw $e;
            // dd($e);
        }
    }

    // TAX GROUPS ==================================================================================================
    public function taxGroupInfo(Request $request)
    {
        return view('settings.finance.taxGroup', [
            'user' => $request->user(),
        ]);
    }

    public function taxGroupData()
    {
        $TaxGroup = TaxGroup::select(['group_id', 'group_description']);
        return DataTables::Of($TaxGroup)
            ->addColumn('action', function ($row) {
                return '<button class="btn btn-outline-primary btn-sm" id="edit_taxGroup">Edit</button>';
            })->make(true);
    }

    public function taxGroupAddData(Request $request)
    {
        try {
            $validator = Validator::make($request->all(), []);
            if ($validator) {
                // If the validation passes, you can proceed to store the data in the database or perform other actions
                TaxGroup::create(
                    [
                        'group_id' => $request->group_id,
                        'group_description' => $request->group_description,
                    ]
                );
                // Redirect or return a response as needed
                return redirect('/settings/finance/taxGroup')->with('success', 'taxGroup information saved successfully');
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

    public function taxGroupEditData(Request $request)
    {
        try {
            $TaxGroup = TaxGroup::findOrFail($request->ed_group_id);
            $TaxGroup->group_description = $request->input('ed_group_description');
            $TaxGroup->save();
            // Redirect or return a response as needed
            return redirect('/settings/finance/taxGroup')->with('success', 'taxGroup information saved successfully');
        } catch (ModelNotFoundException $exception) {
            return redirect('/settings/finance/taxGroup')->with('error', 'Specified code was not found.');
        } catch (\Throwable $e) {
            throw $e;
        }
    }

    // TAX TYPES ==================================================================================================
    public function taxTypeInfo(Request $request)
    {
        return view('settings.finance.taxType', [
            'user' => $request->user(),
        ]);
    }

    public function taxTypeData()
    {
        $taxType = taxType::all();
        return DataTables::Of($taxType)
            ->addColumn('action', function ($row) {
                return '<button class="btn btn-outline-primary btn-sm" id="edit_taxType">Edit</button>';
            })->make(true);
    }

    public function taxTypeAddData(Request $request)
    {
        try {
            $validator = Validator::make($request->all(), []);
            if ($validator) {
                // If the validation passes, you can proceed to store the data in the database or perform other actions
                TaxType::create(
                    [
                        'tax_type' => $request->tax_type,
                        'type_description' => $request->type_description,
                        'add_deduct' => 'Y',
                        'control_account' => $request->control_account,
                        'transtype' => $request->transtype,
                        'basis' => $request->basis,
                        'tax_code' => $request->tax_code,
                        'analyse' => 'Y',
                    ]
                );
                // Redirect or return a response as needed
                return redirect('/settings/finance/taxType')->with('success', 'taxType information saved successfully');
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

    public function taxTypeEditData(Request $request)
    {
        try {
            $TaxType = TaxType::findOrFail($request->ed_id);
            $TaxType->tax_type = $request->input('ed_tax_type');
            $TaxType->type_description = $request->input('ed_type_description');
            $TaxType->add_deduct = $request->input('ed_add_deduct');
            $TaxType->control_account = $request->input('ed_control_account');
            $TaxType->transtype = $request->input('ed_transtype');
            $TaxType->basis = $request->input('ed_basis');
            $TaxType->tax_code = $request->input('ed_tax_code');
            $TaxType->analyse = $request->input('ed_analyse');
            $TaxType->save();
            // Redirect or return a response as needed
            return redirect('/settings/finance/taxType')->with('success', 'taxType information saved successfully');
        } catch (ModelNotFoundException $exception) {
            return redirect('/settings/finance/taxType')->with('error', 'Specified code was not found.');
        } catch (\Throwable $e) {
            throw $e;
        }
    }

    // TAX RATES ==================================================================================================
    public function taxRateInfo(Request $request)
    {
        return view('settings.finance.taxRate', [
            'user' => $request->user(),
            'taxGroup' => TaxGroup::all()
        ]);
    }

    public function taxRateData()
    {
        $TaxRate = TaxRate::all();
        return DataTables::Of($TaxRate)
            ->addColumn('action', function ($row) {
                return '<button class="btn btn-outline-primary btn-sm" id="edit_taxRate">Edit</button>';
            })->make(true);
    }

    public function taxRateAddData(Request $request)
    {
        try {
            $validator = Validator::make($request->all(), []);
            if ($validator) {
                // If the validation passes, you can proceed to store the data in the database or perform other actions
                TaxRate::create(
                    [
                        'group_id' => $request->group_id,
                        'tax_type' => $request->tax_type,
                        'tax_code' => $request->tax_code,
                        'tax_description' => $request->tax_description,
                        'tax_rate' => $request->tax_rate
                    ]
                );
                // Redirect or return a response as needed
                return redirect('/settings/finance/taxRate')->with('success', 'taxRate information saved successfully');
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

    public function taxRateEditData(Request $request)
    {
        try {
            $TaxRate = TaxRate::findOrFail($request->ed_id);
            $TaxRate->group_id = $request->input('ed_group_id');
            $TaxRate->tax_type = $request->input('ed_tax_type');
            $TaxRate->tax_code = $request->input('ed_tax_code');
            $TaxRate->tax_description = $request->input('ed_tax_description');
            $TaxRate->tax_rate = $request->input('ed_tax_rate');
            $TaxRate->save();
            // Redirect or return a response as needed
            return redirect('/settings/finance/taxRate')->with('success', 'taxRate information saved successfully');
        } catch (ModelNotFoundException $exception) {
            return redirect('/settings/finance/taxRate')->with('error', 'Specified code was not found.');
        } catch (\Throwable $e) {
            throw $e;
        }
    }

    // AR CUSTOMER GROUPS ==================================================================================================
    public function ArCustomerGroupInfo(Request $request)
    {
        return view('settings.finance.arCustomerGroup', [
            'user' => $request->user(),
            'taxGroup' => TaxGroup::all()
        ]);
    }

    public function ArCustomerGroupData()
    {
        $ARCustomerGroup = ARCustomerGroup::all();
        return DataTables::Of($ARCustomerGroup)
            ->addColumn('action', function ($row) {
                return '<button class="btn btn-outline-primary btn-sm" id="edit_arCustomerGroup">Edit</button>';
            })->make(true);
    }

    public function ArCustomerGroupAddData(Request $request)
    {
        try {
            $validator = Validator::make($request->all(), []);
            if ($validator) {
                ARCustomerGroup::create(
                    [
                        'group_id' => $request->group_id,
                        'group_title' => $request->group_title,
                        'group_description' => $request->group_description,
                        'default_currency' => $request->default_currency,
                        'control_account' => $request->control_account,
                        'tax_category' => $request->tax_category,
                    ]
                );
                return redirect('/settings/finance/arCustomerGroup')->with('success', 'arCustomerGroup information saved successfully');
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

    public function ArCustomerGroupEditData(Request $request)
    {
        try {
            $ARCustomerGroup = ARCustomerGroup::findOrFail($request->ed_group_id);
            $ARCustomerGroup->group_title = $request->input('ed_group_title');
            $ARCustomerGroup->group_description = $request->input('ed_group_description');
            $ARCustomerGroup->default_currency = $request->input('ed_default_currency');
            $ARCustomerGroup->control_account = $request->input('ed_control_account');
            $ARCustomerGroup->tax_category = $request->input('ed_tax_category');
            $ARCustomerGroup->save();
            // Redirect or return a response as needed
            return redirect('/settings/finance/arCustomerGroup')->with('success', 'arCustomerGroup information saved successfully');
        } catch (ModelNotFoundException $exception) {
            return redirect('/settings/finance/arCustomerGroup')->with('error', 'Specified code was not found.');
        } catch (\Throwable $e) {
            throw $e;
        }
    }

    // AR CUSTOMER ==================================================================================================
    public function ArCustomerInfo(Request $request)
    {
        return view('settings.finance.arCustomer', [
            'user' => $request->user(),
            'taxGroup' => TaxGroup::all()
        ]);
    }

    public function ArCustomerData()
    {
        $ARCustomer = ARCustomer::all();
        return DataTables::Of($ARCustomer)
            ->addColumn('action', function ($row) {
                return '<button class="btn btn-outline-primary btn-sm" id="edit_arCustomer">Edit</button>';
            })->make(true);
    }

    // CB SOURCES ==================================================================================================
    public function CbSourceInfo(Request $request)
    {
        return view('settings.finance.cbSource', [
            'user' => $request->user(),
        ]);
    }

    public function CbSourceData()
    {
        $CBSource = CBSource::all();
        return DataTables::Of($CBSource)
            ->addColumn('action', function ($row) {
                return '<button class="btn btn-outline-primary btn-sm" id="edit_cbSource">Edit</button>';
            })->make(true);
    }

    public function CbSourceAddData(Request $request)
    {
        try {
            $validator = Validator::make($request->all(), []);
            if ($validator) {
                CBSource::create(
                    [
                        'source_code' => $request->source_code,
                        'source_name' => $request->source_name,
                    ]
                );
                return redirect('/settings/finance/cbSource')->with('success', 'cbSource information saved successfully');
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

    public function CbSourceEditData(Request $request)
    {
        try {
            $CBSource = CBSource::findOrFail($request->ed_source_code);
            $CBSource->source_name = $request->input('ed_source_name');
            $CBSource->save();
            // Redirect or return a response as needed
            return redirect('/settings/finance/cbSource')->with('success', 'cbSource information saved successfully');
        } catch (ModelNotFoundException $exception) {
            return redirect('/settings/finance/cbSource')->with('error', 'Specified code was not found.');
        } catch (\Throwable $e) {
            throw $e;
        }
    }

    // CB PAY METHODS ==================================================================================================
    public function CbPayMethodInfo(Request $request)
    {
        return view('settings.finance.cbPayMethod', [
            'user' => $request->user(),
        ]);
    }

    public function CbPayMethodData()
    {
        $CBPaymethod = CBPaymethod::all();
        return DataTables::Of($CBPaymethod)
            ->addColumn('action', function ($row) {
                return '<button class="btn btn-outline-primary btn-sm" id="edit_cbPayMethod">Edit</button>';
            })->make(true);
    }

    public function CbPayMethodAddData(Request $request)
    {
        try {
            $validator = Validator::make($request->all(), []);
            if ($validator) {
                CBPaymethod::create(
                    [
                        'pay_method_code' => $request->pay_method_code,
                        'pay_method_name' => $request->pay_method_name,
                    ]
                );
                return redirect('/settings/finance/cbPayMethod')->with('success', 'cbPayMethod information saved successfully');
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

    public function CbPayMethodEditData(Request $request)
    {
        try {
            $CBPaymethod = CBPaymethod::findOrFail($request->ed_pay_method_code);
            $CBPaymethod->pay_method_name = $request->input('ed_pay_method_name');
            $CBPaymethod->save();
            // Redirect or return a response as needed
            return redirect('/settings/finance/cbPayMethod')->with('success', 'cbPayMethod information saved successfully');
        } catch (ModelNotFoundException $exception) {
            return redirect('/settings/finance/cbPayMethod')->with('error', 'Specified code was not found.');
        } catch (\Throwable $e) {
            throw $e;
        }
    }

    // CB TRANS TYPES ==================================================================================================
    public function CbTransTypeInfo(Request $request)
    {
        return view('settings.finance.cbTransType', [
            'user' => $request->user(),
            'cbSources' => CBSource::all(),
        ]);
    }

    public function CbTransTypeData()
    {
        $CBTransType = CBTransType::all();
        return DataTables::Of($CBTransType)
            ->addColumn('action', function ($row) {
                return '<button class="btn btn-outline-primary btn-sm" id="edit_cbTransType">Edit</button>';
            })->make(true);
    }

    public function CbTransTypeAddData(Request $request)
    {
        try {
            $validator = Validator::make($request->all(), []);
            if ($validator) {
                CBTransType::create(
                    [
                        'doc_type' => $request->doc_type,
                        'type_code' => $request->type_code,
                        'source_code' => $request->source_code,
                        'description' => $request->description,
                        'debit_account' => $request->debit_account,
                        'credit_account' => $request->credit_account,
                        'status' => 'A',
                    ]
                );
                return redirect('/settings/finance/cbTransType')->with('success', 'cbTransType information saved successfully');
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

    public function CbTransTypeEditData(Request $request)
    {
        // dd($request);
        try {
            $CBTransType = CBTransType::where('doc_type', $request->ed_doc_type)->where('type_code', $request->ed_type_code)->update([
                'source_code' => $request->ed_source_code,
                'description' => $request->ed_description,
                'debit_account' => $request->ed_debit_account,
                'credit_account' => $request->ed_credit_account,
            ]);
            // $CBTransType->doc_type = $request->input('ed_doc_type');
            // $CBTransType->source_code = $request->input('ed_source_code');
            // $CBTransType->description = $request->input('ed_description');
            // $CBTransType->debit_account = $request->input('ed_debit_account');
            // $CBTransType->credit_account = $request->input('ed_credit_account');
            // $CBTransType->save();
            // Redirect or return a response as needed
            return redirect('/settings/finance/cbTransType')->with('success', 'cbTransType information saved successfully');
        } catch (ModelNotFoundException $exception) {
            return redirect('/settings/finance/cbTransType')->with('error', 'Specified code was not found.');
        } catch (\Throwable $e) {
            throw $e;
        }
    }


    // CB TRANS TYPES ==================================================================================================
    public function cDepartmentsInfo(Request $request)
    {
        return view('settings.finance.cDepartments', [
            'user' => $request->user(),
            'companies' => Company::all(),
        ]);
    }

    public function cDepartmentsData()
    {
        $cDepartments = Department::all();
        return DataTables::Of($cDepartments)
            ->addColumn('action', function ($row) {
                return '<button class="btn btn-outline-primary btn-sm" id="edit_cDepartments">Edit</button>';
            })->make(true);
    }

    public function cDepartmentsAddData(Request $request)
    {
        try {
            $validator = Validator::make($request->all(), []);
            if ($validator) {
                Department::create(
                    [
                        'company_id' => $request->company_id,
                        'department_code' => $request->department_code,
                        'department_name' => $request->department_name,
                        'status' => 'A',
                    ]
                );
                return redirect('/settings/finance/cDepartments')->with('success', 'cDepartments information saved successfully');
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

    public function cDepartmentsEditData(Request $request)
    {
        try {
            $cDepartments = Department::findOrFail($request->ed_department_code);
            $cDepartments->company_id = $request->input('ed_company_id');
            $cDepartments->department_name = $request->input('ed_department_name');
            $cDepartments->save();
            // Redirect or return a response as needed
            return redirect('/settings/finance/cDepartments')->with('success', 'cDepartments information saved successfully');
        } catch (ModelNotFoundException $exception) {
            return redirect('/settings/finance/cDepartments')->with('error', 'Specified code was not found.');
        } catch (\Throwable $e) {
            throw $e;
        }
    }

    // CB DEDUCTIONS ==================================================================================================
    public function cbDeductionsInfo(Request $request)
    {
        return view('settings.finance.cbDeductions', [
            'user' => $request->user(),
            'glaccounts' => COA_Config::where('segment_code', 0)->get(),
        ]);
    }

    public function cbDeductionsData()
    {
        $cbDeductions = CBDeductions::all();
        return DataTables::Of($cbDeductions)
            ->addColumn('action', function ($row) {
                return '<button class="btn btn-outline-primary btn-sm" id="edit_cbDeductions">Edit</button>';
            })->make(true);
    }

    public function cbDeductionsAddData(Request $request)
    {
        try {
            $validator = Validator::make($request->all(), []);
            if ($validator) {
                CBDeductions::create(
                    [
                        'doc_type' => $request->doc_type,
                        'deduction_code' => $request->deduction_code,
                        'deduction_name' => $request->deduction_name,
                        'percentage_flag' => $request->percentage_flag,
                        'percentage' => $request->percentage,
                        'default_amount' => $request->default_amount,
                        'percentage_basis' => $request->percentage_basis,
                        'add_deduct' => $request->add_deduct,
                        'account_no' => $request->account_no ? $request->account_no : ' ',
                        'created_date' => date('Y-m-d'),
                        'created_by' => Auth::user()->user_name,
                    ]
                );
                return redirect('/settings/finance/cbDeductions')->with('success', 'cbDeductions information saved successfully');
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

    public function cbDeductionsEditData(Request $request)
    {
        try {
            $cbDeductions = CBDeductions::findOrFail($request->ed_deduction_code);
            $cbDeductions->doc_type = $request->input('ed_doc_type');
            $cbDeductions->deduction_name = $request->input('ed_deduction_name');
            $cbDeductions->percentage_flag = $request->input('ed_percentage_flag');
            $cbDeductions->percentage = $request->input('ed_percentage');
            $cbDeductions->default_amount = $request->input('ed_default_amount');
            $cbDeductions->percentage_basis = $request->input('ed_percentage_basis');
            $cbDeductions->add_deduct = $request->input('ed_add_deduct');
            $cbDeductions->account_no = $request->input('ed_account_no');
            $cbDeductions->save();
            // Redirect or return a response as needed
            return redirect('/settings/finance/cbDeductions')->with('success', 'cbDeductions information saved successfully');
        } catch (ModelNotFoundException $exception) {
            return redirect('/settings/finance/cbTransType')->with('error', 'Specified code was not found.');
        } catch (\Throwable $e) {
            throw $e;
        }
    }
}
