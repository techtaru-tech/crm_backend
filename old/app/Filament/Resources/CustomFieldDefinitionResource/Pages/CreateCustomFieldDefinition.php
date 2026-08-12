<?php

namespace App\Filament\Resources\CustomFieldDefinitionResource\Pages;

use App\Filament\Resources\CustomFieldDefinitionResource;
use Filament\Resources\Pages\CreateRecord;

class CreateCustomFieldDefinition extends CreateRecord
{
    protected static string $resource = CustomFieldDefinitionResource::class;

    protected function mutateFormDataBeforeCreate(array $data): array
    {
        if (empty($data['tenant_id'])) {
            $data['tenant_id'] = \App\Support\TenantContext::currentId();
        }
        return $data;
    }
}
