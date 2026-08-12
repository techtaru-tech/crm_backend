<?php

namespace App\Http\Resources\Api;

use Illuminate\Http\Request;

class StageResource extends ApiResource
{
    public function toArray(Request $request): array
    {
        return [
            'id'          => $this->id,
            'pipeline_id' => $this->pipeline_id,
            'name'        => $this->name,
            'color'       => $this->color,
            'sort_order'  => $this->sort_order,
            'is_won'      => (bool) $this->is_won,
            'is_lost'     => (bool) $this->is_lost,
            'leads_count' => $this->whenCounted('leads'),
            'created_at'  => $this->created_at?->toIso8601String(),
            'updated_at'  => $this->updated_at?->toIso8601String(),
        ];
    }
}
