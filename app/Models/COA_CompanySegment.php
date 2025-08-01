<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class COA_CompanySegment extends Model
{
    // use HasFactory;
    // protected $connection = 'mysql'; 
    protected $table='coa_company_segments';
	public $timestamps=false;
	public $primaryKey=['segment_code'];
	public $incrementing=false;
    protected $guarded = [];
}
