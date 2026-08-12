<?php

namespace App\Filament\Resources\LandingPageResource\Pages;

use App\Filament\Resources\LandingPageResource;
use Filament\Resources\Pages\CreateRecord;
use Illuminate\Support\Str;

class CreateLandingPage extends CreateRecord
{
    protected static string $resource = LandingPageResource::class;

    protected function mutateFormDataBeforeCreate(array $data): array
    {
        if (empty($data['slug']) && ! empty($data['name'])) {
            $data['slug'] = Str::slug($data['name']);
        }
        $tenantId = \App\Support\TenantContext::currentId();
        if ($tenantId && empty($data['tenant_id'])) {
            $data['tenant_id'] = $tenantId;
        }
        return $data;
    }
}
