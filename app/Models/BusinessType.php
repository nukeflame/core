<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class BusinessType extends Model
{
    use HasFactory;

    protected $table = 'business_types';
    public $timestamps = false;
    public $primaryKey = 'bus_type_id';
    public $incrementing = false;
    protected $guarded = [];
}
