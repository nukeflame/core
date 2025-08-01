<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\SoftDeletes;

class ClaimDebit extends Model
{
    use HasFactory, SoftDeletes;

    protected $table = 'claim_debit';
    public $timestamps = true;
    public $primaryKey = ['id'];
    public $incrementing = false;
    protected $guarded = [];

    /**
     * Get the cover ripart that owns the ClaimDebit
     *
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    // public function reinsurer(): BelongsTo
    // {
    //     // Assuming claim_debit table has endorsement_no and tran_no columns
    //     // that match the composite key of coverripart table
    //     return $this->belongsTo(CoverRipart::class, ['endorsement_no', 'tran_no'], ['endorsement_no', 'tran_no']);
    // }
}
