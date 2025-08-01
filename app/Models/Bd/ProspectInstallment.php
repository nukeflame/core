<?php
namespace App\Models\Bd;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ProspectInstallment extends Model
{
    use HasFactory;

    protected $table = 'prospect_installments';
    
    protected $fillable = [
        'pipeline_id',
        'opportunity_id',
        'layer_no',
        'trans_type',
        'entry_type',
        'installment_no',
        'installment_date',
        'installment_amt',
        'dr_cr',
        'created_by',
        'updated_by',
    ];
}
