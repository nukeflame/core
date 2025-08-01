<?php

namespace App\Models;

use App\Http\Traits\ModelCompositeKey;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ClaimNtfPeril extends Model
{
    use HasFactory, ModelCompositeKey;

    protected $table = 'claim_ntf_perils';
    public $timestamps = true;
    public $primaryKey = ['intimation_no', 'id'];
    public $incrementing = false;
    protected $guarded = [];
}
