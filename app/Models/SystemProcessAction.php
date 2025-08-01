<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\SoftDeletes;

class SystemProcessAction extends Model
{
    use HasFactory, SoftDeletes;

    protected $table = 'system_process_action';
    // protected $guarded = [];

    protected $fillable = [
        'id',
        'process_id',
        'name',
        'nice_name',
        'created_by',
        'updated_by',
        'description',
        'status',
        'action_type',
        'module',
        'performed_by',
        'performed_at',
        'created_at',
        'updated_at',
        'scheduled_at'
    ];

    protected $dates = ['performed_at'];

    /**
     * Get the process that owns the SystemProcessAction
     *
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function process(): BelongsTo
    {
        return $this->belongsTo(SystemProcess::class, 'process_id', 'id');
    }

    public function performedBy(): BelongsTo
    {
        return $this->belongsTo(User::class, 'performed_by');
    }

    public function scopeSuccessful($query)
    {
        return $query->where('status', 'success');
    }

    public function scopeFailed($query)
    {
        return $query->where('status', 'failed');
    }
}
