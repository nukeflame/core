<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Department extends Model
{
    use HasFactory;

    protected $table = 'company_department';
    protected $guarded = [];

    /**
     * The attributes that should be cast.
     *
     * @var array<string, string>
     */
    protected $casts = [
        'is_active' => 'boolean',
    ];

    /**
     * Get the manager of the department.
     */
    public function manager()
    {
        return $this->belongsTo(User::class, 'manager_id');
    }

    /**
     * Get the users in this department.
     */
    public function users()
    {
        return $this->hasMany(User::class);
    }

    /**
     * Scope a query to only include active departments.
     */
    public function scopeActive($query)
    {
        return $query->where('status', 'A');
    }

    public function roles()
    {
        return $this->belongsToMany(Role::class, 'role_departments');
    }


    //
    //  /**
    //  * Users in this department
    //  */
    // public function users()
    // {
    //     return $this->hasMany(User::class, 'department_id', 'department_code');
    // }

    // /**
    //  * Roles available in this department
    //  */
    // public function roles()
    // {
    //     return $this->hasMany(Role::class, 'department_code', 'department_code');
    // }

    // /**
    //  * Department manager
    //  */
    // public function manager()
    // {
    //     return $this->belongsTo(User::class, 'manager_id');
    // }

    // /**
    //  * Department head
    //  */
    // public function departmentHead()
    // {
    //     return $this->belongsTo(User::class, 'department_head_id');
    // }

    // /**
    //  * Parent department
    //  */
    // public function parent()
    // {
    //     return $this->belongsTo(CompanyDepartment::class, 'parent_id', 'department_code');
    // }

    // /**
    //  * Child departments
    //  */
    // public function children()
    // {
    //     return $this->hasMany(CompanyDepartment::class, 'parent_id', 'department_code');
    // }
}
