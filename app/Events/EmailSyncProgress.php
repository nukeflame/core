<?php

namespace App\Events;

use Illuminate\Broadcasting\Channel;
use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Broadcasting\PrivateChannel;
use Illuminate\Contracts\Broadcasting\ShouldBroadcast;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;

class EmailSyncProgress implements ShouldBroadcast
{
    use Dispatchable, InteractsWithSockets, SerializesModels;

    public int $userId;
    public string $status;
    public int $processed;
    public int $total;
    public int $inserted;
    public int $updated;
    public int $deleted;
    public int $batchNumber;
    public string $timestamp;

    /**
     * Create a new event instance.
     */
    public function __construct(
        int $userId,
        string $status,
        int $processed,
        int $total,
        int $inserted = 0,
        int $updated = 0,
        int $deleted = 0,
        int $batchNumber = 0
    ) {
        $this->userId = $userId;
        $this->status = $status;
        $this->processed = $processed;
        $this->total = $total;
        $this->inserted = $inserted;
        $this->updated = $updated;
        $this->deleted = $deleted;
        $this->batchNumber = $batchNumber;
        $this->timestamp = now()->toIso8601String();
    }

    /**
     * Get the channels the event should broadcast on.
     *
     * @return \Illuminate\Broadcasting\Channel
     */
    public function broadcastOn(): Channel
    {
        return new PrivateChannel("email-sync.{$this->userId}");
    }

    public function broadcastAs(): string
    {
        return 'sync.progress';
    }

    public function broadcastWith(): array
    {
        $percentage = $this->total > 0
            ? round(($this->processed / $this->total) * 100, 2)
            : 0;

        return [
            'status' => $this->status,
            'processed' => $this->processed,
            'total' => $this->total,
            'inserted' => $this->inserted,
            'updated' => $this->updated,
            'deleted' => $this->deleted,
            'percentage' => $percentage,
            'batch_number' => $this->batchNumber,
            'timestamp' => $this->timestamp,
        ];
    }
}
