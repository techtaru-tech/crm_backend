<?php

namespace App\Http\Resources\Api;

use Illuminate\Http\Request;

class TagResource extends ApiResource
{
    public function toArray(Request $request): array
    {
        return [
            'id'          => $this->id,
            'name'        => $this->name,
            'color'       => $this->color,
            'leads_count' => $this->whenCounted('leads'),
            'created_at'  => $this->created_at?->toIso8601String(),
            'updated_at'  => $this->updated_at?->toIso8601String(),
        ];
    }
}
