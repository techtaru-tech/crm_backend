<?php

namespace App\Filament\Resources\LeadCaptureWidgetResource\Pages;

use App\Filament\Resources\LeadCaptureWidgetResource;
use Filament\Resources\Pages\CreateRecord;

class CreateLeadCaptureWidget extends CreateRecord
{
    protected static string $resource = LeadCaptureWidgetResource::class;

    protected function mutateFormDataBeforeCreate(array $data): array
    {
        $data['tenant_id'] = \App\Support\TenantContext::currentId();
        return $data;
    }
}
