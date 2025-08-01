<?php

namespace App\Models;

use App\Http\Traits\ModelCompositeKey;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class CoverPremium extends Model
{
    use HasFactory, ModelCompositeKey, SoftDeletes;

    protected $table = 'cover_premiums';
    public $timestamps = true;
    public $primaryKey = ['cover_no', 'endorsement_no', 'class_code', 'transaction_type', 'premium_type_code', 'entry_type_descr', 'layer_no', 'installment_no'];
    public $incrementing = false;
    protected $guarded = [];
}
