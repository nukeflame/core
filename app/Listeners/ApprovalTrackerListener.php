<?php

namespace App\Listeners;

use App\Events\ApprovalTrackerEvent;
use App\Models\User;
use App\Notifications\ApprovalNotification;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Queue\InteractsWithQueue;

class ApprovalTrackerListener implements ShouldQueue
{
    /**
     * Create the event listener.
     */
    public function __construct()
    {
        //
    }

    /**
     * Handle the event.
     */
    public function handle(ApprovalTrackerEvent $event): void
    {
        //TODO: Implement permissions.
        $users = User::whereHas('permissions', function (Builder $query) {
            $query->whereIn('name', ['super_admin']);
        });

        if (!empty($users)) {
            foreach ($users as $user) {
                $user->notify(new ApprovalNotification($event->approvalNotice, $event->action));
            }
        }
    }
}
