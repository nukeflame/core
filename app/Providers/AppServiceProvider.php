<?php

namespace App\Providers;

use App\Console\Commands\AuthenticateOutlook;
use App\Console\Commands\FetchOutlookEmails;
use App\Enums\PermissionsLevel;
use App\Models\Bd\PipelineOpportunity;
use App\Observers\Bd\PipelineOpportunityObserver;
use Carbon\Carbon;
use App\Models\Company;
use App\Models\Permission;
use App\Models\Role;
use App\Models\User;
use App\Observers\PermissionObserver;
use App\Observers\RoleObserver;
use App\Observers\UserObserver;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Blade;
use Illuminate\Support\Facades\View;
use Illuminate\Support\ServiceProvider;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        //
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        if (app()->runningInConsole() && !app()->runningUnitTests()) {
            return;
        }

        $this->commands([
            FetchOutlookEmails::class,
            AuthenticateOutlook::class,
        ]);

        // observe for changes in this models
        Permission::observe(PermissionObserver::class);
        Role::observe(RoleObserver::class);
        User::observe(UserObserver::class);
        PipelineOpportunity::observe(PipelineOpportunityObserver::class);

        Blade::directive('stampImageOrEmpty', function ($path) {
            return "<?php
            \$filePath = storage_path($path);
            if (file_exists(\$filePath)) {
                \$base64Image = base64_encode(file_get_contents(\$filePath));
                echo '<img src=\"data:image/png;base64,' . \$base64Image . '\" style=\"width: 100px; height: auto;\">';
            } else {
                echo '<img src=\"\" style=\"width: 100px; height: auto;\">';
            }
        ?>";
        });

        // workaround when using ngrok http 8000
        // URL::forceScheme('https');

        View::composer('*', function ($view) {
            $user = Auth::user();
            $firstName = explode(' ', $user?->first_name ?? '')[0];
            $role = $user?->role;
            $username = $user?->user_name;

            $roleName = 'No Role';
            if ((int) $role?->permission_level == PermissionsLevel::SUPERADMIN) {
                $roleName = '--';
            } else {
                $roleName = $role?->name ?? 'No Role';
            }

            $company = Company::where('company_id', 1)->first();

            $current_account_year = Carbon::now()->year;
            $current_account_month = Carbon::now()->month;
            $current_account_month = str_pad($current_account_month, 2, '0', STR_PAD_LEFT);
            $defaultCurrency = 'KES';

            $view->with(compact(
                'user',
                'firstName',
                'roleName',
                'username',
                'company',
                'defaultCurrency',
                'current_account_month',
                'current_account_year'
            ));
        });
    }
}
