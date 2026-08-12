<?php

namespace App\Events;

use App\Models\User;
use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Broadcasting\PrivateChannel;
use Illuminate\Contracts\Broadcasting\ShouldBroadcast;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;

class ExportReady implements ShouldBroadcast
{
    use Dispatchable, InteractsWithSockets, SerializesModels;

    public function __construct(
        public readonly User $user,
        public readonly string $downloadUrl,
        public readonly string $filename,
    ) {}

    public function broadcastOn(): array
    {
        return [new PrivateChannel('user.' . $this->user->id . '.notifications')];
    }

    public function broadcastWith(): array
    {
        return [
            'download_url' => $this->downloadUrl,
            'filename'     => $this->filename,
        ];
    }

    public function broadcastAs(): string
    {
        return 'export.ready';
    }
}
