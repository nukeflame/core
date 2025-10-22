<?php

namespace App\Console;

use App\Jobs\RenewAllSubscriptions;
use Illuminate\Console\Scheduling\Schedule;
use Illuminate\Foundation\Console\Kernel as ConsoleKernel;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\DB;

class Kernel extends ConsoleKernel
{
    /**
     * Define the application's command schedule.
     */
    protected $commands = [
        \App\Console\Commands\SendingEmail::class,
        \App\Console\Commands\ClearSoftDeletes::class,
    ];

    protected function schedule(Schedule $schedule): void
    {
        // $schedule->command('send:emails')->everyMinute();
        $schedule->command('partner:glupdate')->everyMinute();
        $schedule->command('cover:glupdate')->everyMinute();
        $schedule->command('coverReinsurer:glupdate')->everyMinute();

        // $schedule->job(new RenewAllSubscriptions())->dailyAt('03:00')->onOneServer();

        // $schedule->command('renewal:send-notices')
        //     ->daily()
        //     ->at('09:00')
        //     ->withoutOverlapping();
        // $schedule->command('outlook:fetch-emails --user=user@yourdomain.com --limit=50')
        //     ->everyFifteenMinutes()
        //     ->withoutOverlapping();

        // $schedule->call(function () {
        //     DB::table('email_sync_logs')
        //         ->where('created_at', '<', now()->subDays(30))
        //         ->delete();
        // })->daily();

        // Token refresh check - run every hour
        // $schedule->command('outlook:sync --all-users --force-refresh')
        //     ->hourly()
        //     ->withoutOverlapping(5);
    }

    /**
     * Register the commands for the application.
     */
    protected function commands(): void
    {
        $this->load(__DIR__ . '/Commands');

        require base_path('routes/console.php');
    }
}
