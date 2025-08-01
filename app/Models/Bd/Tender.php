<?php

namespace App\Models\Bd;

use App\Models\Bd\Leads\TenderApproval;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Tender extends Model
{
    use HasFactory;
    // protected $connection = 'mysql'; 
    protected $table = 'tenders';
    public $timestamps = true;

    // public $primaryKey = ['bank_code'];
    protected $primaryKey = 'id'; 
    public $incrementing = true;
   
    protected $guarded = [];

  
    public function tender_approval()
    {
        return $this->hasOne(TenderApproval::class, 'tender_no', 'tender_no');
    }
    
}
