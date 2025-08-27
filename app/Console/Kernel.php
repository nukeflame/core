<?php

namespace App\Console;

use Illuminate\Console\Scheduling\Schedule;
use Illuminate\Foundation\Console\Kernel as ConsoleKernel;
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
        // $schedule->command('inspire')->hourly();
        // $schedule->command('send:emails')->everyMinute();
        // $schedule->command('cover:glupdate')->everyMinute();
        // $schedule->command('renewal:send-notices')
        //     ->daily()
        //     ->at('09:00')
        //     ->withoutOverlapping();
        // $schedule->command('outlook:fetch-emails --user=user@yourdomain.com --limit=50')
        //     ->everyFifteenMinutes()
        //     ->withoutOverlapping();

        // $schedule->command('outlook:sync --all-users --folder=inbox --fetch-profile-pictures --download-profile-pictures --attachment-storage=local --profile-picture-size=360×360')
        //     ->everyMinute()
        //     ->withoutOverlapping(1)
        //     ->runInBackground()
        //     ->onFailure(function () {
        //         logger()->error('Outlook email sync scheduled task failed');
        //     });
        // Inbox - every minute
        $schedule->command('outlook:sync --all-users --folder=inbox --fetch-profile-pictures --download-profile-pictures --attachment-storage=local --profile-picture-size=360×360')
            ->everyMinute()
            ->withoutOverlapping(2)
            ->runInBackground()
            ->onFailure(function () {
                logger()->error('Outlook inbox sync failed');
            });

        // // Sent - offset by 20 seconds
        // $schedule->command('outlook:sync --all-users --folder=sent --fetch-profile-pictures --download-profile-pictures --attachment-storage=local --profile-picture-size=360×360')
        //     ->everyMinute()
        //     ->skip(function () {
        //         return (int)date('s') < 20; // Start at 20 seconds
        //     })
        //     ->withoutOverlapping(2)
        //     ->runInBackground()
        //     ->onFailure(function () {
        //         logger()->error('Outlook sent sync failed');
        //     });

        // // Drafts - offset by 40 seconds
        // $schedule->command('outlook:sync --all-users --folder=drafts --fetch-profile-pictures --download-profile-pictures --attachment-storage=local --profile-picture-size=360×360')
        //     ->everyMinute()
        //     ->skip(function () {
        //         return (int)date('s') < 40; // Start at 40 seconds
        //     })
        //     ->withoutOverlapping(2)
        //     ->runInBackground()
        //     ->onFailure(function () {
        //         logger()->error('Outlook drafts sync failed');
        //     });

        // Clean up old sync logs (keep last 30 days)
        $schedule->call(function () {
            DB::table('email_sync_logs')
                ->where('created_at', '<', now()->subDays(30))
                ->delete();
        })->daily();

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
