<?php

namespace App\Http\Resources\Api;

use Illuminate\Http\Request;

class LeadResource extends ApiResource
{
    public function toArray(Request $request): array
    {
        return [
            'id'              => $this->id,
            'first_name'      => $this->first_name,
            'last_name'       => $this->last_name,
            'email'           => $this->email,
            'phone'           => $this->phone,
            'source'          => $this->source,
            'status'          => $this->status,
            'is_starred'      => (bool) $this->is_starred,
            'lead_score'      => (int) ($this->lead_score ?? 0),
            'notes'           => $this->notes,
            'custom_fields'   => $this->custom_fields ?? [],
            'pipeline_id'     => $this->pipeline_id,
            'pipeline_stage_id' => $this->pipeline_stage_id,
            'assigned_user_id'  => $this->assigned_user_id,
            'contacted_at'    => $this->contacted_at?->toIso8601String(),
            'consented_at'    => $this->consented_at?->toIso8601String(),
            'created_at'      => $this->created_at?->toIso8601String(),
            'updated_at'      => $this->updated_at?->toIso8601String(),
            'tags'            => $this->whenLoaded('tags', fn() => $this->tags->map(fn($t) => [
                'id'    => $t->id,
                'name'  => $t->name,
                'color' => $t->color,
            ])),
            'pipeline_stage'  => $this->whenLoaded('pipelineStage', fn() => [
                'id'   => $this->pipelineStage?->id,
                'name' => $this->pipelineStage?->name,
            ]),
            'assigned_user'   => $this->whenLoaded('assignedUser', fn() => [
                'id'   => $this->assignedUser?->id,
                'name' => $this->assignedUser?->name,
            ]),
        ];
    }
}
