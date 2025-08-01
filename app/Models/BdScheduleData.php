<?php

namespace App\Models;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class BdScheduleData extends Model
{
    use HasFactory;
   
    protected $table = 'bd_schedule_template_data';
    public $timestamps = true;
    public $primaryKey = 'clause_id';
    
    public $incrementing = true;
  
    protected $guarded = [];
 




}
