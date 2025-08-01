<?php

namespace App\Repositories;

use App\Models\Bd\Leads\Leads;
use App\Models\Bd\ProspectInstallment;
use App\Models\Branch;
use App\Models\Broker;
use App\Models\BusinessType;
use App\Models\Classes;
use App\Models\Bd\Prospects;
use App\Models\Bd\ProspectPremium;
use App\Models\Bd\ProspectPremtype;
use App\Models\Bd\ProspectRegister;
use App\Models\Bd\ProspectReinclass;
use App\Models\Bd\ProspectReinLayer;
use App\Models\CoverType;
use App\Models\Customer;
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
use App\Models\Bd\ProspectInstallments;
use App\Models\Bd\ProspectReinProp;
use App\Models\PayMethod;
use App\Models\PremiumPayTerm;
use App\Models\ReinclassPremtype;
use App\Models\ReinsClass;
use App\Models\TreatyType;

/**
 * Class ProspectRepositoryRepositoryEloquent.
 *
 * @package namespace App\Repositories;
 */
class ProspectRepository extends BaseRepository
{
    protected $fieldSearchable = [];
    private $_year;
    private $_month;
    private $_quarter;
    private $_opportunity_id;

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
        return ProspectRegister::class;
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


    public function registerProspect($request)
    {
        try {
            $currentYear = str_pad($this->_year, 4, '0', STR_PAD_LEFT);
            $currentMonth = str_pad($this->_month, 2, '0', STR_PAD_LEFT);

            $prospecttype = $request->covertype;
            $branchcode = (int)$request->branchcode;
            $brokercode = $request->brokercode;
            $type_of_bus = $request->type_of_bus;
            $customer_id = $request->customer_id;
            $class_group = $request->class_group;

            // $customer = Customer::where('customer_id', $request->customer_id)->first();
            $treatytype = TreatyType::where('treaty_code', $request->treatytype)->first();

            if ($request->client_category == 'O') {
                $client            = Customer::where('customer_id', $request->client_select)->first();
                $organic_reference = $client->customer_id;
                $full_name         = $client->full_name;
                $industry          = $client->industry;
                $salutation        = $client->salutation_code;
               } else {
            
                $full_name  = $request->full_name;
                $industry   = $request->industry;
                $salutation = $request->salutation;
               }
               $prequalification = $request->prequalification;
            
               $exists = Leads::where('client_type', $request->client_type)
                ->where('year', $request->lead_year)
                ->where('full_name', $full_name)
                ->exists();
            
               if ($exists) {
                return array('status' => 300);
               }

                $lead = new Leads();
                $lead->client_type =$request->client_type;
                $lead->code        =     $nextCode;
                $lead->year        =     $request->lead_year;
                $lead->full_name   =  $full_name;
                $lead->industry    =   $industry;
                $lead->salutation  = $salutation;
                $lead->prequalification = $prequalification;
                $lead->organic_reference =  $organic_reference;
                $lead->save();

            $pipeline = DB::table('pipelines')->where('year', $request->lead_year)->first();

            if (is_null($pipeline)) {
            return array('status' => 400);
            // return redirect()->back()->with('error','Pipeline for '.$request->lead_year.' is not available');
            }
                // $prospect = new Prospects();
                // $prospect->pipeline_id =  $pipeline->id;
                // $prospect->opportunity_id = $nextCode;
                // $prospect->lead_owner =  $request->lead_owner;
                // $prospect->lead_source =  $request->lead_source;
                // $prospect->premium =  $request->premium;
                // $prospect->closing_date =  $request->closing_date;
                // $prospect->currency =  $request->currency;
                // $prospect->source_desc =  $request->source_desc;
                // $prospect->stage =  0;
                // $prospect->save();

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

            // $prospectfrom = $request->coverfrom;
            // $prospectto = $request->coverto;
            $rein_comm_amt = $request->reins_comm_amt ? str_replace(',', '', $request->reins_comm_amt) : 0;
            $pay_method_code = $request->pay_method;
            $currency_code = $request->currency_code;
            $branchcode = str_pad($branchcode, 3, '0', STR_PAD_LEFT);
            $retention_amt = (float)$request->retention_amt ? str_replace(',', '', $request->retention_amt) : 0;
            $no_of_lines = (float)$request->no_of_lines ? $request->no_of_lines : 0;
            $quota_share_total_limit = (float)$request->quota_share_total_limit ? str_replace(',', '', $request->quota_share_total_limit) : 0;
            $opportunity_id = $nextCode;
            $pipeline_id = $pipeline->id;
            $no_of_installments = (int) $request->no_of_installments;
            $cede_premium = $request->cede_premium ? str_replace(',', '', $request->cede_premium) : 0;
            $cede_comm_amt = $request->comm_amt ? str_replace(',', '', $request->comm_amt) : 0;

            if ($request->premium_payment_term) {
                $ppw = PremiumPayTerm::where('pay_term_code', $request->premium_payment_term)->first();
            }
          
                $pipeline_id = $pipeline_id;
                $orig_opportunity_id = $opportunity_id;

            $ProspectRegister = new Prospects();
            $ProspectRegister->pipeline_id = $pipeline_id;
            $ProspectRegister->customer_id = $customer_id;
            $ProspectRegister->type_of_bus = $type_of_bus;
            // $ProspectRegister->pipeline_id = $pipeline_id;
            $ProspectRegister->opportunity_id = $opportunity_id;
            $ProspectRegister->orig_opportunity_id = $orig_opportunity_id;
            $ProspectRegister->opportunity_id = $nextCode;
            $ProspectRegister->lead_owner =  $request->lead_owner;
            $ProspectRegister->lead_source =  $request->lead_source;
            $ProspectRegister->premium =  $request->premium;
            $ProspectRegister->closing_date =  $request->closing_date;
            $ProspectRegister->currency =  $request->currency;
            $ProspectRegister->source_desc =  $request->source_desc;
            $ProspectRegister->stage =  0;
            $ProspectRegister->transaction_type = $request->trans_type;
            $ProspectRegister->premium_payment_code = $request->premium_payment_term;
            if ($ProspectRegister->premium_payment_code != null) {
                $ProspectRegister->premium_payment_days = $ppw->premium_payment_days;
            } else {
                $ProspectRegister->premium_payment_days = 0;
            }
            $ProspectRegister->branch_code = $branchcode;
            $ProspectRegister->broker_code = $brokercode ? $brokercode : 0;
            $ProspectRegister->cover_type = $prospecttype;
            $ProspectRegister->class_code = $classcode;
            $ProspectRegister->class_group_code = $class_group;
            $ProspectRegister->insured_name = $insured_name;
            $ProspectRegister->effective_date = $prospectfrom;
            $ProspectRegister->cover_from = $prospectfrom;
            $ProspectRegister->cover_to = $prospectto;
            $ProspectRegister->account_year = $this->_year;
            $ProspectRegister->account_month = $this->_month;
            $ProspectRegister->binder_cov_no = $request->bindercoverno;
            $ProspectRegister->created_by = Auth::user()->user_name;
            $ProspectRegister->pay_method_code = $pay_method_code;
            $ProspectRegister->currency_code = $currency_code;
            $ProspectRegister->currency_rate = $request->today_currency;
            $ProspectRegister->type_of_sum_insured = $request->sum_insured_type;
            $ProspectRegister->rein_premium = $rein_premium;
            $ProspectRegister->total_sum_insured = $request->total_sum_insured ? str_replace(',', '', $request->total_sum_insured) : 0;
            $ProspectRegister->cedant_premium = $cede_premium;
            $ProspectRegister->apply_eml = $request->apply_eml ?? 'N';
            $ProspectRegister->eml_rate = $request->eml_rate ? $request->eml_rate : 0;
            $ProspectRegister->eml_amount = $request->eml_amt ? str_replace(',', '', $request->eml_amt) : 0;
            $ProspectRegister->effective_sum_insured = $request->effective_sum_insured ? str_replace(',', '', $request->effective_sum_insured) : 0;
            $ProspectRegister->cedant_comm_rate = $request->comm_rate;
            $ProspectRegister->cedant_comm_amount = $cede_comm_amt;
            $ProspectRegister->rein_comm_type = $request->reins_comm_type;
            $ProspectRegister->rein_comm_rate = $request->reins_comm_rate ? $request->reins_comm_rate : 0;
            $ProspectRegister->brokerage_comm_rate = $brokerage_comm_rate ? $brokerage_comm_rate : 0;
            $ProspectRegister->brokerage_comm_amt = $brokerage_comm_amt ? $brokerage_comm_amt : 0;
            $ProspectRegister->brokerage_comm_type = $request->brokerage_comm_type;
            $ProspectRegister->reinsurer_per_treaty = $request->reinsurer_per_treaty;
            $ProspectRegister->rein_comm_amount = $rein_comm_amt;
            $ProspectRegister->division_code = $request->division;
            $ProspectRegister->vat_charged = $request->vat_charged;
            $ProspectRegister->treaty_type = $request->treatytype;
            $ProspectRegister->risk_details = $request->risk_details;
            $ProspectRegister->cover_title = $treaty_name;
            $ProspectRegister->date_offered = $date_offered;
            $ProspectRegister->share_offered = (float)$share_offered ? $share_offered : 0; $ProspectRegister->no_of_installments = $no_of_installments;
            $ProspectRegister->port_prem_rate = (float)$request->port_prem_rate ? $request->port_prem_rate : 0;
            $ProspectRegister->port_loss_rate = (float)$request->port_loss_rate ? $request->port_loss_rate : 0;
            $ProspectRegister->profit_comm_rate = (float)$request->profit_comm_rate ? $request->profit_comm_rate : 0;
            $ProspectRegister->mgnt_exp_rate = (float)$request->mgnt_exp_rate ? $request->mgnt_exp_rate : 0;
            $ProspectRegister->deficit_yrs = (float)$request->deficit_yrs ? $request->deficit_yrs : 0;$ProspectRegister->deposit_frequency = $request->deposit_frequency ? $request->deposit_frequency : 0;
            $ProspectRegister->prem_tax_rate = $request->prem_tax_rate ? $request->prem_tax_rate : 0;
            $ProspectRegister->ri_tax_rate = $request->ri_tax_rate ? $request->ri_tax_rate : 0;
            $ProspectRegister->status = 'A';
            $ProspectRegister->verified = null;
            $ProspectRegister->created_at = now(); //Carbon::now();
            $ProspectRegister->updated_at = now(); // Carbon::now();
            $ProspectRegister->created_by = Auth::user()->user_name;
            $ProspectRegister->updated_by = Auth::user()->user_name;
            $ProspectRegister->save();

            if ($request->type_of_bus == 'TNP') {
                foreach ($reinclass_code as $index => $reinclass) {
                    // Create a new instance of YourModel
                    $ProspectReinclass = new ProspectReinclass();
                    $ProspectReinclass->pipeline_id = $pipeline_id;
                    $ProspectReinclass->opportunity_id = $opportunity_id;
                    $ProspectReinclass->reinclass = $reinclass;
                    $ProspectReinclass->created_by = Auth::user()->user_name;
                    $ProspectReinclass->updated_by = Auth::user()->user_name;
                    $ProspectReinclass->save();
                }
            }

            if ($request->type_of_bus == 'TPR') {
                $treaty_reinclass = $request->treaty_reinclass;
                // Loop through one of the arrays (assuming they all have the same length)
                foreach ($treaty_reinclass as $index => $treaty_class) {

                    $ProspectReinclass = new ProspectReinclass();
                    $ProspectReinclass->pipeline_id = $pipeline_id;
                    $ProspectReinclass->opportunity_id = $opportunity_id;
                    $ProspectReinclass->reinclass = $treaty_class;
                    $ProspectReinclass->created_by = Auth::user()->user_name;
                    $ProspectReinclass->updated_by = Auth::user()->user_name;
                    $ProspectReinclass->save();

                    $retention_per = isset($request->retention_per) && isset($request->retention_per[$index])  ? str_replace(',', '', $request->retention_per[$index]) : 0;
                    $treaty_reice = isset($request->treaty_reice[$index]) ? str_replace(',', '', $request->treaty_reice[$index]) : 0;
                    $surp_retention_amt = isset($request->surp_retention_amt[$index]) ? str_replace(',', '', $request->surp_retention_amt[$index]) : 0;
                    $no_of_lines = isset($request->no_of_lines[$index]) ? str_replace(',', '', $request->no_of_lines[$index]) : 0;
                    $surp_treaty_limit = isset($request->surp_treaty_limit[$index]) ? str_replace(',', '', $request->surp_treaty_limit[$index]) : 0;
                    $quota_retention_amt = isset($request->quota_retention_amt[$index]) ? str_replace(',', '', $request->quota_retention_amt[$index]) : 0;
                    $quota_share_total_limit = isset($request->quota_share_total_limit[$index]) ? str_replace(',', '', $request->quota_share_total_limit[$index]) : 0;$estimated_income = isset($request->estimated_income[$index]) ? str_replace(',', '', $request->estimated_income[$index]) : 0;
                    $cashloss_limit = isset($request->cashloss_limit[$index]) ? str_replace(',', '', $request->cashloss_limit[$index]) : 0;

                    if ($request->treatytype == 'SURP') {
                        $count = ProspectReinProp::where('pipeline_id', $pipeline_id)
                            ->where('opportunity_id', $opportunity_id)
                            ->count();
                        $count = $count + 1;

                        $ProspectReinProp = new ProspectReinProp();
                        $ProspectReinProp->pipeline_id = $pipeline_id;
                        $ProspectReinProp->opportunity_id = $opportunity_id;
                        $ProspectReinProp->reinclass = $treaty_class;
                        $ProspectReinProp->item_no = $count;
                        $ProspectReinProp->item_description = 'SURPLUS';
                        $ProspectReinProp->retention_rate = $retention_per;
                        $ProspectReinProp->treaty_rate = $treaty_reice;
                        $ProspectReinProp->retention_amount = $surp_retention_amt;
                        $ProspectReinProp->no_of_lines = $no_of_lines;
                        $ProspectReinProp->treaty_amount = $surp_treaty_limit;
                        $ProspectReinProp->treaty_limit = $surp_retention_amt + $surp_treaty_limit;
                        $ProspectReinProp->port_prem_rate =  0;
                        $ProspectReinProp->port_loss_rate = 0;
                        $ProspectReinProp->profit_comm_rate = 0;
                        $ProspectReinProp->mgnt_exp_rate = 0;
                        $ProspectReinProp->deficit_yrs = 0;
                        $ProspectReinProp->estimated_income = $estimated_income;
                        $ProspectReinProp->cashloss_limit = $cashloss_limit;
                        $ProspectReinProp->created_at = now(); //Carbon::now();
                        $ProspectReinProp->updated_at = now(); //Carbon::now();
                        $ProspectReinProp->created_by = Auth::user()->user_name;
                        $ProspectReinProp->updated_by = Auth::user()->user_name;
                        $ProspectReinProp->save();
                    } elseif ($request->treatytype == 'QUOT') {
                        $count = ProspectReinProp::where('pipeline_id', $pipeline_id)
                            ->where('opportunity_id', $opportunity_id)
                            ->count();
                        $count = $count + 1;

                        $ProspectReinProp = new ProspectReinProp();
                        $ProspectReinProp->pipeline_id = $pipeline_id;
                        $ProspectReinProp->opportunity_id = $opportunity_id;
                        $ProspectReinProp->reinclass = $treaty_class;
                        $ProspectReinProp->item_no = $count;
                        $ProspectReinProp->item_description = 'QUOTA';
                        $ProspectReinProp->retention_rate = $retention_per;
                        $ProspectReinProp->treaty_rate = $treaty_reice;
                        $ProspectReinProp->retention_amount = $quota_retention_amt;
                        $ProspectReinProp->no_of_lines = $no_of_lines;
                        $ProspectReinProp->treaty_amount = $quota_share_total_limit;
                        $ProspectReinProp->treaty_limit = $quota_retention_amt + $quota_share_total_limit;
                        $ProspectReinProp->port_prem_rate =  0;
                        $ProspectReinProp->port_loss_rate = 0;
                        $ProspectReinProp->profit_comm_rate = 0;
                        $ProspectReinProp->mgnt_exp_rate = 0;
                        $ProspectReinProp->deficit_yrs = 0;
                        $ProspectReinProp->estimated_income = $estimated_income;
                        $ProspectReinProp->cashloss_limit = $cashloss_limit;
                        $ProspectReinProp->created_at = now(); // Carbon::now();
                        $ProspectReinProp->updated_at =  now(); //Carbon::now();
                        $ProspectReinProp->created_by = Auth::user()->user_name;
                        $ProspectReinProp->updated_by = Auth::user()->user_name;
                        $ProspectReinProp->save();
                    } elseif ($request->treatytype == 'SPQT') {
                        if ($request->quota_share_total_limit[$index] > 0) {
                            $count = ProspectReinProp::where('pipeline_id', $pipeline_id)
                                ->where('opportunity_id', $opportunity_id)
                                ->count();
                            $count = $count + 1;

                            $ProspectReinProp = new ProspectReinProp();
                            $ProspectReinProp->pipeline_id = $pipeline_id;
                            $ProspectReinProp->opportunity_id = $opportunity_id;
                            $ProspectReinProp->reinclass = $treaty_class;
                            $ProspectReinProp->item_no = $count;
                            $ProspectReinProp->item_description = 'QUOTA';
                            $ProspectReinProp->retention_rate = $retention_per;
                            $ProspectReinProp->treaty_rate = $treaty_reice;
                            $ProspectReinProp->retention_amount = $quota_retention_amt;
                            $ProspectReinProp->no_of_lines = $no_of_lines;;
                            $ProspectReinProp->treaty_amount = $quota_share_total_limit;
                            $ProspectReinProp->treaty_limit = $quota_retention_amt + $quota_share_total_limit;
                            $ProspectReinProp->port_prem_rate =  0;
                            $ProspectReinProp->port_loss_rate = 0;
                            $ProspectReinProp->profit_comm_rate = 0;
                            $ProspectReinProp->mgnt_exp_rate = 0;
                            $ProspectReinProp->deficit_yrs = 0;
                            $ProspectReinProp->estimated_income = $estimated_income;
                            $ProspectReinProp->cashloss_limit = $cashloss_limit;
                            $ProspectReinProp->created_at =  now(); //Carbon::now();
                            $ProspectReinProp->updated_at =  now(); //Carbon::now();
                            $ProspectReinProp->created_by = Auth::user()->user_name;
                            $ProspectReinProp->updated_by = Auth::user()->user_name;
                            $ProspectReinProp->save();
                        }
                        if ($request->surp_treaty_limit[$index] > 0) {
                            $count = ProspectReinProp::where('pipeline_id', $pipeline_id)
                                ->where('opportunity_id', $opportunity_id)
                                ->count();
                            $count = $count + 1;

                            $ProspectReinProp = new ProspectReinProp();
                            $ProspectReinProp->pipeline_id = $pipeline_id;
                            $ProspectReinProp->opportunity_id = $opportunity_id;
                            $ProspectReinProp->reinclass = $treaty_class;
                            $ProspectReinProp->item_no = $count;
                            $ProspectReinProp->item_description = 'SURPLUS';
                            $ProspectReinProp->retention_rate = $retention_per;
                            $ProspectReinProp->treaty_rate = $treaty_reice;
                            $ProspectReinProp->retention_amount = $surp_retention_amt;
                            $ProspectReinProp->no_of_lines = $no_of_lines;
                            $ProspectReinProp->treaty_amount = $surp_treaty_limit;
                            $ProspectReinProp->treaty_limit = $surp_retention_amt + $surp_treaty_limit;
                            $ProspectReinProp->port_prem_rate = 0;
                            $ProspectReinProp->port_loss_rate = 0;
                            $ProspectReinProp->profit_comm_rate = 0;
                            $ProspectReinProp->mgnt_exp_rate = 0;
                            $ProspectReinProp->deficit_yrs = 0;
                            $ProspectReinProp->estimated_income = $estimated_income;
                            $ProspectReinProp->cashloss_limit = $cashloss_limit;
                            $ProspectReinProp->created_at =  now(); //Carbon::now();
                            $ProspectReinProp->updated_at =  now(); //Carbon::now();
                            $ProspectReinProp->created_by = Auth::user()->user_name;
                            $ProspectReinProp->updated_by = Auth::user()->user_name;
                            $ProspectReinProp->save();
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
                    $ProspectPremtype = new ProspectPremtype();
                    $ProspectPremtype->pipeline_id = $pipeline_id;
                    $ProspectPremtype->opportunity_id = $opportunity_id;
                    $ProspectPremtype->reinclass = $reinclass;
                    $ProspectPremtype->treaty = $prem_type_treaty[$index];
                    $ProspectPremtype->premtype_code = $prem_type_code[$index];
                    $ProspectPremtype->premtype_name = $premtype_reinclass->premtype_name;
                    $ProspectPremtype->comm_rate = $prem_type_comm_rate[$index];
                    $ProspectPremtype->save();
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

                    $ProspectReinLayer = new ProspectReinLayer();
                    $ProspectReinLayer->pipeline_id = $pipeline_id;
                    $ProspectReinLayer->opportunity_id = $opportunity_id;
                    $ProspectReinLayer->layer_no = $layer_no[$index];
                    $ProspectReinLayer->indemnity_limit = (float)str_replace(',', '', $indemnity_limit) ?? 0;
                    $ProspectReinLayer->underlying_limit = (float)str_replace(',', '', $underlying_limit[$index]) ?? 0;
                    $ProspectReinLayer->egnpi = (float)str_replace(',', '', $egnpi[$index]);
                    $ProspectReinLayer->method = $method;
                    $ProspectReinLayer->payment_frequency = $payment_frequency;
                    $ProspectReinLayer->reinclass = $nonprop_reinclass[$index];
                    $ProspectReinLayer->reinstatement_type = $reinstatement_type[$index];
                    $ProspectReinLayer->reinstatement_value = (float)str_replace(',', '', $reinstatement_value[$index]) ?? 0;
                    $ProspectReinLayer->item_no = $item_no;

                    if ($method == 'F') {
                        $ProspectReinLayer->flat_rate = (float)str_replace(',', '', $flat_rate[$index]) ?? 0;
                        $ProspectReinLayer->min_bc_rate = 0;
                        $ProspectReinLayer->max_bc_rate = (float)str_replace(',', '', $flat_rate[$index]) ?? 0;
                        $ProspectReinLayer->upper_adj =   (float)str_replace(',', '', $flat_rate[$index]) ?? 0;
                        $ProspectReinLayer->lower_adj =   0;
                    } else {
                        $ProspectReinLayer->flat_rate = 0;
                        $ProspectReinLayer->min_bc_rate = (float)str_replace(',', '', $min_bc_rate[$index]) ?? 0;
                        $ProspectReinLayer->max_bc_rate = (float)str_replace(',', '', $max_bc_rate[$index]) ?? 0;
                        $ProspectReinLayer->upper_adj = (float)str_replace(',', '', $upper_adj[$index]) ?? 0;
                        $ProspectReinLayer->lower_adj = (float)str_replace(',', '', $lower_adj[$index]) ?? 0;
                    }

                    $ProspectReinLayer->min_deposit = (float)str_replace(',', '', $min_deposit[$index]) ?? 0;
                    $ProspectReinLayer->save();
                }
            } elseif ($request->type_of_bus == 'FPR' || $request->type_of_bus == 'FNP') {

                //Gross Premium
                $ProspectPremium = new ProspectPremium();
                $ProspectPremium->pipeline_id = $pipeline_id;
                $ProspectPremium->opportunity_id = $opportunity_id;
                $ProspectPremium->orig_opportunity_id = $ProspectRegister->orig_opportunity_id;
                $ProspectPremium->transaction_type =  $ProspectRegister->transaction_type;
                $ProspectPremium->premium_type_code =  0;
                $ProspectPremium->premtype_name =  'Gross Premium';
                $ProspectPremium->quarter =  $this->_quarter;
                $ProspectPremium->entry_type_descr =  'PRM';
                $ProspectPremium->premium_type_order_position = 1;
                $ProspectPremium->premium_type_description = 'Gross Premium';
                $ProspectPremium->type_of_bus =  $type_of_bus;
                $ProspectPremium->class_code =  $classcode;
                $ProspectPremium->basic_amount =  $cede_premium;
                $ProspectPremium->apply_rate_flag =  'Y';
                $ProspectPremium->treaty = 'FAC';
                $ProspectPremium->rate =  $share_offered;
                if ($ProspectRegister->transaction_type == 'RFN' || $ProspectRegister->transaction_type == 'CNC') {
                    $ProspectPremium->dr_cr = 'CR';
                } else {
                    $ProspectPremium->dr_cr = 'DR';
                }
                $ProspectPremium->final_amount =  ($share_offered / 100) * $cede_premium;
                $ProspectPremium->created_at =  now(); //Carbon::now() ;
                $ProspectPremium->updated_at =  now(); //Carbon::now() ;
                $ProspectPremium->created_by = Auth::user()->user_name;
                $ProspectPremium->updated_by = Auth::user()->user_name;
                $ProspectPremium->save();


                $rate = $request->comm_rate;
                $cede_premium = ($share_offered / 100) * $cede_premium;
                $cede_comm_amt = ($rate / 100) * $cede_premium;

                //Commissions
                $ProspectPremium = new ProspectPremium();
                $ProspectPremium->pipeline_id = $pipeline_id;
                $ProspectPremium->opportunity_id = $opportunity_id;
                $ProspectPremium->orig_opportunity_id = $ProspectRegister->orig_opportunity_id;
                $ProspectPremium->transaction_type = $ProspectRegister->transaction_type;
                $ProspectPremium->premium_type_code = 0;
                $ProspectPremium->premtype_name = 'Commission';
                $ProspectPremium->quarter = $this->_quarter;
                $ProspectPremium->entry_type_descr = 'COM';
                $ProspectPremium->premium_type_order_position = 2;
                $ProspectPremium->premium_type_description = 'Commission';
                $ProspectPremium->type_of_bus = $type_of_bus;
                $ProspectPremium->class_code = $classcode;
                $ProspectPremium->treaty = 'FAC';
                $ProspectPremium->basic_amount = $cede_premium;
                $ProspectPremium->apply_rate_flag = 'Y';
                $ProspectPremium->rate = $rate;
                if ($ProspectRegister->transaction_type == 'RFN' || $ProspectRegister->transaction_type == 'CNC') {
                    $ProspectPremium->dr_cr = 'DR';
                } else {
                    $ProspectPremium->dr_cr = 'CR';
                }
                $ProspectPremium->final_amount = $cede_comm_amt;
                $ProspectPremium->created_at = now(); //Carbon::now() ;
                $ProspectPremium->updated_at = now(); //Carbon::now() ;
                $ProspectPremium->created_by = Auth::user()->user_name;
                $ProspectPremium->updated_by = Auth::user()->user_name;
                $ProspectPremium->save();
            }

            if ((int) $no_of_installments > 0) {
                $totalDr = ProspectPremium::where('opportunity_id', $ProspectRegister->opportunity_id)
                    ->where('dr_cr', 'DR')
                    ->sum('final_amount');
                $totalCr = ProspectPremium::where('opportunity_id', $ProspectRegister->opportunity_id)
                    ->where('dr_cr', 'CR')
                    ->sum('final_amount');
                $installmentAmount = $totalDr - $totalCr;

                $data = [
                    'pipeline_id'          =>  $ProspectRegister->pipeline_id,
                    'opportunity_id'    =>  $ProspectRegister->opportunity_id,
                    'layer_no'          =>  0,
                    'trans_type'        => $ProspectRegister->type_of_bus,
                    'entry_type'        => $ProspectRegister->transaction_type,
                    'installment_no'    => 1,
                    'installment_date'  => $ProspectRegister->cover_from->addDays((int)$ProspectRegister->premium_payment_days),
                    'installment_amt'   => $installmentAmount,
                    'created_by'        => Auth::user()->user_name,
                    'updated_by'        => Auth::user()->user_name,
                ];

                if ((int) $no_of_installments === 1) {
                    ProspectInstallment::create(array_merge($data, ['dr_cr' => 'DR']));
                } else {
                    for ($i = 0; $i < $no_of_installments; $i++) {
                        ProspectInstallment::create([
                            'pipeline_id'          =>  $ProspectRegister->pipeline_id,
                            'opportunity_id'    =>  $ProspectRegister->opportunity_id,
                            'layer_no'          =>  0,
                            'trans_type'        => $ProspectRegister->type_of_bus,
                            'entry_type'        => $ProspectRegister->transaction_type,
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

            return (object)['opportunity_id' => $opportunity_id];
        } catch (\Exception $e) {
            throw $e;
        }
    }

    public function editProspectRegister($request)
    {
        $prospecttype = $request->covertype;
        $branchcode = (int)$request->branchcode;
        $brokercode = $request->brokercode;
        $class_group = $request->class_group;

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

        // $prospectfrom = $request->coverfrom;
        // $prospectto = $request->coverto;

        $pay_method_code = $request->pay_method;
        $currency_code = $request->currency_code;
        $branchcode = str_pad($branchcode, 3, '0', STR_PAD_LEFT);
        $retention_amt = (float)$request->retention_amt ? str_replace(',', '', $request->retention_amt) : 0;
        $no_of_lines = (float)$request->no_of_lines ? $request->no_of_lines : 0;
        $quota_share_total_limit = (float)$request->quota_share_total_limit ? str_replace(',', '', $request->quota_share_total_limit) : 0;

        $opportunity_id = $request->opportunity_id;
        $pipeline_id = $request->pipeline_id;
        // $this->_opportunity_id = $opportunity_id;
        $cede_premium = $request->cede_premium ? str_replace(',', '', $request->cede_premium) : 0;
        $cede_comm_amt = $request->comm_amt ? str_replace(',', '', $request->comm_amt) : 0;
        $rein_comm_amt = $request->reins_comm_amt ? str_replace(',', '', $request->reins_comm_amt) : 0;

        $ProspectRegister = Prospects::where('opportunity_id', $opportunity_id)->first();
        $ProspectRegister->opportunity_id = $nextCode;
        $ProspectRegister->lead_owner =  $request->lead_owner;
        $ProspectRegister->lead_source =  $request->lead_source;
        $ProspectRegister->premium =  $request->premium;
        $ProspectRegister->closing_date =  $request->closing_date;
        $ProspectRegister->currency =  $request->currency;
        $ProspectRegister->source_desc =  $request->source_desc;
        $ProspectRegister->stage =  0;
        $ProspectRegister->premium_payment_code = $request->premium_payment_term;
        $ProspectRegister->branch_code = $branchcode;
        $ProspectRegister->broker_code = $brokercode ? $brokercode : 0;
        $ProspectRegister->cover_type = $prospecttype;
        $ProspectRegister->class_code = $classcode;
        $ProspectRegister->class_group_code = $class_group;
        $ProspectRegister->effective_date = $request->effective_date ?? $prospectfrom;
        $ProspectRegister->binder_cov_no = $request->bindercoverno;
        $ProspectRegister->pay_method_code = $pay_method_code;
        $ProspectRegister->currency_code = $currency_code;
        $ProspectRegister->currency_rate = $request->today_currency;
        $ProspectRegister->type_of_sum_insured = $request->sum_insured_type;
        $ProspectRegister->rein_premium = $rein_premium;
        $ProspectRegister->total_sum_insured = $request->total_sum_insured ? str_replace(',', '', $request->total_sum_insured) : 0;
        $ProspectRegister->cedant_premium = $cede_premium;
        $ProspectRegister->eml_rate = $request->eml_rate ? $request->eml_rate : 0;
        $ProspectRegister->apply_eml = $request->apply_eml ?? 'N';
        $ProspectRegister->eml_amount = $request->eml_amt ? str_replace(',', '', $request->eml_amt) : 0;
        $ProspectRegister->effective_sum_insured = $request->effective_sum_insured ? str_replace(',', '', $request->effective_sum_insured) : 0;
        $ProspectRegister->cedant_comm_rate = $request->comm_rate;
        $ProspectRegister->cedant_comm_amount = $cede_comm_amt;
        $ProspectRegister->rein_comm_type = $request->reins_comm_type;
        $ProspectRegister->rein_comm_rate = $request->reins_comm_rate ? $request->reins_comm_rate : 0;
        $ProspectRegister->brokerage_comm_type = $request->brokerage_comm_type;
        $ProspectRegister->brokerage_comm_rate = $brokerage_comm_rate ? $brokerage_comm_rate : 0;
        $ProspectRegister->brokerage_comm_amt = $brokerage_comm_amt ? $brokerage_comm_amt : 0;
        $ProspectRegister->reinsurer_per_treaty = $request->reinsurer_per_treaty;
        $ProspectRegister->rein_comm_amount = $rein_comm_amt;
        $ProspectRegister->division_code = $request->division;
        $ProspectRegister->vat_charged = $request->vat_charged;
        $ProspectRegister->treaty_type = $request->treatytype;
        $ProspectRegister->cover_title = $treaty_name;
        $ProspectRegister->date_offered = $date_offered;
        $ProspectRegister->share_offered = (float)$share_offered ? $share_offered : 0;
        $ProspectRegister->port_prem_rate = (float)$request->port_prem_rate ? $request->port_prem_rate : 0;
        $ProspectRegister->port_loss_rate = (float)$request->port_loss_rate ? $request->port_loss_rate : 0;
        $ProspectRegister->profit_comm_rate = (float)$request->profit_comm_rate ? $request->profit_comm_rate : 0;
        $ProspectRegister->mgnt_exp_rate = (float)$request->mgnt_exp_rate ? $request->mgnt_exp_rate : 0;
        $ProspectRegister->deficit_yrs = (int)$request->deficit_yrs ? (int)$request->deficit_yrs : 0;
        $ProspectRegister->deposit_frequency = $request->deposit_frequency ? $request->deposit_frequency : 0;
        $ProspectRegister->prem_tax_rate = $request->prem_tax_rate ? $request->prem_tax_rate : 0;
        $ProspectRegister->ri_tax_rate = $request->ri_tax_rate ? $request->ri_tax_rate : 0;
        $ProspectRegister->risk_details = $request->risk_details;
        $ProspectRegister->status = 'A';
        $ProspectRegister->no_of_installments = (int) $request->no_of_installments;
        $ProspectRegister->updated_by = Auth::user()->user_name;
        $ProspectRegister->save();

        if ($request->type_of_bus == 'TNP') {
            foreach ($reinclass_code as $index => $reinclass) {
                $this->insertProspectReinClass($reinclass);
            }
        }

        if ($request->type_of_bus == 'TPR') {
            //Code to insert into coverreinprop table in a loop
            $treaty_reinclass = $request->treaty_reinclass;
            // Loop through one of the arrays (assuming they all have the same length)
            foreach ($treaty_reinclass as $index => $treaty_class) {

                $this->insertProspectReinClass($treaty_class);

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

                    $this->insertProspectReinProp($data);
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

                    $this->insertProspectReinProp($data);
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

                        $this->insertProspectReinProp($data);
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

                        $this->insertProspectReinProp($data);
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
                $ProspectPremtype = new ProspectPremtype();
                $ProspectRegister = Prospects::where('opportunity_id', $this->_opportunity_id)->first();

                $ProspectPremtypeModel = ProspectPremtype::where('opportunity_id', $this->_opportunity_id)
                    ->where('premtype_code', $prem_type_code[$index])
                    ->where('reinclass', $reinclass)
                    ->where('treaty', $prem_type_treaty[$index]);

                if ($ProspectPremtypeModel->count() > 0) {
                    $ProspectPremtype = $ProspectPremtypeModel->first();
                } else {
                    $ProspectPremtype = new ProspectPremtype();
                    $ProspectPremtype->pipeline_id = $ProspectRegister->pipeline_id;
                    $ProspectPremtype->opportunity_id = $ProspectRegister->opportunity_id;
                }
                $ProspectPremtype->reinclass = $reinclass;
                $ProspectPremtype->treaty = $prem_type_treaty[$index];
                $ProspectPremtype->premtype_code = $prem_type_code[$index];
                $ProspectPremtype->premtype_name = $premtype_reinclass->premtype_name;
                $ProspectPremtype->comm_rate = $prem_type_comm_rate[$index];
                $ProspectPremtype->save();
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

                $ProspectReinLayerModel = ProspectReinLayer::where('opportunity_id', $opportunity_id)
                    ->where('reinclass', $nonprop_reinclass[$index])
                    ->where('layer_no', $layer_no[$index]);
                if ($ProspectReinLayerModel->count() > 0) {
                    $ProspectReinLayer = $ProspectReinLayerModel->first();
                } else {
                    $ProspectReinLayer = new ProspectReinLayer();
                    $ProspectReinLayer->pipeline_id = $ProspectRegister->pipeline_id;
                    $ProspectReinLayer->opportunity_id = $opportunity_id;
                    $ProspectReinLayer->layer_no = $layer_no[$index];
                    $ProspectReinLayer->reinclass = $nonprop_reinclass[$index];
                }

                $ProspectReinLayer->indemnity_limit = (float)str_replace(',', '', $indemnity_limit) ?? 0;
                $ProspectReinLayer->underlying_limit = (float)str_replace(',', '', $underlying_limit[$index]) ?? 0;
                $ProspectReinLayer->egnpi = (float)str_replace(',', '', $egnpi[$index]);
                $ProspectReinLayer->method = $method;
                $ProspectReinLayer->payment_frequency = $payment_frequency;
                $ProspectReinLayer->reinstatement_type = $reinstatement_type[$index];
                $ProspectReinLayer->reinstatement_value = (float)str_replace(',', '', $reinstatement_value[$index]) ?? 0;
                $ProspectReinLayer->item_no = $item_no;

                if ($method == 'F') {
                    $ProspectReinLayer->flat_rate = (float)str_replace(',', '', $flat_rate[$index]) ?? 0;
                    $ProspectReinLayer->min_bc_rate = 0;
                    $ProspectReinLayer->max_bc_rate = (float)str_replace(',', '', $flat_rate[$index]) ?? 0;
                    $ProspectReinLayer->upper_adj =   (float)str_replace(',', '', $flat_rate[$index]) ?? 0;
                    $ProspectReinLayer->lower_adj =   0;
                } else {
                    $ProspectReinLayer->flat_rate = 0;
                    $ProspectReinLayer->min_bc_rate = (float)str_replace(',', '', $min_bc_rate[$index]) ?? 0;
                    $ProspectReinLayer->max_bc_rate = (float)str_replace(',', '', $max_bc_rate[$index]) ?? 0;
                    $ProspectReinLayer->upper_adj = (float)str_replace(',', '', $upper_adj[$index]) ?? 0;
                    $ProspectReinLayer->lower_adj = (float)str_replace(',', '', $lower_adj[$index]) ?? 0;
                }

                $ProspectReinLayer->min_deposit = (float)str_replace(',', '', $min_deposit[$index]) ?? 0;
                $ProspectReinLayer->save();
            }
        } elseif ($request->type_of_bus == 'FPR' || $request->type_of_bus == 'FNP') {

            $ProspectPremiumModel = ProspectPremium::where('opportunity_id', $opportunity_id)
                ->where('transaction_type', $ProspectRegister->transaction_type)
                ->where('class_code', $classcode)
                ->where('treaty', 'FAC')
                ->where('entry_type_descr', 'PRM');
            // dd($ProspectPremiumModel);
            if ($ProspectPremiumModel->count() > 0) {
                $ProspectPremium = $ProspectPremiumModel->first();
            } else {
                $ProspectPremium = new ProspectPremium();
                $ProspectPremium->pipeline_id = $pipeline_id;
                $ProspectPremium->opportunity_id = $opportunity_id;
                $ProspectPremium->orig_opportunity_id = $opportunity_id;
                $ProspectPremium->treaty = 'FAC';
                $ProspectPremium->quarter =  $this->_quarter;
                $ProspectPremium->entry_type_descr =  'PRM';
                $ProspectPremium->created_by = Auth::user()->user_name;
                $ProspectPremium->premtype_name =  'Gross Premium';
                $ProspectPremium->premium_type_description = 'Gross Premium';
                $ProspectPremium->type_of_bus =  $request->type_of_bus;
                $ProspectPremium->class_code =  $classcode;
                $ProspectPremium->premium_type_order_position = 1;
                $ProspectPremium->transaction_type =  $ProspectRegister->transaction_type;
                if ($ProspectPremium->transaction_type == 'RFN' || $ProspectPremium->transaction_type == 'CNC') {
                    $ProspectPremium->dr_cr = 'CR';
                } else {
                    $ProspectPremium->dr_cr = 'DR';
                }
            }
            //Gross Premium
            $ProspectPremium->premium_type_code =  0;
            $ProspectPremium->basic_amount =  $cede_premium;
            $ProspectPremium->apply_rate_flag =  'Y';
            $ProspectPremium->rate =  $share_offered;
            $ProspectPremium->final_amount =  ($share_offered / 100) * $cede_premium;
            $ProspectPremium->updated_by = Auth::user()->user_name;
            $ProspectPremium->save();

            $rate = $request->comm_rate;
            $cede_premium = ($share_offered / 100) * $cede_premium;
            $cede_comm_amt = ($rate / 100) * $cede_premium;

            //Commissions
            $ProspectPremiumModel = ProspectPremium::where('opportunity_id', $opportunity_id)
                ->where('transaction_type', $ProspectRegister->transaction_type)
                ->where('class_code', $classcode)
                ->where('treaty', 'FAC')
                ->where('entry_type_descr', 'COM');
            if ($ProspectPremiumModel->count() > 0) {
                $ProspectPremium = $ProspectPremiumModel->first();
            } else {
                $ProspectPremium = new PropectPremium();
                $ProspectPremium->pipeline_id = $pipeline_id;
                $ProspectPremium->opportunity_id = $opportunity_id;
                $ProspectPremium->orig_opportunity_id = $opportunity_id;
                $ProspectPremium->transaction_type =  $ProspectRegister->transaction_type;
                $ProspectPremium->quarter =  $this->_quarter;
                $ProspectPremium->entry_type_descr =  'COM';
                $ProspectPremium->premium_type_description = 'Commission';
                $ProspectPremium->type_of_bus =  $request->type_of_bus;
                $ProspectPremium->class_code =  $classcode;
                $ProspectPremium->treaty = 'FAC';
                if ($ProspectPremium->transaction_type == 'RFN' || $ProspectPremium->transaction_type == 'CNC') {
                    $ProspectPremium->dr_cr = 'DR';
                } else {
                    $ProspectPremium->dr_cr = 'CR';
                }
                $ProspectPremium->premtype_name =  'Commission';
                $ProspectPremium->created_by = Auth::user()->user_name;
                $ProspectPremium->created_at = Carbon::now();
            }

            $ProspectPremium->premium_type_code =  0;
            $ProspectPremium->premium_type_order_position = 2;
            $ProspectPremium->basic_amount =  $cede_premium;
            $ProspectPremium->apply_rate_flag =  'Y';
            $ProspectPremium->rate =  $rate;
            $ProspectPremium->final_amount =  $cede_comm_amt;
            $ProspectPremium->updated_by = Auth::user()->user_name;
            $ProspectPremium->save();
        }

        //Cover Installments Edit
        $paymethods = PayMethod::all();
        $selected_pay_method = collect($paymethods)->first(
            fn($item) => $item->pay_method_code == $request->pay_method,
        );
        $installmentData = [
            'pipeline_id'         => $ProspectRegister->pipeline_id,
            'opportunity_id'   => $ProspectRegister->opportunity_id,
            'layer_no'         => 0,
            'trans_type'       => $ProspectRegister->type_of_bus,
            'entry_type'       => $ProspectRegister->transaction_type,
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
                        ['opportunity_id', '=', $ProspectRegister->opportunity_id],
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
                            ['opportunity_id', '=', $ProspectRegister->opportunity_id],
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
            $inst = PropectInstallments::where([
                'opportunity_id' => $ProspectRegister->opportunity_id,
                'dr_cr' => 'DR'
            ])->get();
            if (count($inst) > 1) {
                ProspectInstallments::where('id', '!=', $inst->first()->id)->delete();
            }
        }

        return (object) ['opportunity_id' => $ProspectRegister->opportunity_id];
    }

    public function insertProspectReinClass($reinclass)
    {
        $ProspectRegister = Prospects::where('opportunity_id', $opportunity_id)->first();
        $ProspectReinclass = ProspectReinclass::where('opportunity_id', $opportunity_id)
            ->where('reinclass', $reinclass);
        if ($ProspectReinclass->count() > 0) {
            $ProspectReinclass = $ProspectReinclass->first();
        } else {
            $ProspectReinclass = new ProspectReinclass();
            $ProspectReinclass->created_by = Auth::user()->user_name;
            $ProspectReinclass->pipeline_id = $ProspectRegister->pipeline_id;
            $ProspectReinclass->opportunity_id = $opportunity_id;
        }
    }
}
