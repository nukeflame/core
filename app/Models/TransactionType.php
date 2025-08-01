<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class TransactionType extends Model
{
    // use HasFactory;
    // protected $connection = 'mysql'; 
    protected $table = 'transaction_types';
    public $timestamps = true;
    public $primaryKey=['type_of_bus','transaction_type'];
    public $incrementing = false;
    protected $guarded = [];
}
