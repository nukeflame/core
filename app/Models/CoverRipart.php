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

    /**
     * Get the partner that owns the CoverRipart
     *
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function partner(): BelongsTo
    {
        return $this->belongsTo(Customer::class, 'partner_no', 'customer_id');
    }

    /**
     * Get the contacts for the CoverRipart through the partner
     *
     *
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function contacts(): HasMany
    {
        return $this->hasMany(CustomerContact::class, 'customer_id', 'partner_no');
    }

    /**
     * Get the claim debits for the CoverRipart
     *
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function debits(): HasMany
    {
        // Assuming claim_debit table has endorsement_no and tran_no columns
        return $this->hasMany(ClaimDebit::class, ['endorsement_no', 'cover_no'], ['endorsement_no', 'cover_no']);
    }

    /**
     * Get the claim debits for the CoverRipart
     *
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function claim_documents(): HasMany
    {
        return $this->hasMany(ClaimDocs::class, ['endorsement_no', 'cover_no'], ['endorsement_no', 'tran_no']);
    }
}
