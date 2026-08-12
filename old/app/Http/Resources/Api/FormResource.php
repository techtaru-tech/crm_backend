<?php

namespace App\Http\Resources\Api;

use Illuminate\Http\Request;

class FormResource extends ApiResource
{
    public function toArray(Request $request): array
    {
        return [
            'id'               => $this->id,
            'name'             => $this->name,
            'slug'             => $this->slug,
            'title'            => $this->title,
            'description'      => $this->description,
            'submit_label'     => $this->submit_label,
            'thank_you_message'=> $this->thank_you_message,
            'active'           => (bool) $this->active,
            'pipeline_id'      => $this->pipeline_id,
            'pipeline_stage_id'=> $this->pipeline_stage_id,
            'fields'           => $this->whenLoaded('fields', fn() => $this->fields->map(fn($f) => [
                'id'       => $f->id,
                'type'     => $f->type,
                'label'    => $f->label,
                'required' => (bool) $f->required,
            ])),
            'created_at'       => $this->created_at?->toIso8601String(),
            'updated_at'       => $this->updated_at?->toIso8601String(),
        ];
    }
}
