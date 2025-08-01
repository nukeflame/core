<?php

namespace App\Http\Controllers;

use Throwable;
use Carbon\Carbon;
use App\Models\Branch;
use App\Models\CashBook;
use App\Models\CBSource;
use App\Models\COA_Config;
use App\Models\Currency;
use App\Models\Customer;
use App\Models\CoverDebit;
use App\Models\CashBookana;
use App\Models\CBPaymethod;
use App\Models\CBTransType;
use App\Models\CurrencyRate;
use App\Models\CustomerAccDet;
use Illuminate\Http\Request;
use App\Models\CoverRegister;
use App\Models\FinancePeriod;
use App\Models\GlTransaction;
use Yajra\DataTables\DataTables;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Session;
use Illuminate\Support\Facades\Validator;
use App\Http\Controllers\GLedgerController;
use App\Models\BankBranch;
use App\Models\Banks;
use App\Models\CBDeductions;
use App\Models\CBRequisition;
use App\Models\ClaimRegister;
use App\Models\Company;
use App\Models\Department;
use App\Models\TaxGroup;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Support\Facades\DB;
use NumberFormatter;
use Symfony\Component\Console\Output\Output;
use Illuminate\Validation\ValidationException;
use Symfony\Component\HttpFoundation\Response;

class ReceiptController extends Controller
{
    private $_year;
    private $_month;
    public function __construct()
    {
        $this->_year = Carbon::now()->year;
        $this->_month = Carbon::now()->month;
    }

    public function getReceipts(Request $request)
    {
        return view('finance.rec.receipts', [
            'user' => $request->user(),
        ]);
    }

    public function issueReceipt(Request $request)
    {
        $covers = CoverRegister::select('cover_no')->distinct()->get();
        $today = Carbon::now();
        return view('finance.rec.issueReceipt', [
            'user' => $request->user(),
            'branches' => Branch::all(),
            'glaccounts' => COA_Config::where('segment_code', 'COD')->where('status', 'A')->get(),
            'types' => CBTransType::all(),
            // 'accPeriod' => FinancePeriod::all(),
            'accPeriod' => Carbon::now()->format('m-Y'), // Output will be "04-2024"
            'drns' => CoverDebit::all(),
            'currency' => Currency::all(),
            'customers' => Customer::all(),
            'covers' => $covers,
            'cbsources' => CBSource::all(),
            'receipt_modes' => CBPaymethod::all(),
        ]);
    }

    public function receiptsgetAccPar(Request $request)
    {
        try {
            $CBTransType = CBTransType::where(['type_code' => $request->type_code])->firstOrfail();

            $debit_account = $CBTransType->debit_account;
            $credit_account = $CBTransType->credit_account;

            $debitAcc = COA_Config::where(['segment_code' => 'COD', 'account_number' => $CBTransType->debit_account])->firstOrfail();
            if ($debitAcc) {
                $debit_account_description = ' - ' . $debitAcc->description;
            } else {
                $debit_account_description = '';
            }

            $creditAcc = COA_Config::where(['segment_code' => 'COD', 'account_number' => $CBTransType->credit_account])->firstOrfail();
            if ($creditAcc) {
                $credit_account_description = ' - ' . $creditAcc->description;
            } else {
                $credit_account_description = '';
            }

            return response(json_encode([
                'debit_account_description' => $debit_account_description,
                'debit_account' => $debit_account,
                'credit_account_description' => $credit_account_description,
                'credit_account' => $credit_account,
            ]));
        } catch (\Throwable $th) {
            throw $th;
        }
    }

    function getCreditAcc(Request $request)
    {
        // dd($request);
        $creditAcc = '';
        $CBTransType = CBTransType::where('doc_type', $request->doc_type)->where('type_code', $request->receipt_type)->first();

        if ($CBTransType->credit_account) {
            $creditAcc = COA_Config::where(['segment_code' => 'COD', 'account_number' => $CBTransType->credit_account])->first();
        }
        return response(json_encode([
            'credit_account_description' => $creditAcc->description,
            'credit_account' => $creditAcc->account_number,
        ]));
    }
    public function receiptsgetDRN(Request $request)
    {
        try {
            $drnReqType = $request->drnReqType;
            $getParam = $request->getParam;
            if (empty($getParam)) {
                $CustomerAccDet = CustomerAccDet::where('doc_type', 'DRN')
                    ->where('unallocated_amount', '>', 0)
                    ->get();
            } else {
                $CustomerAccDet = CustomerAccDet::where('doc_type', 'DRN')
                    ->where($drnReqType, $getParam)
                    ->where('unallocated_amount', '>', 0)
                    ->get();
            }
            return response(json_encode($CustomerAccDet));
        } catch (\Throwable $th) {
            throw $th;
        }
    }

    public function receiptsgetDRNInfo(Request $request)
    {
        try {
            $CustomerAccDet = CustomerAccDet::where('reference', $request->reference)->where('doc_type', 'DRN')->first();
            $out_amt = CustomerAccDet::where('endorsement_no', $CustomerAccDet->endorsement_no)->sum('unallocated_amount');
            return response(json_encode([
                'unallocated_amount' => $out_amt,
            ]));
        } catch (\Throwable $th) {
            throw $th;
        }
    }

    public function receiptsgetDebitNotes(Request $request)
    {
        try {
            $CustomerAccDet = CustomerAccDet::where('unallocated_amount', '>', 0)->where('doc_type', 'DRN')->get();
            return response(json_encode($CustomerAccDet));
        } catch (\Throwable $th) {
            throw $th;
        }
    }

    public function receiptsgetCurrencyRate(Request $request)
    {
        try {
            $CurrencyRate = CurrencyRate::where(['currency_code' => $request->currency_code])->firstOrfail();
            return response(json_encode([
                'currency_rate' => $CurrencyRate->currency_rate,
            ]));
        } catch (\Throwable $th) {
            throw $th;
        }
    }

    public function receiptsData($category)
    {
        $CashBook = CashBook::all();

        $CashBookData = [];
        if ($category == 'all') {
            $CashBookData = $CashBook;
        } else {
            foreach ($CashBook as $value) {
                if ($value->cancelled === 'Y') {
                    $CashBookData[] = $value;
                }
            }
        }

        return DataTables::Of($CashBookData)
            ->addColumn('reversed', function ($row) {
                if ($row->cancelled === 'Y') {
                    return 'Yes';
                } else {
                    return 'No';
                }
            })->make(true);
    }

    public function receiptsAddData(Request $request)
    {
        DB::beginTransaction();
        try {
            $validator = Validator::make($request->all(), []);
            if ($validator) {
                $year = $this->_year;
                $month = $this->_month;
                $transaction_no = (int)CashBook::where('account_year', $year)->max('transaction_no') + 1;
                $today = Carbon::now();
                $receipt_no = STR_PAD($transaction_no, 6, '0', STR_PAD_LEFT);
                $reference_no = STR_PAD($transaction_no, 6, '0', STR_PAD_LEFT) . $year;
                if (!empty($request->customer_id)) {
                    $customer = Customer::where('customer_id', $request->customer_id)->first();
                    $customerID = $customer->customer_id;
                    $customerName = $customer->name;
                }
                $coa_config_dr_count = COA_Config::where('account_number', $request->debit_account)
                    ->where('segment_code', 'COD')
                    ->count();
                $coa_config_cr_count = COA_Config::where('account_number', $request->credit_account)
                    ->where('segment_code', 'COD')
                    ->count();

                if ($coa_config_dr_count == 0 || $coa_config_cr_count == 0) {
                    Session::flash('error', 'Account code missing');
                } else {
                    $coa_config_dr = COA_Config::where('account_number', $request->debit_account)
                        ->where('segment_code', 'COD')
                        ->first();
                }
                $cbtrans_type = CBTransType::where('type_code', $request->cbtrans_type_code)->first();

                if (isset($coa_config_dr) && ($coa_config_dr->bank_flag == 'Y')) {
                    $bank_account_code = $request->debit_account;
                } else {
                    $bank_account_code = '';
                }
                $rec_amount = (float)$request->receipt_amount ? str_replace(',', '', $request->receipt_amount) : 0;
                $exh_rate = $request->currency_rate ? $request->currency_rate : 1;
                $local_amount = $rec_amount * $exh_rate;

                if (($rec_amount * -1) > 0) {
                    $dr_cr = 'D';
                } else {
                    $dr_cr = 'C';
                }

                $cover_debit = CoverDebit::where('cover_no', $request->cover_no)->first();
                if ($cover_debit) {
                    $rec_endorsement_no = $request->endorsement_no;
                } else {
                    $rec_endorsement_no = 'Null';
                }

                $create_cashbook = CashBook::create(
                    [
                        'doc_type' => 'REC',
                        'transaction_no' => $transaction_no,
                        'account_year' => $year,
                        'account_month' => $month,
                        'entry_type_descr' => $request->cbtrans_type_code,
                        'line_no' => 1,
                        'branch' => $request->offcd,
                        'created_by' => Auth::user()->user_name,
                        'created_date' => $today,
                        'created_time' => $today,
                        'receipt_date' => $today,
                        'cheque_no' => $request->cheque_no,
                        'cheque_date' => $request->cheque_date,
                        'name' => $customerName,
                        'payee' => $request->received_from,
                        'customer_id' => $customerID,
                        'cbpay_method_code' => $request->receipt_mode,
                        'updated_by' => Auth::user()->user_name,
                        'updated_date' => $today,
                        'updated_time' => $today,
                        'cover_no' => $request->cover_no,
                        'claim_no' => $request->claim_no,
                        'endorsement_no' => $rec_endorsement_no,
                        'local_cheque' => 'N',
                        'debit_account' => $request->debit_account,
                        'credit_account' => $request->credit_account,
                        'source_code' => $cbtrans_type->source_code,
                        'pay_request_no' => '',
                        'offcd' => $request->offcd,
                        'analysed_cover' => $request->analysed_cover,
                        'narration' => $request->narration,
                        'amount_in_words' => $request->amount_in_words,
                        'cancelled' => 'N',
                        'cancelled_reference' => '',
                        'cancelled_reason' => '',
                        'orig_entry_type_descr' => $request->cbtrans_type_code,
                        'multi_claims' => 'N',
                        'foreign_amount' =>  $rec_amount,
                        'local_amount' => $local_amount,
                        'currency_code' => $request->currency_code,
                        'currency_rate' => $request->currency_rate,
                        'bank_account_code' => $bank_account_code,
                        'debit_note_no' => $request->drn,
                        'credit_note_no' => $request->credit_note_no

                    ]
                );

                if ($cbtrans_type->source_code == 'U/W') {
                    $CustomerAccDet = CustomerAccDet::where('reference', $request->drn)->where('doc_type', 'DRN')->first();
                    $CoverDebit = CoverDebit::where('endorsement_no', $CustomerAccDet->endorsement_no)->first();
                    $CoverRegister = CoverRegister::where('endorsement_no', $CoverDebit->endorsement_no)->first();
                    $rec_amount = $rec_amount * -1;
                    $local_amount = $local_amount * -1;

                    $customerAccDet = CustomerAccDet::create([
                        'branch' => $request->offcd,
                        'customer_id' => $customerID,
                        'source_code' => 'CB',
                        'doc_type' => 'REC',
                        'entry_type_descr' => $request->cbtrans_type_code,
                        'reference' => $reference_no,
                        'account_year' => $this->_year,
                        'account_month' => $this->_month,
                        'line_no' => 1,
                        'cheque_no' => $request->cheque_no,
                        'cheque_date' => $request->cheque_date,
                        'cover_no' => $CoverDebit->cover_no,
                        'endorsement_no' => $CoverDebit->endorsement_no,
                        'insured' => $customerName,
                        'class' => $CoverRegister->class_code,
                        'currency_code' => $request->currency_code,
                        'currency_rate' => $request->currency_rate,
                        'created_by' => Auth::user()->user_name,
                        'created_date' => Carbon::now(),
                        'created_time' => Carbon::now(),
                        'updated_by' => Auth::user()->user_name,
                        'updated_datetime' => Carbon::now(),
                        'dr_cr' => $dr_cr,
                        'foreign_basic_amount' => $rec_amount,
                        'local_basic_amount' => $local_amount,
                        'foreign_taxes_amount' => 0,
                        'local_taxes_amount' => 0,
                        'foreign_nett_amount' => $rec_amount,
                        'local_nett_amount' => $local_amount,
                        'allocated_amount' => 0,
                        'unallocated_amount' => $local_amount,
                    ]);
                }

                $cashbook = CashBook::where('offcd', $request->offcd)
                    ->where('transaction_no', $receipt_no)
                    ->where('account_year', $year)
                    ->where('account_month', $month)
                    ->where('doc_type', 'REC')
                    ->where('entry_type_descr', '!=', 'REC')
                    ->first();

                $offcd = $cashbook->offcd;
                $source_code = $cashbook->source_code;
                $doc_type = $cashbook->doc_type;
                $entry_type_descr = $cashbook->entry_type_descr;
                $transaction_no = $cashbook->transaction_no;
                $account_year = $cashbook->account_year;
                $line_no = $cashbook->line_no;

                if ($create_cashbook) {

                    // --insert into cashbookana -- this is receipt analysis
                    if ($request->analysed_cover == 'Y') {

                        for ($i = 0; $i < count($request->endorsement_no); $i++) {
                            $n = $i + 1;
                            $endorsement_no = $request->endorsement_no[$i];

                            $drn = CoverDebit::where('endorsement_no', $endorsement_no)
                                ->where('document', $doc_type)->first();

                            if ($drn) {
                                $debit_note_no = substr($drn->dr_no, 0, 6);
                                //create cashbookana
                                $line_no = CashBookana::where('reference_no', $reference_no)
                                    ->count();

                                $dr_cr = 'C';
                                // $foreign_amount = str_replace(',', '', $request->analyse_endt[$i]);
                                $local_amount = str_replace(',', '', $request->analyse_endt[$i]) * $cashbook->currency_rate;
                                $final_amount = $local_amount;
                                $create_cashbookana = CashBookana::create([
                                    'source_code' => $cbtrans_type->source_code,
                                    'doc_type' => $cashbook->doc_type,
                                    'reference_no' => $reference_no,
                                    'line_no' => $line_no,
                                    'branch' => $request->offcd,
                                    'item_no' => $n,
                                    'created_by' => Auth::user()->name,
                                    'allocated_amount' => $final_amount,
                                    'unallocated_amount' => $final_amount,
                                    'updated_by' => Auth::user()->name,
                                    'updated_date' => $today,
                                    'entry_type_descr' => $cashbook->entry_type_descr,
                                    'cover_no' => $request->policy_no[$i],
                                    'claim_no' => '',
                                    'customer_id' => $drn->customer_id,
                                    'endorsement_no' => $endorsement_no,
                                    'pay_request_no' => '',
                                    'gl_account' => '',
                                    'offcd' => $cashbook->offcd,
                                    'dr_cr' => $dr_cr,
                                    'amount_in_words' => convert_to_words($local_amount),
                                    'orig_entry_type_descr' => $cashbook->entry_type_descr,
                                    'analyse_amount' => $local_amount * -1,
                                    'currency_code' => $request->currency_code,
                                    'currency_rate' => $request->currency_rate,
                                    'debit_note_no' => $cashbook->debit_note_no,
                                    'credit_note_no' => $cashbook->debit_note_no,
                                    'narration' => '',
                                ]);

                                // $gledgerController=new GLedgerController;
                                // $gledgerController->updateGlMastbal($cashbook->credit_account,$cashbook->offcd,$year,$month,$dr_cr,$local_amount);
                            }
                        } //end for loop
                    }
                    $gledgerController = new GLedgerController;
                    $gledgerController->insertGlTransactFromCB($cashbook);
                }

                DB::commit();

                $redirectUrl = route('receipts.one_receipt_details', [
                    'offcd' => $offcd,
                    'source_code' => $source_code,
                    'doc_type' => $doc_type,
                    'entry_type_descr' => $entry_type_descr,
                    'transaction_no' => $transaction_no,
                    'account_year' => $account_year,
                    'line_no' => $line_no,
                ]);

                return redirect($redirectUrl)->with('success', 'Receipt information saved successfully');
            } else {
                DB::rollback();
                DB::connection(env('DB_CONNECTION'))->rollback();
                Session::flash('error', 'some field are missing');
                return [
                    'code' => -1,
                    'msg' => $validator->errors(),
                ];
            }
        } catch (\Throwable $e) {
            DB::rollback();
            throw $e;
        }
    }

    function receiptDetails(Request $request)
    {
        $cashbookana = [];
        $reference_no = STR_PAD($request->transaction_no, 6, '0', STR_PAD_LEFT) . $request->account_year;
        $cashbook = CashBook::where('offcd', $request->offcd)
            ->where('source_code', $request->source_code)
            ->where('doc_type', $request->doc_type)
            ->where('transaction_no', $request->transaction_no)
            ->where('account_year', $request->account_year)
            // ->where('line_no', $request->line_no)
            ->first();
        // dd($cashbook);

        if ($cashbook) {
            $cashbookana_count = CashBookana::where('offcd', $cashbook->offcd)
                ->where('source_code', $cashbook->source_code)
                ->where('doc_type', $cashbook->doc_type)
                ->where('reference_no', $reference_no)
                // ->where('line_no', $request->line_no)
                ->count();

            // dd($cashbookana_count);

            // if ($cashbookana_count > 0) {
            $cashbookana = CashBookana::where('offcd', $cashbook->offcd)
                ->where('source_code', $cashbook->source_code)
                ->where('doc_type', $cashbook->doc_type)
                ->where('reference_no', $reference_no)
                ->get();

            $branch = Branch::where('branch_code', $cashbook->customer_id)->first();
            $customer = Customer::where('customer_id', $cashbook->customer_id)->first();
            $pay_method = CBPaymethod::where('pay_method_code', $cashbook->cbpay_method_code)->first();
            $dracc = COA_Config::where('segment_code', 'COD')->where('account_number', $cashbook->debit_account)->first();
            $cracc = COA_Config::where('segment_code', 'COD')->where('account_number', $cashbook->credit_account)->first();
            $branch = Branch::where('branch_code', $cashbook->branch)->first();
            $currency = Currency::where('currency_code', $cashbook->currency_code)->first();
            $entry_type = CBTransType::where('doc_type', $cashbook->doc_type)
                ->where('type_code', $cashbook->entry_type_descr)
                ->where('source_code', $cashbook->source_code)
                ->first();

            $receiptPrintData = [
                'cashbook' => $cashbook,
                'branch' => $branch,
                'cashbookana_count' => $cashbookana_count,
                'cashbookana' => $cashbookana,
                'reference' => $reference_no,
                'customer_name' => $customer->name,
                'pay_method' => $pay_method->pay_method_name,
                'dracc_name' => $dracc->description ?? 'Unknown Account',
                'cracc_name' => $cracc->description ?? 'Unknown Account',
                'branch_name' => $branch->branch_name,
                'currency_name' => $currency->currency_name,
                'entry_type' => $entry_type->description,
            ];
            // dd($receiptPrintData);
        } else {
            $receiptPrintData = ['receiptError' => 'Error encountered getting receipt information'];
        }

        return view('finance.rec.receipt_details', $receiptPrintData);
    }

    public function Convert_Amount_to_words(Request $request)
    {
        try {
            $p_number = $request->p_number;
            $c_rate = $request->c_rate;
            $local_amount = $p_number * $c_rate;
            return response(json_encode([
                'amount_in_words' => convert_to_words($p_number),
                'local_amount' => $local_amount,
            ]));
        } catch (\Throwable $th) {
            throw $th;
        }
    }


    function receiptReversal(Request $request)
    {
        // dd($request);
        DB::beginTransaction();
        try {

            $year = $this->_year;
            $month = $this->_month;
            $transaction_no = (int)CashBook::where('account_year', $year)->max('transaction_no') + 1;
            $today = Carbon::now();
            $receipt_no = STR_PAD($transaction_no, 6, '0', STR_PAD_LEFT);
            $reference_no = STR_PAD($transaction_no, 6, '0', STR_PAD_LEFT) . $year;
            $reference_no = (string)$reference_no;

            // dd($transaction_no);
            $cashbook = CashBook::where('offcd', $request->offcd)
                ->where('source_code', $request->source_code)
                ->where('doc_type', $request->doc_type)
                ->where('transaction_no', $request->transaction_no)
                ->where('account_year', $request->account_year)
                ->first();
            $orig_reference_no = STR_PAD($cashbook->transaction_no, 6, '0', STR_PAD_LEFT) . $cashbook->account_year;

            $data = $cashbook->getAttributes();
            $data['transaction_no'] = $transaction_no;
            $data['entry_type_descr'] = 'REV';
            $data['line_no'] = 2;
            $data['account_year'] = $year;
            $data['account_month'] = $month;
            $data['created_by'] = Auth::user()->user_name;
            $data['created_date'] = $today;
            $data['created_time'] = $today;
            $data['receipt_date'] = $today;
            $data['updated_by'] = Auth::user()->user_name;
            $data['updated_date'] = $today;
            $data['updated_time'] = $today;
            $data['debit_account'] = $cashbook->credit_account;
            $data['credit_account'] = $cashbook->debit_account;
            $data['narration'] = $request->reason_reverse;
            CashBook::create($data);

            // dd('sisi',$cashbook);
            CashBook::where('offcd', $request->offcd)
                ->where('source_code', $request->source_code)
                ->where('doc_type', $request->doc_type)
                ->where('transaction_no', $request->transaction_no)
                ->where('account_year', $request->account_year)
                ->update([
                    'cancelled' => 'Y',
                    'cancelled_reference' => $reference_no,
                    'cancelled_reason' => $request->reason_reverse,
                ]);


            $CustomerAccDet = CustomerAccDet::where('branch', $cashbook->branch)
                ->where('source_code', 'CB')
                ->where('doc_type', $cashbook->doc_type)
                ->where('reference', $orig_reference_no)
                ->get();
            // dd('rec',$CustomerAccDet,$cashbook->branch,$cashbook->doc_type,$reference_no);
            foreach ($CustomerAccDet as $CustomerAccRec) {
                $data = $CustomerAccRec->getAttributes();
                $data['entry_type_descr'] = 'REV';
                $data['reference'] = $reference_no;
                $data['account_year'] = $year;
                $data['account_month'] = $month;
                $data['line_no'] = 2;
                $data['created_by'] = Auth::user()->user_name;
                $data['created_date'] = Carbon::now();
                $data['created_time'] = Carbon::now();
                $data['updated_by'] = Auth::user()->user_name;
                $data['updated_datetime'] = Carbon::now();
                $data['dr_cr'] = 'D';
                $data['foreign_basic_amount'] = $CustomerAccRec->foreign_basic_amount * -1;
                $data['local_basic_amount'] = $CustomerAccRec->local_basic_amount * -1;
                $data['foreign_nett_amount'] = $CustomerAccRec->foreign_nett_amount * -1;
                $data['local_nett_amount'] = $CustomerAccRec->local_nett_amount * -1;
                $data['allocated_amount'] = 0;
                $data['unallocated_amount'] = $CustomerAccRec->local_nett_amount * -1;
                CustomerAccDet::create($data);
            }

            $cashbookana = CashBookana::where('offcd', $cashbook->offcd)
                ->where('source_code', $cashbook->source_code)
                ->where('doc_type', $cashbook->doc_type)
                ->where('reference_no', $cashbook->transaction_no)
                ->get();

            foreach ($cashbookana as $cashbookanarec) {
                $data = $cashbookanarec->getAttributes();
                $data['entry_type_descr'] = 'REV';
                $data['reference'] = $reference_no;
                $data['account_year'] = $year;
                $data['account_month'] = $month;
                $data['line_no'] = 2;
                $data['created_by'] = Auth::user()->user_name;
                $data['created_date'] = Carbon::now();
                $data['created_time'] = Carbon::now();
                $data['updated_by'] = Auth::user()->user_name;
                $data['updated_datetime'] = Carbon::now();
                $data['dr_cr'] = 'C';
                $data['foreign_basic_amount'] = $cashbookanarec->foreign_basic_amount;
                $data['local_basic_amount'] = $cashbookanarec->local_basic_amount;
                $data['foreign_nett_amount'] = $cashbookanarec->foreign_nett_amount;
                $data['local_nett_amount'] = $cashbookanarec->local_nett_amount;
                $data['allocated_amount'] = 0;
                $data['unallocated_amount'] = $cashbookanarec->local_nett_amount;
                CashBookana::create($data);
            }


            $cashbookRev = CashBook::where('offcd', $cashbook->offcd)
                ->where('transaction_no', $transaction_no)
                ->where('account_year', $year)
                ->where('account_month', $month)
                ->where('doc_type', $cashbook->doc_type)
                ->where('entry_type_descr', 'REV')
                ->first();

            // dd('wewe',$cashbookRev)  ;
            $gledgerController = new GLedgerController;
            $gledgerController->insertGlTransactFromCB($cashbookRev);
            DB::commit();
            return response()->json([
                'status' => 200,
                'message' => 'Data saved successfully'
            ]);
        } catch (ValidationException $e) {
            dd($e);
            // If validation fails, return a JSON response with errors
            return response()->json([
                'status' => Response::HTTP_UNPROCESSABLE_ENTITY,
                'errors' => $e->errors()
            ], 422);
        } catch (Throwable $e) {
            DB::rollback();
            dd($e);
            return response()->json([
                'status' => $e->getCode(),
                'message' => 'Failed to save'
            ]);
        }
    }
    //END
}
