<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class PremiumPayTerm extends Model
{
    use HasFactory;
    // protected $connection = 'mysql'; 
    protected $table = 'premium_pay_terms';
    public $timestamps = true;
    public $primaryKey = 'pay_term_code';
    public $incrementing = false;
    protected $guarded = [];
}
