<?php

namespace App\Models;

use App\Models\Bd\CustomerContact;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Customer extends Model
{
    use HasFactory;

    protected $table = 'customers';
    public $timestamps = false;
    public $primaryKey = 'customer_id';
    public $incrementing = false;
    protected $guarded = [];

    protected $casts = [
        'customer_type' => 'array'
    ];

    /* Get the country that owns the Customer
     *
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function country(): BelongsTo
    {
        return $this->belongsTo(Country::class, 'country_iso', 'country_iso');
    }

    public function customerType()
    {
        return $this->belongsTo(CustomerTypes::class, 'customer_type', 'type_id');
    }

    /**
     * Get all contacts for this customer
     */
    public function contacts(): HasMany
    {
        return $this->hasMany(CustomerContact::class, 'customer_id', 'customer_id');
    }

    /**
     * Get the primary contact for this customer
     */
    public function primaryContact()
    {
        return $this->hasOne(CustomerContact::class, 'customer_id', 'customer_id')
            ->where('is_primary', true);
    }
}
