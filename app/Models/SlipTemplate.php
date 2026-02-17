<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class SlipTemplate extends Model
{
    use HasFactory;

    protected $table = 'slip_templates';

    public $timestamps = true;
    public $primaryKey = 'id';
    public $incrementing = true;
    protected $guarded = [];

    public function classGroup(): BelongsTo
    {
        return $this->belongsTo(ClassGroup::class, 'class_group_code', 'group_code');
    }

    public function businessClass(): BelongsTo
    {
        return $this->belongsTo(Classes::class, 'class_code', 'class_code');
    }
}
