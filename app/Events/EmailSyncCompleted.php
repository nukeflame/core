<?php

namespace App\Events;

use Illuminate\Broadcasting\Channel;
use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Broadcasting\PrivateChannel;
use Illuminate\Contracts\Broadcasting\ShouldBroadcast;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;

class EmailSyncCompleted implements ShouldBroadcast
{
    use Dispatchable, InteractsWithSockets, SerializesModels;

    public int $userId;
    public int $totalProcessed;
    public int $totalInserted;
    public int $totalUpdated;
    public int $totalDeleted;
    public string $timestamp;

    /**
     * Create a new event instance.
     */
    public function __construct(
        int $userId,
        int $totalProcessed,
        int $totalInserted,
        int $totalUpdated,
        int $totalDeleted
    ) {
        $this->userId = $userId;
        $this->totalProcessed = $totalProcessed;
        $this->totalInserted = $totalInserted;
        $this->totalUpdated = $totalUpdated;
        $this->totalDeleted = $totalDeleted;
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
        return 'sync.completed';
    }

    public function broadcastWith(): array
    {
        return [
            'status' => 'completed',
            'total_processed' => $this->totalProcessed,
            'total_inserted' => $this->totalInserted,
            'total_updated' => $this->totalUpdated,
            'total_deleted' => $this->totalDeleted,
            'timestamp' => $this->timestamp,
        ];
    }
}
