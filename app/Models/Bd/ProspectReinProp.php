<?php

namespace App\Models\Bd;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ProspectReinProp extends Model
{
    use HasFactory;

    protected $table = 'prospect_reinprops';

    protected $fillable = [
        'pipeline_id',
        'opportunity_id',
        'reinclass',
        'item_no',
        'item_description',
        'retention_rate',
        'treaty_rate',
        'retention_amount',
        'no_of_lines',
        'treaty_amount',
        'treaty_limit',
        'port_prem_rate',
        'port_loss_rate',
        'profit_comm_rate',
        'mgnt_exp_rate',
        'deficit_yrs',
        'estimated_income',
        'cashloss_limit',
        'created_by',
        'updated_by',
    ];
}
