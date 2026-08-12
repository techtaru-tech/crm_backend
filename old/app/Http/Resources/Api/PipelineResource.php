<?php

namespace App\Http\Resources\Api;

use Illuminate\Http\Request;

class PipelineResource extends ApiResource
{
    public function toArray(Request $request): array
    {
        return [
            'id'          => $this->id,
            'name'        => $this->name,
            'description' => $this->description,
            'is_default'  => (bool) $this->is_default,
            'stages_count'=> $this->whenCounted('stages'),
            'stages'      => $this->whenLoaded('stages', fn() => StageResource::collection($this->stages)),
            'created_at'  => $this->created_at?->toIso8601String(),
            'updated_at'  => $this->updated_at?->toIso8601String(),
        ];
    }
}
