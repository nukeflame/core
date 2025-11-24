<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Support\Facades\Auth;

class ApprovalSourceLink extends Model
{
    use HasFactory;


    protected $table = 'approval_source_link';

    protected $primaryKey = 'id';

    /**
     * Indicates if the model's ID is auto-incrementing.
     *
     * @var bool
     */
    public $incrementing = false;

    /**
     * The data type of the auto-incrementing ID.
     *
     * @var string
     */
    protected $keyType = 'int';

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'id',
        'approval_id',
        'process_id',
        'process_action',
        'source_table',
        'source_column_name',
        'source_column_data',
        'source_approval_column',
        'source_approval_by_column',
        'source_approval_at_column',
        'created_by',
        'updated_by',
    ];

    /**
     * The attributes that should be cast.
     *
     * @var array<string, string>
     */
    protected $casts = [
        'id' => 'integer',
        'approval_id' => 'integer',
        'process_id' => 'integer',
        'process_action' => 'integer',
        'created_at' => 'datetime',
        'updated_at' => 'datetime',
    ];

    /**
     * Bootstrap the model and its traits.
     *
     * @return void
     */
    protected static function boot()
    {
        parent::boot();

        // Auto-set created_by and updated_by
        static::creating(function ($model) {
            if (Auth::check()) {
                $user = Auth::user();
                $model->created_by = $model->created_by ?? ($user->user_name ?? $user->name);
                $model->updated_by = $model->updated_by ?? ($user->user_name ?? $user->name);
            }
        });

        static::updating(function ($model) {
            if (Auth::check()) {
                $user = Auth::user();
                $model->updated_by = $user->user_name ?? $user->name;
            }
        });
    }

    /*
    |--------------------------------------------------------------------------
    | Relationships
    |--------------------------------------------------------------------------
    */

    /**
     * Get the approval that owns this source link
     *
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function approval(): BelongsTo
    {
        return $this->belongsTo(ApprovalsTracker::class, 'approval_id', 'id');
    }

    /**
     * Get the system process associated with this link
     *
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function process(): BelongsTo
    {
        return $this->belongsTo(SystemProcess::class, 'process_id', 'id');
    }

    /**
     * Get the process action associated with this link
     *
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function processAction(): BelongsTo
    {
        return $this->belongsTo(SystemProcessAction::class, 'process_action', 'id');
    }

    /*
    |--------------------------------------------------------------------------
    | Scopes
    |--------------------------------------------------------------------------
    */

    /**
     * Scope a query to only include links for a specific approval
     *
     * @param \Illuminate\Database\Eloquent\Builder $query
     * @param int $approvalId
     * @return \Illuminate\Database\Eloquent\Builder
     */
    public function scopeForApproval($query, int $approvalId)
    {
        return $query->where('approval_id', $approvalId);
    }

    /**
     * Scope a query to only include links for a specific source table
     *
     * @param \Illuminate\Database\Eloquent\Builder $query
     * @param string $table
     * @return \Illuminate\Database\Eloquent\Builder
     */
    public function scopeFromTable($query, string $table)
    {
        return $query->where('source_table', $table);
    }

    /**
     * Scope a query to find a specific source record
     *
     * @param \Illuminate\Database\Eloquent\Builder $query
     * @param string $table
     * @param string $column
     * @param mixed $value
     * @return \Illuminate\Database\Eloquent\Builder
     */
    public function scopeSourceRecord($query, string $table, string $column, $value)
    {
        return $query->where('source_table', $table)
            ->where('source_column_name', $column)
            ->where('source_column_data', $value);
    }

    /*
    |--------------------------------------------------------------------------
    | Helper Methods
    |--------------------------------------------------------------------------
    */

    /**
     * Get the approval columns as an array
     *
     * @return array
     */
    public function getApprovalColumns(): array
    {
        return array_map('trim', explode(',', $this->source_approval_column));
    }

    /**
     * Check if source has approval by column
     *
     * @return bool
     */
    public function hasApprovalByColumn(): bool
    {
        return !empty($this->source_approval_by_column);
    }

    /**
     * Check if source has approval at column
     *
     * @return bool
     */
    public function hasApprovalAtColumn(): bool
    {
        return !empty($this->source_approval_at_column);
    }

    /**
     * Get formatted source identifier
     *
     * @return string
     */
    public function getSourceIdentifier(): string
    {
        return "{$this->source_table}.{$this->source_column_name} = {$this->source_column_data}";
    }

    /**
     * Check if this link has pending approval in source
     *
     * @return bool
     */
    public function isPendingInSource(): bool
    {
        return $this->approval && $this->approval->is_pending;
    }
}
