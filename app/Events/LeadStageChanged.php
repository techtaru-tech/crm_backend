<?php

namespace App\Events;

use App\Models\Lead;
use App\Models\PipelineStage;
use App\Models\User;
use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Broadcasting\PrivateChannel;
use Illuminate\Contracts\Broadcasting\ShouldBroadcast;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;

class LeadStageChanged implements ShouldBroadcast
{
    use Dispatchable, InteractsWithSockets, SerializesModels;

    public function __construct(
        public readonly Lead $lead,
        public readonly ?PipelineStage $fromStage,
        public readonly ?PipelineStage $toStage,
        public readonly ?User $movedBy,
    ) {}

    public function broadcastOn(): array
    {
        return [new PrivateChannel('tenant.' . $this->lead->tenant_id . '.leads')];
    }

    public function broadcastWith(): array
    {
        return [
            'lead_id'       => $this->lead->id,
            'lead_name'     => trim("{$this->lead->first_name} {$this->lead->last_name}"),
            'from_stage_id' => $this->fromStage?->id,
            'from_stage'    => $this->fromStage?->name,
            'to_stage_id'   => $this->toStage?->id,
            'to_stage'      => $this->toStage?->name,
            'moved_by_id'   => $this->movedBy?->id,
            'moved_by'      => $this->movedBy?->name,
            'pipeline_id'   => $this->lead->pipeline_id,
        ];
    }

    public function broadcastAs(): string
    {
        return 'lead.stage_changed';
    }
}
