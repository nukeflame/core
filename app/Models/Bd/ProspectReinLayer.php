<?php

namespace App\Models\Bd;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ProspectReinLayer extends Model
{
    use HasFactory;

    protected $table = 'prospect_rein_layers';

    protected $fillable = [
        'pipeline_id',
        'opportunity_id',
        'layer_no',
        'indemnity_limit',
        'underlying_limit',
        'egnpi',
        'method',
        'payment_frequency',
        'reinclass',
        'reinstatement_type',
        'reinstatement_value',
        'item_no',
        'flat_rate',
        'min_bc_rate',
        'max_bc_rate',
        'upper_adj',
        'lower_adj',
        'min_deposit',
    ];
}
