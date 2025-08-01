<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class CoverInstallments extends Model
{
    use HasFactory, SoftDeletes;

    protected $table = 'cover_installments';
    protected $guarded = [];

    // protected $fillable = [
    //     'id',
    //     'cover_no',
    //     'endorsement_no',
    //     'layer_no',
    //     'trans_type',
    //     'entry_type',
    //     'installment_no',
    //     'installment_date',
    //     'installment_amt',
    //     'dr_cr',
    //     'partner_no',
    //     'created_by',
    //     'updated_by'
    // ];
}
