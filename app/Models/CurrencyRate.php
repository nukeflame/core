<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class CurrencyRate extends Model
{
    use HasFactory;
    // protected $connection = 'mysql';
    protected $table = 'currency_rate';
    public $timestamps = false;
    public $primaryKey = 'currency_code';
    public $incrementing = false;
    protected $guarded = [];
}
