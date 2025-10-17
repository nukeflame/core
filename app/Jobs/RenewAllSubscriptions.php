<?php

namespace App\Jobs;

use App\Models\EmailSyncState;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;

class RenewAllSubscriptions implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    /**
     * Create a new job instance.
     */
    public function __construct()
    {
        //
    }

    /**
     * Execute the job.
     */
    public function handle()
    {
        $states = EmailSyncState::whereNotNull('subscription_id')
            ->where('subscription_expires_at', '<', now()->addDay())
            ->get();

        foreach ($states as $state) {
            SyncUserEmails::dispatch($state->user_id);
        }

        logger()->info('Renewed subscriptions', ['count' => $states->count()]);
    }
}
