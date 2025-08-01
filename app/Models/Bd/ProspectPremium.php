<?php

namespace App\Models\Bd;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ProspectPremium extends Model
{
    use HasFactory;

 
    protected $table = 'prospect_premiums';

    protected $fillable = [
        'pipeline_id',
        'opportunity_id',
        'orig_opportunity_id',
        'transaction_type',
        'premium_type_code',
        'premtype_name',
        'quarter',
        'entry_type_descr',
        'premium_type_order_position',
        'premium_type_description',
        'type_of_bus',
        'class_code',
        'basic_amount',
        'apply_rate_flag',
        'treaty',
        'rate',
        'dr_cr',
        'final_amount',
        'created_by',
        'updated_by',
        'created_at',
        'updated_at',
    ];

    // Automatically manage the timestamps
    public $timestamps = true;

    // Add custom logic if necessary, e.g., mutators, accessors, or relationships
}
