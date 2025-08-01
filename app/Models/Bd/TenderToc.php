<?php

namespace App\Models\Bd;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class TenderToc extends Model
{
    use HasFactory;
    // protected $connection = 'mysql'; 
    protected $table = 'tender_toc';
    public $timestamps = true;
    public $primaryKey = ['tender_no', 'tender_name', 'toc_no'];
    public $incrementing = false;
    protected $guarded = [];
}
