<?php

namespace App\Observers;

use App\Models\Role;
use Illuminate\Support\Facades\Cache;

class RoleObserver
{
    /**
     * Handle the Role "updated" event.
     */
    public function updated(Role $role)
    {
        $this->clearCacheForUsersWithRole($role);
    }

    /**
     * Handle the "permissions synced" event.
     */
    public function permissionsSynced(Role $role)
    {
        $this->clearCacheForUsersWithRole($role);
    }

    /**
     * Clear cache for all users with the specified role
     */
    private function clearCacheForUsersWithRole(Role $role)
    {
        $role->users()->chunk(100, function ($users) {
            foreach ($users as $user) {
                Cache::tags(["user_{$user->id}"])->flush();
            }
        });
    }
}
