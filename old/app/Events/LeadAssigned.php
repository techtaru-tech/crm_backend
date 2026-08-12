<?php

namespace App\Events;

use App\Models\Lead;
use App\Models\User;
use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Broadcasting\PrivateChannel;
use Illuminate\Contracts\Broadcasting\ShouldBroadcast;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;

class LeadAssigned implements ShouldBroadcast
{
    use Dispatchable, InteractsWithSockets, SerializesModels;

    public function __construct(
        public readonly Lead $lead,
        public readonly ?User $assignedTo,
        public readonly ?User $assignedBy,
    ) {}

    public function broadcastOn(): array
    {
        $channels = [new PrivateChannel('tenant.' . $this->lead->tenant_id . '.leads')];
        if ($this->assignedTo) {
            $channels[] = new PrivateChannel('user.' . $this->assignedTo->id . '.notifications');
        }
        return $channels;
    }

    public function broadcastWith(): array
    {
        return [
            'lead_id'        => $this->lead->id,
            'lead_name'      => trim("{$this->lead->first_name} {$this->lead->last_name}"),
            'assigned_to_id' => $this->assignedTo?->id,
            'assigned_to'    => $this->assignedTo?->name,
            'assigned_by'    => $this->assignedBy?->name,
        ];
    }

    public function broadcastAs(): string
    {
        return 'lead.assigned';
    }
}
