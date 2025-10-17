<?php

namespace App\Events;

use Illuminate\Broadcasting\Channel;
use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Broadcasting\PrivateChannel;
use Illuminate\Contracts\Broadcasting\ShouldBroadcast;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;

class EmailSyncFailed implements ShouldBroadcast
{
    use Dispatchable, InteractsWithSockets, SerializesModels;

    public int $userId;
    public string $error;
    public string $timestamp;

    /**
     * Create a new event instance.
     */
    public function __construct(int $userId, string $error)
    {
        $this->userId = $userId;
        $this->error = $error;
        $this->timestamp = now()->toIso8601String();
    }

    /**
     * Get the channels the event should broadcast on.
     *
     * @return array<int, \Illuminate\Broadcasting\Channel>
     */
    public function broadcastOn(): Channel
    {
        return new PrivateChannel("email-sync.{$this->userId}");
    }

    public function broadcastAs(): string
    {
        return 'sync.failed';
    }

    public function broadcastWith(): array
    {
        return [
            'status' => 'failed',
            'error' => $this->error,
            'timestamp' => $this->timestamp,
        ];
    }
}
