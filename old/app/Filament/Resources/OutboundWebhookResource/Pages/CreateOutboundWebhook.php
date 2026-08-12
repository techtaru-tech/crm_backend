<?php

namespace App\Filament\Resources\OutboundWebhookResource\Pages;

use App\Filament\Resources\OutboundWebhookResource;
use Filament\Resources\Pages\CreateRecord;

class CreateOutboundWebhook extends CreateRecord
{
    protected static string $resource = OutboundWebhookResource::class;

    protected function mutateFormDataBeforeCreate(array $data): array
    {
        $data['tenant_id'] = \App\Support\TenantContext::currentId();
        return $data;
    }

    protected function getRedirectUrl(): string
    {
        return $this->getResource()::getUrl('index');
    }
}
