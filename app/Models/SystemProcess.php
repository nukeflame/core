<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;

class SystemProcess extends Model
{
    use HasFactory, SoftDeletes;

    protected $table = 'system_process';
    protected $guarded = [];

    protected $fillable = [
        'id',
        'description',
        'status',
        'priority',
        'permission_id',
        'completed_at',
        'name',
        'nice_name',
        'created_by',
        'initiated_by',
        'updated_by',
        'execution_type',
        'parameters',
        'started_at',
        'created_at',
        'updated_at',
    ];

    protected $dates = [
        'created_at',
        'completed_at'
    ];

    /**
     * Get all of the actions for the SystemProcess
     *
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function actions(): HasMany
    {
        return $this->hasMany(SystemProcessAction::class, 'process_id', 'id');
    }

    public function permissions(): BelongsToMany
    {
        return $this->belongsToMany(Permission::class, 'system_process_permissions')->withTimestamps();
    }

    public function initiatedBy()
    {
        return $this->belongsTo(User::class, 'initiated_by');
    }

    public function scopeActive($query)
    {
        return $query->whereNull('completed_at');
    }

    public function scopeCompleted($query)
    {
        return $query->whereNotNull('completed_at');
    }
}
