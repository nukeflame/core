<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class GLTransaction extends Model
{
    // use HasFactory;

    protected $table = 'gltransactions';
    public $timestamps = false;
    public $primaryKey = ['source_code', 'offcd', 'doc_type', 'entry_type_descr', 'reference_no', 'line_no', 'item_no'];
    public $incrementing = false;
    protected $guarded = [];
}
