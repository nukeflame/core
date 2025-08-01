<?php

namespace App\Models;

use App\Http\Traits\ModelCompositeKey;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class ClaimAckParams extends Model
{
    use HasFactory, SoftDeletes, ModelCompositeKey;


    protected $table = 'claim_ack_params';
    public $timestamps = true;
    protected $guarded = [];

    public function classGroup()
    {
        return $this->belongsTo(ClassGroup::class, 'class_group', 'group_code');
    }
}
