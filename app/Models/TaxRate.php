<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class TaxRate extends Model
{
    use HasFactory;
    // protected $connection = 'mysql';
    protected $table = 'tax_rates';
    public $timestamps = false;
    public $primaryKey = 'group_id';
    public $incrementing = false;
    protected $guarded = [];
}
