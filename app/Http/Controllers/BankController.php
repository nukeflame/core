<?php

namespace App\Http\Controllers;

use Carbon\Carbon;
use App\Models\Banks;
use App\Models\BankBranch;
use App\Models\COA_Config;
use Illuminate\Http\Request;
use App\Models\BankTransaction;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Session;
use Yajra\DataTables\Facades\DataTables;
use Illuminate\Support\Facades\Validator;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Validation\ValidationException;
use Symfony\Component\HttpFoundation\Response;

class BankController extends Controller
{
    // BANKS ==================================================================================================
    public function banksInfo(Request $request)
    {
        return view('finance.bank.banks', [
            'user' => $request->user(),
        ]);
    }

    public function banksData()
    {
        $banks = Banks::all();
        return DataTables::Of($banks)
            ->addColumn('action', function ($row) {
                return '<button class="btn btn-outline-primary btn-sm" id="edit_banks">Edit</button>';
            })->make(true);
    }

    public function banksAddData(Request $request)
    {
        try {
            $validator = Validator::make($request->all(), []);
            if ($validator) {
                Banks::create(
                    [
                        'bank_code' => $request->bank_code,
                        'bank_name' => $request->bank_name,
                        'swift_code' => $request->swift_code,
                        'created_date' => date('Y-m-d'),
                        'created_by' => Auth::user()->user_name,
                    ]
                );
                return redirect('/finance/bank/banks')->with('success', 'banks information saved successfully');
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

    public function banksEditData(Request $request)
    {
        try {
            $banks = Banks::findOrFail($request->ed_bank_code);
            $banks->bank_name = $request->input('ed_bank_name');
            $banks->swift_code = $request->input('ed_swift_code');
            $banks->save();
            // Redirect or return a response as needed
            return redirect('/finance/finance/banks')->with('success', 'banks information saved successfully');
        } catch (ModelNotFoundException $exception) {
            return redirect('/finance/finance/cbTransType')->with('error', 'Specified code was not found.');
        } catch (\Throwable $e) {
            throw $e;
        }
    }

    // BANKS BRANCHES ==================================================================================================
    public function bankBranchesInfo(Request $request)
    {
        return view('finance.bank.bankBranches', [
            'user' => $request->user(),
            'banks' => Banks::all(),
            'bank_glaccounts' => COA_Config::where('segment_code','COD')->where('bank_flag','Y')->get(),
        ]);
    }

    public function bankBranchesData()
    {
        $bankBranches = BankBranch::all();
        return DataTables::Of($bankBranches)
            ->addColumn('action', function ($row) {
                return '<button class="btn btn-outline-primary btn-sm" id="edit_bankBranches">Edit</button>';
            })->make(true);
    }

    public function bankBranchesAddData(Request $request)
    {
        try {
            $validator = Validator::make($request->all(), []);
            if ($validator) {
                BankBranch::create(
                    [
                        'bank_code' => $request->bank_code,
                        'bank_branch_code' => $request->bank_branch_code,
                        'bank_branch_name' => $request->bank_branch_name,
                        'created_date' => Carbon::now(),
                        'created_by' => Auth::user()->user_name,
                        'updated_by' => Auth::user()->user_name,
                        'bank_account_no' => $request->bank_account_no,
                        'bank_account_name' =>$request->bank_account_name,
                        'gl_account' => $request->gl_account,
                        'status' => 'A',
                    ]
                );
                return redirect('/finance/bank/bankBranches')->with('success', 'bankBranches information saved successfully');
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

    public function bankBranchesEditData(Request $request)
    {
        try {
            $bankBranches = BankBranch::where('bank_code',$request->ed_bank_code)->where('bank_branch_code',$request->ed_bank_branch_code)->update([
                'bank_branch_name' => $request->ed_bank_branch_name,
                'bank_account_no' => $request->ed_bank_account_no,
                'bank_account_name' => $request->ed_bank_account_name,
                'gl_account' => $request->ed_gl_account,
            ]);
            // $bankBranches->bank_code = $request->input('ed_bank_code');
            // $bankBranches->bank_branch_name = $request->input('ed_bank_branch_name');
            // $bankBranches->save();
            // Redirect or return a response as needed
            return redirect('/finance/finance/bankBranches')->with('success', 'bankBranches information saved successfully');
        } catch (ModelNotFoundException $exception) {
            return redirect('/finance/finance/cbTransType')->with('error', 'Specified code was not found.');
        } catch (\Throwable $e) {
            throw $e;
        }
    }
    function BankTransaction(Request $request) {

        return view('finance.bank.bank_transactions', [
            'bank_trans' => BankTransaction::all(),
            'bank_glaccounts' => COA_Config::where('segment_code','COD')->where('bank_flag','Y')->get(),
        ]);
    }


    function BankBranchDataTable(Request $request) {
        
        $bankBranches = BankBranch::orderBy('gl_account')->get();
        return DataTables::Of($bankBranches)
        ->addColumn('gl_account_name', function ($data) {
            $gl_name = COA_Config::where('account_number',$data->gl_account)->first() ;
            return $gl_name->description;
        })
        ->addColumn('bank_name', function ($data) {
            $bk_name = Banks::where('bank_code',$data->bank_code)->first() ;
            return $bk_name->bank_name;
        })
        ->make(true);
        
    }

    function BankBranchEnquiry(Request $request) {
        $bank_code = $request->bk_code;
        // $bank_branch_code = $request->bk_branch_code;
        $bank_account_no = $request->bk_account_no;
        $gl_account_no = $request->gl_account_no;

        $bank = Banks::where('bank_code',$bank_code)->first();
        $bankBranch = BankBranch::where('bank_code',$bank_code)->where('bank_account_no',$bank_account_no)->where('gl_account',$gl_account_no)->first();
        $coa_config = COA_Config::where('segment_code','COD')->where('account_number',$gl_account_no)->first();
        $bankTrans = BankTransaction::where('bank_acc_code',$gl_account_no)->orderBy('created_at')->get();

        return view('finance.bank.bank_trans_home',[
            'bank'=>$bank,
            'bankBranch'=>$bankBranch,
            'coa_config'=>$coa_config,
            'bankTrans'=>$bankTrans,
        ]);
    }

    function BankTransData(Request $request) {
        $gl_account_no = $request->gl_account_no;

        $bankTrans = BankTransaction::where('bank_acc_code',$gl_account_no);
        // dd($bankTrans);
        return DataTables::of($bankTrans)
        ->make(true);
    }

    function BankTransRecon(Request $request) {
        DB::beginTransaction();
        try {
        // Iterate over each recon item in the request
        foreach ($request->recon as $key => $recon) {
            // Explode the recon string into its components
            $recon_parts = explode('-', $recon);
    
            // Ensure the exploded array has the expected number of parts
            if (count($recon_parts) === 5) {
                // Assign individual parts to variables
                $batch_no = $recon_parts[0];
                $source = $recon_parts[1];
                $reference_no = $recon_parts[2];
                $doc_type = $recon_parts[3];
                $gl_account_no = $recon_parts[4];
                $bankTrans = BankTransaction::where('batch_no',$batch_no)
                                            ->where('source',$source)
                                            ->where('reference_no',$reference_no)
                                            ->where('doc_type',$doc_type)
                                            ->where('bank_acc_code',$gl_account_no)
                                            ->update([
                                                'reconcilled' =>'Y',
                                                'reconcilliation_date' => Carbon::now(),
                                                'reconcilliation_year' => str_pad(Carbon::now()->year, 4, '0', STR_PAD_LEFT),
                                                'reconcilliation_month' => str_pad(Carbon::now()->month, 2, '0', STR_PAD_LEFT)
                                            ]);
                // dd($bankTrans);
            } else {
                // Handle the case where the recon does not have the expected format
                dd('Unexpected recon format.');
            }
        }
        DB::commit();
            
        return response()->json([
            'status' => Response::HTTP_CREATED,
            'message' => 'Recon saved successfully'
        ]);
    } catch (ValidationException $e) {
        return response()->json([
            'status' => Response::HTTP_UNPROCESSABLE_ENTITY,
            'errors' => $e->errors()
        ], 422);
    }
    catch(Throwable $e)
    {
        DB::rollBack();
        dd($e);
        return response()->json([
            'status' => $e->getCode(),
            'message' => $e->getMessage()
        ]);
    }
    }        
    
    
    //END
}
