<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Support\Facades\Auth;

class Notification extends Model
{
    use HasFactory, SoftDeletes;

    protected $with = ['creator', 'users', 'read_status'];
    protected $table = 'notifications';
    protected $appends = ['is_read'];
    protected $guarded = [];
    // protected $fillabl = ['notification_type'];

    public static function boot(): void
    {
        Parent::boot();

        // static::creating(function ($notice) {
        //     $notice->created_by = Auth::id();
        // });
        // static::updating(function ($notice) {
        //     $notice->updated_by = Auth::id();
        // });
    }

    public function creator(): BelongsTo
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    public function updater(): BelongsTo
    {
        return $this->belongsTo(User::class, 'updated_by');
    }

    public function read_status(): BelongsToMany
    {
        return $this->belongsToMany(User::class, 'approval_notification_read', 'approval_notification_id');
    }

    public function users(): BelongsToMany
    {
        return $this->belongsToMany(User::class, 'approval_notification_user', 'approval_notification_id');
    }

    // public function getCreatorRoleAttribute(): string
    // {
    //     // permissions
    //     return '';
    // }

    public function getIsReadAttribute(): bool
    {
        return false;
    }
}
