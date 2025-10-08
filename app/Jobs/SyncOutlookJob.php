<?php

namespace App\Jobs;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Artisan;

class SyncOutlookJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    public $timeout = 7200;
    public $tries = 1;
    public $maxExceptions = 1;

    public function __construct()
    {
        //
    }

    public function handle()
    {
        Artisan::call('outlook:sync', [
            '--all-users' => true,
            '--folder' => 'inbox',
            '--fetch-profile-pictures' => true,
            '--download-profile-pictures' => true,
            '--attachment-storage' => 'local',
            '--profile-picture-size' => '360×360'
        ]);
    }
}
