<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class CashBook extends Model
{
    // use HasFactory;
    // protected $connection = 'mysql'; 
    protected $table = 'cashbook';
    public $timestamps = false;
    public $primaryKey=['offcd','doc_type','cbtrans_type_code','transaction_no','account_year','account_month','line_no'];
    public $incrementing = false;
    protected $guarded = [];
}
