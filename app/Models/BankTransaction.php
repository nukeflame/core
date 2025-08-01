<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class BankTransaction extends Model
{
    // use HasFactory;
    // protected $connection = 'mysql'; 
    protected $table = 'banktransactions';
    public $timestamps = true;
    public $primaryKey=['source', 'doc_type','reference_no', 'batch_no','item_no', 'bank_acc_code'];
    public $incrementing = false;
    protected $guarded = [];
}
