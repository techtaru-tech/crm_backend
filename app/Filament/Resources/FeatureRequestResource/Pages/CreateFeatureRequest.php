<?php

namespace App\Filament\Resources\FeatureRequestResource\Pages;

use App\Filament\Resources\FeatureRequestResource;
use Filament\Resources\Pages\CreateRecord;

class CreateFeatureRequest extends CreateRecord
{
    protected static string $resource = FeatureRequestResource::class;

    protected function mutateFormDataBeforeCreate(array $data): array
    {
        $data['tenant_id']   = \App\Support\TenantContext::currentId();
        $data['user_id']     = auth()->id();
        $data['status']      = $data['status'] ?? 'open';
        $data['votes_count'] = 0;
        return $data;
    }
}
