<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class ClaimStatus extends Model
{
    use HasFactory,SoftDeletes;

    protected $table='claim_status';
	public $timestamps=true;
	public $primaryKey=['claim_no','id'];
	public $incrementing=false;
    protected $guarded = [];

    /**
     * Get the status that owns the ClaimStatus
     *
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function status(): BelongsTo
    {
        return $this->belongsTo(ClaimStatusParam::class,'status_id','id');
    }
}
