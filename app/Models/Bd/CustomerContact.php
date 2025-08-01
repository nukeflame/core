<?php

namespace App\Models\Bd;

use App\Models\Customer;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class CustomerContact extends Model
{
    use HasFactory;
    protected $table = 'customer_contacts';
    protected $fillable = [
        'customer_id',
        'contact_name',
        'contact_position',
        'contact_mobile_no',
        'contact_email',
        'is_primary',
        'order'
    ];

    protected $casts = [
        'is_primary' => 'boolean'
    ];

    /**
     * Get the customer that owns this contact
     */
    public function customer(): BelongsTo
    {
        return $this->belongsTo(Customer::class, 'customer_id', 'customer_id');
    }
}
