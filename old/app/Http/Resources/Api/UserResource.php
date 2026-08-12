<?php

namespace App\Http\Resources\Api;

use Illuminate\Http\Request;

class UserResource extends ApiResource
{
    public function toArray(Request $request): array
    {
        return [
            'id'         => $this->id,
            'name'       => $this->name,
            'email'      => $this->email,
            'roles'      => $this->roles->pluck('name'),
            'created_at' => $this->created_at?->toIso8601String(),
        ];
    }
}
