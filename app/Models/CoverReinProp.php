<?php

namespace App\Models;

use App\Http\Traits\ModelCompositeKey;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\SoftDeletes;

class CoverReinProp extends Model
{
    use HasFactory, ModelCompositeKey, SoftDeletes;

    protected $table = 'coverreinprop';

    public $timestamps = true;
    public $primaryKey = ['cover_no', 'endorsement_no', 'reinclass', 'item_no'];
    public $incrementing = false;
    protected $guarded = [];
}
