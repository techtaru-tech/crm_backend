<?php

namespace App\Filament\Resources\TrackingSnippetResource\Pages;

use App\Filament\Resources\TrackingSnippetResource;
use Filament\Resources\Pages\CreateRecord;

class CreateTrackingSnippet extends CreateRecord
{
    protected static string $resource = TrackingSnippetResource::class;

    protected function mutateFormDataBeforeCreate(array $data): array
    {
        $data['tenant_id'] = \App\Support\TenantContext::currentId();
        return $data;
    }
}
