<?php

namespace App\Events;

use App\Models\ApprovalsTracker;
use App\Models\Notification;
use App\Models\User;
use Exception;
use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Broadcasting\PrivateChannel;
use Illuminate\Contracts\Broadcasting\ShouldBroadcast;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;

class ApprovalTrackerEvent implements ShouldQueue, ShouldBroadcast
{
    use Dispatchable, InteractsWithSockets, SerializesModels;

    public $user;
    public $approvalNotice;
    public $action;
    public $approval;

    /**
     * Create a new event instance.
     */
    public function __construct(Notification $approvalNotice, ?string $action, User $user, ApprovalsTracker $approval)
    {
        $this->user = $user;
        $this->approvalNotice = $approvalNotice;
        $this->action = $action;
        $this->approval = $approval;
    }

    /**
     * Get the channels the event should broadcast on.
     *
     * @return array<int, \Illuminate\Broadcasting\Channel>
     */
    public function broadcastOn(): array
    {
        $event_channels = [];

        if (isset($this->approvalNotice->users)) {
            foreach ($this->approvalNotice->users as $key => $user) {
                if ((int) $this->approval->approver === (int) $user->id && (int) $this->approval->approver !== (int) $this->user->id) {
                    $channel_name = new PrivateChannel('approval_notification.created.' . $user->id);
                    array_push($event_channels, $channel_name);
                }
            }
        }
        return $event_channels;
    }

    /**
     * Get the data to broadcast
     *
     * @return array<string, mixed>
     */
    public function broadcastWith(): array
    {
        return [
            'title' => $this->approvalNotice->title,
            'message' => $this->approvalNotice->message,
            'link' => $this->approvalNotice->link,
            'icon' => $this->approvalNotice->icon,
            'id' => $this->approvalNotice->id,
        ];
    }

    /**
     * The event`s broadcast name.
     *
     */
    public function broadcastAs(): string
    {
        return 'approval_notification_event';
    }

    /**
     * Determine if this event should broadcast
     *
     */
    public function broadcastWhen(): bool
    {
        try {
            // $settings = Settings::first();
            $enableBroadcast = true;
            // if (config('system.pusher.enabled') === null) {
            //     return false;
            // }
            // if (isset($settings->options['pushNotification']['all']['approvalNotifications'])) {
            //     if (isset($settings->options['pushNotification']['all']['approvalNotifications']) === true && $this->action = 'create') {
            //         $enableBroadcast = true;
            //     }
            // }

            return $enableBroadcast;
        } catch (\Exception $e) {
            throw new Exception('SOMETHING_WENT_WRONG: ' . $e->getMessage());
        }
    }
}
