<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class TaxType extends Model
{
    use HasFactory;
    protected $table = 'tax_types';
    public $timestamps = false;
    public $primaryKey = 'id';
    public $incrementing = true;
    protected $guarded = [];
}
