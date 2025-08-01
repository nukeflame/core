<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class ClaimNtfRegister extends Model
{
    use HasFactory;

    protected $table = 'claim_ntf_register';
    public $timestamps = true;
    public $primaryKey = 'intimation_no';
    public $incrementing = false;
    protected $guarded = [];

    public function customer(): BelongsTo
    {
        return $this->belongsTo(Customer::class, 'customer_id', 'customer_id');
    }
}
