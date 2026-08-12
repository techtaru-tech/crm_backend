<?php

declare(strict_types=1);

namespace App\Filament\Resources\AiSdrAgentResource\Pages;

use App\Filament\Resources\AiSdrAgentResource;
use Filament\Resources\Pages\CreateRecord;

class CreateAiSdrAgent extends CreateRecord
{
    protected static string $resource = AiSdrAgentResource::class;

    protected function mutateFormDataBeforeCreate(array $data): array
    {
        $data['tenant_id'] = \App\Support\TenantContext::currentId();
        return $data;
    }
}
