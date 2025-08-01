<?php

namespace App\Models\Bd;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class BdReinprop extends Model
{
    use HasFactory;
    protected $table = 'bd_reinprops';
    public $timestamps = true;
    public $primaryKey ='id';
    public $incrementing = true;
    protected $guarded = [];

    protected $fillable = [
        'cover_no',
        'endorsement_no',
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
    ];

}
