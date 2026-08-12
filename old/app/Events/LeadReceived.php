<?php

namespace App\Events;

use App\Models\Lead;
use Illuminate\Broadcasting\Channel;
use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Broadcasting\PrivateChannel;
use Illuminate\Contracts\Broadcasting\ShouldBroadcast;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;

class LeadReceived implements ShouldBroadcast
{
    use Dispatchable, InteractsWithSockets, SerializesModels;

    public function __construct(public readonly Lead $lead) {}

    public function broadcastOn(): array
    {
        return [new PrivateChannel('tenant.' . $this->lead->tenant_id . '.leads')];
    }

    public function broadcastWith(): array
    {
        return [
            'id'         => $this->lead->id,
            'name'       => trim("{$this->lead->first_name} {$this->lead->last_name}"),
            'email'      => $this->lead->email,
            'phone'      => $this->lead->phone,
            'company'    => $this->lead->company,
            'source'     => $this->lead->source,
            'status'     => $this->lead->status,
            'created_at' => $this->lead->created_at?->toIso8601String(),
        ];
    }

    public function broadcastAs(): string
    {
        return 'lead.received';
    }
}
