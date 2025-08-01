<?php

namespace App\Models\Bd;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ProspectReinclass extends Model
{
    use HasFactory;

    /**
     * The table associated with the model.
     *
     * @var string
     */
    protected $table = 'prospect_reinclasses';

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'pipeline_id',
        'opportunity_id',
        'reinclass',
        'created_by',
        'updated_by',
    ];

    /**
     * Disable timestamps if not needed.
     */
    public $timestamps = true;

    // Add relationships if needed, e.g., pipeline or opportunity
}
