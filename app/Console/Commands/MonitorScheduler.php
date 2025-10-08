<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\Cache;

class MonitorScheduler extends Command
{
    protected $signature = 'scheduler:monitor';
    protected $description = 'Monitor scheduler performance';

    public function handle()
    {
        $lastRun = Cache::get('scheduler_last_run', 'Never');
        $totalRuns = Cache::get('scheduler_total_runs', 0);
        $failedRuns = Cache::get('scheduler_failed_runs', 0);

        $this->info("Scheduler Monitoring");
        $this->table(
            ['Metric', 'Value'],
            [
                ['Last Run', $lastRun],
                ['Total Runs', $totalRuns],
                ['Failed Runs', $failedRuns],
                ['Success Rate', $totalRuns > 0 ? round(($totalRuns - $failedRuns) / $totalRuns * 100, 2) . '%' : 'N/A']
            ]
        );
    }
}
