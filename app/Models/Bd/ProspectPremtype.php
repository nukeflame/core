<?php

namespace App\Models\Bd;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ProspectPremtype extends Model
{
    use HasFactory;

    protected $table = 'prospect_premtypes';  
    protected $fillable = [
        'pipeline_id',
        'opportunity_id',
        'reinclass',
        'treaty',
        'premtype_code',
        'premtype_name',
        'comm_rate',
        'created_by',
        'updated_by',
    ];

    // Relationships can be added here if applicable, for example:
    // public function pipeline()
    // {
    //     return $this->belongsTo(Pipeline::class);
    // }
}
