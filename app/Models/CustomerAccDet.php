<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class CustomerAccDet extends Model
{
    use HasFactory, SoftDeletes;

    protected $table = 'customeracc_det';
    public $timestamps = false;
    public $primaryKey = ['source_code', 'doc_type', 'entry_type_descr', 'reference', 'account_year', 'account_month', 'line_no'];
    public $incrementing = false;
    protected $guarded = [];

    protected $casts = [
        'premium_levy' => 'decimal:12',
        'reinsurance_levy' => 'decimal:12',
        'withholding_tax' => 'decimal:12',
        'port_prem_rate' => 'decimal:4',
        'port_premium_amt' => 'decimal:2',
        'port_loss_rate' => 'decimal:4',
        'profit_comm_rate' => 'decimal:4',
        'mgnt_exp_rate' => 'decimal:4',
        'port_outstanding_loss_amt' => 'decimal:2',
        'port_prem_amt' => 'decimal:2',
        'port_loss_amt' => 'decimal:2',
        'posting_date' => 'date',
        'show_cedant' => 'boolean',
        'show_reinsurer' => 'boolean',
        'compute_premium_tax' => 'boolean',
        'compute_reinsurance_tax' => 'boolean',
        'compute_withholding_tax' => 'boolean',
        'loss_participation' => 'boolean',
        'sliding_commission' => 'boolean',
    ];
}
