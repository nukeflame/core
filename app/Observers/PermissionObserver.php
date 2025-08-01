<?php

namespace App\Observers;

use App\Models\Permission;
use Illuminate\Support\Facades\Cache;

class PermissionObserver
{
    /**
     * Handle the Permission "created" event.
     */
    public function created(Permission $permission)
    {
        Cache::tags(['sidebar_menu'])->flush();
    }

    /**
     * Handle the Permission "updated" event.
     */
    public function updated(Permission $permission)
    {
        Cache::tags(['sidebar_menu'])->flush();
    }

    /**
     * Handle the Permission "deleted" event.
     */
    public function deleted(Permission $permission)
    {
        Cache::tags(['sidebar_menu'])->flush();
    }
}
