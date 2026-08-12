<?php

namespace App\Http\Resources\Api;

use Illuminate\Http\Request;

class AutomationResource extends ApiResource
{
    public function toArray(Request $request): array
    {
        return [
            'id'             => $this->id,
            'name'           => $this->name,
            'description'    => $this->description,
            'enabled'        => (bool) $this->enabled,
            'trigger_type'   => $this->trigger_type,
            'trigger_config' => $this->trigger_config ?? [],
            'steps_count'    => $this->whenCounted('steps'),
            'created_at'     => $this->created_at?->toIso8601String(),
            'updated_at'     => $this->updated_at?->toIso8601String(),
        ];
    }
}
