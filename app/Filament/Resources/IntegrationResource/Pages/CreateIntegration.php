<?php

namespace App\Filament\Resources\IntegrationResource\Pages;

use App\Filament\Resources\IntegrationResource;
use Filament\Resources\Pages\CreateRecord;

class CreateIntegration extends CreateRecord
{
    protected static string $resource = IntegrationResource::class;

    protected function mutateFormDataBeforeCreate(array $data): array
    {
        return IntegrationResource::mutateFormDataBeforeCreate($data);
    }
}
