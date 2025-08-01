<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;

class CoverRegister extends Model
{
    use SoftDeletes;

    protected $table = 'cover_register';
    public $timestamps = true;
    public $primaryKey = 'endorsement_no';
    public $incrementing = false;
    protected $guarded = [];

    protected $casts = [
        'cover_from' => 'date',
        'cover_to' => 'date',
    ];
    /**
     * Get the customer that owns the CoverRegister
     *
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function customer(): BelongsTo
    {
        return $this->belongsTo(Customer::class, 'customer_id', 'customer_id');
    }

    public function treaty()
    {
        return $this->hasOne(TreatyType::class, 'treaty_code', 'treaty_type');
    }

    public function riClasses(): HasMany
    {
        return $this->hasMany(CoverReinclass::class, 'endorsement_no', 'endorsement_no');
    }


    public function uwClasses(): HasMany
    {
        return $this->hasMany(CoverClass::class, 'endorsement_no', 'endorsement_no');
    }

    // public function reinsurers()
    // {
    //     return $this->hasMany(CoverRipart::class, 'endorsement_no', 'endorsement_no')
    //         ->where('cover_no', $this->cover_no);
    // }
}
