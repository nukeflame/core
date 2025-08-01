<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class CustomerAccDet extends Model
{
    use HasFactory, SoftDeletes;

    protected $table = 'customeracc_det';
    public $timestamps = false;
    public $primaryKey = ['source_code', 'doc_type', 'entry_type_descr', 'reference', 'account_year', 'account_month', 'line_no'];
    public $incrementing = false;
    protected $guarded = [];
}
