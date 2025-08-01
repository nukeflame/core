<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class TaxType extends Model
{
    use HasFactory;
    // protected $connection = 'mysql';
    protected $table = 'tax_types';
    public $timestamps = false;
    public $primaryKey = 'tax_type';
    public $incrementing = false;
    protected $guarded = [];
}
