<?php

namespace App\Filament\SuperAdmin\Resources\StaticPageResource\Pages;

use App\Filament\SuperAdmin\Resources\StaticPageResource;
use Filament\Resources\Pages\CreateRecord;

class CreateStaticPage extends CreateRecord
{
    protected static string $resource = StaticPageResource::class;

    protected function getRedirectUrl(): string
    {
        // Return to the list so the operator can see the new row
        // (and its published/nav/footer state) at a glance.
        return $this->getResource()::getUrl('index');
    }

    protected function mutateFormDataBeforeCreate(array $data): array
    {
        $data['updated_by']   = auth()->id();
        $data['published_at'] = $data['published_at'] ?? now();
        return $data;
    }
}
