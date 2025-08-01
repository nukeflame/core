<?php

namespace App\Traits;

use Illuminate\Support\Facades\Cache;

trait HasPermissions
{
    public function hasPermission($permission)
    {
        return $this->permissions->contains('name', $permission) ||
            $this->roles->flatMap->permissions->contains('name', $permission);
    }

    public function hasAnyPermission($permissions)
    {
        if (is_string($permissions)) {
            $permissions = [$permissions];
        }

        foreach ($permissions as $permission) {
            if ($this->hasPermission($permission)) {
                return true;
            }
        }

        return false;
    }

    public function getCachedPermissions()
    {
        return Cache::remember("user_permissions_{$this->id}", now()->addHours(1), function () {
            return $this->getAllPermissions()->pluck('name');
        });
    }

    public function forgetCachedPermissions()
    {
        Cache::forget("user_permissions_{$this->id}");
    }
}
