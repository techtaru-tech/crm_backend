<?php

namespace App\Events;

use App\Models\Integration;
use App\Models\Lead;
use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Broadcasting\PrivateChannel;
use Illuminate\Contracts\Broadcasting\ShouldBroadcast;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;

class IntegrationFailed implements ShouldBroadcast
{
    use Dispatchable, InteractsWithSockets, SerializesModels;

    public function __construct(
        public readonly Integration $integration,
        public readonly Lead $lead,
        public readonly string $error,
    ) {}

    public function broadcastOn(): array
    {
        return [new PrivateChannel('tenant.' . $this->integration->tenant_id . '.leads')];
    }

    public function broadcastWith(): array
    {
        return [
            'integration_id'   => $this->integration->id,
            'integration_name' => $this->integration->name,
            'lead_id'          => $this->lead->id,
            'lead_name'        => trim("{$this->lead->first_name} {$this->lead->last_name}"),
            'error'            => $this->error,
        ];
    }

    public function broadcastAs(): string
    {
        return 'integration.failed';
    }
}
