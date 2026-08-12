<?php

namespace App\Http\Resources\Api;

use App\Models\Integration;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class IntegrationResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        /** @var Integration $this */
        return [
            'id'             => $this->id,
            'type'           => $this->type,
            'name'           => $this->name,
            'label'          => $this->getLabel(),
            'enabled'        => $this->enabled,
            'status'         => $this->status,
            'field_mapping'  => $this->field_mapping,
            'filter_config'  => $this->filter_config,
            'last_synced_at' => $this->last_synced_at?->toIso8601String(),
            'created_at'     => $this->created_at?->toIso8601String(),
            'updated_at'     => $this->updated_at?->toIso8601String(),
        ];
    }
}
