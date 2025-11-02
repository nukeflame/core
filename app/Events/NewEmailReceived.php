<?php

namespace App\Events;

use Illuminate\Broadcasting\Channel;
use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Broadcasting\PrivateChannel;
use Illuminate\Contracts\Broadcasting\ShouldBroadcast;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;

class NewEmailReceived implements ShouldBroadcast
{
    use Dispatchable, InteractsWithSockets, SerializesModels;

    public int $userId;
    public array $email;
    public string $timestamp;

    /**
     * Create a new event instance.
     */
    public function __construct(int $userId, array $email)
    {
        $this->userId = $userId;
        $this->email = $email;
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
        return 'email.new';
    }

    public function broadcastWith(): array
    {
        return [
            'email' => [
                'id' => $this->email['id'] ?? null,
                'uid' => $this->email['uid'] ?? null,
                'subject' => $this->email['subject'] ?? 'No Subject',
                'from_name' => $this->email['from_name'] ?? '',
                'from_email' => $this->email['from_email'] ?? '',
                'body_preview' => $this->email['body_preview'] ?? '',
                'date_received' => $this->email['date_received'] ?? null,
                'is_read' => $this->email['is_read'] ?? false,
                'has_attachments' => $this->email['has_attachments'] ?? false,
                'folder' => $this->email['folder'] ?? 'inbox',
            ],
            'timestamp' => $this->timestamp,
        ];
    }
}
