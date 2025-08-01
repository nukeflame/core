<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class ClaimNtfStatus extends Model
{
    use HasFactory;

    protected $table='claim_ntf_status';
	public $timestamps=true;
	public $primaryKey=['intimation_no','id'];
	public $incrementing=false;
    protected $guarded = [];

    /**
     * Get the status that owns the ClaimStatus
     *
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function statusReason(): BelongsTo
    {
        return $this->belongsTo(ClaimStatusParam::class,'status_id','id');
    }
}
