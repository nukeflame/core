<?php

namespace App\Http\Controllers;

use View;
use DataTables;
use Carbon\Carbon;
use App\Models\User;
use App\Models\GLBatch;
use App\Models\CBSource;
use App\Models\Currency;
use App\Models\GLMastBal;
use App\Models\COA_Config;
use App\Models\CoverDebit;
use App\Models\GLBatchDtl;
use App\Models\CashBookana;
use App\Models\CoverPremium;
use App\Models\CurrencyRate;
use Illuminate\Http\Request;
use App\Models\CoverRegister;
use App\Models\GLTransaction;
use App\Models\SystemProcess;
use App\Models\BankTransaction;
use App\Models\CoverGledgerLink;
use App\Models\COA_CompanySegment;
use Illuminate\Support\Facades\DB;
use App\Models\SystemProcessAction;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Session;

class GLedgerController extends Controller
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

    function getChartOfAccounts(Request $request){

        return view::make('finance.gl.chartofaccounts');
    }
    function getChartofAccountsData(Request $request){
        $glh = COA_CompanySegment::orderBy('segment_position','DESC')->get()[0];
        

       $accounts = COA_Config::where('segment_code',$glh->segment_code);
       $segments = COA_CompanySegment::where('segment_code','<>',$glh->segment_code)
                            ->orderBy('segment_position','ASC')
                            ->get();

                            // dd($segments[1]->segment_position,$segments[1]->segment_length);


       return Datatables::Of($accounts)
       //foreach($segments as $seg){
        ->addColumn('seg_'.$segments[0]->segment_position, function ($grp) use ($segments) {
            
            $prsno = substr($grp->account_number,0,(int)$segments[0]->segment_length);
            // dd($prsno);
            return $prsno;
            
        })
        ->addColumn('seg_desc_'.$segments[0]->segment_position, function ($grp) use ($segments) {

            $prsno = substr($grp->account_number,0,$segments[0]->segment_length);

            $nlparam = COA_Config::where('segment_code',$segments[0]->segment_code)
                                ->whereRaw("trim(account_number) = '".$prsno."'")
                                ->get()[0];
            // dd($nlparam);
            return $nlparam->description;

            
        })
        ->addColumn('seg_'.$segments[1]->segment_position, function ($grp) use ($segments) {
            
            $prsno = substr($grp->account_number,$segments[1]->segment_length,$segments[1]->segment_length);

            return $prsno;
            
        })
        ->addColumn('seg_desc_'.$segments[1]->segment_position, function ($grp) use ($segments) {
            
            $prsno = substr($grp->account_number,0,$segments[0]->segment_length+$segments[1]->segment_length);

            $nlparam = COA_Config::where('segment_code',$segments[1]->segment_code)
                                ->whereRaw("trim(account_number) = '".$prsno."'")
                                ->get()[0];

            return $nlparam->description;

            
        })
        ->addColumn('action', function ($leveldtl) {
            return '<button class="btn btn-outline-primary btn-sm" id="edit_code_dtl">Edit</button>';
        })
        ->make(true);
    }

    function insertGlTransactFromCB($cashbook){
        $transaction_no = str_pad($cashbook->transaction_no, 6, '0', STR_PAD_LEFT);
        $reference_no=$transaction_no.$cashbook->account_year;
        if($cashbook->analyse=='Y'){
            
            $casbookana = CashBookana::where('offcd',$cashbook->offcd)
                                ->where('cbsource_code',$cashbook->cbsource_code)
                                ->where('doc_type',$cashbook->doc_type)
                                ->where('entry_type_descr',$cashbook->entry_type_descr)
                                ->where('reference_no',$reference_no)
                                ->where('line_no',$cashbook->line_no)
                                ->get();

                                // Loop
            foreach ($casbookana as $casbookanadtl) {
            // --insert credit account to Gl transactions table
                $create_gltransaction = new GLTransaction();
                $create_gltransaction->source_code = $casbookanadtl->cbsource_code;
                $create_gltransaction->offcd = $casbookanadtl->offcd;
                $create_gltransaction->doc_type = $casbookanadtl->doc_type;
                $create_gltransaction->entry_type_descr = $casbookanadtl->entry_type_descr;
                $create_gltransaction->reference_no = $reference_no;
                $create_gltransaction->line_no = $casbookanadtl->line_no;
                $create_gltransaction->cover_no = $casbookanadtl->cover_no;
                $create_gltransaction->endorsement_no = $casbookanadtl->endorsement_no;
                $create_gltransaction->claim_no = $casbookanadtl->claim_no;
                $create_gltransaction->item_no = $casbookanadtl->item_no;
                $create_gltransaction->dr_cr = $casbookanadtl->dr_cr;
                $create_gltransaction->pay_request_no = $casbookanadtl->pay_request_no;
                $create_gltransaction->gl_account = $cashbook->credit_account;
                $create_gltransaction->period_year = $cashbook->account_year;
                $create_gltransaction->period_month = $cashbook->account_month;
                $create_gltransaction->local_amount = $casbookanadtl->analyse_amount;
                $create_gltransaction->foreign_amount = $casbookanadtl->analyse_amount / $cashbook->currency_rate;
                $create_gltransaction->branch = $cashbook->branch;
                $create_gltransaction->currency_code = $cashbook->currency_code;
                $create_gltransaction->currency_rate = $cashbook->currency_rate;
                $create_gltransaction->debit_credit_no = $casbookanadtl->debit_note_no;
                $create_gltransaction->customer_id = $casbookanadtl->customer_id;
                $create_gltransaction->created_date = $cashbook->created_date;
                $create_gltransaction->created_by = $cashbook->created_by;
                $create_gltransaction->created_time = $cashbook->created_time;
                $create_gltransaction->updated_date = $cashbook->updated_date;
                $create_gltransaction->updated_by = $cashbook->updated_by;
                $create_gltransaction->updated_time = $cashbook->updated_time;
                $create_gltransaction->narration = $cashbook->name;
                $create_gltransaction->save();
                

                $bank_flag = COA_Config::where('account_number', $create_gltransaction->gl_account)->where('segment_code', 'COD')->first()->bank_flag;

                if($bank_flag =='Y'){
                $this->insertBankTransaction('CB',$create_gltransaction->source_code, $create_gltransaction->offcd, $create_gltransaction->gl_account, $create_gltransaction->doc_type, $create_gltransaction->entry_type_descr, $create_gltransaction->reference_no, $create_gltransaction->pay_request_no, $create_gltransaction->line_no, $create_gltransaction->item_no);
                }

                $this->updateGlMastbalfromCB($cashbook->credit_account,$casbookanadtl->dr_cr,$casbookanadtl->offcd,$cashbook->account_year,$cashbook->account_month,$casbookanadtl->analyse_amount);
            }
            // ENd of Loop

                // --insert dr account gl transactions table
                    $create_gltransaction = new GLTransaction();
                    $create_gltransaction->source_code = $cashbook->cbsource_code;
                    $create_gltransaction->offcd = $cashbook->offcd;
                    $create_gltransaction->doc_type = $cashbook->doc_type;
                    $create_gltransaction->entry_type_descr = $cashbook->entry_type_descr;
                    $create_gltransaction->reference_no = $reference_no;
                    $create_gltransaction->line_no = $cashbook->line_no;
                    $create_gltransaction->cover_no = $cashbook->cover_no;
                    $create_gltransaction->endorsement_no = $cashbook->endorsement_no;
                    $create_gltransaction->claim_no = $cashbook->claim_no;
                    $create_gltransaction->item_no = 1;
                    $create_gltransaction->dr_cr = 'D';
                    $create_gltransaction->pay_request_no = $cashbook->pay_request_no;
                    $create_gltransaction->gl_account = $cashbook->debit_account;
                    $create_gltransaction->period_year = $cashbook->account_year;
                    $create_gltransaction->period_month = $cashbook->account_month;
                    $create_gltransaction->foreign_amount = $cashbook->foreign_amount;
                    $create_gltransaction->local_amount = $cashbook->local_amount;
                    $create_gltransaction->branch = $cashbook->branch;
                    $create_gltransaction->currency_code = $cashbook->currency_code;
                    $create_gltransaction->currency_rate = $cashbook->currency_rate;
                    $create_gltransaction->debit_credit_no = $cashbook->debit_note_no;
                    $create_gltransaction->customer_id = $cashbook->customer_id;
                    $create_gltransaction->created_date = $cashbook->created_date;
                    $create_gltransaction->created_by = $cashbook->created_by;
                    $create_gltransaction->created_time = $cashbook->created_time;
                    $create_gltransaction->updated_date = $cashbook->updated_date;
                    $create_gltransaction->updated_by = $cashbook->updated_by;
                    $create_gltransaction->updated_time = $cashbook->updated_time;
                    $create_gltransaction->narration = $cashbook->name;
                    $create_gltransaction->save();

                    $bank_flag = COA_Config::where('account_number', $create_gltransaction->gl_account)->where('segment_code', 'COD')->first()->bank_flag;

                if($bank_flag =='Y'){
                $this->insertBankTransaction('CB',$create_gltransaction->source_code, $create_gltransaction->offcd, $create_gltransaction->gl_account, $create_gltransaction->doc_type, $create_gltransaction->entry_type_descr, $create_gltransaction->reference_no, $create_gltransaction->pay_request_no, $create_gltransaction->line_no, $create_gltransaction->item_no);
                }
                $this->updateGlMastbalfromCB($cashbook->debit_account,'D',$cashbook->offcd,$cashbook->account_year,$cashbook->account_month,$cashbook->local_amount);
        }else{
            // Define arrays of gl_accounts and dr_crs you want to insert
            $new_gl_accounts = [$cashbook->debit_account, $cashbook->credit_account]; // Add more gl_accounts as needed
            $new_dr_crs = ['D', 'C']; // Add corresponding dr_crs

                // Make sure both arrays have the same length
                if (count($new_gl_accounts) !== count($new_dr_crs)) {
                    throw new Exception("The number of gl_accounts and dr_crs should be the same.");
                }

                // Loop over both arrays simultaneously
                for ($i = 0; $i < count($new_gl_accounts); $i++) {
                    // Insert a new entry with the current gl_account and dr_cr values
                    $create_gltransaction = new GLTransaction();
                    $create_gltransaction->source_code =  $cashbook->source_code;
                    $create_gltransaction->offcd =  $cashbook->offcd;
                    $create_gltransaction->doc_type =  $cashbook->doc_type;
                    $create_gltransaction->entry_type_descr =  $cashbook->entry_type_descr;
                    $create_gltransaction->reference_no =  $reference_no;
                    $create_gltransaction->line_no = $cashbook->line_no;
                    $create_gltransaction->cover_no =  $cashbook->cover_no;
                    $create_gltransaction->endorsement_no =  $cashbook->endorsement_no;
                    $create_gltransaction->claim_no =  $cashbook->claim_no;
                    $create_gltransaction->item_no =  1;
                    $create_gltransaction->dr_cr =  $new_dr_crs[$i];
                    $create_gltransaction->pay_request_no =  $cashbook->pay_request_no;
                    $create_gltransaction->gl_account =  $new_gl_accounts[$i];
                    $create_gltransaction->period_year =  $cashbook->account_year;
                    $create_gltransaction->period_month =  $cashbook->account_month;
                    $create_gltransaction->foreign_amount =  $cashbook->foreign_amount;
                    $create_gltransaction->local_amount =  $cashbook->local_amount;
                    $create_gltransaction->branch =  $cashbook->branch;
                    $create_gltransaction->currency_code =  $cashbook->currency_code;
                    $create_gltransaction->currency_rate =  $cashbook->currency_rate;
                    $create_gltransaction->debit_credit_no =  $cashbook->debit_note_no;
                    $create_gltransaction->customer_id =  $cashbook->customer_id;
                    $create_gltransaction->created_date =  $cashbook->created_date;
                    $create_gltransaction->created_by =  $cashbook->created_by;
                    $create_gltransaction->created_time =  $cashbook->created_time;
                    $create_gltransaction->updated_date =  $cashbook->updated_date;
                    $create_gltransaction->updated_by =  $cashbook->updated_by;
                    $create_gltransaction->updated_time =  $cashbook->updated_time;
                    $create_gltransaction->narration = $cashbook->name;
                    $create_gltransaction->save();
                    
                    $bank_flag = COA_Config::where('account_number', $create_gltransaction->gl_account)->where('segment_code', 'COD')->first()->bank_flag;
                    // dd( $create_gltransaction,$bank_flag,'wewe');
                    if($bank_flag =='Y'){
                    $this->insertBankTransaction('CB',$create_gltransaction->source_code, $create_gltransaction->offcd, $create_gltransaction->gl_account, $create_gltransaction->doc_type, $create_gltransaction->entry_type_descr, $create_gltransaction->reference_no, $create_gltransaction->pay_request_no, $create_gltransaction->line_no, $create_gltransaction->item_no);
                    }

                    $this->updateGlMastbalfromCB($new_gl_accounts[$i],$new_dr_crs[$i],$cashbook->offcd,$cashbook->account_year,$cashbook->account_month,$cashbook->local_amount);
                }


        }
        
    }
    public function updateGlMastbalfromCB($account_number, $dr_cr, $offcd, $account_year, $account_month, $local_amount) {
        $local_dr_amount = 0;
        $local_cr_amount = 0;
        $local_amount = floatval($local_amount);
    
        if ($dr_cr == 'C') {
            $local_cr_amount = $local_amount;
        } elseif ($dr_cr == 'D') {
            $local_dr_amount = $local_amount;
        }
    
        //Create or Update the account in GLMast Table
        $glmastbal = GLMastBal::where('account_number',$account_number)
            ->where('offcd', $offcd)
            ->where('account_year', $account_year)
            ->where('account_month', $account_month)
            ->first();
    
        if ($glmastbal) {
            // Update the existing record
            // dd($glmastbal->period_closing_bal,$glmastbal->period_closing_bal + $local_dr_amount - $local_cr_amount);
            // $ytd_closing_bal = isset($glmastbal->ytd_closing_bal) ? $glmastbal->ytd_closing_bal : 0;
               $updglmastbal =  GLMastBal::where('account_number',$account_number)
                ->where('offcd', $offcd)
                ->where('account_year', $account_year)
                ->where('account_month', $account_month)->update([
                'ytd_bal' => $glmastbal->ytd_bal + $local_dr_amount - $local_cr_amount,
                'ytd_debits' => $glmastbal->ytd_debits + $local_dr_amount,
                'ytd_credits' => $glmastbal->ytd_credits + $local_cr_amount,
                'period_debits' => $glmastbal->period_debits + $local_dr_amount,
                'period_credits' => $glmastbal->period_credits +  $local_cr_amount,
                'period_closing_bal' => $glmastbal->period_closing_bal + $local_dr_amount - $local_cr_amount,
                'ytd_closing_bal' => $glmastbal->ytd_closing_bal + $local_dr_amount - $local_cr_amount,
            ]);
            // dd($updglmastbal);
        } else {
            // Create a new record
            GLMastBal::create([
                'account_number' => $account_number,
                'offcd' => $offcd,
                'account_year' => $account_year,
                'account_month' => $account_month,
                'ytd_bal' => $local_dr_amount - $local_cr_amount,
                'ytd_debits' => $local_dr_amount,
                'ytd_credits' => $local_cr_amount,
                'period_debits' => $local_dr_amount,
                'period_credits' => $local_cr_amount,
                'year_opening_bal' => 0,
                'period_opening_bal' => 0,
                'period_closing_bal' => $local_dr_amount - $local_cr_amount,
                'ytd_closing_bal' => $local_dr_amount - $local_cr_amount,
            ]);
        }
    }
    
    

    function getChartOfAccountDetails(Request $request) {
        // dd($request);
        $acc_code = $request->acc_code;
        $acc_no = $request->acc_no;
        $coa_config = COA_Config::where('segment_code',$acc_code)
                                ->whereRaw("trim(account_number) = '".$acc_no."'")
                                ->get()[0];

        $bal = GLMastBal::whereRaw("trim(account_number) = '".$acc_no."'")
                        ->where('account_year',$this->_year)
                        ->where('account_month',$this->_month)
                        ->sum('ytd_bal');
            
            //dd($bal);

        $acc_years = GLMastBal::whereRaw("trim(account_number) = '".$acc_no."'")
                                ->where('account_year',$this->_year)
                                ->where('account_month',$this->_month) 
                                ->selectRaw('distinct(account_year) as period_year,ytd_bal,account_month')
                                ->orderBy('account_year','DESC')
                                ->get();
                    //dd($acc_years);

        $sql = "select a.* from gltransactions a where a.gl_account=trim()";


        return view::make('finance.gl.gl_chartdetails',compact('coa_config','acc_years','bal'));
    }

    public function getAccTransactions(Request $request){
    	$acc_no = $request->get('acc_no');
        $gltransactions = GLTransaction::whereRaw("trim(gl_account) = '".$acc_no."'")->get();

    	return Datatables::Of($gltransactions)
        
        ->editColumn('currency_code',function($tran){
            $currency = Currency::where('currency_code',$tran->currency_code)
                                ->get()[0];

            return $currency->currency_name;
        })
        ->editColumn('foreign_amount',function($tran){
            return number_format($tran->foreign_amount,2);
        })
        ->editColumn('currency_rate',function($tran){
            return number_format($tran->currency_rate,2);
        })
        ->editColumn('dr_cr', function($tran){
            if($tran->dr_cr == "D"){
                return "Dr";
            }
            else{
                return "Cr";
            }
        })
        ->editColumn('trans_date',function($tran){
            return formatDate($tran->created_date);
        })
    	->make(true);

    }

    function getAccBalances(Request $request) {
        $acc_no = $request->get('acc_no');

    	$balances = GLMastBal::whereRaw("trim(account_number) = '".$acc_no."'")
    					->get();

    					//dd($balances);
    	return Datatables::Of($balances)
        ->editColumn('year_opening_bal',function($bal){
            return number_format($bal->year_opening_bal,2);
        })
        ->editColumn('period_opening_bal',function($bal){
            return number_format($bal->period_opening_bal,2);
        })
        ->editColumn('ytd_bal',function($bal){
            return number_format($bal->ytd_bal,2);
        })
        ->editColumn('ytd_debits',function($bal){
            return number_format($bal->ytd_debits,2);
        })
        ->editColumn('ytd_credits',function($bal){
            return number_format($bal->ytd_credits,2);
        })
        ->editColumn('period_credits',function($bal){
            return number_format($bal->period_credits,2);
        })
        ->editColumn('period_debits',function($bal){
            return number_format($bal->period_debits,2);
        })

    	
    	->make(true);
    }

    function glBatch(Request $request) {
        $glsources = CBSource::all();
        $gl_accounts = COA_Config::where('segment_code', 'COD')->where('bank_flag', '!=', 'Y')->get();
        $currencies = Currency::all();
        $rates = CurrencyRate::where('currency_date', Carbon::today())->get();

        return view::make('finance.gl.glbatches', compact('glsources','gl_accounts', 'currencies', 'rates'));
    }

    function getglBatch(Request $request) {
        $nlbatches = GLBatch::whereRaw("(batch_source != 'AP' and batch_source != 'AR' and batch_source != 'BNK' and batch_status not in('004', '005'))
        or (batch_source =  'AP' and batch_status not in('004', '005'))
        or (batch_source =  'AR' and batch_status  not in('004', '005'))
        or (batch_source =  'BNK' and batch_status  not in('004', '005'))")
        ->orderBy("created_at", "desc")->get();

                return Datatables::of($nlbatches)
            ->editColumn('updated_at', function ($ch) {
                return formatDate($ch->updated_at);
            })
            ->editColumn('updated_at', function ($dt) {
                return formatDate($dt->updated_at);
            })
            ->editColumn('local_batch_amount', function ($dt) {
                return number_format($dt->local_batch_amount, 2);
            })
            ->addIndexColumn()
            ->addColumn('action', function ($row) {
                $btn = '<a href="javascript:void(0)" data-toggle="tooltip"  data-id="' . $row->batch_no . '" data-status ="' . $row->batch_status . '" data-original-title="View" class="edit"><i class="fa fa-pencil-square-o"></i></a>';
                return $btn;
            })
            ->rawColumns(['action'])
            ->make(true);
    }

    function getPostedglBatch(Request $request) {
        $nlbatches = GLBatch::whereRaw("(batch_source != 'AP' and batch_source != 'AR' and batch_source != 'BNK' and  batch_status in ('004', '005'))
        or (batch_source =  'AP' and batch_status in ('004', '005'))
        or (batch_source =  'AR' and batch_status in ('004', '005'))
        or (batch_source =  'BNK' and batch_status in ('004', '005'))  and  batch_status in ('004', '005')")
            ->orderBy("created_at", "asc")->get();

            return Datatables::of($nlbatches)
            ->editColumn('updated_at', function ($ch) {
                return formatDate($ch->updated_at);
            })
            ->editColumn('updated_at', function ($dt) {
                return formatDate($dt->updated_at);
            })
            ->editColumn('local_batch_amount', function ($dt) {
                return number_format($dt->local_batch_amount, 2);
            })
            ->addIndexColumn()
            ->make(true);
    }

    function insertGlTransactFromCoverDebit($coverdebit){
        // DB::beginTransaction();
        try {

        $transaction_no = str_pad($coverdebit->dr_no, 6, '0', STR_PAD_LEFT);
        $reference_no=$transaction_no.$coverdebit->period_year;
        $endorsement_no = $coverdebit->endorsement_no;
        $net_amt = $coverdebit->net_amt;
        $verified = 'N';
        $gl_accounts_availed = 'N';
        $error_message = '';
        $netDR = 0;
        $netCR = 0;
        if($net_amt >=0){
            $netCR = $net_amt;
            $netDrCr = 'C';
        }else{
            $netDR = $net_amt * -1;
            $netDrCr = 'D';
        }
        $CoverRegister = CoverRegister::where('endorsement_no', $endorsement_no)->first();
        $entryTypes = CoverPremium::where('endorsement_no', $endorsement_no)->where('final_amount','!=',0)
                                            ->pluck('entry_type_descr')
                                            ->unique();
        $missingEntryTypes = [];
        foreach ($entryTypes as $entryType) {
            $exists = CoverGledgerLink::where('type_of_bus', $CoverRegister->type_of_bus)
                ->where('transaction_type', $CoverRegister->transaction_type)
                ->where('entry_type_descr', $entryType)
                ->whereNotNull('cedant_glaccount')
                ->exists();
            if (!$exists) {
                $missingEntryTypes[] = $entryType;
            }
        }

       $netExists = CoverGledgerLink::where('type_of_bus', $CoverRegister->type_of_bus)
                ->where('transaction_type', $CoverRegister->transaction_type)
                ->where('entry_type_descr', 'NET')
                ->whereNotNull('cedant_glaccount')
                ->count();
        $sumDR = CoverPremium::where('endorsement_no', $endorsement_no)
                                ->where('dr_cr', 'DR')
                                ->sum('final_amount');
    
        // Sum of final_amount where dr_cr = 'CR'
        $sumCR = CoverPremium::where('endorsement_no', $endorsement_no)
                                ->where('dr_cr', 'CR')
                                ->sum('final_amount');
        $totalDR = $sumDR + $netDR;
        $totalCR = $sumCR + $netCR;
        $totalVariance = $totalDR - $totalCR ;
        // dd($sumDR,$netDR,$sumCR , $netCR,$netExists,$missingEntryTypes);

        if($totalVariance ==0 && empty($missingEntryTypes) && $netExists>0 ){
            $verified = 'Y';
        }
        // dd($verified);
        if($verified =='Y'){
            $CoverPremiums = CoverPremium::where('endorsement_no', $endorsement_no)->where('final_amount','!=',0)
                                            ->distinct(['transaction_type', 'entry_type_descr'])
                                            ->get();
                //get distinct entry_type_decr
            // dd($coverdebit);
            $counter = 0;
            // Loop
            foreach ($CoverPremiums as $CoverPremium) {
                $gledger_link = CoverGledgerLink::where('type_of_bus', $CoverRegister->type_of_bus)
                                            ->where('transaction_type', $CoverPremium->transaction_type)
                                            ->where('entry_type_descr', $CoverPremium->entry_type_descr)->first();
                $counter = $counter + 1 ;
                $foreign_amount = CoverPremium::where('endorsement_no', $endorsement_no)
                                                    ->where('transaction_type', $CoverPremium->transaction_type)
                                                    ->where('entry_type_descr', $CoverPremium->entry_type_descr)
                                                    ->sum('final_amount');
                $local_amount = $foreign_amount * $CoverRegister->currency_rate;
                // dd(substr($CoverPremium->dr_cr, 0, 1));
                // dd('start');
                // --insert cover premiums to Gl transactions table
                $create_gltransaction = new GLTransaction();
                $create_gltransaction->source_code ='U/W';
                $create_gltransaction->offcd = $CoverRegister->branch_code;
                $create_gltransaction->doc_type = $coverdebit->document;
                $create_gltransaction->entry_type_descr = $CoverPremium->entry_type_descr;
                $create_gltransaction->reference_no = $reference_no;
                $create_gltransaction->line_no = $counter;
                $create_gltransaction->cover_no = $CoverPremium->cover_no;
                $create_gltransaction->endorsement_no = $CoverPremium->endorsement_no;
                $create_gltransaction->claim_no = ' ';
                $create_gltransaction->item_no = 0;
                $create_gltransaction->dr_cr = substr($CoverPremium->dr_cr, 0, 1);
                $create_gltransaction->pay_request_no =  ' ';
                $create_gltransaction->gl_account = $gledger_link->cedant_glaccount;
                $create_gltransaction->period_year = $coverdebit->period_year;
                $create_gltransaction->period_month = $coverdebit->period_month;
                $create_gltransaction->local_amount = $local_amount;
                $create_gltransaction->foreign_amount = $foreign_amount;
                $create_gltransaction->branch = $CoverRegister->branch_code;
                $create_gltransaction->currency_code = $CoverRegister->currency_code;
                $create_gltransaction->currency_rate = $CoverRegister->currency_rate;
                $create_gltransaction->debit_credit_no = $transaction_no;
                $create_gltransaction->customer_id = $CoverRegister->customer_id;
                $create_gltransaction->created_date = $coverdebit->created_at->format('Y-m-d');
                $create_gltransaction->created_by = $coverdebit->created_by;
                $create_gltransaction->created_time = $coverdebit->created_at->format('H:i:s');
                $create_gltransaction->updated_date = $coverdebit->updated_at->format('Y-m-d');
                $create_gltransaction->updated_by = $coverdebit->updated_by;
                $create_gltransaction->updated_time = $coverdebit->updated_at->format('H:i:s');
                $create_gltransaction->narration = $gledger_link->entry_type_name;
                $create_gltransaction->save();
                
                $bank_flag = COA_Config::where('account_number', $create_gltransaction->gl_account)->where('segment_code', 'COD')->first()->bank_flag;

                if($bank_flag =='Y'){
                $this->insertBankTransaction('U/W',$create_gltransaction->source_code, $create_gltransaction->offcd, $create_gltransaction->gl_account, $create_gltransaction->doc_type, $create_gltransaction->entry_type_descr, $create_gltransaction->reference_no, $create_gltransaction->pay_request_no, $create_gltransaction->line_no, $create_gltransaction->item_no);
                }
                // dd('wewe',$create_gltransaction);
                $this->updateGlMastbalfromCB($gledger_link->cedant_glaccount,substr($CoverPremium->dr_cr, 0, 1),$CoverRegister->branch_code,$coverdebit->period_year,$coverdebit->period_month,$local_amount);
            }

            // --insert Net account to Gl transactions table
            $gledger_link = CoverGledgerLink::where('type_of_bus', $CoverRegister->type_of_bus)
                                                ->where('transaction_type', $CoverRegister->transaction_type)
                                                ->where('entry_type_descr', 'NET')->first();
            $foreign_amount = $coverdebit->net_amt ;
            $local_amount = $coverdebit->net_amt * $CoverRegister->currency_rate;
            $create_gltransaction = new GLTransaction();
            $create_gltransaction->source_code = 'U/W';
            $create_gltransaction->offcd = $CoverRegister->branch_code;
            $create_gltransaction->doc_type = $coverdebit->document;
            $create_gltransaction->entry_type_descr = 'NET';
            $create_gltransaction->reference_no = $reference_no;
            $create_gltransaction->line_no = $counter + 1;
            $create_gltransaction->cover_no = $CoverRegister->cover_no;
            $create_gltransaction->endorsement_no = $CoverRegister->endorsement_no;
            $create_gltransaction->claim_no = ' ';
            $create_gltransaction->item_no = 0;
            $create_gltransaction->dr_cr = $netDrCr;
            $create_gltransaction->pay_request_no =  ' ';
            $create_gltransaction->gl_account = $gledger_link->cedant_glaccount;
            $create_gltransaction->period_year = $coverdebit->period_year;
            $create_gltransaction->period_month = $coverdebit->period_month;
            $create_gltransaction->local_amount = $local_amount;
            $create_gltransaction->foreign_amount = $foreign_amount;
            $create_gltransaction->branch = $CoverRegister->branch_code;
            $create_gltransaction->currency_code = $CoverRegister->currency_code;
            $create_gltransaction->currency_rate = $CoverRegister->currency_rate;
            $create_gltransaction->debit_credit_no = $transaction_no;
            $create_gltransaction->customer_id = $CoverRegister->customer_id;
            $create_gltransaction->created_date = $coverdebit->created_at->format('Y-m-d');
            $create_gltransaction->created_by = $coverdebit->created_by;
            $create_gltransaction->created_time = $coverdebit->created_at->format('H:i:s');
            $create_gltransaction->updated_date = $coverdebit->updated_at->format('Y-m-d');
            $create_gltransaction->updated_by = $coverdebit->updated_by;
            $create_gltransaction->updated_time = $coverdebit->updated_at->format('H:i:s');
            $create_gltransaction->narration = $gledger_link->entry_type_name;
            $create_gltransaction->save();
            

            $bank_flag = COA_Config::where('account_number', $create_gltransaction->gl_account)->where('segment_code', 'COD')->first()->bank_flag;

            if($bank_flag =='Y'){
            $this->insertBankTransaction('U/W',$create_gltransaction->source_code, $create_gltransaction->offcd, $create_gltransaction->gl_account, $create_gltransaction->doc_type, $create_gltransaction->entry_type_descr, $create_gltransaction->reference_no, $create_gltransaction->pay_request_no, $create_gltransaction->line_no, $create_gltransaction->item_no);
            }
            // dd($create_gltransaction);
            $this->updateGlMastbalfromCB($gledger_link->cedant_glaccount,$netDrCr,$CoverRegister->branch_code,$coverdebit->period_year,$coverdebit->period_month,$local_amount);
            
            $Debit = CoverDebit::where('id',$coverdebit->id)->update([
                'gl_updated' =>'Y',
                'gl_updated_errors' => '',
            ]);
            // dd($Debit);
            // DB::commit(); 
        }else{
            if ($totalVariance != 0) {
                $error_message .='Out of balance where debits are '.$totalDR.' and Credits are '.$totalCR.'.';
            }
            if( !empty($missingEntryTypes)){
                $error_message .= "Missing transaction types: " . implode(', ', $missingEntryTypes) . '.';
            }
            
            $Debit = CoverDebit::where('id',$coverdebit->id)->update([
                'gl_updated' =>'N',
                'gl_updated_errors' => $error_message,
            ]);
        }
                DB::commit(); 
                //code...
    } catch (\Throwable $e) {
        DB::rollback();
        dd($e);
    }

    }

    function PostglBatchHeader(Request $request) {
        // dd($request);
        $period_year = $request->period_year;
        $period_month_request = $request->period_month;
        $period_month = str_pad($period_month_request, 2, 0, STR_PAD_LEFT);
        
        $batch_src = $request->b_source;
        $batch_no = GLBatch::where('batch_source',$batch_src)->max('batch_no') + 1;
        $batch_no =str_pad($batch_no, 6, '0', STR_PAD_LEFT);
        DB::beginTransaction();

        try {
            $new_batch = GLBatch::create([
                'batch_no' => $batch_src.$batch_no,
                'batch_source' => $batch_src,
                'batch_title' => $request->b_title,
                'batch_description' => $request->narration,
                'account_year' => $period_year,
                'account_month' => $period_month,
                'batch_type' => $request->batch_type,
                'batch_date' => Carbon::now(),
                'created_at' => Carbon::now(),
                'created_by' => Auth::user()->user_name,
                'updated_by' => Auth::user()->user_name,
                'updated_at' => Carbon::now(),
                'no_of_entries' => 0,
                'foreign_batch_amount' => 0,
                'local_batch_amount' => 0,
                // 'expected_batch_total' => str_replace(',','',$request->exp_amount),
                'currency_code' => 'KES',
                'currency_rate' => 1,
                'batch_status' => '001',
                'reversal_reference' => ' ',
                'entry_type_descr' => $batch_src,
                'doc_type' => 'JV',
                'offcd'=>'101'
            ]);

            DB::commit();
            return response()->json([
                'status' => 200,
                'message' => 'Data saved successfully'
            ]);
    } catch (ValidationException $e) {
        DB::rollback();
        dd($e);
        // If validation fails, return a JSON response with errors
        return response()->json([
            'status' => Response::HTTP_UNPROCESSABLE_ENTITY,
            'errors' => $e->errors()
        ], 422);
    }
    catch(Throwable $e)
    {
        DB::rollback();
        dd($e);
        return response()->json([
            'status' => $e->getCode(),
            'message' => 'Failed to save'
        ]);
    }
    }
    function glBatchDtl(Request $request) {
        // dd($request);
        $batch_no = $request->batch_no;
        $year = $request->period_year;
        $month = $request->period_month;

        $gl_accounts = COA_Config::where('segment_code', 'COD')->where('bank_flag', '!=', 'Y')->get();
        $currencies = Currency::all();
        $rates = CurrencyRate::where('currency_date', Carbon::today())->get();

        $batch = GLBatch::whereRaw("trim(batch_no) = '" . $batch_no . "'")
                        ->where('account_year', $year)
                        ->where('account_month', $month)
                        ->first();

        $FamountDR = GLBatchDtl::whereRaw("trim(batch_no) = '" . $batch_no . "'")->where('dr_cr','D')->sum('foreign_dr_amount'); 
        $FamountDR = $FamountDR ? $FamountDR : 0;               
        $LamountDR = GLBatchDtl::whereRaw("trim(batch_no) = '" . $batch_no . "'")->where('dr_cr','D')->sum('local_dr_amount');                
        $LamountDR = $LamountDR ? $LamountDR : 0 ;

        $FamountCR = GLBatchDtl::whereRaw("trim(batch_no) = '" . $batch_no . "'")->where('dr_cr','C')->sum('foreign_cr_amount'); 
        $FamountCR = $FamountCR ? $FamountCR : 0;               
        $LamountCR = GLBatchDtl::whereRaw("trim(batch_no) = '" . $batch_no . "'")->where('dr_cr','C')->sum('foreign_cr_amount');                
        $LamountCR = $LamountCR ? $LamountCR : 0;
        $verifiers = User::permission('verify glbatch')
                            ->where('user_name','<>',Auth::user()->user_name)
                            ->get();
        $process = SystemProcess::where('nice_name','gl-batch-process')->first();
        // dd($process);
        $verifyprocessAction = SystemProcessAction::where('nice_name','verify-glbatch')->first();
        return view::make('finance.gl.glbatchdtl', compact('batch', 'currencies', 'rates','gl_accounts','FamountDR','LamountDR','FamountCR','LamountCR','verifiers','verifyprocessAction','process'));    
    }

    function getglBatchDtl(Request $request) {
        // dd($request);
        $batchitems = DB::table('glbatchdtl')
                    ->join('coa_config', function($join) {
                        $join->on('glbatchdtl.glaccount', '=', 'coa_config.account_number')
                            ->where('coa_config.segment_code', 'COD');
                    })
                    ->select('glbatchdtl.*', 'coa_config.segment_code', 'coa_config.description as glaccount_name') // Select the necessary columns
                    ->whereRaw("trim(glbatchdtl.batch_no) = ?", [$request->batch_no])
                    ->get();

                    return Datatables::of($batchitems)
                    
                    ->addIndexColumn()
                    ->addColumn('action', function ($row) {
                        $btn = '<a href="javascript:void(0)" data-toggle="tooltip"  data-batch_no="' . $row->batch_no . '" data-item_no ="' . $row->item_no . '" data-original-title="View" class="edit"><i class="fa fa-pencil-square-o"></i>Edit</a>';
                        return $btn;
                    })
                    ->rawColumns(['action'])
                    ->make(true);
    }

    function postBatchItem(Request $request) {
        // dd($request);
        $batch_no = $request->batch_no;
        $batch = GLBatch::where('batch_no',$batch_no)->first();
        $item_no = GLBatchDtl::where('batch_no',$batch_no)->max('item_no') + 1;
        $foreign_dr_amount = 0;
        $foreign_cr_amount = 0;
        $item_amt = str_replace(',','',$request->item_amount);
        $dr_cr = $request->dr_cr;
        DB::beginTransaction();

        try {

            if($dr_cr =='D'){
                $foreign_dr_amount = $item_amt;
            }else{
                $foreign_cr_amount = $item_amt;
            }
            $currency_code = $batch->currency_code;
            $currency_rate = $batch->currency_rate ? $batch->currency_rate : 1;

            $local_dr_amount = $foreign_dr_amount * $currency_rate;
            $local_cr_amount = $foreign_cr_amount * $currency_rate;

            $batchitem = new GLBatchDtl();
            $batchitem->batch_no = $batch_no;
            $batchitem->item_no = $item_no;
            $batchitem->item_description = $request->item_desc;
            $batchitem->glaccount = $request->gl_account;
            $batchitem->dr_cr = $dr_cr;
            $batchitem->glaccount = $request->gl_account;
            $batchitem->foreign_dr_amount = $foreign_dr_amount;
            $batchitem->local_dr_amount = $local_dr_amount;
            $batchitem->foreign_cr_amount = $foreign_cr_amount;
            $batchitem->local_cr_amount = $local_cr_amount;
            $batchitem->currency_code = $currency_code;
            $batchitem->currency_rate = $currency_rate;
            $batchitem->deleted = 'N';
            $batchitem->offcd = $batch->offcd;
            $batchitem->payee_name = ' ';
            $batchitem->reference_no = ' ';
            $batchitem->entry_type_descr = $batch->entry_type_descr;
            $batchitem->created_by = Auth::user()->user_name;
            $batchitem->updated_by = Auth::user()->user_name;
            $batchitem->save();

                DB::commit();
                return response()->json([
                    'status' => 200,
                    'message' => 'Data saved successfully'
                ]);
        } 
        catch (ValidationException $e) {
            DB::rollback();
            dd($e);
            // If validation fails, return a JSON response with errors
            return response()->json([
                'status' => Response::HTTP_UNPROCESSABLE_ENTITY,
                'errors' => $e->errors()
            ], 422);
        }
        catch(Throwable $e)
        {
            DB::rollback();
            dd($e);
            return response()->json([
                'status' => $e->getCode(),
                'message' => 'Failed to save'
            ]);
        }

    }

    public function preBatchVerification(Request $request)
    {
        try
        {
            $batch_no = $request->batch_no;
            $batch = GLBatch::whereRaw("trim(batch_no) = '" . $batch_no . "'")->first();
            $currency_rate = $batch->currency_rate;
    
            $FamountDR = GLBatchDtl::whereRaw("trim(batch_no) = '" . $batch_no . "'")->where('dr_cr','D')->sum('foreign_dr_amount'); 
            $FamountDR = $FamountDR ? $FamountDR : 0;               
            $LamountDR = GLBatchDtl::whereRaw("trim(batch_no) = '" . $batch_no . "'")->where('dr_cr','D')->sum('local_dr_amount');                
            $LamountDR = $LamountDR ? $LamountDR : 0 ;
    
            $FamountCR = GLBatchDtl::whereRaw("trim(batch_no) = '" . $batch_no . "'")->where('dr_cr','C')->sum('foreign_cr_amount'); 
            $FamountCR = $FamountCR ? $FamountCR : 0;               
            $LamountCR = GLBatchDtl::whereRaw("trim(batch_no) = '" . $batch_no . "'")->where('dr_cr','C')->sum('foreign_cr_amount');                
            $LamountCR = $LamountCR ? $LamountCR : 0;

            $pending = [];

            if($LamountDR != $LamountCR){
                array_push($pending,'The Batch is out of balance');
            }

            if($FamountDR != $FamountDR * $currency_rate){
                array_push($pending,'Debit Foreign and converted Debit Amount not the same');
            }

            if($FamountCR != $FamountCR * $currency_rate){
                array_push($pending,'Credit Foreign and converted Credit Amount not the same');
            }

            return $pending;
        }
        catch(Throwable $e)
        {
            dd($e);
            return ['An internal error occured'];
        }
    }

    function postglBatch(Request $request){
        DB::beginTransaction();
        try {
            $batch_no = $request->batch_no;
            $batch = GLBatch::where('batch_no',$batch_no)->first();
            $batchitems = GLBatchDtl::where('batch_no',$batch_no)->get();
            foreach ($batchitems as $batchitem) {
                // --insert credit account to Gl transactions table
                if($batchitem->dr_cr=='D'){
                    $foreign_amount = $batchitem->foreign_dr_amount;
                    $local_amount = $batchitem->local_dr_amount;
                }else{
                    $foreign_amount = $batchitem->foreign_cr_amount;
                    $local_amount = $batchitem->local_cr_amount;
                }
                $create_gltransaction = GLTransaction::create([
                    'source_code'=>$batch->batch_source,
                    'offcd'=>$batch->offcd,
                    'doc_type'=>$batch->batch_type,
                    'entry_type_descr'=>$batch->entry_type_descr,
                    'reference_no'=>$batchitem->reference_no,
                    'line_no'=>$batchitem->item_no,
                    'cover_no'=>' ',
                    'endorsement_no'=>' ',
                    'claim_no'=>' ',
                    'item_no'=>$batchitem->item_no,
                    'dr_cr'=>$batchitem->dr_cr,
                    'pay_request_no'=>$batchitem->reference_no,
                    'gl_account'=>$batchitem->glaccount,
                    'period_year'=>$batch->account_year,
                    'period_month'=>$batch->account_month,
                    'local_amount'=>$local_amount,
                    'foreign_amount'=>$foreign_amount,
                    'branch'=>$batchitem->offcd,
                    'currency_code'=>$batch->currency_code,
                    'currency_rate'=>$batch->currency_rate,
                    'debit_credit_no'=>' ',
                    'customer_id'=>' ',
                    'created_date'=>Carbon::now(),
                    'created_by'=>Auth::user()->user_name,
                    'created_time'=>Carbon::now(),
                    'updated_date'=>Carbon::now(),
                    'updated_by'=>Auth::user()->user_name,
                    'updated_time'=>Carbon::now(),
                    'narration'=> ' ',
                    ]);
    
                    $this->updateGlMastbalfromCB($batchitem->glaccount,$batchitem->dr_cr,$batch->offcd,$batch->account_year,$batch->account_month,$local_amount);   
            }
            DB::commit();
            return response()->json([
                'status' => 200,
                'message' => 'Data saved successfully'
            ]);
        }
        catch (ValidationException $e) {
            DB::rollback();
            dd($e);
            // If validation fails, return a JSON response with errors
            return response()->json([
                'status' => Response::HTTP_UNPROCESSABLE_ENTITY,
                'errors' => $e->errors()
            ], 422);
        }
        catch(Throwable $e)
        {
            DB::rollback();
            dd($e);
            return response()->json([
                'status' => $e->getCode(),
                'message' => 'Failed to save'
            ]);
        }
    }

    function insertBankTransaction($glsource,$source_code, $offcd, $gl_account, $doc_type, $entry_type_descr, $reference_no, $pay_request_no, $line_no, $item_no) {
        $gltransaction = GLTransaction::where('source_code',$source_code) 
                                        ->where('offcd',$offcd)
                                        ->where('gl_account',$gl_account)
                                        ->where('doc_type',$doc_type)
                                        ->where('entry_type_descr',$entry_type_descr)
                                        ->where('reference_no',$reference_no)
                                        ->where('pay_request_no',$pay_request_no)
                                        ->where('line_no',$line_no)
                                        ->where('item_no',$item_no)
                                        ->first();
        
        $create_banktransaction = new BankTransaction();
        $create_banktransaction->source = $glsource;
        // $create_banktransaction->offcd = $offcd;
        $create_banktransaction->bank_acc_code = $gl_account;
        $create_banktransaction->doc_type = $doc_type;
        // $create_banktransaction->entry_type_descr = $entry_type_descr;
        $create_banktransaction->reference_no = $reference_no;
        // $create_banktransaction->pay_request_no = $pay_request_no;
        $create_banktransaction->item_no = $item_no;
        // $create_banktransaction->line_no = $line_no;
        $create_banktransaction->foreign_amount = $gltransaction->foreign_amount;
        $create_banktransaction->local_amount = $gltransaction->local_amount;
        $create_banktransaction->dr_cr = $gltransaction->dr_cr;
        $create_banktransaction->currency_code = $gltransaction->currency_code;
        $create_banktransaction->currency_rate = $gltransaction->currency_rate;
        $create_banktransaction->created_by = Auth::user()->user_name;
        $create_banktransaction->updated_by = Auth::user()->user_name;
        $create_banktransaction->cheque_no = $gltransaction->cheque_no;
        $create_banktransaction->reconcilled = 'N';
        $create_banktransaction->reconcilliation_date = null;
        $create_banktransaction->reconcilliation_year = null;
        $create_banktransaction->reconcilliation_month = null;
        $create_banktransaction->batch_no = $gltransaction->batch_no ? $gltransaction->batch_no : $reference_no;
        $create_banktransaction->trans_description = $gltransaction->narration;
        $create_banktransaction->save();
        
    }
    //END
}
