<?php

namespace App\Models\Bd;

use App\Models\Customer;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ReinsurersDeclined extends Model
{
    use HasFactory;
    protected $table = 'reinsurers_declined';
    protected $primaryKey = 'id';
    protected $fillable = [
        'customer_id',
        'reason',
        'opportunity_id',
        'decline_unchecked_count'
    ];
    public function customer_name()
    {
        return $this->belongsTo(Customer::class, 'customer_id', 'customer_id');
    }
}
