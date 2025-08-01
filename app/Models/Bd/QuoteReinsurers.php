<?php

namespace App\Models\Bd;

use App\Models\Customer;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class QuoteReinsurers extends Model
{
    use HasFactory;
    protected $table = 'quote_reinsurers';
    protected $primaryKey = 'id';
    protected $fillable = [
        'reinsurer_id',
        'email',
        'contact_name'



    ];
    public function reinsurer()
    {
        return $this->belongsTo(Customer::class, 'reinsurer_id', 'customer_id');
    }
    public function quote()
    {
        return $this->belongsTo(Quote::class, 'quote_id');
    }


}
