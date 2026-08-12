<?php

namespace App\Filament\Resources\AutomationResource\Pages;

use App\Filament\Resources\AutomationResource;
use Filament\Resources\Pages\CreateRecord;

class CreateAutomation extends CreateRecord
{
    protected static string $resource = AutomationResource::class;

    protected function mutateFormDataBeforeCreate(array $data): array
    {
        $data['tenant_id'] = \App\Support\TenantContext::currentId();
        return $data;
    }
}
