<?php

namespace App\Filament\Resources\OutboundWebhookResource\Pages;

use App\Filament\Resources\OutboundWebhookResource;
use Filament\Resources\Pages\EditRecord;

class EditOutboundWebhook extends EditRecord
{
    protected static string $resource = OutboundWebhookResource::class;

    protected function getRedirectUrl(): string
    {
        return $this->getResource()::getUrl('index');
    }
}
