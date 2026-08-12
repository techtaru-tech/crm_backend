<?php

namespace App\Events;

use App\Models\Automation;
use App\Models\Lead;
use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Broadcasting\PrivateChannel;
use Illuminate\Contracts\Broadcasting\ShouldBroadcast;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;

class AutomationRan implements ShouldBroadcast
{
    use Dispatchable, InteractsWithSockets, SerializesModels;

    public function __construct(
        public readonly Automation $automation,
        public readonly Lead $lead,
        public readonly bool $success,
        public readonly string $message = '',
    ) {}

    public function broadcastOn(): array
    {
        return [new PrivateChannel('tenant.' . $this->lead->tenant_id . '.leads')];
    }

    public function broadcastWith(): array
    {
        return [
            'automation_id'   => $this->automation->id,
            'automation_name' => $this->automation->name,
            'lead_id'         => $this->lead->id,
            'lead_name'       => trim("{$this->lead->first_name} {$this->lead->last_name}"),
            'success'         => $this->success,
            'message'         => $this->message,
        ];
    }

    public function broadcastAs(): string
    {
        return 'automation.ran';
    }
}
