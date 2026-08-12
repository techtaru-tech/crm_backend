<?php

declare(strict_types=1);

namespace App\Filament\Resources\ChatbotConfigResource\Pages;

use App\Filament\Resources\ChatbotConfigResource;
use App\Support\TenantContext;
use Filament\Resources\Pages\CreateRecord;

class CreateChatbotConfig extends CreateRecord
{
    protected static string $resource = ChatbotConfigResource::class;

    protected function mutateFormDataBeforeCreate(array $data): array
    {
        $data['tenant_id'] = TenantContext::currentId();

        return $data;
    }
}
