<?php

namespace App\Models;

use App\Http\Traits\ModelCompositeKey;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class WhtRate extends Model
{
    use HasFactory, SoftDeletes, ModelCompositeKey;

    protected $table = 'wht_rates';
    public $timestamps = true;
    public $primaryKey = 'id';
    public $incrementing = false;
    protected $guarded = [];
}
