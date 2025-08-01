<?php

namespace App\Models;

use App\Http\Traits\ModelCompositeKey;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class CoverReinInstallment extends Model
{
    use HasFactory, SoftDeletes, ModelCompositeKey;

    protected $table = 'rein_installments';
    public $timestamps = true;
    public $primaryKey = ['cover_no', 'endorsement_no', 'installment_no', 'layer_no', 'partner_no'];
    public $incrementing = false;
    protected $guarded = [];
}
