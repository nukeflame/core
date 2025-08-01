<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\SoftDeletes;

class CoverClass extends Model
{
    use HasFactory, SoftDeletes;

    protected $table = 'cover_classes';
    public $timestamps = true;
    public $primaryKey = ['cover_no', 'class'];
    public $incrementing = false;
    protected $guarded = [];
    protected $casts = [
        'class' => 'string'
    ];

    /**
     * Get the class that owns the CoverClass
     *
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function insurance_class(): BelongsTo
    {
        return $this->belongsTo(Classes::class, 'class', 'class_code');
    }

    /**
     * Get the class that owns the CoverClass
     *
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function ri_class(): BelongsTo
    {
        return $this->belongsTo(ReinsClass::class, 'reinclass', 'class_code');
    }
}
