<?php

namespace App\Models;

use App\Http\Traits\ModelCompositeKey;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\SoftDeletes;

class CoverPremtype extends Model
{
    use HasFactory, ModelCompositeKey, SoftDeletes;

    protected $table = 'cover_premtypes';

    public $timestamps = false;
    public $primaryKey = ['cover_no', 'endorsement_no', 'reinclass', 'premtype_code'];
    public $incrementing = false;
    protected $guarded = [];

    public function treaty_dtl(): BelongsTo
    {
        return $this->belongsTo(TreatyType::class, 'treaty', 'treaty_code');
    }

    /**
     * Get the premiumType that owns the CoverPremtype
     *
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function premiumType(): BelongsTo
    {
        return $this->belongsTo(ReinclassPremtype::class, 'reinclass', 'reinclass');
    }
}
