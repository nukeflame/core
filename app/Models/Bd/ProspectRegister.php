<?php
namespace App\Models\Bd;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ProspectRegister extends Model
{
    use HasFactory;

    protected $table = 'prospect_registers';

    protected $fillable = [
        'pipeline_id',
        'opportunity_id',
        'branch_code',
        'broker_code',
        'cover_type',
        'class_code',
        'class_group_code',
        'insured_name',
        'effective_date',
        'cover_from',
        'cover_to',
        'account_year',
        'account_month',
        'binder_cov_no',
        'pay_method_code',
        'currency_code',
        'currency_rate',
        'type_of_sum_insured',
        'rein_premium',
        'total_sum_insured',
        'cedant_premium',
        'apply_eml',
        'eml_rate',
        'eml_amount',
        'effective_sum_insured',
        'cedant_comm_rate',
        'cedant_comm_amount',
        'rein_comm_type',
        'rein_comm_rate',
        'brokerage_comm_rate',
        'brokerage_comm_amt',
        'brokerage_comm_type',
        'reinsurer_per_treaty',
        'rein_comm_amount',
        'division_code',
        'vat_charged',
        'treaty_type',
        'risk_details',
        'cover_title',
        'date_offered',
        'share_offered',
        'no_of_installments',
        'port_prem_rate',
        'port_loss_rate',
        'profit_comm_rate',
        'mgnt_exp_rate',
        'deficit_yrs',
        'deposit_frequency',
        'prem_tax_rate',
        'ri_tax_rate',
        'status',
        'verified',
        'created_by',
        'updated_by',
    ];
}


