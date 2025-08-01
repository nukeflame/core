<?php

namespace App\Http\Controllers;

use App\Http\Controllers\GLedgerController;

use App\Models\User;
use App\Models\Banks;
use App\Models\Branch;
use App\Models\Company;
use App\Models\CashBook;
use App\Models\CBSource;
use App\Models\Currency;
use App\Models\Customer;
use App\Models\TaxGroup;
use App\Models\BankBranch;
use App\Models\COA_Config;
use App\Models\CoverDebit;
use App\Models\CashBookana;
use App\Models\CBPaymethod;
use App\Models\CBTransType;
use App\Models\CBDeductions;
use App\Models\CurrencyRate;
use Illuminate\Http\Request;
use App\Models\CBRequisition;
use App\Models\ClaimRegister;
use App\Models\FinancePeriod;
use App\Models\SystemProcess;
use Illuminate\Support\Carbon;
use App\Models\CBDeductionMain;
use App\Models\Department;
use Illuminate\Support\Facades\DB;
use App\Models\SystemProcessAction;
use Illuminate\Support\Facades\Auth;
use Yajra\DataTables\Facades\DataTables;
use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\ValidationException;
use Symfony\Component\HttpFoundation\Response;

class PaymentController extends Controller
{
    private $_year;
    private $_month;
    public function __construct()
    {
        $this->_year = Carbon::now()->year;
        $this->_month = Carbon::now()->month;
    }

    public function getPayments(Request $request)
    {
        return view('finance.pay.payments', [
            'user' => $request->user(),
            'companies' => Company::all(),
        ]);
    }

    public function paymentsData()
    {
        $CBRequisition = CBRequisition::all();

        return DataTables::Of($CBRequisition)
            ->addColumn('cancelled', function ($row) {
                if ($row->cancelled === 'Y') {
                    return 'Yes';
                } else {
                    return 'No';
                }
            })->make(true);
    }

    public function postedReqData()
    {
        $CBRequisition = CBRequisition::where('voucher_raised_flag', 'Y');

        return DataTables::Of($CBRequisition)
            ->addColumn('cancelled', function ($row) {
                if ($row->cancelled === 'Y') {
                    return 'Yes';
                } else {
                    return 'No';
                }
            })->make(true);
    }

    public function unpostedReqData()
    {
        $CBRequisition = CBRequisition::where('voucher_raised_flag', '!=', 'Y');

        return DataTables::Of($CBRequisition)
            ->addColumn('cancelled', function ($row) {
                if ($row->cancelled === 'Y') {
                    return 'Yes';
                } else {
                    return 'No';
                }
            })->make(true);
    }

    public function raiseRequisition(Request $request)
    {
        return view('finance.pay.raiseRequisition', [
            'user' => $request->user(),
            'branches' => Branch::all(),
            'types' => CBTransType::all(),
            'accPeriod' => FinancePeriod::all(),
            'coverDebits' => CoverDebit::all(),
            'currency' => Currency::all(),
            'customers' => Customer::all(),
            'cbsources' => CBSource::all(),
            'departments' => Department::all(),
            'banks' => Banks::all(),
            'bank_branches' => BankBranch::all(),
            'deductions' => CBDeductions::where('doc_type', 'PAY')->get(),
            'taxgroups' => TaxGroup::all(),
            'glaccounts' => COA_Config::where('segment_code', 'COD')->get(),
        ]);
    }

    public function requisitionData()
    {
        $CBRequisition = CBRequisition::all();

        return DataTables::Of($CBRequisition)
            ->addColumn('action', function ($row) {
                return '<button>View</button>';
            })->make(true);
    }

    public function requisitionAddData(Request $request)
    {
        DB::beginTransaction();
        try {

            $validator = Validator::make($request->all(), []);
            if ($validator) {
                $gross_amount = (int)str_replace(",", "", $request->gross_amount);
                $local_amount = (int)str_replace(",", "", $request->local_amount);
                $today = Carbon::now();
                $acc_year = date('Y');
                $acc_month = date('m');

                $offcd = $request->offcd;

                $count_get = CBRequisition::where('offcd', $offcd)
                    ->where('dept_code', $request->dept_code)
                    ->where('account_year', $acc_year)
                    ->get();

                if (count($count_get) > 0) {
                    $count_serial = CBRequisition::where('offcd', $offcd)
                        ->where('dept_code', $request->dept_code)
                        ->where('account_year', $acc_year)
                        ->count();
                } else {
                    $count_serial = 0;
                }

                $serial_no = STR_PAD(($count_serial + 1), 6, '0', STR_PAD_LEFT);

                $cbTransType = CBTransType::where('doc_type', 'PAY')->where('type_code', $request->cbtrans_type_code)->get()->first();
                if ($cbTransType) {
                    $doc_type = $cbTransType->doc_type;
                    $source_code = $cbTransType->source_code;
                    $entry_type_descr = $cbTransType->type_code;
                    $debit_account = $cbTransType->debit_account;
                    $credit_account = $cbTransType->credit_account;
                } else {
                    $doc_type = null;
                    $source_code = null;
                    $entry_type_descr = null;
                    $debit_account = null;
                    $credit_account = null;
                }

                if (isset($request->deduction_addition_amount)) {
                    // $foreign_add_deduct_amount = array_sum((int)str_replace(",","",$request->deduction_addition_amount));
                    $deduction_addition_amount = array_map(function ($value) {
                        return (int)str_replace(",", "", $value);
                    }, $request->deduction_addition_amount);

                    $foreign_add_deduct_amount = array_sum($deduction_addition_amount);

                    $local_add_deduct_amount = $foreign_add_deduct_amount * $request->currency_rate;
                    $foreign_nett_amount = $gross_amount - $foreign_add_deduct_amount;
                    $local_nett_amount = $local_amount - $local_add_deduct_amount;
                } else {
                    $foreign_add_deduct_amount = 0;
                    $local_add_deduct_amount = 0;
                    $foreign_nett_amount = $gross_amount;
                    $local_nett_amount = $local_amount;
                }

                if (isset($request->cover_no)) {
                    $cover_no = $request->cover_no;
                    $coverDebits = CoverDebit::where('cover_no', $cover_no)->get()->first();
                    $endorsement_no = $coverDebits->endorsement_no;
                    $dr_no = $coverDebits->dr_no;
                } else {
                    $cover_no = '';
                    $endorsement_no = '';
                    $dr_no = '';
                }

                if ($request->doc_type == 'U/W') {
                    $Customer = Customer::where('customer_id', $request->customer_id)->first();
                    $customerName = $Customer->name;
                    $customerID = $Customer->customer_id;
                } else {
                    $customerName = $request->payee;
                    $customerID = 0;
                }
                $bank = Banks::where('bank_code', $request->payee_bank)->first();
                $bank_branch = BankBranch::where('bank_code', $request->payee_bank)->where('bank_branch_code', $request->payee_bank_branch)->first();
                $requisition_no = $offcd . $request->dep_code . $serial_no . $acc_year . $acc_month;

                $create_requisition = new CBRequisition();
                $create_requisition->doc_type = $doc_type;
                $create_requisition->dept_code = $request->dept_code;
                $create_requisition->serial_no = $serial_no;
                $create_requisition->account_year = $acc_year;
                $create_requisition->account_month = $acc_month;
                $create_requisition->effective_date = $today;
                $create_requisition->cheque_no = $request->cheque_no ? $request->cheque_no : null;
                $create_requisition->cheque_date = $request->cheque_no ? $request->cheque_date : null;
                $create_requisition->name = $customerName;
                $create_requisition->customer_id = $customerID;
                $create_requisition->foreign_gross_amount = $gross_amount;
                $create_requisition->local_gross_amount = $local_amount;
                $create_requisition->foreign_add_deduct_amount = $foreign_add_deduct_amount;
                $create_requisition->local_add_deduct_amount = $local_add_deduct_amount;
                $create_requisition->foreign_nett_amount = $foreign_nett_amount;
                $create_requisition->local_nett_amount = $local_nett_amount;
                $create_requisition->created_date = $today;
                $create_requisition->created_by = Auth::user()->user_name;
                $create_requisition->updated_date = $today;
                $create_requisition->updated_by = Auth::user()->user_name;
                $create_requisition->entry_type_descr = $entry_type_descr;
                $create_requisition->cover_no = $cover_no;
                $create_requisition->claim_no = $request->claim_no;
                $create_requisition->endorsement_no = $endorsement_no;
                $create_requisition->debit_account =  '0';
                $create_requisition->credit_account = '0';
                $create_requisition->classcode = $request->classcode ? $request->classcode : ' ';
                $create_requisition->analyse_payment = $request->analyse_payment;
                $create_requisition->checked_flag = 'N';
                $create_requisition->authorized_flag = 'N';
                $create_requisition->approved_flag = 'N';
                $create_requisition->checked_by = null;
                $create_requisition->authorized_by = null;
                $create_requisition->approved_by = null;
                $create_requisition->checked_date = null;
                $create_requisition->authorized_date = null;
                $create_requisition->approved_date = null;
                $create_requisition->source_code = $source_code;
                $create_requisition->offcd = $offcd;
                $create_requisition->branch = $offcd;
                $create_requisition->narration = $request->narration;
                $create_requisition->payee_bank_code = $request->payee_bank;
                $create_requisition->payee_bank_name = $bank->bank_name;
                $create_requisition->payee_bank_branch_code = $request->payee_bank_branch;
                $create_requisition->payee_bank_branch_name = $bank_branch->bank_branch_name;
                $create_requisition->payee_bank_acc_no = $request->payee_bank_account;
                $create_requisition->currency_code = $request->currency_code;
                $create_requisition->currency_rate = $request->currency_rate;
                $create_requisition->cancelled_flag = 'N';
                $create_requisition->cancelled_by = null;
                $create_requisition->cancelled_date = null;
                $create_requisition->voucher_raised_flag = 'N';
                $create_requisition->voucher_posted_flag = 'N';
                $create_requisition->invoice_no = $dr_no;
                $create_requisition->requisition_no = $requisition_no;
                $create_requisition->wht_cert_no = null;
                $create_requisition->save();
                // dd($create_requisition);
                if ($create_requisition) {

                    $Requisition = CBRequisition::where('offcd', $offcd)
                        ->where('requisition_no', $requisition_no)
                        ->where('account_year', $acc_year)
                        ->where('account_month', $acc_month)
                        ->where('doc_type', $doc_type)
                        ->first();

                    if ($request->analyse_payment == 'Y') {
                        for ($i = 0; $i < count($request->GL_account); $i++) {
                            $n = $i + 1;
                            $GL_account = $request->GL_account[$i];
                            $analysed_narration = $request->analysed_narration[$i];
                            $analyse_amt = $request->analyse_amt[$i];
                            $create_cashbookana = new CashBookana();
                            $create_cashbookana->source_code = $source_code;
                            $create_cashbookana->offcd = $Requisition->offcd;
                            $create_cashbookana->doc_type = $Requisition->doc_type;
                            $create_cashbookana->orig_entry_type_descr = $entry_type_descr;
                            $create_cashbookana->reference_no = $serial_no;
                            $create_cashbookana->line_no = $n;
                            $create_cashbookana->cover_no = $Requisition->cover_no;
                            $create_cashbookana->endorsement_no = $Requisition->endorsement_no;
                            $create_cashbookana->item_no = $n;
                            $create_cashbookana->gl_account = $GL_account;
                            $create_cashbookana->dr_cr = '';
                            $create_cashbookana->created_by = Auth::user()->user_name;
                            $create_cashbookana->analyse_amount = $analyse_amt;
                            $create_cashbookana->unallocated_amount = 0;
                            $create_cashbookana->allocated_amount = 0;
                            $create_cashbookana->updated_by = Auth::user()->user_name;
                            $create_cashbookana->updated_date = $today;
                            $create_cashbookana->branch = $request->offcd;
                            $create_cashbookana->amount_in_words = null;
                            $create_cashbookana->entry_type_descr = $entry_type_descr;
                            $create_cashbookana->debit_note_no = null;
                            $create_cashbookana->currency_code = $request->currency_code;
                            $create_cashbookana->currency_rate = $request->currency_rate;
                            $create_cashbookana->narration = $analysed_narration;
                            $create_cashbookana->customer_id = $request->customer_i;
                            $create_cashbookana->save();
                        }
                    }

                    if ($request->reflect_add_dedu == 'Y') {
                        for ($i = 0; $i < count($request->deduction_addition_code); $i++) {
                            $n = $i + 1;
                            $deduction_addition_code = $request->deduction_addition_code[$i];
                            $deduct_param = CBDeductions::where('doc_type', $doc_type)->where('deduction_code', $deduction_addition_code)->first();
                            $deduction_addition_amount = $request->deduction_addition_amount[$i];
                            $create_deduction_addition = new CBDeductionMain();
                            $create_deduction_addition->reference_no = $requisition_no;
                            $create_deduction_addition->cb_source_code = $source_code;
                            $create_deduction_addition->deduction_code = $deduction_addition_code;
                            $create_deduction_addition->deduction_name = $deduct_param->deduction_name;
                            $create_deduction_addition->foreign_amount = $deduction_addition_amount;
                            $create_deduction_addition->local_amount = $deduction_addition_amount * $request->currency_rate;
                            $create_deduction_addition->add_deduct = $deduct_param->add_deduct;
                            $create_deduction_addition->account_no = $deduct_param->account_no;
                            $create_deduction_addition->created_date = $today;
                            $create_deduction_addition->created_by = Auth::user()->user_name;
                            $create_deduction_addition->save();
                        }
                    }
                }
                DB::commit();

                $redirectUrl = route('requisition.payRequestDetails', [
                    'requisition_no' => $requisition_no
                ]);

                return redirect($redirectUrl)->with('success', 'Requisition information saved successfully');

                // return redirect('/finance/pay/raiseRequisition')->with('success', 'Requisition information saved successfully');
            }
        } catch (\Throwable $e) {
            DB::rollback();
            dd($e);
        }
    }

    public function paymentsgetCurrencyRate(Request $request)
    {
        try {
            $Currency = Currency::where(['currency_code' => $request->currency_code])->firstOrfail();
            if ($Currency->base_currency == 'Y') {
                $CurRate = 1;
            } else {
                $CurrencyRate = CurrencyRate::where(['currency_code' => $request->currency_code])->firstOrfail();
                if ($CurrencyRate) {
                    $CurRate = $CurrencyRate->currency_rate;
                } else {
                    $CurRate = 'Currency rate not set, kindly go to settings and set currency rate!';
                }
            }
            return response(json_encode([
                'currency_rate' => $CurRate,
            ]));
        } catch (\Throwable $th) {
            throw $th;
        }
    }

    public function payRequestDetails(Request $request)
    {
        $CBRequisition = CBRequisition::where('requisition_no', $request->requisition_no)->first();
        $Currency = Currency::where('currency_code', $CBRequisition->currency_code)->first();
        $Banks = Banks::where('bank_code', $CBRequisition->payee_bank_code)->first();
        $Department = Department::where('department_code', $CBRequisition->dept_code)->first();
        if ($CBRequisition->doc_type == 'U/W') {
            $Customer = Customer::where('customer_id', $CBRequisition->customer_id)->first();
            $customerName = $Customer->name;
        } else {
            $customerName = $CBRequisition->name;
        }
        $CBDeductionMain = CBDeductionMain::where('reference_no', $CBRequisition->requisition_no)->get();
        $claim_no = ClaimRegister::where('cover_no', $CBRequisition->cover_no)->first();
        if ($claim_no) {
            $claim_no = $claim_no->claim_no;
        } else {
            $claim_no = '';
        }
        $req_authorizers = User::permission('authorize requisition')
            ->where('user_name', '<>', Auth::user()->user_name)
            ->get();
        $req_approvers = User::permission('approve requisition')
            ->where('user_name', '<>', Auth::user()->user_name)
            ->get();

        $process = SystemProcess::where('nice_name', 'requisition-process')->first();
        $reqAuthorizeprocessAction = SystemProcessAction::where('nice_name', 'authorize-requisition')->first();
        $reqApproveprocessAction = SystemProcessAction::where('nice_name', 'approve-requisition')->first();

        return view('finance.pay.payRequestDetails', [
            'user' => $request->user(),
            'currency' => $Currency,
            'Banks' => $Banks,
            'Department' => $Department,
            'customerName' => $customerName,
            'voucher' => $CBRequisition,
            'ded_add' => $CBDeductionMain,
            'claim_no' => $claim_no,
            'payment_modes' => CBPaymethod::all(),
            'glaccounts' => COA_Config::where('segment_code', 'COD')->where('status', 'A')->get(),
            'req_authorizers' => $req_authorizers,
            'req_approvers' => $req_approvers,
            'process' => $process,
            'reqAuthorizeprocessAction' => $reqAuthorizeprocessAction,
            'reqApproveprocessAction' => $reqApproveprocessAction,
        ]);
    }

    function payRequestAuthorize(Request $request)
    {
        // dd($request);
        try {
            DB::beginTransaction();

            $requisition_no = $request->requisition_no;
            $credit_acc = $request->credit_acc;
            $debit_acc = $request->debit_acc;
            $payment_mode = $request->payment_mode;
            $updRequest = CBRequisition::where('requisition_no', $requisition_no)->update([
                'debit_account' => $debit_acc,
                'credit_account' => $credit_acc,
                'pay_method_code' => $payment_mode,
            ]);
            $Approvals = new ApprovalsController();
            $Approvals->sendForApproval($request);

            DB::commit();

            return response()->json([
                'status' => Response::HTTP_CREATED,
                'message' => 'Data saved successfully'
            ]);
        } catch (ValidationException $e) {
            return response()->json([
                'status' => Response::HTTP_UNPROCESSABLE_ENTITY,
                'errors' => $e->errors()
            ], 422);
        } catch (\Exception $e) {
            DB::rollBack();
            dd($e, $request->all());
            return response()->json([
                'status' => $e->getCode(),
                'message' => 'Failed to save data'
            ]);
        }
    }

    function payRequestPost(Request $request)
    {
        try {
            DB::beginTransaction();

            $requisition_no = $request->requisition_no;
            $process = $request->process;
            $process_action = $request->process_action;

            $requisition = CBRequisition::where('requisition_no', $requisition_no)->first();
            $year = $this->_year;
            $month = $this->_month;
            $transaction_no = (int)CashBook::where('account_year', $year)->max('transaction_no') + 1;
            $today = Carbon::now();
            $receipt_no = STR_PAD($transaction_no, 6, '0', STR_PAD_LEFT);
            $reference_no = STR_PAD($transaction_no, 6, '0', STR_PAD_LEFT) . $year;
            $cbdeduction_count = CBDeductionMain::where('reference_no', $requisition_no)->count();
            $coa_config_dr = COA_Config::where('account_number', $requisition->debit_account)
                ->where('segment_code', 'COD')
                ->first();
            $coa_config_cr = COA_Config::where('account_number', $requisition->credit_account)
                ->where('segment_code', 'COD')
                ->first();
            if ($coa_config_dr->bank_flag == 'Y' || $coa_config_dr->bank_flag == 'Y') {
                $bank_account_code = $requisition->credit_account;
            } else {
                $bank_account_code = null;
            }
            $create_cashbook = new CashBook();
            $create_cashbook->doc_type =  'PAY';
            $create_cashbook->transaction_no =  $transaction_no;
            $create_cashbook->account_year =  $year;
            $create_cashbook->account_month =  $month;
            $create_cashbook->entry_type_descr =  $requisition->entry_type_descr;
            $create_cashbook->line_no =  1;
            $create_cashbook->branch =  $requisition->offcd;
            $create_cashbook->created_by =  Auth::user()->user_name;
            $create_cashbook->created_date =  $today;
            $create_cashbook->created_time =  $today;
            $create_cashbook->receipt_date =  $today;
            $create_cashbook->cheque_no =  $requisition->cheque_no;
            $create_cashbook->cheque_date =  $requisition->cheque_no ? $requisition->cheque_date : null;
            $create_cashbook->name =  $requisition->name;
            $create_cashbook->payee =  $requisition->name;
            $create_cashbook->customer_id =  $requisition->customer_id;
            $create_cashbook->cbpay_method_code =  $requisition->pay_method_code;
            $create_cashbook->updated_by =  Auth::user()->user_name;
            $create_cashbook->updated_date =  $today;
            $create_cashbook->updated_time =  $today;
            $create_cashbook->cover_no =  $requisition->cover_no;
            $create_cashbook->claim_no =  $requisition->claim_no;
            $create_cashbook->endorsement_no =  $requisition->endorsement_no;
            $create_cashbook->local_cheque =  'N';
            $create_cashbook->debit_account =  $requisition->debit_account;
            $create_cashbook->credit_account =  $requisition->credit_account;
            $create_cashbook->source_code =  $requisition->source_code;
            $create_cashbook->pay_request_no =  $requisition_no;
            $create_cashbook->offcd =  $requisition->offcd;
            $create_cashbook->analysed_cover =  ($cbdeduction_count > 0) ? 'Y' : 'N';
            $create_cashbook->narration =  $requisition->narration;
            $create_cashbook->amount_in_words =  convert_to_words(str_replace(',', '', $requisition->local_nett_amount));
            $create_cashbook->cancelled =  'N';
            $create_cashbook->cancelled_reference =  '';
            $create_cashbook->cancelled_reason =  '';
            $create_cashbook->orig_entry_type_descr =  $requisition->entry_type_descr;
            $create_cashbook->multi_claims =  'N';
            $create_cashbook->foreign_amount =   (int)$requisition->local_nett_amount;
            $create_cashbook->local_amount =  (int)$requisition->local_nett_amount;
            $create_cashbook->currency_code =  $requisition->currency_code;
            $create_cashbook->currency_rate =  $requisition->currency_rate;
            $create_cashbook->bank_account_code =  $bank_account_code;
            $create_cashbook->debit_note_no =  $requisition->invoice_no;
            $create_cashbook->credit_note_no =  $requisition->invoice_no;
            // dd($create_cashbook);
            $create_cashbook->save();

            $updrequisition = CBRequisition::where('requisition_no', $requisition_no)->update([
                'voucher_raised_flag' => 'Y',
                'voucher_posted_flag' => 'Y'
            ]);

            if ($cbdeduction_count > 0) {
                $cbdeductions = CBDeductionMain::where('reference_no', $requisition_no)->get();
                $counter = 1;
                foreach ($cbdeductions as $key => $cbdeduction) {
                    $create_cashbookana = new CashBookana();
                    $create_cashbookana->source_code =  $requisition->source_code;
                    $create_cashbookana->offcd =  $requisition->offcd;
                    $create_cashbookana->doc_type =  $requisition->doc_type;
                    $create_cashbookana->orig_entry_type_descr =  $requisition->entry_type_descr;
                    $create_cashbookana->reference_no =  $reference_no;
                    $create_cashbookana->line_no =  $counter;
                    $create_cashbookana->cover_no =  $requisition->cover_no;
                    $create_cashbookana->endorsement_no =  $requisition->endorsement_no;
                    $create_cashbookana->item_no =  $counter;
                    $create_cashbookana->gl_account =  $cbdeduction->account_no;
                    $create_cashbookana->dr_cr =  ($cbdeduction->add_deduct == 'D') ? 'C' : 'D';
                    $create_cashbookana->created_by =  Auth::user()->user_name;
                    $create_cashbookana->analyse_amount =  $cbdeduction->local_amount;
                    $create_cashbookana->unallocated_amount =  0;
                    $create_cashbookana->allocated_amount =  0;
                    $create_cashbookana->pay_request_no =  $requisition_no;
                    $create_cashbookana->updated_by =  Auth::user()->user_name;
                    $create_cashbookana->updated_date =  $today;
                    $create_cashbookana->branch =  $requisition->offcd;
                    $create_cashbookana->amount_in_words =  convert_to_words(str_replace(',', '', $cbdeduction->local_amount));
                    $create_cashbookana->entry_type_descr =  $requisition->entry_type_descr;
                    $create_cashbookana->debit_note_no =  null;
                    $create_cashbookana->currency_code =  $requisition->currency_code;
                    $create_cashbookana->currency_rate =  $requisition->currency_rate;
                    $create_cashbookana->narration =  $cbdeduction->deduction_name;
                    $create_cashbookana->customer_id =  $requisition->customer_id;
                    // dd($create_cashbookana);
                    $create_cashbookana->save();

                    $counter++;
                }
            }
            $gledgerController = new GLedgerController;
            $gledgerController->insertGlTransactFromCB($create_cashbook);
            DB::commit();

            return response()->json([
                'status' => Response::HTTP_CREATED,
                'message' => 'Data saved successfully'
            ]);
        } catch (ValidationException $e) {
            return response()->json([
                'status' => Response::HTTP_UNPROCESSABLE_ENTITY,
                'errors' => $e->errors()
            ], 422);
        } catch (\Exception $e) {
            DB::rollBack();
            dd($e, $request->all());
            return response()->json([
                'status' => $e->getCode(),
                'message' => 'Failed to save data'
            ]);
        }
    }
}
