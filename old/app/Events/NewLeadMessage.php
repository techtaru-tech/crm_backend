<?php

namespace App\Events;

use App\Models\LeadMessage;
use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Broadcasting\PrivateChannel;
use Illuminate\Contracts\Broadcasting\ShouldBroadcast;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;

class NewLeadMessage implements ShouldBroadcast
{
    use Dispatchable, InteractsWithSockets, SerializesModels;

    public function __construct(public readonly LeadMessage $message) {}

    public function broadcastOn(): array
    {
        return [new PrivateChannel('tenant.' . $this->message->tenant_id . '.messages')];
    }

    public function broadcastWith(): array
    {
        return [
            'id'              => $this->message->id,
            'lead_id'         => $this->message->lead_id,
            'channel'         => $this->message->channel,
            'direction'       => $this->message->direction,
            'from_identifier' => $this->message->from_identifier,
            'to_identifier'   => $this->message->to_identifier,
            'body'            => $this->message->body,
            'media_url'       => $this->message->media_url,
            'media_type'      => $this->message->media_type,
            'status'          => $this->message->status,
            'created_at'      => $this->message->created_at?->toIso8601String(),
        ];
    }

    public function broadcastAs(): string
    {
        return 'lead.message.new';
    }
}
