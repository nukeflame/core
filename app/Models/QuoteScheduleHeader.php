<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class QuoteScheduleHeader extends Model
{
    use HasFactory;
    protected $table = 'quote_schedule_headers';
    protected $primaryKey = 'id';
    protected $fillable = [

        'name',
        'position',
        'amount_field',
        'sum_insured_type',
        'data_determinant',
        'class',
        'class_group',
        'business_type',
        'created_at',
        'updated_at',
    ];
}
