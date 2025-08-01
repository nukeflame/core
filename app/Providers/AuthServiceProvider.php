<?php

namespace App\Providers;

// use Illuminate\Support\Facades\Gate;

use App\Models\Permission;
use App\Models\User;
use Illuminate\Foundation\Support\Providers\AuthServiceProvider as ServiceProvider;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Gate;
use Illuminate\Support\Facades\Schema;

class AuthServiceProvider extends ServiceProvider
{
    /**
     * The model to policy mappings for the application.
     *
     * @var array<class-string, class-string>
     */
    protected $policies = [
        //
    ];

    /**
     * Register any authentication / authorization services.
     */
    public function boot(): void
    {
        $user =  Auth::user();

        // Cache::remember("user_permissions_{$user->id}", now()->addHours(1), function () use ($user) {
        //     // return $user->getAllPermissions()->pluck('name');
        // });
    }
}
