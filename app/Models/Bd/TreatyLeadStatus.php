<?php

namespace App\Models\Bd;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class TreatyLeadStatus extends Model
{
    use HasFactory;
    protected $table = 'treaty_lead_status';
    protected $primaryKey = 'lead_id'; 

    public $incrementing = true;

    protected $fillable = [
        'lead_id',
        'id',
        'status_name',
        'category_type',
    ];

    public $timestamps = true; 
}
