<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class CashBookana extends Model
{
    // use HasFactory;
    // protected $connection = 'mysql'; 
    protected $table = 'cashbookana';
    public $timestamps = false; 
    public $primaryKey=['offcd', 'doc_type', 'cbtrans_type_code','reference_no','line_no','item_no'];
    public $incrementing = false;
    protected $guarded = [];
}
