<?php

namespace App\Observers;

use App\Models\User;
use Illuminate\Support\Facades\Cache;

class UserObserver
{
    /**
     * Handle the User "updated" event.
     */
    public function updated(User $user)
    {
        if ($user->isDirty('permissions') || $user->wasChanged('permissions')) {
            $this->clearUserSidebarCache($user);
        }
    }

    /**
     * Handle role assigned/removed events
     */
    public function rolesChanged(User $user)
    {
        $this->clearUserSidebarCache($user);
    }

    /**
     * Handle direct permission assigned/removed events
     */
    public function permissionsChanged(User $user)
    {
        $this->clearUserSidebarCache($user);
    }

    /**
     * Clear sidebar cache for a specific user
     */
    private function clearUserSidebarCache(User $user)
    {
        Cache::tags(["user_{$user->id}"])->flush();
    }
}
