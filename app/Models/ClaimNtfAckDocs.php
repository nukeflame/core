<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class ClaimNtfAckDocs extends Model
{
    use HasFactory;

    protected $table = 'claim_ntf_ack_docs';

    protected $guarded = [];

    /**
     * Get the ack_params that owns the ClaimAckDocs
     *
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function document(): BelongsTo
    {
        return $this->belongsTo(ClaimAckParams::class, 'doc_id', 'id');
    }
}
