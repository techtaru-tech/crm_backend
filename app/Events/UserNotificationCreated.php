<?php

namespace App\Events;

use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Broadcasting\PrivateChannel;
use Illuminate\Contracts\Broadcasting\ShouldBroadcast;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;

class UserNotificationCreated implements ShouldBroadcast
{
    use Dispatchable, InteractsWithSockets, SerializesModels;

    public function __construct(
        public readonly int $userId,
        public readonly string $type,
        public readonly string $message,
        public readonly ?int $leadId = null,
    ) {}

    public function broadcastOn(): array
    {
        return [new PrivateChannel('user.' . $this->userId . '.notifications')];
    }

    public function broadcastAs(): string
    {
        return 'notification.new';
    }

    public function broadcastWith(): array
    {
        return [
            'type'    => $this->type,
            'message' => $this->message,
            'lead_id' => $this->leadId,
        ];
    }
}
