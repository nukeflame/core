<?php

namespace App\Models;

use App\Http\Traits\ModelCompositeKey;
use App\Models\Bd\CustomerContact;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class CoverRipart extends Model
{
    use HasFactory, SoftDeletes, ModelCompositeKey;

    protected $table = 'coverripart';
    public $timestamps = true;
    protected $primaryKey = ['endorsement_no', 'tran_no'];
    public $incrementing = false;
    protected $guarded = [];

    public function partner(): BelongsTo
    {
        return $this->belongsTo(Customer::class, 'partner_no', 'customer_id');
    }

    public function contacts(): HasMany
    {
        return $this->hasMany(CustomerContact::class, 'customer_id', 'partner_no');
    }

    public function debits(): HasMany
    {
        return $this->hasMany(ClaimDebit::class, ['endorsement_no', 'cover_no'], ['endorsement_no', 'cover_no']);
    }

    public function claim_documents(): HasMany
    {
        return $this->hasMany(ClaimDocs::class, ['endorsement_no', 'cover_no'], ['endorsement_no', 'tran_no']);
    }
}
