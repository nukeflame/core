<?php

namespace App\Repositories;

use App\Models\Branch;
use App\Models\Broker;
use App\Models\BusinessType;
use App\Models\ClaimRegister;
use App\Models\Classes;
use App\Models\ClauseParam;
use App\Models\CoverAttachment;
use App\Models\CoverClass;
use App\Models\CoverClause;
use App\Models\CoverDebit;
use App\Models\CoverPremium;
use App\Models\CoverPremtype;
use App\Models\CoverRegister;
use App\Models\CoverReinclass;
use App\Models\CoverReinLayer;
use App\Models\CoverRipart;
use App\Models\CoverType;
use App\Models\Customer;
use App\Models\EndorsementNarration;
use App\Models\ReinNote;
use App\Models\ScheduleHeader;
use App\Models\SystemProcess;
use App\Models\SystemProcessAction;
use App\Models\User;
use App\Models\WhtRate;
use Carbon\Carbon;
use Illuminate\Support\Facades\Auth;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Symfony\Component\HttpFoundation\Response;
use Illuminate\Validation\Rule;
use Prettus\Repository\Criteria\RequestCriteria;
use Prettus\Repository\Exceptions\RepositoryException;
use App\Models\CoverInstallments;
use App\Models\CoverReinProp;
use App\Models\CoverRisk;
use App\Models\CoverSlipWording;
use App\Models\CustomerAccDet;
use App\Models\EndorsementType;
use App\Models\PayMethod;
use App\Models\PolicyRenewal;
use App\Models\PremiumPayTerm;
use App\Models\ReinclassPremtype;
use App\Models\ReinsClass;
use App\Models\SlipTemplate;
use App\Models\TreatyType;

/**
 * Class CoverRepositoryRepositoryEloquent.
 *
 * @package namespace App\Repositories;
 */
class CoverRepository extends BaseRepository
{
    protected $fieldSearchable = [];
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

    /**
     * Specify Model class name
     *
     * @return string
     */
    public function model()
    {
        return CoverRegister::class;
    }

    /**
     * Boot up the repository, pushing criteria
     */
    public function boot()
    {
        try {
            $this->pushCriteria(app(RequestCriteria::class));
        } catch (RepositoryException $e) {
        }
    }

    public function processCoverHome(Request $request)
    {
        try {
            $endorsement_no = $request->endorsement_no;
            $CoverRegister = CoverRegister::where('endorsement_no', $endorsement_no)->first();
            $coverpart = CoverRipart::where('endorsement_no', $endorsement_no)->get();
            // $coverAttachments = CoverAttachment::where('endorsement_no', $endorsement_no)->get();
            $coverReinclass = CoverReinclass::where('endorsement_no', $endorsement_no)->get();
            $coverTreaties = CoverPremtype::join('treaty_types', 'cover_premtypes.treaty', '=', 'treaty_types.treaty_code')
                ->where('cover_premtypes.endorsement_no', $endorsement_no)
                ->distinct('cover_premtypes.treaty')
                ->get(['cover_premtypes.treaty', 'treaty_types.treaty_name']);
            $branch = Branch::where('branch_code', $CoverRegister->branch_code)->first();
            $broker = Broker::where('broker_code', $CoverRegister->broker_code)->first();
            $class = Classes::where('class_code', $CoverRegister->class_code)->first();
            $clauses = ClauseParam::where('status', 'A')->where('class_code', $CoverRegister->class_code)->get();
            $selected_clauses = CoverClause::where('endorsement_no', $endorsement_no)->get();
            $ins_classes = Classes::where('status', 'A')->get();
            $customer = Customer::where('customer_id', $CoverRegister->customer_id)->first();
            $covertype = CoverType::where('type_id', $CoverRegister->cover_type)->first();
            $type_of_bus = BusinessType::where('bus_type_id', $CoverRegister->type_of_bus)->first();
            // $reinsurers=Customer::all();
            if (in_array($CoverRegister->type_of_bus, ['TPR', 'TNP'])) {
                $cusType = ['REINCO'];
            } elseif (in_array($CoverRegister->type_of_bus, ['FPR', 'FNP'])) {
                $cusType = ['REINCO', 'INSCO'];
            }

            $endorsementNarration = EndorsementNarration::where('endorsement_no', $endorsement_no)->get();
            $reinsurers = DB::table('customers')
                ->join('customer_types', function ($join) {
                    $join->on('customer_types.type_id', '=', DB::raw("ANY (SELECT json_array_elements_text(customers.customer_type)::int)"));
                })
                ->select('customers.customer_id', 'customers.name')
                ->whereIn('customer_types.code', $cusType)
                ->distinct()
                ->get();

            $schedHeaders = ScheduleHeader::orderBy('position')->get();
            $verifiers = User::where('user_name', '<>', Auth::user()->user_name)
                // permission('app.cover_administration.manage')
                ->get();

            $process = SystemProcess::where('nice_name', 'cover_registration')->first();
            $verifyprocessAction = SystemProcessAction::where('nice_name', 'verify_cover')->first();
            $debitsCount = CoverDebit::where('endorsement_no', $endorsement_no)
                ->where('reversed', 'N')
                ->count();

            // $debitedGross = CoverDebit::where('endorsement_no', $endorsement_no)
            //     ->where('reversed', 'N')
            //     ->sum('gross');
            $actionable = static::coverDebitedCommited($endorsement_no);

            $TPRTotalPrem = CoverPremium::where('endorsement_no', $endorsement_no)
                ->where('entry_type_descr', 'PRM')
                ->sum('final_amount');

            $TPRTotalCom = CoverPremium::where('endorsement_no', $endorsement_no)
                ->where('entry_type_descr', 'COM')
                ->sum('final_amount');

            $TPRTotalClaim = CoverPremium::where('endorsement_no', $endorsement_no)
                ->where('entry_type_descr', 'CLM')
                ->sum('final_amount');

            $mdpAmount = CoverReinLayer::where('endorsement_no', $endorsement_no)->sum('min_deposit');
            $reinLayersCount = CoverReinLayer::where('endorsement_no', $CoverRegister->orig_endorsement_no)
                ->groupBy('layer_no')
                ->count();
            // $reinLayers=CoverReinLayer::where('endorsement_no',$CoverRegister->orig_endorsement_no)->get();
            $reinLayers = DB::select("SELECT cover_no ,endorsement_no ,layer_no ,SUM(min_deposit) min_deposit
                FROM coverreinlayers c
                WHERE endorsement_no='$CoverRegister->orig_endorsement_no'
                group by cover_no ,endorsement_no ,layer_no
                ");
            $reinLayers = collect($reinLayers);
            $TNPTotalMdp = CoverPremium::where('endorsement_no', $endorsement_no)
                ->where('transaction_type', 'MDP')
                ->where('entry_type_descr', 'MDP')
                ->sum('final_amount');

            $TPRTotalPremTax = CoverPremium::where('endorsement_no', $endorsement_no)
                ->where('entry_type_descr', 'PTX')
                ->sum('final_amount');

            $TPRTotalRiTax = CoverPremium::where('endorsement_no', $endorsement_no)
                ->where('entry_type_descr', 'RTX')
                ->sum('final_amount');

            $totalDr = CoverPremium::where('endorsement_no', $endorsement_no)
                ->where('dr_cr', 'DR')
                ->sum('final_amount');
            // $totalDr = ($CoverRegister->share_offered / 100) * $totalDr;

            $totalCr = CoverPremium::where('endorsement_no', $endorsement_no)
                ->where('dr_cr', 'CR')
                ->sum('final_amount');
            // $totalCr = ($CoverRegister->share_offered / 100) * $totalCr;

            $totalInstallments = (int) $CoverRegister->no_of_installments;
            $remInstallment = $totalInstallments - $debitsCount;
            $nextInstallment = 0;
            $installmentAmount = 0;
            if ($remInstallment > 0) {
                $nextInstallment = $debitsCount + 1;
                switch ($CoverRegister->type_of_bus) {
                    case 'FPR':
                    case 'FNP':
                    case 'TPR':
                    case 'TNP':
                        $installmentAmount = $totalDr - $totalCr;
                        break;
                    default:
                        break;
                }
            }

            $whtRates = WhtRate::all();
            $paymethods = PayMethod::all();
            $selected_pay_method = collect($paymethods)->first(
                fn($item) => $item->pay_method_code == $CoverRegister->pay_method_code,
            );
            $isInstallment = false;
            if ($selected_pay_method && $selected_pay_method->short_description === 'I') {
                $isInstallment = true;
            }

            $CoverInstallments = CoverInstallments::where([
                'cover_no'          =>  $CoverRegister->cover_no,
                'endorsement_no'    =>  $CoverRegister->endorsement_no,
                'dr_cr'             => 'DR'
            ])->get();

            return [
                'coverReg' => $CoverRegister,
                'coverpart' => $coverpart,
                'branch' => $branch,
                'broker' => $broker,
                'class' => $class,
                'clauses' => $clauses,
                'selected_clauses' => $selected_clauses,
                'type_of_bus' => $type_of_bus,
                'ins_classes' => $ins_classes,
                'customer' => $customer,
                'covertype' => $covertype,
                'reinsurers' => $reinsurers,
                'verifiers' => $verifiers,
                'process' => $process,
                'verifyprocessAction' => $verifyprocessAction,
                'remInstallment' => $remInstallment,
                'nextInstallment' => $nextInstallment,
                'installmentAmount' => $installmentAmount,
                'coverReinclass' => $coverReinclass,
                'actionable' => $actionable,
                'TPRTotalPrem' => $TPRTotalPrem,
                'TPRTotalCom' => $TPRTotalCom,
                'TPRTotalClaim' => $TPRTotalClaim,
                'TNPTotalMdp' => $TNPTotalMdp,
                'TPRTotalPremTax' => $TPRTotalPremTax,
                'TPRTotalRiTax' => $TPRTotalRiTax,
                'schedHeaders' => $schedHeaders,
                'debitsCount' => $debitsCount,
                'coverTreaties' => $coverTreaties,
                'mdpAmount' => $mdpAmount,
                'reinLayersCount' => $reinLayersCount,
                'reinLayers' => $reinLayers,
                'whtRates' => $whtRates,
                'endorsementNarration' => $endorsementNarration,
                'paymethods' => $paymethods,
                'isInstallment' => boolval($isInstallment),
                'coverInstallments' => $CoverInstallments
            ];
        } catch (\Exception $e) {
            throw $e;
        }
    }

    public static function coverDebitedCommited($endorsement): bool
    {
        $cover = CoverRegister::select('endorsement_no', 'cover_no', 'commited', 'verified')
            ->where('endorsement_no', $endorsement)
            ->first();

        $debitted = CoverDebit::where('endorsement_no', $endorsement)
            ->count();

        $actionable = true;
        // if TNP| TPR inital cover commited
        if ($cover->commited == 'Y' || $debitted > 0) {
            $actionable = false;
        }

        return $actionable;
    }

    public function editReinsurer(Request $request)
    {
        $request->validate([
            'tran_no' => 'required',
            'endorsement_no' => 'required',
            'reinsurer' => 'required',
            'share' => 'required',
            'comm_rate' => Rule::requiredIf(function () use ($request) {
                $CoverRegister = CoverRegister::select('transaction_type', 'type_of_bus')
                    ->where('endorsement_no', $request->endorsement_no)
                    ->first();
                return in_array($CoverRegister->type_of_bus, ['FPR', 'FNP']);
            }),
        ]);

        $CoverRegister = CoverRegister::where('endorsement_no', $request->endorsement_no)->first();
        $coverRipart = CoverRipart::where('tran_no', $request->tran_no)
            ->where('endorsement_no', $request->endorsement_no)
            ->first();

        if (in_array($CoverRegister->type_of_bus, ['FPR', 'FNP'])) {

            $total_sum_insured = $CoverRegister->total_sum_insured ?? 0;
            $total_premium     = $CoverRegister->rein_premium ?? 0;
            $total_commission  = $CoverRegister->rein_comm_amount ?? 0;
            $comm_rate         = (float)str_replace(',', '', $request->comm_rate) ?? 0;;
            $wht_rate          = (float)str_replace(',', '', $request->wht_rate) ?? 0;;
            $wht_amt           = 0;
            $fronting_rate     = (float)str_replace(',', '', $request->fronting_rate) ?? 0;;
            $fronting_amt      = 0;
            $sum_insured       = (float)str_replace(',', '', $request->sum_insured) ?? 0;
            $premium           = (float)str_replace(',', '', $request->premium) ?? 0;
            $commission        = (float) ($comm_rate / 100) * $premium;
            // $commission        = (float)str_replace(',','',$request->comm_amt)??0;
            $brokerage_comm_amt = (float)str_replace(',', '', $request->brokerage_comm_amt) ?? 0;

            if ($fronting_rate > 0) {
                $fronting_amt = ($fronting_rate / 100) * ($premium - $commission);
            }
            if ($wht_rate > 0) {
                $wht_amt = ($wht_rate / 100) * ($premium - $commission);
            }

            $brokerage_comm_amt     = (float)$brokerage_comm_amt ?? 0;
            $brokerage_comm_rate    = (float)($brokerage_comm_amt / $premium) * 100 ?? 0;
        } elseif (in_array($CoverRegister->type_of_bus, ['TPR', 'TNP'])) {

            $coverPremiums = CoverPremium::where('endorsement_no', $request->endorsement_no)->get();

            $total_premium = $coverPremiums->where('entry_type_descr', 'PRM')->sum('final_amount') ?? 0;
            $total_commission = $coverPremiums->where('entry_type_descr', 'COM')->sum('final_amount') ?? 0;
            $total_sum_insured = $coverPremiums->where('entry_type_descr', 'SUM')->sum('final_amount') ?? 0;

            $sum_insured = $total_sum_insured * $coverRipart->share / 100;
            $premium = $total_premium * $coverRipart->share / 100;
            $commission = $total_commission * $coverRipart->share / 100;
            $fronting_rate = $coverRipart->fronting_rate ?? 0;
            $fronting_amt =  ($fronting_rate / 100) * ($premium - $commission);
            $wht_rate = $request->wht_rate ?? 0;
            $wht_amt = ($wht_rate / 100) * ($premium - $commission);
            $wht_amt = ($premium - $commission) * $wht_rate / 100;
        }

        $coverRipart = CoverRipart::where('tran_no', $request->tran_no)
            ->where('endorsement_no', $request->endorsement_no)
            ->where('partner_no', $request->reinsurer)
            ->update([
                'share' => $request->share,
                'written_lines' => $request->written_share,
                'total_sum_insured'  => $total_sum_insured ?? 0,
                'sum_insured' => $sum_insured ?? 0,
                'total_premium' => $total_premium  ?? 0,
                'premium' => $premium ?? 0,
                'total_commission' => $total_commission ?? 0,
                'comm_rate' => $request->comm_rate ?? 0,
                'commission' => $commission ?? 0,
                'wht_rate' => $wht_rate ?? 0,
                'wht_amt' => $wht_amt    ?? 0,
                'fronting_rate' => $fronting_rate ?? 0,
                'fronting_amt' => $fronting_amt ?? 0,
                'brokerage_comm_amt' => $brokerage_comm_amt ?? 0,
                'updated_by'  => Auth::user()->user_name,
                'updated_at' => now()
            ]);

        $coverRipart = $coverRipart ? CoverRipart::where('tran_no', $request->tran_no)->where('endorsement_no', $request->endorsement_no)->where('partner_no', $request->reinsurer)->first() : null;

        //
        $premItemTypes = [
            'PRM' => [
                'descr' => 'Gross Premium',
                'dr_cr' => 'CR',
                'tax_rate' => $coverRipart->share  ?? 0,
                'total_amount' => $CoverRegister->rein_premium ?? 0,
                'amount' => $coverRipart->premium  ?? 0,
            ],
            'BRC' => [
                'descr' => 'Brokerage Commission',
                'dr_cr' => 'DR',
                'amount' => $coverRipart->brokerage_comm_amt  ?? 0,
                'tax_rate' => $coverRipart->brokerage_comm_rate  ?? 0,
                'total_amount' => $coverRipart->premium  ?? 0,
            ],
            'COM' => [
                'descr' => 'Commission',
                'dr_cr' => 'DR',
                'tax_rate' => $coverRipart->comm_rate  ?? 0,
                'amount' => $coverRipart->commission  ?? 0,
                'total_amount' => $coverRipart->premium  ?? 0,
            ],
            'WHT' => [
                'descr' => 'With holding Tax',
                'dr_cr' => 'DR',
                'tax_rate' => $coverRipart->wht_rate  ?? 0,
                'amount' => $coverRipart->wht_amt  ?? 0,
                'total_amount' => (float)(($coverRipart->premium  ?? 0) - ($coverRipart->commission  ?? 0)),
            ],
            'FRF' => [
                'descr' => 'Fronting Fees',
                'dr_cr' => 'CR',
                'tax_rate' => $coverRipart->fronting_rate  ?? 0,
                'amount' => $coverRipart->fronting_amt  ?? 0,
                'total_amount' => (float)(($coverRipart->premium  ?? 0) - ($coverRipart->commission  ?? 0)),
            ],
        ];

        foreach ($premItemTypes as $key => $premItemType) {
            $totalCredit = 0;
            $totalDebit = 0;
            if ($premItemType['dr_cr'] === 'CR' && $key !== 'BRC') {
                $totalCredit = $premItemType['amount'];
            }
            $tran_no = (int) ReinNote::where('endorsement_no', $CoverRegister->endorsement_no)->max('tran_no') + 1;

            $ln_no = (int)ReinNote::where('endorsement_no', $CoverRegister->endorsement_no)
                ->where('transaction_type', $CoverRegister->transaction_type)
                ->where('entry_type_descr', $key)
                ->count() + 1;

            $totalDebit = (float)($coverRipart->commission ?? 0 + $coverRipart->wht_amt ?? 0 + $coverRipart->fronting_amt ?? 0);
            $tran_no = (int)ReinNote::where('endorsement_no', $CoverRegister->endorsement_no)->max('tran_no') + 1;
            $ln_no = (int)ReinNote::where('endorsement_no', $CoverRegister->endorsement_no)
                ->where('transaction_type', $CoverRegister->transaction_type)
                ->where('entry_type_descr', $key)
                ->count() + 1;
            $share = (float)$coverRipart->share ?? 0;
            $username = Auth::user()->user_name;
            $net_amnt = $totalCredit - $totalDebit;

            $data = [
                'cover_no'          => $CoverRegister->cover_no,
                'endorsement_no'    => $CoverRegister->endorsement_no,
                'partner_no'        => $coverRipart->partner_no,
                'transaction_type'  => $CoverRegister->transaction_type,
                'account_year'      => $this->_year,
                'account_month'     => $this->_month,
                'share'             => $share,
                'created_by'        => $username,
                'updated_by'        => $username,
                'tran_no'           => $tran_no,
                'ln_no'             => $ln_no,
                'entry_type_descr'  => $key,
                'item_title'        => $premItemType['descr'],
                'dr_cr'             => $premItemType['dr_cr'],
                'rate'              => $premItemType['tax_rate'] ?? 0,
                'total_gross'       => $premItemType['total_amount'],
                'gross'             => $premItemType['amount'],
                'net_amt'           => $net_amnt,
            ];

            ReinNote::create($data);
        }

        return response()->json([
            'status' => Response::HTTP_CREATED,
            'message' => 'Data saved successfully'
        ]);
    }

    public function preCoverVerification(Request $request)
    {
        try {
            $cover = CoverRegister::where('endorsement_no', $request->endorsement_no)->first();
            $share_offered = $cover->share_offered;
            $pending = [];
            switch ($cover->type_of_bus) {
                case 'FPR':
                case 'FNP':
                    $total_share_placed = (float)CoverRipart::where('endorsement_no', $request->endorsement_no)
                        ->sum('share');

                    if ($share_offered <> $total_share_placed) {
                        array_push($pending, 'There is a pending Reinsurer share not yet placed');
                    }
                    break;
                case 'TPR':
                case 'TNP':
                    $coverTreaties = CoverPremtype::join('treaty_types', 'cover_premtypes.treaty', '=', 'treaty_types.treaty_code')
                        ->where('cover_premtypes.endorsement_no', $request->endorsement_no)
                        ->distinct('cover_premtypes.treaty')
                        ->get(['cover_premtypes.treaty', 'treaty_types.treaty_name']);

                    if ($coverTreaties->count() > 1) {
                        $totalSharePlaced = CoverRipart::select('endorsement_no', 'treaty', DB::raw('SUM(amount) as total_amount'))
                            ->where('endorsement_no', $request->endorsement_no)
                            ->groupBy('endorsement_no', 'treaty')
                            ->get();

                        $filteredTransactions = $totalSharePlaced->filter(function ($transaction) use ($share_offered) {
                            return $transaction->total_amount != $share_offered;
                        });

                        if ($filteredTransactions->count() > 0) {
                            array_push($pending, 'There is a pending Reinsurer share not yet placed');
                        }
                    } else {
                        $total_share_placed = (float)CoverRipart::where('endorsement_no', $request->endorsement_no)
                            ->sum('share');

                        if ($share_offered <> $total_share_placed) {
                            array_push($pending, 'There is a pending Reinsurer share not yet placed');
                        }
                    }
                    break;
            }
            $installmentPrems = 0;
            $installmentPrems = CoverInstallments::where(['endorsement_no' => $request->endorsement_no, 'dr_cr' => 'DR'])->sum('installment_amt');
            $totalDr = CoverPremium::where('endorsement_no', $request->endorsement_no)
                ->where('dr_cr', 'DR')
                ->sum('final_amount');
            $totalCr = CoverPremium::where('endorsement_no', $request->endorsement_no)
                ->where('dr_cr', 'CR')
                ->sum('final_amount');

            $installmentAmount = $totalDr - $totalCr;

            if (round((float)$installmentPrems) !== round((float)$installmentAmount)) {
                array_push($pending, 'The total installment amount does not match the total amount.');
            }

            return $pending;
        } catch (\Exception $e) {
            return ['An internal error occured'];
        }
    }

    public function generateEndorseNo($type_of_bus, $trans_type)
    {
        $endorse_serial_no = (CoverRegister::where('type_of_bus', $type_of_bus)
            ->where('transaction_type', $trans_type)
            ->where('account_year', $this->_year)
            ->withTrashed()
            ->max('cover_serial_no') ?? 0) + 1;
        $endorse_serial_no = str_pad($endorse_serial_no, 6, '0', STR_PAD_LEFT);

        return (object)[
            // 'endorsement_no' => $type_of_bus . $trans_type . $endorse_serial_no . $this->_year,
            'endorsement_no' => 'C' . $trans_type . $endorse_serial_no . $this->_year,
            'serial_no' => $endorse_serial_no
        ];
    }

    public function registerCover($request)
    {
        try {
            // $currentYear = str_pad($this->_year, 4, '0', STR_PAD_LEFT);
            // $currentMonth = str_pad($this->_month, 2, '0', STR_PAD_LEFT);
            $risk_details = $request->risk_details;
            // $risk_details = Purifier::clean($request->risk_details);

            $covertype = $request->covertype;
            $branchcode = (int)$request->branchcode;
            $brokercode = $request->brokercode;
            $type_of_bus = $request->type_of_bus;
            $customer_id = $request->customer_id;
            $class_group = $request->class_group;

            $customer = Customer::where('customer_id', $request->customer_id)->first();
            $treatytype = TreatyType::where('treaty_code', $request->treatytype)->first();

            if ($request->type_of_bus == 'FPR' || $request->type_of_bus == 'FNP') {
                $classcode = $request->classcode;
                $insured_name = $request->insured_name;
                $class_name = Classes::select('class_name')->where('class_code', $classcode)->first();
                $treaty_name = $class_name->class_name . ' FACULTATIVE';
                $date_offered = $request->fac_date_offered;
                $share_offered = $request->fac_share_offered;
                $rein_premium = $request->rein_premium ? str_replace(',', '', $request->rein_premium) : 0;
                if ($request->brokerage_comm_type == 'R') {
                    $brokerage_comm_rate = $request->brokerage_comm_rate ? str_replace(',', '', $request->brokerage_comm_rate) : 0;
                    $brokerage_comm_amt = ($brokerage_comm_rate / 100) * $rein_premium;
                } else {
                    $brokerage_comm_amt = $request->brokerage_comm_amt ? str_replace(',', '', $request->brokerage_comm_amt) : 0;
                    $brokerage_comm_rate = ($brokerage_comm_amt / $rein_premium) * 100;
                }
            } elseif ($request->type_of_bus == 'TNP') {
                $brokerage_comm_rate = $request->brokerage_comm_rate;
                $reinclass_code = $request->reinclass_code;
                $classcode = 'TRT';
                $insured_name = $customer->name;
                $reinclass = ReinsClass::wherein('class_code', $reinclass_code)->pluck('class_name')->toArray();
                $treaty_name = implode('-', $reinclass) . ' ' . $treatytype->treaty_name . ' TREATY';
                $date_offered = $request->date_offered;
                $share_offered = $request->share_offered;
            } elseif ($request->type_of_bus == 'TPR') {
                $brokerage_comm_rate = $request->brokerage_comm_rate;
                $treaty_reinclass = $request->treaty_reinclass;
                $classcode = 'TRT';
                $insured_name = $customer->name;
                $reinclass = ReinsClass::wherein('class_code', $treaty_reinclass)->pluck('class_name')->toArray();
                $treaty_name = implode('-', $reinclass) . ' ' . $treatytype->treaty_name . ' TREATY';
                $date_offered = $request->date_offered;
                $share_offered = $request->share_offered;
            }

            $coverfrom = $request->coverfrom;
            $coverto = $request->coverto;
            $rein_comm_amt = $request->reins_comm_amt ? str_replace(',', '', $request->reins_comm_amt) : 0;
            $pay_method_code = $request->pay_method;
            $currency_code = $request->currency_code;
            $branchcode = str_pad($branchcode, 3, '0', STR_PAD_LEFT);
            $retention_amt = (float)$request->retention_amt ? str_replace(',', '', $request->retention_amt) : 0;
            $no_of_lines = (float)$request->no_of_lines ? $request->no_of_lines : 0;
            $quota_share_total_limit = (float)$request->quota_share_total_limit ? str_replace(',', '', $request->quota_share_total_limit) : 0;
            $endorsement = $this->generateEndorseNo($type_of_bus, $request->trans_type);
            $endorsement_no = $endorsement->endorsement_no;
            $this->_endorsement_no = $endorsement_no;
            $cover_serial_no = $endorsement->serial_no;
            $no_of_installments = (int) $request->no_of_installments;
            $cede_premium = $request->cede_premium ? str_replace(',', '', $request->cede_premium) : 0;
            $cede_comm_amt = $request->comm_amt ? str_replace(',', '', $request->comm_amt) : 0;

            if ($request->premium_payment_term) {
                $ppw = PremiumPayTerm::where('pay_term_code', $request->premium_payment_term)->first();
            }
            if ($request->trans_type == 'NEW') {
                // $cover_no = $type_of_bus . $cover_serial_no . $currentYear;
                $cover_no = 'C' . $cover_serial_no;
                $orig_endorsement_no = $endorsement_no;

                $CoverRegister = new CoverRegister();
            } else {
                $cover_no = $request->cover_no;
                $old_endorsement_no = $request->endorsement_no;
                $prevCoverRegister = CoverRegister::where('endorsement_no', $old_endorsement_no)->first();
                $orig_endorsement_no = $prevCoverRegister->orig_endorsement_no;
                $CoverRegister = new CoverRegister($prevCoverRegister->getAttributes());
            }

            $CoverRegister->cover_serial_no = $cover_serial_no;
            $CoverRegister->customer_id = $customer_id;
            $CoverRegister->type_of_bus = $type_of_bus;
            $CoverRegister->cover_no = $cover_no;
            $CoverRegister->endorsement_no = $endorsement_no;
            $CoverRegister->orig_endorsement_no = $orig_endorsement_no;
            $CoverRegister->transaction_type = $request->trans_type;
            $CoverRegister->premium_payment_code = $request->premium_payment_term;
            if ($CoverRegister->premium_payment_code != null) {
                $CoverRegister->premium_payment_days = $ppw->premium_payment_days;
            } else {
                $CoverRegister->premium_payment_days = 0;
            }
            $CoverRegister->branch_code = $branchcode;
            $CoverRegister->broker_code = $brokercode ? $brokercode : 0;
            $CoverRegister->cover_type = $covertype;
            $CoverRegister->class_code = $classcode;
            $CoverRegister->class_group_code = $class_group;
            $CoverRegister->insured_name = $insured_name;
            $CoverRegister->effective_date = $coverfrom;
            $CoverRegister->cover_from = $coverfrom;
            $CoverRegister->cover_to = $coverto;
            $CoverRegister->account_year = $this->_year;
            $CoverRegister->account_month = $this->_month;
            $CoverRegister->binder_cov_no = $request->bindercoverno;
            $CoverRegister->created_by = Auth::user()->user_name;
            $CoverRegister->pay_method_code = $pay_method_code;
            $CoverRegister->currency_code = $currency_code;
            $CoverRegister->currency_rate = $request->today_currency;
            $CoverRegister->type_of_sum_insured = $request->sum_insured_type;
            $CoverRegister->rein_premium = $rein_premium;
            $CoverRegister->total_sum_insured = $request->total_sum_insured ? str_replace(',', '', $request->total_sum_insured) : 0;
            $CoverRegister->cedant_premium = $cede_premium;
            $CoverRegister->apply_eml = $request->apply_eml ?? 'N';
            $CoverRegister->eml_rate = $request->eml_rate ? $request->eml_rate : 0;
            $CoverRegister->eml_amount = $request->eml_amt ? str_replace(',', '', $request->eml_amt) : 0;
            $CoverRegister->effective_sum_insured = $request->effective_sum_insured ? str_replace(',', '', $request->effective_sum_insured) : 0;
            $CoverRegister->cedant_comm_rate = $request->comm_rate;
            $CoverRegister->cedant_comm_amount = $cede_comm_amt;
            $CoverRegister->rein_comm_type = $request->reins_comm_type;
            $CoverRegister->rein_comm_rate = $request->reins_comm_rate ? $request->reins_comm_rate : 0;
            $CoverRegister->brokerage_comm_rate = $brokerage_comm_rate ? $brokerage_comm_rate : 0;
            $CoverRegister->brokerage_comm_amt = $brokerage_comm_amt ? $brokerage_comm_amt : 0;
            $CoverRegister->brokerage_comm_type = $request->brokerage_comm_type;
            $CoverRegister->reinsurer_per_treaty = $request->reinsurer_per_treaty;
            $CoverRegister->rein_comm_amount = $rein_comm_amt;
            $CoverRegister->division_code = $request->division;
            $CoverRegister->vat_charged = $request->vat_charged;
            $CoverRegister->treaty_type = $request->treatytype;
            $CoverRegister->risk_details = $risk_details;
            $CoverRegister->cover_title = $treaty_name;
            $CoverRegister->date_offered = $date_offered;
            $CoverRegister->share_offered = (float)$share_offered ? $share_offered : 0;
            // $CoverRegister->quota_share_total_limit= $quota_share_total_limit ? $quota_share_total_limit : 0;
            // $CoverRegister->retention_per=(float)$request->retention_per ? $request->retention_per : 0;
            // $CoverRegister->retention_amt=$retention_amt ? $retention_amt : 0;
            // $CoverRegister->no_of_lines= $no_of_lines ? $no_of_lines : 0;
            // $CoverRegister->treaty_reice=(float)$request->treaty_reice ? $request->treaty_reice : 0;
            // $CoverRegister->treaty_limit=(float)$treaty_limit ? $treaty_limit : 0;
            // $CoverRegister->treaty_comm_rate=(float)$request->treaty_comm_rate ? $request->treaty_comm_rate : 0;
            $CoverRegister->no_of_installments = $no_of_installments;
            $CoverRegister->port_prem_rate = (float)$request->port_prem_rate ? $request->port_prem_rate : 0;
            $CoverRegister->port_loss_rate = (float)$request->port_loss_rate ? $request->port_loss_rate : 0;
            $CoverRegister->profit_comm_rate = (float)$request->profit_comm_rate ? $request->profit_comm_rate : 0;
            $CoverRegister->mgnt_exp_rate = (float)$request->mgnt_exp_rate ? $request->mgnt_exp_rate : 0;
            $CoverRegister->deficit_yrs = (float)$request->deficit_yrs ? $request->deficit_yrs : 0;
            // $CoverRegister->estimated_income=(float)$request->estimated_income ? str_replace(',','',$request->estimated_income) : 0;
            // $CoverRegister->cashloss_limit=(float)$request->cashloss_limit ? str_replace(',','',$request->cashloss_limit) : 0;
            // $CoverRegister->method=$request->method ? $request->method : 'A';
            $CoverRegister->deposit_frequency = $request->deposit_frequency ? $request->deposit_frequency : 0;
            $CoverRegister->prem_tax_rate = $request->prem_tax_rate ? $request->prem_tax_rate : 0;
            $CoverRegister->ri_tax_rate = $request->ri_tax_rate ? $request->ri_tax_rate : 0;
            $CoverRegister->status = 'A';
            $CoverRegister->verified = null;
            $CoverRegister->prospect_id = $request->prospect_id;
            $CoverRegister->created_at = now(); //Carbon::now();
            $CoverRegister->updated_at = now(); // Carbon::now();
            $CoverRegister->created_by = Auth::user()->user_name;
            $CoverRegister->updated_by = Auth::user()->user_name;
            $CoverRegister->save();

            if ($request->type_of_bus == 'TNP') {
                foreach ($reinclass_code as $index => $reinclass) {
                    // Create a new instance of YourModel
                    $CoverReinclass = new CoverReinclass();
                    $CoverReinclass->cover_no = $cover_no;
                    $CoverReinclass->endorsement_no = $endorsement_no;
                    $CoverReinclass->reinclass = $reinclass;
                    $CoverReinclass->created_by = Auth::user()->user_name;
                    $CoverReinclass->updated_by = Auth::user()->user_name;
                    $CoverReinclass->save();
                }
            }

            if ($request->type_of_bus == 'TPR') {
                $treaty_reinclass = $request->treaty_reinclass;
                // Loop through one of the arrays (assuming they all have the same length)
                foreach ($treaty_reinclass as $index => $treaty_class) {

                    $CoverReinclass = new CoverReinclass();
                    $CoverReinclass->cover_no = $cover_no;
                    $CoverReinclass->endorsement_no = $endorsement_no;
                    $CoverReinclass->reinclass = $treaty_class;
                    $CoverReinclass->created_by = Auth::user()->user_name;
                    $CoverReinclass->updated_by = Auth::user()->user_name;
                    $CoverReinclass->save();

                    $retention_per = isset($request->retention_per) && isset($request->retention_per[$index])  ? str_replace(',', '', $request->retention_per[$index]) : 0;
                    $treaty_reice = isset($request->treaty_reice[$index]) ? str_replace(',', '', $request->treaty_reice[$index]) : 0;
                    $surp_retention_amt = isset($request->surp_retention_amt[$index]) ? str_replace(',', '', $request->surp_retention_amt[$index]) : 0;
                    $no_of_lines = isset($request->no_of_lines[$index]) ? str_replace(',', '', $request->no_of_lines[$index]) : 0;
                    $surp_treaty_limit = isset($request->surp_treaty_limit[$index]) ? str_replace(',', '', $request->surp_treaty_limit[$index]) : 0;
                    $quota_retention_amt = isset($request->quota_retention_amt[$index]) ? str_replace(',', '', $request->quota_retention_amt[$index]) : 0;
                    $quota_share_total_limit = isset($request->quota_share_total_limit[$index]) ? str_replace(',', '', $request->quota_share_total_limit[$index]) : 0;
                    // $port_prem_rate = isset($request->port_prem_rate[$index]) ? str_replace(',','',$request->port_prem_rate[$index]) : 0;
                    // $port_loss_rate = isset($request->port_loss_rate[$index]) ? str_replace(',','',$request->port_loss_rate[$index]) : 0;
                    // $profit_comm_rate = isset($request->profit_comm_rate[$index]) ? str_replace(',','',$request->profit_comm_rate[$index]) : 0;
                    // $mgnt_exp_rate = isset($request->mgnt_exp_rate[$index]) ? str_replace(',','',$request->mgnt_exp_rate[$index]) : 0;
                    // $deficit_yrs = isset($request->deficit_yrs[$index]) ? str_replace(',','',$request->deficit_yrs[$index]) : 0;
                    $estimated_income = isset($request->estimated_income[$index]) ? str_replace(',', '', $request->estimated_income[$index]) : 0;
                    $cashloss_limit = isset($request->cashloss_limit[$index]) ? str_replace(',', '', $request->cashloss_limit[$index]) : 0;

                    if ($request->treatytype == 'SURP') {
                        $count = CoverReinProp::where('cover_no', $cover_no)
                            ->where('endorsement_no', $endorsement_no)
                            ->count();
                        $count = $count + 1;

                        $CoverReinProp = new CoverReinProp();
                        $CoverReinProp->cover_no = $cover_no;
                        $CoverReinProp->endorsement_no = $endorsement_no;
                        $CoverReinProp->reinclass = $treaty_class;
                        $CoverReinProp->item_no = $count;
                        $CoverReinProp->item_description = 'SURPLUS';
                        $CoverReinProp->retention_rate = $retention_per;
                        $CoverReinProp->treaty_rate = $treaty_reice;
                        $CoverReinProp->retention_amount = $surp_retention_amt;
                        $CoverReinProp->no_of_lines = $no_of_lines;
                        $CoverReinProp->treaty_amount = $surp_treaty_limit;
                        $CoverReinProp->treaty_limit = $surp_retention_amt + $surp_treaty_limit;
                        $CoverReinProp->port_prem_rate =  0;
                        $CoverReinProp->port_loss_rate = 0;
                        $CoverReinProp->profit_comm_rate = 0;
                        $CoverReinProp->mgnt_exp_rate = 0;
                        $CoverReinProp->deficit_yrs = 0;
                        $CoverReinProp->estimated_income = $estimated_income;
                        $CoverReinProp->cashloss_limit = $cashloss_limit;
                        $CoverReinProp->created_at = now(); //Carbon::now();
                        $CoverReinProp->updated_at = now(); //Carbon::now();
                        $CoverReinProp->created_by = Auth::user()->user_name;
                        $CoverReinProp->updated_by = Auth::user()->user_name;
                        $CoverReinProp->save();
                    } elseif ($request->treatytype == 'QUOT') {
                        $count = CoverReinProp::where('cover_no', $cover_no)
                            ->where('endorsement_no', $endorsement_no)
                            ->count();
                        $count = $count + 1;

                        $CoverReinProp = new CoverReinProp();
                        $CoverReinProp->cover_no = $cover_no;
                        $CoverReinProp->endorsement_no = $endorsement_no;
                        $CoverReinProp->reinclass = $treaty_class;
                        $CoverReinProp->item_no = $count;
                        $CoverReinProp->item_description = 'QUOTA';
                        $CoverReinProp->retention_rate = $retention_per;
                        $CoverReinProp->treaty_rate = $treaty_reice;
                        $CoverReinProp->retention_amount = $quota_retention_amt;
                        $CoverReinProp->no_of_lines = $no_of_lines;
                        $CoverReinProp->treaty_amount = $quota_share_total_limit;
                        $CoverReinProp->treaty_limit = $quota_retention_amt + $quota_share_total_limit;
                        $CoverReinProp->port_prem_rate =  0;
                        $CoverReinProp->port_loss_rate = 0;
                        $CoverReinProp->profit_comm_rate = 0;
                        $CoverReinProp->mgnt_exp_rate = 0;
                        $CoverReinProp->deficit_yrs = 0;
                        $CoverReinProp->estimated_income = $estimated_income;
                        $CoverReinProp->cashloss_limit = $cashloss_limit;
                        $CoverReinProp->created_at = now(); // Carbon::now();
                        $CoverReinProp->updated_at =  now(); //Carbon::now();
                        $CoverReinProp->created_by = Auth::user()->user_name;
                        $CoverReinProp->updated_by = Auth::user()->user_name;
                        $CoverReinProp->save();
                    } elseif ($request->treatytype == 'SPQT') {
                        if ($request->quota_share_total_limit[$index] > 0) {
                            $count = CoverReinProp::where('cover_no', $cover_no)
                                ->where('endorsement_no', $endorsement_no)
                                ->count();
                            $count = $count + 1;

                            $CoverReinProp = new CoverReinProp();
                            $CoverReinProp->cover_no = $cover_no;
                            $CoverReinProp->endorsement_no = $endorsement_no;
                            $CoverReinProp->reinclass = $treaty_class;
                            $CoverReinProp->item_no = $count;
                            $CoverReinProp->item_description = 'QUOTA';
                            $CoverReinProp->retention_rate = $retention_per;
                            $CoverReinProp->treaty_rate = $treaty_reice;
                            $CoverReinProp->retention_amount = $quota_retention_amt;
                            $CoverReinProp->no_of_lines = $no_of_lines;;
                            $CoverReinProp->treaty_amount = $quota_share_total_limit;
                            $CoverReinProp->treaty_limit = $quota_retention_amt + $quota_share_total_limit;
                            $CoverReinProp->port_prem_rate =  0;
                            $CoverReinProp->port_loss_rate = 0;
                            $CoverReinProp->profit_comm_rate = 0;
                            $CoverReinProp->mgnt_exp_rate = 0;
                            $CoverReinProp->deficit_yrs = 0;
                            $CoverReinProp->estimated_income = $estimated_income;
                            $CoverReinProp->cashloss_limit = $cashloss_limit;
                            $CoverReinProp->created_at =  now(); //Carbon::now();
                            $CoverReinProp->updated_at =  now(); //Carbon::now();
                            $CoverReinProp->created_by = Auth::user()->user_name;
                            $CoverReinProp->updated_by = Auth::user()->user_name;
                            $CoverReinProp->save();
                        }
                        if ($request->surp_treaty_limit[$index] > 0) {
                            $count = CoverReinProp::where('cover_no', $cover_no)
                                ->where('endorsement_no', $endorsement_no)
                                ->count();
                            $count = $count + 1;

                            $CoverReinProp = new CoverReinProp();
                            $CoverReinProp->cover_no = $cover_no;
                            $CoverReinProp->endorsement_no = $endorsement_no;
                            $CoverReinProp->reinclass = $treaty_class;
                            $CoverReinProp->item_no = $count;
                            $CoverReinProp->item_description = 'SURPLUS';
                            $CoverReinProp->retention_rate = $retention_per;
                            $CoverReinProp->treaty_rate = $treaty_reice;
                            $CoverReinProp->retention_amount = $surp_retention_amt;
                            $CoverReinProp->no_of_lines = $no_of_lines;
                            $CoverReinProp->treaty_amount = $surp_treaty_limit;
                            $CoverReinProp->treaty_limit = $surp_retention_amt + $surp_treaty_limit;
                            $CoverReinProp->port_prem_rate = 0;
                            $CoverReinProp->port_loss_rate = 0;
                            $CoverReinProp->profit_comm_rate = 0;
                            $CoverReinProp->mgnt_exp_rate = 0;
                            $CoverReinProp->deficit_yrs = 0;
                            $CoverReinProp->estimated_income = $estimated_income;
                            $CoverReinProp->cashloss_limit = $cashloss_limit;
                            $CoverReinProp->created_at =  now(); //Carbon::now();
                            $CoverReinProp->updated_at =  now(); //Carbon::now();
                            $CoverReinProp->created_by = Auth::user()->user_name;
                            $CoverReinProp->updated_by = Auth::user()->user_name;
                            $CoverReinProp->save();
                        }
                    }
                }

                //Code to insert premtype commisssion in a loop
                $prem_type_reinclass = $request->prem_type_reinclass;
                $prem_type_treaty = $request->prem_type_treaty;
                $prem_type_code = $request->prem_type_code;
                $prem_type_comm_rate = $request->prem_type_comm_rate;
                // Loop through one of the arrays (assuming they all have the same length)
                foreach ($prem_type_reinclass as $index => $reinclass) {
                    $premtype_reinclass = ReinclassPremtype::where('reinclass', $reinclass)
                        ->where('premtype_code', $prem_type_code[$index])
                        ->first();
                    // Create a new instance of YourModel
                    $CoverPremtype = new CoverPremtype();
                    $CoverPremtype->cover_no = $cover_no;
                    $CoverPremtype->endorsement_no = $endorsement_no;
                    $CoverPremtype->reinclass = $reinclass;
                    $CoverPremtype->treaty = $prem_type_treaty[$index];
                    $CoverPremtype->premtype_code = $prem_type_code[$index];
                    $CoverPremtype->premtype_name = $premtype_reinclass->premtype_name;
                    $CoverPremtype->comm_rate = $prem_type_comm_rate[$index];
                    $CoverPremtype->save();
                }
            } elseif ($request->type_of_bus == 'TNP') {
                $indemnity_limits = $request->indemnity_treaty_limit;
                $underlying_limit = $request->underlying_limit;
                $egnpi = $request->egnpi;
                $method = $request->method;
                $payment_frequency = $request->deposit_frequency ? $request->deposit_frequency : 0;
                $min_bc_rate = $request->min_bc_rate;
                $max_bc_rate = $request->max_bc_rate;
                $flat_rate = $request->flat_rate;
                $upper_adj = $request->upper_adj;
                $lower_adj = $request->lower_adj;
                $min_deposit = $request->min_deposit;
                $nonprop_reinclass = $request->nonprop_reinclass;
                $layer_no = $request->layer_no;
                $reinstatement_type = $request->reinstatement_type;
                $reinstatement_value = $request->reinstatement_value;
                $item_no = 1;
                // Loop through one of the arrays (assuming they all have the same length)
                foreach ($indemnity_limits as $index => $indemnity_limit) {

                    if ($index > 0 && $layer_no[$index - 1] == $layer_no[$index]) {
                        $item_no = $item_no + 1;
                    } else {
                        $item_no = 1;
                    }

                    $CoverReinLayer = new CoverReinLayer();
                    $CoverReinLayer->cover_no = $cover_no;
                    $CoverReinLayer->endorsement_no = $endorsement_no;
                    $CoverReinLayer->layer_no = $layer_no[$index];
                    $CoverReinLayer->indemnity_limit = (float)str_replace(',', '', $indemnity_limit) ?? 0;
                    $CoverReinLayer->underlying_limit = (float)str_replace(',', '', $underlying_limit[$index]) ?? 0;
                    $CoverReinLayer->egnpi = (float)str_replace(',', '', $egnpi[$index]);
                    $CoverReinLayer->method = $method;
                    $CoverReinLayer->payment_frequency = $payment_frequency;
                    $CoverReinLayer->reinclass = $nonprop_reinclass[$index];
                    $CoverReinLayer->reinstatement_type = $reinstatement_type[$index];
                    $CoverReinLayer->reinstatement_value = (float)str_replace(',', '', $reinstatement_value[$index]) ?? 0;
                    $CoverReinLayer->item_no = $item_no;

                    if ($method == 'F') {
                        $CoverReinLayer->flat_rate = (float)str_replace(',', '', $flat_rate[$index]) ?? 0;
                        $CoverReinLayer->min_bc_rate = 0;
                        $CoverReinLayer->max_bc_rate = (float)str_replace(',', '', $flat_rate[$index]) ?? 0;
                        $CoverReinLayer->upper_adj =   (float)str_replace(',', '', $flat_rate[$index]) ?? 0;
                        $CoverReinLayer->lower_adj =   0;
                    } else {
                        $CoverReinLayer->flat_rate = 0;
                        $CoverReinLayer->min_bc_rate = (float)str_replace(',', '', $min_bc_rate[$index]) ?? 0;
                        $CoverReinLayer->max_bc_rate = (float)str_replace(',', '', $max_bc_rate[$index]) ?? 0;
                        $CoverReinLayer->upper_adj = (float)str_replace(',', '', $upper_adj[$index]) ?? 0;
                        $CoverReinLayer->lower_adj = (float)str_replace(',', '', $lower_adj[$index]) ?? 0;
                    }

                    $CoverReinLayer->min_deposit = (float)str_replace(',', '', $min_deposit[$index]) ?? 0;
                    $CoverReinLayer->save();
                }
            } elseif ($request->type_of_bus == 'FPR' || $request->type_of_bus == 'FNP') {

                //Gross Premium
                $CoverPremium = new CoverPremium();
                $CoverPremium->cover_no = $cover_no;
                $CoverPremium->endorsement_no = $endorsement_no;
                $CoverPremium->orig_endorsement_no = $CoverRegister->orig_endorsement_no;
                $CoverPremium->transaction_type =  $CoverRegister->transaction_type;
                $CoverPremium->premium_type_code =  0;
                $CoverPremium->premtype_name =  'Gross Premium';
                $CoverPremium->quarter =  $this->_quarter;
                $CoverPremium->entry_type_descr =  'PRM';
                $CoverPremium->premium_type_order_position = 1;
                $CoverPremium->premium_type_description = 'Gross Premium';
                $CoverPremium->type_of_bus =  $type_of_bus;
                $CoverPremium->class_code =  $classcode;
                $CoverPremium->basic_amount =  $cede_premium;
                $CoverPremium->apply_rate_flag =  'Y';
                $CoverPremium->treaty = 'FAC';
                $CoverPremium->rate =  $share_offered;
                if ($CoverRegister->transaction_type == 'RFN' || $CoverRegister->transaction_type == 'CNC') {
                    $CoverPremium->dr_cr = 'CR';
                } else {
                    $CoverPremium->dr_cr = 'DR';
                }
                $CoverPremium->final_amount =  ($share_offered / 100) * $cede_premium;
                $CoverPremium->created_at =  now(); //Carbon::now() ;
                $CoverPremium->updated_at =  now(); //Carbon::now() ;
                $CoverPremium->created_by = Auth::user()->user_name;
                $CoverPremium->updated_by = Auth::user()->user_name;
                $CoverPremium->save();

                $rate = $request->comm_rate;
                $cede_premium = ($share_offered / 100) * $cede_premium;
                $cede_comm_amt = ($rate / 100) * $cede_premium;
                // $cede_comm_amt = ($share_offered / 100) * $cede_comm_amt;

                // $rein_comm_amt = ($share_offered / 100) * $rein_comm_amt;
                // $cede_premium = $cede_premium;
                // $cede_comm_amt = $cede_comm_amt;
                // $rein_comm_amt = $rein_comm_amt;
                // $broker_comm_amt = $rein_comm_amt - $cede_comm_amt;

                //Commissions
                $CoverPremium = new CoverPremium();
                $CoverPremium->cover_no = $cover_no;
                $CoverPremium->endorsement_no = $endorsement_no;
                $CoverPremium->orig_endorsement_no = $CoverRegister->orig_endorsement_no;
                $CoverPremium->transaction_type = $CoverRegister->transaction_type;
                $CoverPremium->premium_type_code = 0;
                $CoverPremium->premtype_name = 'Commission';
                $CoverPremium->quarter = $this->_quarter;
                $CoverPremium->entry_type_descr = 'COM';
                $CoverPremium->premium_type_order_position = 2;
                $CoverPremium->premium_type_description = 'Commission';
                $CoverPremium->type_of_bus = $type_of_bus;
                $CoverPremium->class_code = $classcode;
                $CoverPremium->treaty = 'FAC';
                $CoverPremium->basic_amount = $cede_premium;
                $CoverPremium->apply_rate_flag = 'Y';
                $CoverPremium->rate = $rate;
                if ($CoverRegister->transaction_type == 'RFN' || $CoverRegister->transaction_type == 'CNC') {
                    $CoverPremium->dr_cr = 'DR';
                } else {
                    $CoverPremium->dr_cr = 'CR';
                }
                $CoverPremium->final_amount = $cede_comm_amt;
                $CoverPremium->created_at = now(); //Carbon::now() ;
                $CoverPremium->updated_at = now(); //Carbon::now() ;
                $CoverPremium->created_by = Auth::user()->user_name;
                $CoverPremium->updated_by = Auth::user()->user_name;
                $CoverPremium->save();
            }

            if (in_array($request->type_of_bus, ['TPR', 'TNP'])) {
                $slipTemplate = SlipTemplate::where('treaty_type', $request->type_of_bus)->first();

                // $templateWording = null;
                if (isset($slipTemplate)) {
                    $treatySlip = new CoverSlipWording();
                    $treatySlip->cover_no = $cover_no;
                    $treatySlip->endorsement_no = $endorsement_no;
                    $treatySlip->wording = $slipTemplate->wording;
                    $treatySlip->created_by = Auth::user()->user_name;
                    $treatySlip->updated_by = Auth::user()->user_name;
                    $treatySlip->save();
                }
            }

            if ((int) $no_of_installments > 0) {
                $totalDr = CoverPremium::where('endorsement_no', $CoverRegister->endorsement_no)
                    ->where('dr_cr', 'DR')
                    ->sum('final_amount');
                $totalCr = CoverPremium::where('endorsement_no', $CoverRegister->endorsement_no)
                    ->where('dr_cr', 'CR')
                    ->sum('final_amount');
                $installmentAmount = $totalDr - $totalCr;

                $data = [
                    'cover_no'          =>  $CoverRegister->cover_no,
                    'endorsement_no'    =>  $CoverRegister->endorsement_no,
                    'layer_no'          =>  0,
                    'trans_type'        => $CoverRegister->type_of_bus,
                    'entry_type'        => $CoverRegister->transaction_type,
                    'installment_no'    => 1,
                    'installment_date'  => $CoverRegister->cover_from->addDays((int)$CoverRegister->premium_payment_days),
                    'installment_amt'   => $installmentAmount,
                    'created_by'        => Auth::user()->user_name,
                    'updated_by'        => Auth::user()->user_name,
                ];

                if ((int) $no_of_installments === 1) {
                    CoverInstallments::create(array_merge($data, ['dr_cr' => 'DR']));
                } else {
                    for ($i = 0; $i < $no_of_installments; $i++) {
                        CoverInstallments::create([
                            'cover_no'          =>  $CoverRegister->cover_no,
                            'endorsement_no'    =>  $CoverRegister->endorsement_no,
                            'layer_no'          =>  0,
                            'trans_type'        => $CoverRegister->type_of_bus,
                            'entry_type'        => $CoverRegister->transaction_type,
                            'installment_no'    => $request->installment_no[$i],
                            'installment_date'  => $request->installment_date[$i],
                            'installment_amt'   => str_replace(",", "", $request->installment_amt[$i]),
                            'dr_cr'             => 'DR',
                            'created_by'        => Auth::user()->user_name,
                            'updated_by'        => Auth::user()->user_name,
                        ]);
                    }
                }
            }

            switch ($CoverRegister->transaction_type) {
                case 'NEW':
                    break;
                default:
                    $this->replicateFromPrevious($old_endorsement_no);
                    break;
            }

            return (object)[
                'endorsement_no' => $endorsement_no,
                'customer_id' => $customer?->customer_id,
                'prospect_id' => $CoverRegister?->prospect_id,
            ];
        } catch (\Exception $e) {
            throw $e;
        }
    }

    public function replicateFromPrevious($prev_endorsement_no, $request = null)
    {
        $cover = CoverRegister::where('endorsement_no', $this->_endorsement_no)->first();
        $base_cover = CoverRegister::where('endorsement_no', $cover->orig_endorsement_no)->first();
        $riclasses = CoverReinclass::where('endorsement_no', $prev_endorsement_no)->get();
        $isChangeDueDate = $request?->endorse_type === 'change-due-date' && isset($request->new_premium_due_date);

        foreach ($riclasses as $ricls) {
            $data = $ricls->getAttributes();
            $data['endorsement_no'] = $this->_endorsement_no;
            $data['created_by'] = Auth::user()->user_name;
            $data['updated_by'] = Auth::user()->user_name;
            $data['created_at'] = Carbon::now();
            $data['updated_at'] = Carbon::now();

            CoverReinclass::create($data);
        }

        $uwclasses = CoverClass::where('endorsement_no', $prev_endorsement_no)->get();

        foreach ($uwclasses as $uwcls) {
            $data = $uwcls->getAttributes();
            $data['endorsement_no'] = $this->_endorsement_no;
            $data['created_by'] = Auth::user()->user_name;
            $data['updated_by'] = Auth::user()->user_name;
            $data['created_at'] = Carbon::now();
            $data['updated_at'] = Carbon::now();

            CoverClass::create($data);
        }

        $risks = CoverRisk::where('endorsement_no', $prev_endorsement_no)->get();

        foreach ($risks as $risk) {
            $data = $risk->getAttributes();
            $data['endorsement_no'] = $this->_endorsement_no;
            $data['created_by'] = Auth::user()->user_name;
            $data['updated_by'] = Auth::user()->user_name;
            $data['created_at'] = Carbon::now();
            $data['updated_at'] = Carbon::now();

            CoverRisk::create($data);
        }

        $attachments = CoverAttachment::where('endorsement_no', $prev_endorsement_no)->get();

        foreach ($attachments as $attachment) {
            $data = $attachment->getAttributes();
            $id = (int) CoverAttachment::withTrashed()->max('id') + 1;
            $data['id'] = $id;
            $data['endorsement_no'] = $this->_endorsement_no;
            $data['created_by'] = Auth::user()->user_name;
            $data['updated_by'] = Auth::user()->user_name;
            $data['created_at'] = Carbon::now();
            $data['updated_at'] = Carbon::now();

            CoverAttachment::create($data);
        }

        $reinsurers = CoverRipart::where('endorsement_no', $base_cover->endorsement_no)->get();

        $currTotalRIShare = 0;
        foreach ($reinsurers as $ripart) {
            $tran_no = (int) CoverRipart::withTrashed()->max('tran_no') + 1;
            $data = $ripart->getAttributes();

            $remShare = (float) $cover->share_offered - $currTotalRIShare;
            $data['claim_amt'] = 0;
            $data['total_sum_insured'] = 0;
            $data['total_premium'] = 0;
            $data['total_commission'] = 0;
            $data['sum_insured'] = 0;
            $data['premium'] = 0;
            $data['fronting_rate'] = 0;
            $data['fronting_amt'] = 0;
            $data['commission'] = 0;
            $data['wht_rate'] = 0;
            $data['wht_amt'] = 0;
            $data['brokerage_comm_rate'] = 0;
            $data['brokerage_comm_amt'] = 0;
            $data['claim_amt'] = 0;
            $data['prem_tax_rate']  = 0;
            $data['prem_tax']  = 0;
            $data['ri_tax_rate']  = 0;
            $data['ri_tax']  = 0;
            $total_claim_amt = 0;

            if ($cover->type_of_bus == 'FPR' || $cover->type_of_bus == 'FNP') {
                $data['total_sum_insured'] = $cover->total_sum_insured;
                $data['total_premium']     = $cover->rein_premium;
                $data['total_commission']  = $cover->rein_comm_amount;

                if ($remShare > 0) {
                    $data['sum_insured'] = max(0, ceil($data['total_sum_insured'] * $ripart->share / 100));

                    if ((int) $cover?->cedant_premium === 0) {
                        $data['premium'] = 0;
                        $data['fronting_rate'] = 0;
                        $data['commission'] = 0;
                        $data['wht_rate'] = 0;
                        $data['wht_amt'] = 0;
                        $data['claim_amt'] = 0;
                        $data['brokerage_comm_rate'] = 0;
                        $data['brokerage_comm_amt'] = 0;
                    } else {
                        $data['premium'] = max(0, ceil($data['total_premium'] * $ripart->share / 100));
                        $data['fronting_rate'] = $ripart->fronting_rate;
                        $data['commission'] = max(0, ceil($data['total_commission'] * $ripart->share / 100));
                        $data['wht_rate'] = $ripart->wht_rate;
                        $data['wht_amt'] = max(0, ceil($data['premium'] * $ripart->wht_rate / 100));
                        $data['claim_amt'] = 0;

                        $data['fronting_amt'] = 0;
                        $data['wht_amt'] = 0;

                        // Withholding tax
                        if (round($data['wht_rate'], 5) > 0) {
                            $data['wht_amt'] = max(0, ceil(($data['wht_rate'] / 100) * ($data['premium'] - $ripart->commission)));
                        }

                        // Fronting fees
                        if (round($data['fronting_rate'], 5) > 0) {
                            $data['fronting_amt']  = max(0, ceil(($data['fronting_rate'] / 100) * ($data['premium'] - $ripart->commission)));
                        }

                        // Brokerage commission
                        if ($cover->brokerage_comm_type == 'R') {
                            $total_brokerage_comm = ($cover->brokerage_comm_rate * $cover->rein_premium) / 100;
                            $brokerage_comm_amt = ($cover->brokerage_comm_rate * $data['premium']) / 100;
                        } else {
                            $total_brokerage_comm = $cover->brokerage_comm_amt;
                            $brokerage_comm_amt = $total_brokerage_comm * $ripart->wht_rate / 100;
                        }

                        $data['brokerage_comm_rate'] = (float) ($cover->brokerage_comm_rate ?? 0);
                        $data['brokerage_comm_amt'] = max(0, ceil((float) ($brokerage_comm_amt ?? 0)));
                    }
                }
            } elseif ($cover->type_of_bus == 'TPR' || $cover->type_of_bus == 'TNP') {
                $coverPremiums = CoverPremium::where('endorsement_no', $this->_endorsement_no)->get();
                $data['total_premium'] = $coverPremiums->where('entry_type_descr', 'PRM')->sum('final_amount') ?? 0;
                $data['total_commission'] = $coverPremiums->where('entry_type_descr', 'COM')->sum('final_amount') ?? 0;
                $data['total_sum_insured'] = $coverPremiums->where('entry_type_descr', 'SUM')->sum('final_amount') ?? 0;
                $premium_tax_rate = $cover->prem_tax_rate ?? 0;
                $total_premium_tax = $coverPremiums->where('entry_type_descr', 'PTX')->sum('final_amount') ?? 0;
                $reinsurance_tax_rate = $cover->ri_tax_rate ?? 0;
                $total_reinsurance_tax = $coverPremiums->where('entry_type_descr', 'RTX')->sum('final_amount') ?? 0;
                $total_claim_amt = $coverPremiums->where('entry_type_descr', 'CLM')->sum('final_amount') ?? 0;
                $brokerage_comm_rate = $coverPremiums->where('entry_type_descr', 'BRC')->select('rate')[0] ?? 0;
                $brokerage_comm_amt = $coverPremiums->where('entry_type_descr', 'BRC')->sum('final_amount') ?? 0;

                $data['sum_insured'] = max(0, ceil($data['total_sum_insured'] * $ripart->share / 100));
                $data['premium'] = max(0, ceil($data['total_premium'] * $ripart->share / 100));
                $data['fronting_rate'] = $ripart->fronting_rate;
                $data['commission'] = max(0, ceil($data['total_commission'] * $ripart->share / 100));

                $data['fronting_amt'] = max(0, ceil((($data['premium'] ?? 0) - ($data['commission'] ?? 0)) * $ripart->fronting_rate / 100));
                $data['wht_rate'] = $ripart->wht_rate;
                $data['wht_amt'] = max(0, ceil((($data['premium'] ?? 0) - ($data['commission'] ?? 0)) * $ripart->wht_rate / 100));

                $data['brokerage_comm_rate'] = $brokerage_comm_rate;
                $data['brokerage_comm_amt'] = max(0, ceil((float) ($brokerage_comm_amt ?? 0)));

                $data['claim_amt'] = max(0, ceil((float) ($total_claim_amt * $ripart->share / 100)));

                $data['prem_tax_rate'] = (float) ($premium_tax_rate ?? 0);
                $data['prem_tax'] = max(0, ceil((float) ($data['premium'] * $data['prem_tax_rate'] / 100)));

                $data['ri_tax_rate'] = $reinsurance_tax_rate ?? 0;
                $data['ri_tax'] = max(0, ceil((float) ($data['premium'] * $data['ri_tax_rate'] / 100)));
            }

            $data['tran_no'] = $tran_no;
            $data['endorsement_no'] = $this->_endorsement_no;
            $data['period_year'] = $this->_year;
            $data['period_month'] = $this->_month;
            $data['created_by'] = Auth::user()->user_name;
            $data['updated_by'] = Auth::user()->user_name;
            $data['created_at'] = Carbon::now();
            $data['updated_at'] = Carbon::now();

            CoverRipart::create($data);

            // Save installments
            $this->generateCoverInstallment($prev_endorsement_no, $ripart, $data, $cover, $isChangeDueDate, $request);
            ReinNote::where('endorsement_no', $this->_endorsement_no)
                ->where('partner_no', $ripart->partner_no)
                ->forceDelete();

            $premItemTypes = [
                'PRM' => [
                    'descr' => 'Gross Premium',
                    'dr_cr' => 'CR',
                    'tax_rate' => $ripart->share,
                    'amount' => $data['premium'],
                    'total_amount' => $data['total_premium'] ?? 0,
                ],
                'BRC' => [
                    'descr' => 'Brokerage Commission',
                    'dr_cr' => 'DR',
                    'tax_rate' => $data['brokerage_comm_rate'],
                    'amount' => $data['brokerage_comm_amt'],
                    'total_amount' => $data['brokerage_comm_amt'],
                ],
                'COM' => [
                    'descr' => 'Commission',
                    'dr_cr' => 'DR',
                    'tax_rate' => $ripart->comm_rate,
                    'amount' => $data['commission'],
                    'total_amount' => $data['total_commission'],
                ],
                'WHT' => [
                    'descr' => 'With holding Tax',
                    'dr_cr' => 'DR',
                    'tax_rate' => $data['wht_rate'],
                    'amount' => $data['wht_amt'],
                    'total_amount' => ($data['premium'] ?? 0) - ($data['commission'] ?? 0),
                ],
                'FRF' => [
                    'descr' => 'Fronting Fees',
                    'dr_cr' => 'DR',
                    'tax_rate' => $data['fronting_rate'],
                    'amount' => $data['fronting_amt'],
                    'total_amount' => ($data['premium'] ?? 0) - ($data['commission'] ?? 0),
                ],
                // 'PTX' => [
                //     'descr' => 'Premium Tax',
                //     'dr_cr' => 'DR',
                //     'tax_rate' => $data['prem_tax_rate'],
                //     'amount' => $data['prem_tax'],
                //     'total_amount' => $data['premium'],
                // ],
                // 'RTX' => [
                //     'descr' => 'Reinsurance Tax',
                //     'dr_cr' => 'DR',
                //     'tax_rate' => $data['ri_tax_rate'],
                //     'amount' => $data['ri_tax'],
                //     'total_amount' => $data['premium'],
                // ],
                // 'CLM' => [
                //     'descr' => 'Claim',
                //     'dr_cr' => 'CR',
                //     'tax_rate' => $ripart->share,
                //     'amount' => $data['claim_amt'],
                //     'total_amount' => $total_claim_amt,
                // ],
            ];

            foreach ($premItemTypes as $key => $premItemType) {
                if ($premItemType['amount'] > 0) {
                    $tran_no = DB::transaction(function () {
                        $max_tran_no = DB::table('rein_notes')
                            ->whereNull('deleted_at')
                            ->where('endorsement_no', $this->_endorsement_no)
                            ->max('tran_no');
                        return ($max_tran_no ?? 0) + 1;
                    });

                    $ln_no = DB::transaction(function () use ($cover, $key) {
                        $count = DB::table('rein_notes')
                            ->whereNull('deleted_at')
                            ->where('endorsement_no', $this->_endorsement_no)
                            ->where('transaction_type', $cover->transaction_type)
                            ->where('entry_type_descr', $key)
                            ->count();
                        return $count + 1;
                    });

                    $share = (float) $ripart->share ?? 0;
                    $username = auth()->user()->user_name;
                    $net_amnt = $premItemType['amount'];

                    $data = [
                        'cover_no'          => $cover->cover_no,
                        'endorsement_no'    => $cover->endorsement_no,
                        'partner_no'        => $ripart->partner_no,
                        'transaction_type'  => $cover->transaction_type,
                        'account_year'      => $this->_year,
                        'account_month'     => $this->_month,
                        'share'             => $share,
                        'created_by'        => $username,
                        'updated_by'        => $username,
                        'tran_no'           => $tran_no,
                        'ln_no'             => $ln_no,
                        'entry_type_descr'  => $key,
                        'item_title'        => $premItemType['descr'],
                        'dr_cr'             => $premItemType['dr_cr'],
                        'rate'              => $premItemType['tax_rate'] ?? 0,
                        'total_gross'       => $premItemType['total_amount'],
                        'gross'             => $premItemType['amount'],
                        'net_amt'           => $net_amnt,
                    ];

                    ReinNote::create($data);
                }
            }
            $currTotalRIShare += (float)$ripart->share;
        }

        $premtypes = CoverPremtype::where('endorsement_no', $prev_endorsement_no)->get();

        foreach ($premtypes as $premtype) {
            $data = $premtype->getAttributes();
            $data['endorsement_no'] = $this->_endorsement_no;

            CoverPremtype::create($data);
        }

        $treatypropsects = CoverReinProp::where('endorsement_no', $prev_endorsement_no)->get();

        foreach ($treatypropsects as $treatypropsect) {
            $data = $treatypropsect->getAttributes();
            $data['endorsement_no'] = $this->_endorsement_no;

            CoverReinProp::create($data);
        }

        $treatynonproplayers = CoverReinLayer::where('endorsement_no', $prev_endorsement_no)->get();

        foreach ($treatynonproplayers as $treatynonproplayer) {
            $data = $treatynonproplayer->getAttributes();
            $data['endorsement_no'] = $this->_endorsement_no;

            CoverReinLayer::create($data);
        }

        $coverInstalments = CoverInstallments::where('endorsement_no', $prev_endorsement_no)->where('dr_cr', 'DR')->get();
        foreach ($coverInstalments as $instalment) {
            $attributes = $instalment->getAttributes();
            unset($attributes['id']);

            $installment_date = $attributes['installment_date'];
            $installment_amt = $attributes['installment_amt'];
            $entry_type = $cover?->transaction_type;

            if ($isChangeDueDate) {
                $installment_date = $request->new_premium_due_date;
            }

            if (round($cover?->cedant_premium) <= 0) {
                $installment_amt = 0;
            } else {
                $totalDr = CoverPremium::where('endorsement_no', $cover?->endorsement_no)
                    ->where('dr_cr', 'DR')
                    ->sum('final_amount');
                $totalCr = CoverPremium::where('endorsement_no', $cover?->endorsement_no)
                    ->where('dr_cr', 'CR')
                    ->sum('final_amount');
                $installment_amt = (float) $totalDr - $totalCr;
            }

            $newInstalment = [
                ...$attributes,
                'endorsement_no' => $this->_endorsement_no,
                'installment_date' => $installment_date,
                'installment_amt' => $installment_amt,
                'entry_type' => $entry_type,
            ];

            CoverInstallments::create($newInstalment);
        }

        $coverClauses = CoverClause::where('endorsement_no', $prev_endorsement_no)->get();
        foreach ($coverClauses as $coverClause) {
            $data = $coverClause->getAttributes();
            $data['endorsement_no'] = $this->_endorsement_no;

            CoverClause::create($data);
        }
    }

    private function generateCoverInstallment($prev_endorsement_no, $ripart, $data, $cover, $isChangeDueDate, $request)
    {
        $coverInstalment = CoverInstallments::where([
            'endorsement_no' => $prev_endorsement_no,
            'partner_no' => $ripart->partner_no,
            'dr_cr' => 'CR'
        ])->first();

        if (!$coverInstalment) {
            return;
        }

        $attributes = $coverInstalment->getAttributes();
        unset($attributes['id']);

        $installment_date = $attributes['installment_date'];
        $installment_amt = $attributes['installment_amt'];

        $entry_type = $cover?->transaction_type;
        $wht_amt = 0;
        $fronting_amt = 0;
        $total_deducted = 0;
        $total_add = 0;

        // Withholding tax
        if (round($data['wht_rate'], 5) > 0) {
            $wht_amt = max(0, ceil(($data['wht_rate'] / 100) * ($data['premium'] - $ripart->commission)));
            $total_deducted += $wht_amt;
        }
        // Fronting fees
        if (round($data['fronting_rate'], 5) > 0) {
            $fronting_amt = max(0, ceil(($data['fronting_rate'] / 100) * ($data['premium'] - $ripart->commission)));
            $total_deducted += $fronting_amt;
        }

        $totalDr = (float) $data['premium'] - $total_deducted;
        $totalCr = (float) (($ripart->comm_rate / 100) * $data['premium']);
        $installmentAmount = max(0, ceil(($totalDr - $totalCr) + $total_add));

        if ($isChangeDueDate) {
            $installment_date = $request->new_premium_due_date;
        }

        if (round($cover?->cedant_premium) <= 0) {
            $installment_amt = 0;
        } else {
            $installment_amt = $installmentAmount;
        }

        $newInstalment = array_merge($attributes, [
            'endorsement_no' => $this->_endorsement_no,
            'installment_date' => $installment_date,
            'installment_amt' => $installment_amt,
            'entry_type' => $entry_type,
        ]);

        return CoverInstallments::create($newInstalment);
    }


    public function editCoverRegister($request)
    {
        $covertype = $request->covertype;
        $branchcode = (int)$request->branchcode;
        $brokercode = $request->brokercode;
        $class_group = $request->class_group;
        $risk_details = $request->risk_details;
        // $risk_details = Purifier::clean($request->risk_details);

        $treatytype = TreatyType::where('treaty_code', $request->treatytype)->first();

        if ($request->type_of_bus == 'FPR' || $request->type_of_bus == 'FNP') {
            $classcode = $request->classcode;
            $class_name = Classes::select('class_name')->where('class_code', $classcode)->first();
            $treaty_name = $class_name->class_name . ' FACULTATIVE';
            $date_offered = $request->fac_date_offered;
            $share_offered = $request->fac_share_offered;
            $rein_premium = $request->rein_premium ? str_replace(',', '', $request->rein_premium) : 0;
            if ($request->brokerage_comm_type == 'R') {
                $brokerage_comm_rate = $request->brokerage_comm_rate ? str_replace(',', '', $request->brokerage_comm_rate) : 0;
                $brokerage_comm_amt = ($brokerage_comm_rate / 100) * $rein_premium;
            } else {
                $brokerage_comm_amt = $request->brokerage_comm_amt ? str_replace(',', '', $request->brokerage_comm_amt) : 0;
                $brokerage_comm_rate = ($brokerage_comm_amt / $rein_premium) * 100;
            }

            // if($request->reins_comm_type =='R'){
            //     $brokerage_comm_rate = $request->reins_comm_rate - $request->comm_rate;
            // }else{
            //     $brokerage_comm_rate = 0;
            // }
        } elseif ($request->type_of_bus == 'TNP') {
            $brokerage_comm_rate = $request->brokerage_comm_rate;
            $reinclass_code = $request->reinclass_code;
            $classcode = 'TRT';
            $reinclass = ReinsClass::wherein('class_code', $reinclass_code)->pluck('class_name')->toArray();
            $treaty_name = implode('-', $reinclass) . ' ' . $treatytype->treaty_name . ' TREATY';
            $date_offered = $request->date_offered;
            $share_offered = $request->share_offered;
        } elseif ($request->type_of_bus == 'TPR') {
            $brokerage_comm_rate = $request->brokerage_comm_rate;
            $treaty_reinclass = $request->treaty_reinclass;
            $classcode = 'TRT';
            $reinclass = ReinsClass::wherein('class_code', $treaty_reinclass)->pluck('class_name')->toArray();
            $treaty_name = implode('-', $reinclass) . ' ' . $treatytype->treaty_name . ' TREATY';
            $date_offered = $request->date_offered;
            $share_offered = $request->share_offered;
        }

        $coverfrom = $request->coverfrom;
        $coverto = $request->coverto;

        $pay_method_code = $request->pay_method;
        $currency_code = $request->currency_code;
        $branchcode = str_pad($branchcode, 3, '0', STR_PAD_LEFT);
        $retention_amt = (float)$request->retention_amt ? str_replace(',', '', $request->retention_amt) : 0;
        $no_of_lines = (float)$request->no_of_lines ? $request->no_of_lines : 0;
        $quota_share_total_limit = (float)$request->quota_share_total_limit ? str_replace(',', '', $request->quota_share_total_limit) : 0;

        $endorsement_no = $request->endorsement_no;
        $cover_no = $request->cover_no;
        $this->_endorsement_no = $endorsement_no;
        $cede_premium = $request->cede_premium ? str_replace(',', '', $request->cede_premium) : 0;
        $cede_comm_amt = $request->comm_amt ? str_replace(',', '', $request->comm_amt) : 0;
        // $rein_premium = $request->rein_premium ? str_replace(',','',$request->rein_premium) : 0;
        $rein_comm_amt = $request->reins_comm_amt ? str_replace(',', '', $request->reins_comm_amt) : 0;

        $CoverRegister = CoverRegister::where('endorsement_no', $endorsement_no)->first();

        $CoverRegister->premium_payment_code = $request->premium_payment_term;
        $CoverRegister->branch_code = $branchcode;
        $CoverRegister->broker_code = $brokercode ? $brokercode : 0;
        $CoverRegister->cover_type = $covertype;
        $CoverRegister->class_code = $classcode;
        $CoverRegister->class_group_code = $class_group;
        $CoverRegister->effective_date = $request->effective_date ?? $coverfrom;
        $CoverRegister->cover_from = $coverfrom;
        $CoverRegister->cover_to = $coverto;
        $CoverRegister->binder_cov_no = $request->bindercoverno;
        $CoverRegister->pay_method_code = $pay_method_code;
        $CoverRegister->currency_code = $currency_code;
        $CoverRegister->currency_rate = $request->today_currency;
        $CoverRegister->type_of_sum_insured = $request->sum_insured_type;
        $CoverRegister->rein_premium = $rein_premium;
        $CoverRegister->total_sum_insured = $request->total_sum_insured ? str_replace(',', '', $request->total_sum_insured) : 0;
        $CoverRegister->cedant_premium = $cede_premium;
        $CoverRegister->eml_rate = $request->eml_rate ? $request->eml_rate : 0;
        $CoverRegister->apply_eml = $request->apply_eml ?? 'N';
        $CoverRegister->eml_amount = $request->eml_amt ? str_replace(',', '', $request->eml_amt) : 0;
        $CoverRegister->effective_sum_insured = $request->effective_sum_insured ? str_replace(',', '', $request->effective_sum_insured) : 0;
        $CoverRegister->cedant_comm_rate = $request->comm_rate;
        $CoverRegister->cedant_comm_amount = $cede_comm_amt;
        $CoverRegister->rein_comm_type = $request->reins_comm_type;
        $CoverRegister->rein_comm_rate = $request->reins_comm_rate ? $request->reins_comm_rate : 0;
        $CoverRegister->brokerage_comm_type = $request->brokerage_comm_type;
        $CoverRegister->brokerage_comm_rate = $brokerage_comm_rate ? $brokerage_comm_rate : 0;
        $CoverRegister->brokerage_comm_amt = $brokerage_comm_amt ? $brokerage_comm_amt : 0;
        $CoverRegister->reinsurer_per_treaty = $request->reinsurer_per_treaty;
        $CoverRegister->rein_comm_amount = $rein_comm_amt;
        $CoverRegister->division_code = $request->division;
        $CoverRegister->vat_charged = $request->vat_charged;
        $CoverRegister->treaty_type = $request->treatytype;
        $CoverRegister->cover_title = $treaty_name;
        $CoverRegister->date_offered = $date_offered;
        $CoverRegister->share_offered = (float)$share_offered ? $share_offered : 0;
        $CoverRegister->port_prem_rate = (float)$request->port_prem_rate ? $request->port_prem_rate : 0;
        $CoverRegister->port_loss_rate = (float)$request->port_loss_rate ? $request->port_loss_rate : 0;
        $CoverRegister->profit_comm_rate = (float)$request->profit_comm_rate ? $request->profit_comm_rate : 0;
        $CoverRegister->mgnt_exp_rate = (float)$request->mgnt_exp_rate ? $request->mgnt_exp_rate : 0;
        $CoverRegister->deficit_yrs = (int)$request->deficit_yrs ? (int)$request->deficit_yrs : 0;
        $CoverRegister->deposit_frequency = $request->deposit_frequency ? $request->deposit_frequency : 0;
        $CoverRegister->prem_tax_rate = $request->prem_tax_rate ? $request->prem_tax_rate : 0;
        $CoverRegister->ri_tax_rate = $request->ri_tax_rate ? $request->ri_tax_rate : 0;
        $CoverRegister->risk_details = $risk_details;
        $CoverRegister->status = 'A';
        $CoverRegister->no_of_installments = (int) $request->no_of_installments;
        $CoverRegister->updated_by = Auth::user()->user_name;
        $CoverRegister->save();

        if ($request->type_of_bus == 'TNP') {
            foreach ($reinclass_code as $index => $reinclass) {
                $this->insertCoverReinClass($reinclass);
            }
        }

        if ($request->type_of_bus == 'TPR') {
            //Code to insert into coverreinprop table in a loop
            $treaty_reinclass = $request->treaty_reinclass;
            // Loop through one of the arrays (assuming they all have the same length)
            foreach ($treaty_reinclass as $index => $treaty_class) {

                $this->insertCoverReinClass($treaty_class);

                $retention_per = isset($request->retention_per) && isset($request->retention_per[$index])  ? str_replace(',', '', $request->retention_per[$index]) : 0;
                $treaty_reice = isset($request->treaty_reice[$index]) ? str_replace(',', '', $request->treaty_reice[$index]) : 0;
                $surp_retention_amt = isset($request->surp_retention_amt[$index]) ? str_replace(',', '', $request->surp_retention_amt[$index]) : 0;
                $no_of_lines = isset($request->no_of_lines[$index]) ? str_replace(',', '', $request->no_of_lines[$index]) : 0;
                $surp_treaty_limit = isset($request->surp_treaty_limit[$index]) ? str_replace(',', '', $request->surp_treaty_limit[$index]) : 0;
                $quota_retention_amt = isset($request->quota_retention_amt[$index]) ? str_replace(',', '', $request->quota_retention_amt[$index]) : 0;
                $quota_treaty_limit = isset($request->quota_treaty_limit[$index]) ? str_replace(',', '', $request->quota_treaty_limit[$index]) : 0;
                $quota_share_total_limit = isset($request->quota_share_total_limit[$index]) ? str_replace(',', '', $request->quota_share_total_limit[$index]) : 0;
                $estimated_income = isset($request->estimated_income[$index]) ? str_replace(',', '', $request->estimated_income[$index]) : 0;
                $cashloss_limit = isset($request->cashloss_limit[$index]) ? str_replace(',', '', $request->cashloss_limit[$index]) : 0;

                if ($request->treatytype == 'SURP') {
                    $data = [
                        'treaty_class'  => $treaty_class,
                        'item_description'  => 'SURPLUS',
                        'retention_per' => $retention_per,
                        'treaty_rate'   => $treaty_reice,
                        'retention_amount'  => $surp_retention_amt,
                        'no_of_lines'   => $no_of_lines,
                        'treaty_amount' => $surp_treaty_limit,
                        'treaty_limit'  => $surp_retention_amt + $surp_treaty_limit,
                        'estimated_income'  => $estimated_income,
                        'cashloss_limit'    => $cashloss_limit,
                    ];

                    $this->insertCoverReinProp($data);
                } elseif ($request->treatytype == 'QUOT') {
                    $data = [
                        'treaty_class'  => $treaty_class,
                        'item_description'  => 'QUOTA',
                        'retention_per' => $retention_per,
                        'treaty_rate'   => $treaty_reice,
                        'retention_amount'  => $quota_retention_amt,
                        'no_of_lines'   => $no_of_lines,
                        'treaty_amount' => $quota_treaty_limit,
                        'treaty_limit'  => $quota_retention_amt + $quota_treaty_limit,
                        'estimated_income'  => $estimated_income,
                        'cashloss_limit'    => $cashloss_limit,
                    ];

                    $this->insertCoverReinProp($data);
                } elseif ($request->treatytype == 'SPQT') {
                    if ($request->quota_share_total_limit[$index] > 0) {
                        $data = [
                            'treaty_class'  => $treaty_class,
                            'item_description'  => 'QUOTA',
                            'retention_per' => $retention_per,
                            'treaty_rate'   => $treaty_reice,
                            'retention_amount'  => $quota_retention_amt,
                            'no_of_lines'   => $no_of_lines,
                            'treaty_amount' => $quota_treaty_limit,
                            'treaty_limit'  => $quota_retention_amt + $quota_treaty_limit,
                            'estimated_income'  => $estimated_income,
                            'cashloss_limit'    => $cashloss_limit,
                        ];

                        $this->insertCoverReinProp($data);
                    }
                    if ($request->surp_treaty_limit[$index] > 0) {
                        $data = [
                            'treaty_class'  => $treaty_class,
                            'item_description'  => 'SURPLUS',
                            'retention_per' => $retention_per,
                            'treaty_rate'   => $treaty_reice,
                            'retention_amount'  => $surp_retention_amt,
                            'no_of_lines'   => $no_of_lines,
                            'treaty_amount' => $surp_treaty_limit,
                            'treaty_limit'  => $surp_retention_amt + $surp_treaty_limit,
                            'estimated_income'  => $estimated_income,
                            'cashloss_limit'    => $cashloss_limit,
                        ];

                        $this->insertCoverReinProp($data);
                    }
                }
            }

            //Code to insert premtype commisssion in a loop
            $prem_type_reinclass = $request->prem_type_reinclass;
            $prem_type_treaty = $request->prem_type_treaty;
            $prem_type_code = $request->prem_type_code;
            $prem_type_comm_rate = $request->prem_type_comm_rate;
            // Loop through one of the arrays (assuming they all have the same length)
            foreach ($prem_type_reinclass as $index => $reinclass) {
                $premtype_reinclass = ReinclassPremtype::where('reinclass', $reinclass)
                    ->where('premtype_code', $prem_type_code[$index])
                    ->first();
                // Create a new instance of YourModel
                $CoverPremtype = new CoverPremtype();
                $CoverRegister = CoverRegister::where('endorsement_no', $this->_endorsement_no)->first();

                $CoverPremtypeModel = CoverPremtype::where('endorsement_no', $this->_endorsement_no)
                    ->where('premtype_code', $prem_type_code[$index])
                    ->where('reinclass', $reinclass)
                    ->where('treaty', $prem_type_treaty[$index]);

                if ($CoverPremtypeModel->count() > 0) {
                    $CoverPremtype = $CoverPremtypeModel->first();
                } else {
                    $CoverPremtype = new CoverPremtype();
                    $CoverPremtype->cover_no = $CoverRegister->cover_no;
                    $CoverPremtype->endorsement_no = $this->_endorsement_no;
                }
                $CoverPremtype->reinclass = $reinclass;
                $CoverPremtype->treaty = $prem_type_treaty[$index];
                $CoverPremtype->premtype_code = $prem_type_code[$index];
                $CoverPremtype->premtype_name = $premtype_reinclass->premtype_name;
                $CoverPremtype->comm_rate = $prem_type_comm_rate[$index];
                $CoverPremtype->save();
            }
        } elseif ($request->type_of_bus == 'TNP') {

            $indemnity_limits = $request->indemnity_treaty_limit;
            $underlying_limit = $request->underlying_limit;
            $egnpi = $request->egnpi;
            $method = $request->method;
            $payment_frequency = $request->deposit_frequency ? $request->deposit_frequency : 0;
            $min_bc_rate = $request->min_bc_rate;
            $max_bc_rate = $request->max_bc_rate;
            $flat_rate = $request->flat_rate;
            $upper_adj = $request->upper_adj;
            $lower_adj = $request->lower_adj;
            $min_deposit = $request->min_deposit;
            $nonprop_reinclass = $request->nonprop_reinclass;
            $layer_no = $request->layer_no;
            $reinstatement_type = $request->reinstatement_type;
            $reinstatement_value = $request->reinstatement_value;
            $item_no = 1;
            // Loop through one of the arrays (assuming they all have the same length)
            foreach ($indemnity_limits as $index => $indemnity_limit) {

                if ($index > 0 && $layer_no[$index - 1] == $layer_no[$index]) {
                    $item_no = $item_no + 1;
                } else {
                    $item_no = 1;
                }

                $CoverReinLayerModel = CoverReinLayer::where('endorsement_no', $this->_endorsement_no)
                    ->where('reinclass', $nonprop_reinclass[$index])
                    ->where('layer_no', $layer_no[$index]);
                if ($CoverReinLayerModel->count() > 0) {
                    $CoverReinLayer = $CoverReinLayerModel->first();
                } else {
                    $CoverReinLayer = new CoverReinLayer();
                    $CoverReinLayer->cover_no = $CoverRegister->cover_no;
                    $CoverReinLayer->endorsement_no = $this->_endorsement_no;
                    $CoverReinLayer->layer_no = $layer_no[$index];
                    $CoverReinLayer->reinclass = $nonprop_reinclass[$index];
                }

                $CoverReinLayer->indemnity_limit = (float)str_replace(',', '', $indemnity_limit) ?? 0;
                $CoverReinLayer->underlying_limit = (float)str_replace(',', '', $underlying_limit[$index]) ?? 0;
                $CoverReinLayer->egnpi = (float)str_replace(',', '', $egnpi[$index]);
                $CoverReinLayer->method = $method;
                $CoverReinLayer->payment_frequency = $payment_frequency;
                $CoverReinLayer->reinstatement_type = $reinstatement_type[$index];
                $CoverReinLayer->reinstatement_value = (float)str_replace(',', '', $reinstatement_value[$index]) ?? 0;
                $CoverReinLayer->item_no = $item_no;

                if ($method == 'F') {
                    $CoverReinLayer->flat_rate = (float)str_replace(',', '', $flat_rate[$index]) ?? 0;
                    $CoverReinLayer->min_bc_rate = 0;
                    $CoverReinLayer->max_bc_rate = (float)str_replace(',', '', $flat_rate[$index]) ?? 0;
                    $CoverReinLayer->upper_adj =   (float)str_replace(',', '', $flat_rate[$index]) ?? 0;
                    $CoverReinLayer->lower_adj =   0;
                } else {
                    $CoverReinLayer->flat_rate = 0;
                    $CoverReinLayer->min_bc_rate = (float)str_replace(',', '', $min_bc_rate[$index]) ?? 0;
                    $CoverReinLayer->max_bc_rate = (float)str_replace(',', '', $max_bc_rate[$index]) ?? 0;
                    $CoverReinLayer->upper_adj = (float)str_replace(',', '', $upper_adj[$index]) ?? 0;
                    $CoverReinLayer->lower_adj = (float)str_replace(',', '', $lower_adj[$index]) ?? 0;
                }

                $CoverReinLayer->min_deposit = (float)str_replace(',', '', $min_deposit[$index]) ?? 0;
                $CoverReinLayer->save();
            }
        } elseif ($request->type_of_bus == 'FPR' || $request->type_of_bus == 'FNP') {

            $CoverPremiumModel = CoverPremium::where('endorsement_no', $this->_endorsement_no)
                ->where('transaction_type', $CoverRegister->transaction_type)
                ->where('class_code', $classcode)
                ->where('treaty', 'FAC')
                ->where('entry_type_descr', 'PRM');
            // dd($CoverPremiumModel);
            if ($CoverPremiumModel->count() > 0) {
                $CoverPremium = $CoverPremiumModel->first();
            } else {
                $CoverPremium = new CoverPremium();
                $CoverPremium->cover_no = $cover_no;
                $CoverPremium->endorsement_no = $endorsement_no;
                $CoverPremium->orig_endorsement_no = $endorsement_no;
                $CoverPremium->treaty = 'FAC';
                $CoverPremium->quarter =  $this->_quarter;
                $CoverPremium->entry_type_descr =  'PRM';
                $CoverPremium->created_by = Auth::user()->user_name;
                $CoverPremium->premtype_name =  'Gross Premium';
                $CoverPremium->premium_type_description = 'Gross Premium';
                $CoverPremium->type_of_bus =  $request->type_of_bus;
                $CoverPremium->class_code =  $classcode;
                $CoverPremium->premium_type_order_position = 1;
                $CoverPremium->transaction_type =  $CoverRegister->transaction_type;
                if ($CoverPremium->transaction_type == 'RFN' || $CoverPremium->transaction_type == 'CNC') {
                    $CoverPremium->dr_cr = 'CR';
                } else {
                    $CoverPremium->dr_cr = 'DR';
                }
            }
            //Gross Premium
            $CoverPremium->premium_type_code =  0;
            $CoverPremium->basic_amount =  $cede_premium;
            $CoverPremium->apply_rate_flag =  'Y';
            $CoverPremium->rate =  $share_offered;
            $CoverPremium->final_amount =  ($share_offered / 100) * $cede_premium;
            $CoverPremium->updated_by = Auth::user()->user_name;
            $CoverPremium->save();

            // $rate = $request->comm_rate;
            // $cede_premium = ($share_offered / 100) * $cede_premium;
            // $cede_comm_amt = ($share_offered / 100) * $cede_comm_amt;
            // $rein_comm_amt = ($share_offered / 100) * $rein_comm_amt;
            // $broker_comm_amt = $rein_comm_amt - $cede_comm_amt;

            $rate = $request->comm_rate;
            $cede_premium = ($share_offered / 100) * $cede_premium;
            $cede_comm_amt = ($rate / 100) * $cede_premium;

            //Commissions
            $CoverPremiumModel = CoverPremium::where('endorsement_no', $this->_endorsement_no)
                ->where('transaction_type', $CoverRegister->transaction_type)
                ->where('class_code', $classcode)
                ->where('treaty', 'FAC')
                ->where('entry_type_descr', 'COM');
            if ($CoverPremiumModel->count() > 0) {
                $CoverPremium = $CoverPremiumModel->first();
            } else {
                $CoverPremium = new CoverPremium();
                $CoverPremium->cover_no = $cover_no;
                $CoverPremium->endorsement_no = $endorsement_no;
                $CoverPremium->orig_endorsement_no = $endorsement_no;
                $CoverPremium->transaction_type =  $CoverRegister->transaction_type;
                $CoverPremium->quarter =  $this->_quarter;
                $CoverPremium->entry_type_descr =  'COM';
                $CoverPremium->premium_type_description = 'Commission';
                $CoverPremium->type_of_bus =  $request->type_of_bus;
                $CoverPremium->class_code =  $classcode;
                $CoverPremium->treaty = 'FAC';
                if ($CoverPremium->transaction_type == 'RFN' || $CoverPremium->transaction_type == 'CNC') {
                    $CoverPremium->dr_cr = 'DR';
                } else {
                    $CoverPremium->dr_cr = 'CR';
                }
                $CoverPremium->premtype_name =  'Commission';
                $CoverPremium->created_by = Auth::user()->user_name;
                $CoverPremium->created_at = Carbon::now();
            }

            $CoverPremium->premium_type_code =  0;
            $CoverPremium->premium_type_order_position = 2;
            $CoverPremium->basic_amount =  $cede_premium;
            $CoverPremium->apply_rate_flag =  'Y';
            $CoverPremium->rate =  $rate;
            $CoverPremium->final_amount =  $cede_comm_amt;
            $CoverPremium->updated_by = Auth::user()->user_name;
            $CoverPremium->save();
        }

        //Cover Installments Edit
        $paymethods = PayMethod::all();
        $selected_pay_method = collect($paymethods)->first(
            fn($item) => $item->pay_method_code == $request->pay_method,
        );
        $installmentData = [
            'cover_no'         => $CoverRegister->cover_no,
            'endorsement_no'   => $CoverRegister->endorsement_no,
            'layer_no'         => 0,
            'trans_type'       => $CoverRegister->type_of_bus,
            'entry_type'       => $CoverRegister->transaction_type,
            'dr_cr'            => 'DR',
            'created_by'       => Auth::user()->user_name,
            'updated_by'       => Auth::user()->user_name,
            'created_at'       => now(),
            'updated_at'       => now(),
        ];
        if ($selected_pay_method->short_description === 'I') {
            if ((int) $request->no_of_installments > 1) {
                DB::table('cover_installments')
                    ->where([
                        ['endorsement_no', '=', $CoverRegister->endorsement_no],
                        ['dr_cr', '=', 'DR']
                    ])
                    ->delete();
                for ($i = 0; $i < (int) $request->no_of_installments; $i++) {
                    DB::table('cover_installments')->insert(
                        [
                            ...$installmentData,
                            ...[
                                'installment_no'   => $request->installment_no[$i],
                                'installment_date' => Carbon::parse($request->installment_date[$i])->format('Y-m-d'),
                                'installment_amt'  => (float) str_replace(",", "", $request->installment_amt[$i]),
                            ]
                        ]
                    );
                }
            } else {
                if (!empty($request->installment_id[0]) && count($request->installment_id) <= 1) {
                    DB::table('cover_installments')
                        ->where('id', $request->installment_id[0])
                        ->update([
                            'installment_no'   => $request->installment_no[0],
                            'installment_date' => Carbon::parse($request->installment_date[0])->format('Y-m-d'),
                            'installment_amt'  => (float) str_replace(",", "", $request->installment_amt[0]),
                            'updated_by'       => Auth::user()->user_name,
                        ]);
                } else {
                    DB::table('cover_installments')
                        ->where([
                            ['endorsement_no', '=', $CoverRegister->endorsement_no],
                            ['dr_cr', '=', 'DR']
                        ])
                        ->delete();
                    DB::table('cover_installments')->insert([...$installmentData, ...[
                        'installment_no'   => $request->installment_no[0],
                        'installment_date' => Carbon::parse($request->installment_date[0])->format('Y-m-d'),
                        'installment_amt'  => (float) str_replace(",", "", $request->installment_amt[0]),
                    ]]);
                }
            }
        } else {
            $inst = CoverInstallments::where([
                'endorsement_no' => $CoverRegister->endorsement_no,
                'dr_cr' => 'DR'
            ])->get();
            if (count($inst) > 1) {
                CoverInstallments::where('id', '!=', $inst->first()->id)->delete();
            }
            $totalDr = CoverPremium::where('endorsement_no', $CoverRegister->endorsement_no)
                ->where('dr_cr', 'DR')
                ->sum('final_amount');
            $totalCr = CoverPremium::where('endorsement_no', $CoverRegister->endorsement_no)
                ->where('dr_cr', 'CR')
                ->sum('final_amount');
            $installmentAmount = $totalDr - $totalCr;
            $data = [
                'cover_no'          =>  $CoverRegister->cover_no,
                'endorsement_no'    =>  $CoverRegister->endorsement_no,
                'layer_no'          =>  0,
                'trans_type'        => $CoverRegister->type_of_bus,
                'entry_type'        => $CoverRegister->transaction_type,
                'installment_no'    => 1,
                'installment_date'  => $CoverRegister->cover_from->addDays((int)$CoverRegister->premium_payment_days),
                'installment_amt'   => $installmentAmount,
                'created_by'        => Auth::user()->user_name,
                'updated_by'        => Auth::user()->user_name,
            ];

            CoverInstallments::create(array_merge($data, ['dr_cr' => 'DR']));
        }

        return (object) ['endorsement_no' => $CoverRegister->endorsement_no];
    }

    public function insertCoverReinClass($reinclass)
    {
        $CoverRegister = CoverRegister::where('endorsement_no', $this->_endorsement_no)->first();
        $CoverReinclass = CoverReinclass::where('endorsement_no', $this->_endorsement_no)
            ->where('reinclass', $reinclass);
        if ($CoverReinclass->count() > 0) {
            $CoverReinclass = $CoverReinclass->first();
        } else {
            $CoverReinclass = new CoverReinclass();
            $CoverReinclass->created_by = Auth::user()->user_name;
            $CoverReinclass->cover_no = $CoverRegister->cover_no;
            $CoverReinclass->endorsement_no = $this->_endorsement_no;
        }
    }

    public function saveCoverEndorsement($request)
    {
        $type_of_bus = $request->type_of_bus;
        $endorse_type_slug = $request->endorse_type;
        $customer = (object) [];

        $coverfrom = $request->coverfrom;
        $effective_date = $request->coverfrom;
        $coverto = $request->coverto;
        $premium_payment_days = 0;
        $new_insured_name = $request->insured_name;
        $sum_insured = $request->total_sum_insured;
        $effective_sum_insured = $request->effective_sum_insured;
        $cede_premium = $request->cede_premium;
        $share_offered = $request->fac_share_offered;
        $rein_premium = $request->rein_premium;

        switch ($request->endorse_type) {
            case 'change-due-date':
                $extension_days = $request->extension_days;
                if ($request->premium_payment_code != null) {
                    $premium_payment_days =  $extension_days;
                } else {
                    $premium_payment_days =  0;
                }
                break;

            case 'change-inception-date':
                $coverfrom = $request->new_coverfrom;
                $effective_date = $request->new_coverfrom;
                $coverto = $request->new_coverto;
                break;

            case 'change-insured':
                $new_insured_name = $request->new_insured_name;
                break;

            case 'change-sum-insured' || 'change-premium':
                $sum_insured = (float) str_replace(',', '', $request->endorsed_total_sum_insured ?? '0');

                if ($request->apply_eml != 'Y') {
                    $effective_sum_insured = $sum_insured;
                } else {
                    $eml_rate = (float) ($request->eml_rate ?? 0);
                    $eml_rate = max(0, min(100, $eml_rate));
                    $eml_amt = $sum_insured * ($eml_rate / 100);
                    $effective_sum_insured = $eml_amt;
                }

                $endorsed_cede_premium = (float) str_replace(',', '', $request->endorsed_cede_premium ?? '0');

                if ($endorsed_cede_premium <= 0) {
                    $cede_premium = '0.00';
                    $rein_premium = '0.00';
                } else {
                    $cede_premium = number_format($endorsed_cede_premium, 2, '.', '');
                    $rein_premium = $cede_premium;
                }

                $share_offered = (float) str_replace(',', '', $request->new_fac_share_offered ?? '0');

                break;

            default:
                break;
        }

        if ($type_of_bus == 'FPR' || $type_of_bus == 'FNP') {
            $insured_name = $new_insured_name;
            $date_offered = $request->fac_date_offered;
            if ($request->brokerage_comm_type == 'R') {
                $brokerage_comm_rate = $request->reins_comm_rate - $request->comm_rate;
            } else {
                $brokerage_comm_rate = 0;
            }
        } elseif ($type_of_bus == 'TNP') {
            $brokerage_comm_rate = $request->brokerage_comm_rate;
            $insured_name = $customer->name;
            $date_offered = $request->date_offered;
            $share_offered = $request->share_offered;
        } elseif ($type_of_bus == 'TPR') {
            $brokerage_comm_rate = $request->brokerage_comm_rate;
            $insured_name = $customer->name;
            $date_offered = $request->date_offered;
            $share_offered = $request->share_offered;
        }

        $endorse_type = EndorsementType::where('type_of_bus', $type_of_bus)->where('endorse_type_slug', $endorse_type_slug)->first();
        $trans_type = $endorse_type->transaction_type;
        $endorsement = $this->generateEndorseNo($type_of_bus, $trans_type);
        $endorsement_no = $endorsement->endorsement_no;
        $this->_endorsement_no = $endorsement_no;
        $cover_serial_no = $endorsement->serial_no;
        $no_of_installments = (int) $request->no_of_installments ? $request->no_of_installments : 1;
        $cover_no = $request->cover_no;
        $old_endorsement_no = $request->endorsement_no;
        $prevCReg = CoverRegister::where('cover_no', $old_endorsement_no)->first();
        $prevCoverRegister = empty($prevCReg) ? CoverRegister::where(['cover_no' => $cover_no, 'transaction_type' => 'NEW'])->first() ?? null : null;
        $orig_endorsement_no = $prevCoverRegister?->orig_endorsement_no;
        $CoverRegister = new CoverRegister($prevCoverRegister?->getAttributes());

        //Default fields
        $CoverRegister->cover_serial_no = $cover_serial_no;
        $CoverRegister->type_of_bus = $type_of_bus;
        $CoverRegister->cover_no = $cover_no;
        $CoverRegister->endorsement_no = $endorsement_no;
        $CoverRegister->orig_endorsement_no = $orig_endorsement_no;
        $CoverRegister->transaction_type = $trans_type;

        //Changes if request has value
        $CoverRegister->cedant_premium = $cede_premium ? str_replace(',', '', $cede_premium) : ($CoverRegister->cedant_premium ?: 0);
        $CoverRegister->risk_details = $request->risk_details ?? $CoverRegister->risk_details;
        $CoverRegister->customer_id = $request->customer_id ?? $CoverRegister->customer_id;
        $CoverRegister->premium_payment_code = $request->premium_payment_term ?? $CoverRegister->premium_payment_code;
        $CoverRegister->branch_code = $request->branchcode ?? $CoverRegister->branch_code;
        $CoverRegister->broker_code = $request->brokercode ?? $CoverRegister->broker_code ?: 0;
        $CoverRegister->cover_type = $request->covertype ?? $CoverRegister->cover_type;
        $CoverRegister->class_code = $request->classcode ?? $CoverRegister->class_code;
        $CoverRegister->class_group_code = $request->class_group ?? $CoverRegister->class_group_code;
        $CoverRegister->insured_name = $insured_name ?? $CoverRegister->insured_name;
        $CoverRegister->effective_date = $effective_date ?? $CoverRegister->effective_date;
        $CoverRegister->cover_from = $coverfrom ?? $CoverRegister->cover_from;
        $CoverRegister->cover_to = $coverto ?? $CoverRegister->cover_to;
        $CoverRegister->binder_cov_no = $request->bindercoverno ?? $CoverRegister->binder_cov_no;
        $CoverRegister->pay_method_code = $request->pay_method_code ?? $CoverRegister->pay_method_code;
        $CoverRegister->currency_code = $request->currency_code ?? $CoverRegister->currency_code;
        $CoverRegister->currency_rate = $request->today_currency ?? $CoverRegister->currency_rate;
        $CoverRegister->type_of_sum_insured = $request->sum_insured_type ?? $CoverRegister->type_of_sum_insured;
        $CoverRegister->rein_premium = $rein_premium ? str_replace(',', '', $rein_premium) : ($CoverRegister->rein_premium ?: 0);
        $CoverRegister->total_sum_insured = $sum_insured ? str_replace(',', '', $sum_insured) : ($CoverRegister->total_sum_insured ?: 0);
        $CoverRegister->apply_eml = $request->apply_eml ?? $CoverRegister->apply_eml ?? 'N';
        $CoverRegister->eml_rate = $request->eml_rate ?? $CoverRegister->eml_rate ?: 0;
        $CoverRegister->eml_amount = $request->eml_amt ? str_replace(',', '', $request->eml_amt) : ($CoverRegister->eml_amount ?: 0);
        $CoverRegister->effective_sum_insured = $effective_sum_insured ? str_replace(',', '', $effective_sum_insured) : ($CoverRegister->effective_sum_insured ?: 0);
        $CoverRegister->cedant_comm_rate = $request->comm_rate ? $request->comm_rate : $CoverRegister->cedant_comm_rate;
        $CoverRegister->cedant_comm_amount = $request->comm_amt ? str_replace(',', '', $request->comm_amt) : ($CoverRegister->cedant_comm_amount ?: 0);
        $CoverRegister->rein_comm_type = $request->reins_comm_type ? $request->reins_comm_type : $CoverRegister->rein_comm_type;
        $CoverRegister->rein_comm_rate = $request->reins_comm_rate ? $request->reins_comm_rate : ($CoverRegister->rein_comm_rate ?: 0);
        $CoverRegister->brokerage_comm_rate = $brokerage_comm_rate ? $brokerage_comm_rate : ($CoverRegister->brokerage_comm_rate ?: 0);
        $CoverRegister->brokerage_comm_type = $request->brokerage_comm_type ? $request->brokerage_comm_type : $CoverRegister->brokerage_comm_type;
        $CoverRegister->reinsurer_per_treaty = $request->reinsurer_per_treaty ? $request->reinsurer_per_treaty : $CoverRegister->reinsurer_per_treaty;
        $CoverRegister->rein_comm_amount = $request->reins_comm_amt ? str_replace(',', '', $request->reins_comm_amt) : ($CoverRegister->rein_comm_amount ?: 0);
        $CoverRegister->division_code = $request->division ? $request->division : $CoverRegister->division_code;
        $CoverRegister->vat_charged = $request->vat_charged ? $request->vat_charged : $CoverRegister->vat_charged;
        $CoverRegister->treaty_type = $request->treatytype ? $request->treatytype : $CoverRegister->treaty_type;
        $CoverRegister->cover_title = $CoverRegister->cover_title;
        $CoverRegister->premium_payment_days = $premium_payment_days;

        $CoverRegister->date_offered = $date_offered ? $date_offered : $CoverRegister->date_offered;
        $CoverRegister->share_offered = (float) $share_offered ? $share_offered : ($CoverRegister->share_offered ?: 0);
        $CoverRegister->port_prem_rate = (float) $request->port_prem_rate ?? $CoverRegister->port_prem_rate ?: 0;
        $CoverRegister->port_loss_rate = (float) $request->port_loss_rate ?? $CoverRegister->port_loss_rate ?: 0;
        $CoverRegister->profit_comm_rate = (float) $request->profit_comm_rate ?? $CoverRegister->profit_comm_rate ?: 0;
        $CoverRegister->mgnt_exp_rate = (float) $request->mgnt_exp_rate ?? $CoverRegister->mgnt_exp_rate ?: 0;
        $CoverRegister->deficit_yrs = (float) $request->deficit_yrs ?? $CoverRegister->deficit_yrs ?: 0;
        $CoverRegister->deposit_frequency = $request->deposit_frequency ?? $CoverRegister->deposit_frequency ?: 0;
        $CoverRegister->prem_tax_rate = $request->prem_tax_rate ?? $CoverRegister->prem_tax_rate ?: 0;
        $CoverRegister->ri_tax_rate = $request->ri_tax_rate ?? $CoverRegister->ri_tax_rate ?: 0;
        //other default fields
        $CoverRegister->account_year = $this->_year;
        $CoverRegister->account_month = $this->_month;
        $CoverRegister->status = 'A';
        $CoverRegister->verified = null;
        $CoverRegister->created_at = now();
        $CoverRegister->updated_at = now();
        $CoverRegister->created_by = Auth::user()->user_name;
        $CoverRegister->updated_by = Auth::user()->user_name;
        $CoverRegister->save();

        if ($type_of_bus == 'FPR' || $type_of_bus == 'FNP') {
            $prevCoverPremiums = CoverPremium::where('endorsement_no', $old_endorsement_no)->get();

            foreach ($prevCoverPremiums as $prevCoverPremium) {
                $data = $prevCoverPremium->getAttributes();
                $data['endorsement_no'] = $this->_endorsement_no;
                $data['quarter'] = $this->_quarter;
                $data['created_by'] = Auth::user()->user_name;
                $data['updated_by'] = Auth::user()->user_name;
                $data['created_at'] = Carbon::now();
                $data['updated_at'] = Carbon::now();
                $data['transaction_type'] = $CoverRegister->transaction_type;

                $cede_premium = max(0, ceil(($CoverRegister->share_offered / 100) * $CoverRegister->cedant_premium));
                $cede_comm_amt = 0;
                if ((int) $CoverRegister->cedant_premium > 0) {
                    $cede_comm_amt = max(0, ceil(($CoverRegister->share_offered / 100) * $CoverRegister->cedant_comm_amount));
                }

                if ($prevCoverPremium->entry_type_descr == 'PRM') {
                    $data['basic_amount'] = $cede_premium;
                    $data['final_amount'] = $cede_premium;
                } elseif ($prevCoverPremium->entry_type_descr == 'COM') {
                    $data['basic_amount'] = $cede_premium;
                    $data['final_amount'] = $cede_comm_amt;
                }

                CoverPremium::create($data);
            }
        }

        $EndorsementNarration = new EndorsementNarration();
        $EndorsementNarration->cover_no = $cover_no;
        $EndorsementNarration->endorsement_no = $endorsement_no;
        $EndorsementNarration->endorse_type_slug = $endorse_type_slug;
        $EndorsementNarration->endorse_type_descr = $endorse_type->endorse_type_descr;
        $EndorsementNarration->narration = $request->endorse_narration;
        $EndorsementNarration->extension_days = $request->extension_days ?? 0;
        $EndorsementNarration->endorsed_sum_insured = str_replace(',', '', $request->endorsed_total_sum_insured) ?? 0;
        $EndorsementNarration->endorsed_cede_premium = str_replace(',', '', $request->endorsed_cede_premium) ?? 0;
        $EndorsementNarration->endorsed_rein_premium = str_replace(',', '', $request->endorsed_cede_premium) ?? 0;
        $EndorsementNarration->new_sum_insured =  str_replace(',', '', $request->new_total_sum_insured) ?? 0;
        $EndorsementNarration->new_cede_premium = str_replace(',', '', $request->new_cede_premium) ?? 0;
        $EndorsementNarration->new_rein_premium = str_replace(',', '', $request->new_cede_premium) ?? 0;
        $EndorsementNarration->fac_shared = str_replace(',', '', $request->new_fac_share_offered) ?? 0;
        $EndorsementNarration->sum_insured_type = $request->change_in_sum_insured_type;
        $EndorsementNarration->document_no = $this->generateExtDocNumber();
        $EndorsementNarration->created_at = Carbon::now();
        $EndorsementNarration->updated_at = Carbon::now();
        $EndorsementNarration->created_by = Auth::user()->user_name;
        $EndorsementNarration->updated_by = Auth::user()->user_name;
        $EndorsementNarration->save();

        $this->replicateFromPrevious($old_endorsement_no, $request);
        return ['endorsement_no' => $this->_endorsement_no];
    }

    public function generateExtDocNumber()
    {
        $maxNumber = EndorsementNarration::max(DB::raw('CAST(SUBSTRING(document_no, 3) AS INTEGER)')) ?? 0305;
        $newNumber = $maxNumber + 1;
        return 'EN' . str_pad($newNumber, 6, '0', STR_PAD_LEFT);
    }

    /**
     * Delete all related data for a specific endorsement number
     *
     * @param string $coverNo
     * @return void
     */
    public function deleteCoverData($coverNo, $endorsementNo)
    {
        $models = [
            CoverAttachment::class,
            CoverClass::class,
            CoverDebit::class,
            CoverInstallments::class,
            CoverPremium::class,
            CoverPremtype::class,
            CoverReinclass::class,
            CoverRisk::class,
            CoverReinLayer::class,
            CoverReinProp::class,
            CoverRipart::class,
            CoverSlipWording::class,
            ClaimRegister::class,
            CustomerAccDet::class,
            ReinNote::class
        ];

        foreach ($models as $model) {
            $model::where(['cover_no' => $coverNo, 'endorsement_no' => $endorsementNo])->delete();
        }

        // PolicyRenewal::where('policy_number', $coverNo)->delete();
    }
}
