<?php

namespace App\Models;

use App\Http\Controllers\OutlookOAuthController;
use Laravel\Passport\HasApiTokens;
use Spatie\Permission\Traits\HasRoles;
use Illuminate\Notifications\Notifiable;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Support\Facades\DB;

class User extends Authenticatable
{
    use HasApiTokens, HasFactory, Notifiable, SoftDeletes, HasRoles;

    protected $table = 'users';
    protected $guarded = [];

    // protected $fillable = [
    //     'user_name',
    //     'email',
    //     'first_name',
    //     'last_name',
    //     'phone_number',
    //     'password',
    //     'department_id',
    //     'role_id',
    //     'is_active',
    //     'created_by',
    //     'requires_password_reset',
    //     'two_factor_enabled',
    //     'failed_login_attempts',
    //     'password_expires_at',
    // ];

    protected $hidden = [
        'password',
        'remember_token',
    ];

    protected $casts = [
        'email_verified_at' => 'datetime',
        'password_expires_at' => 'datetime',
        'last_login' => 'datetime',
        'is_active' => 'boolean',
        'requires_password_reset' => 'boolean',
        'password' => 'hashed',
        'password_changed_at' => 'datetime',
        'skills' => 'array',
        'social_networks' => 'array',
    ];

    /**
     * Get the department that the user belongs to.
     */
    public function department()
    {
        // return $this->belongsTo(Department::class, 'department_code', 'department_id', 'company_department');
    }

    /**
     * Get the role that the user has.
     */
    public function role()
    {
        return $this->belongsTo(Role::class, 'role_id', 'id');
    }

    /**
     * Get the activity logs for the user.
     */
    public function activityLogs()
    {
        return $this->hasMany(UserActivityLog::class);
    }

    /**
     * Get the user who created this user.
     */
    public function creator()
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    /**
     * Get the department that this user manages.
     */
    public function managedDepartment()
    {
        // return $this->hasOne(Department::class, 'manager_id');
    }

    /**
     * Get full name attribute.
     */
    public function getFullNameAttribute()
    {
        return "{$this->first_name} {$this->last_name}";
    }

    /**
     * Scope a query to only include active users.
     */
    public function scopeActive($query)
    {
        return $query->where('is_active', true);
    }

    /**
     * Check if user has a specific permission.
     */
    public function hasPermission($permissionCode)
    {
        return $this->role->permissions()
            ->where('permission_code', $permissionCode)
            ->where('is_granted', true)
            ->exists();
    }

    /**
     * Check if password needs to be changed.
     */
    public function needsPasswordChange()
    {
        return $this->requires_password_reset ||
            ($this->password_expires_at && $this->password_expires_at->isPast());
    }

    public function performanceRecord()
    {
        return $this->hasOne(User::class)->withTimestamps();
    }

    /**
     * Check if user needs to change password on first login
     *
     * @return bool
     */
    public function requiresPasswordChange()
    {
        return $this->requires_password_reset === true && $this->password_changed_at === null;
    }

    public function hasOutlookConnection()
    {
        $response = app(OutlookOAuthController::class)->status();
        $status = json_decode($response->getContent(), true);

        return $status['connected'] ?? false;
    }

    public function assigndDepartmentRole($roleId)
    {
        $role = Role::where('id', $roleId)->where('is_active', true)->first();

        if (!$role) {
            throw new \Exception("Role with ID {$roleId} not found or inactive");
        }

        $existingAssignment = DB::table('role_departments')
            ->where('role_id', $roleId)
            ->where('department_id', $this->id)
            ->first();

        if (!$existingAssignment) {
            DB::table('role_departments')->updateOrInsert([
                'role_id' => $roleId,
                'department_id' => $this->id,
                'created_at' => now(),
                'updated_at' => now(),
            ]);
        }

        return $this;
    }
}
