<?php

namespace App\Events;

use App\Models\Lead;
use App\Models\User;
use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Broadcasting\PrivateChannel;
use Illuminate\Contracts\Broadcasting\ShouldBroadcast;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;

class TeamMemberMentioned implements ShouldBroadcast
{
    use Dispatchable, InteractsWithSockets, SerializesModels;

    public function __construct(
        public readonly User $mentionedUser,
        public readonly User $mentionedBy,
        public readonly Lead $lead,
        public readonly string $noteExcerpt,
    ) {}

    public function broadcastOn(): array
    {
        return [new PrivateChannel('user.' . $this->mentionedUser->id . '.notifications')];
    }

    public function broadcastWith(): array
    {
        return [
            'lead_id'       => $this->lead->id,
            'lead_name'     => trim("{$this->lead->first_name} {$this->lead->last_name}"),
            'mentioned_by'  => $this->mentionedBy->name,
            'note_excerpt'  => $this->noteExcerpt,
        ];
    }

    public function broadcastAs(): string
    {
        return 'team.mentioned';
    }
}
