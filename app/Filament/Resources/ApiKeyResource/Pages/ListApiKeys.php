<?php

namespace App\Filament\Resources\ApiKeyResource\Pages;

use App\Filament\Resources\ApiKeyResource;
use Filament\Actions\CreateAction;
use Filament\Resources\Pages\ListRecords;

class ListApiKeys extends ListRecords
{
    protected static string $resource = ApiKeyResource::class;

    protected string $view = 'filament.resources.api-key-resource.pages.list-api-keys';

    public ?string $newApiKey = null;

    public function mount(): void
    {
        parent::mount();
        $this->newApiKey = session('new_api_key');
    }

    protected function getHeaderActions(): array
    {
        return [
            CreateAction::make()->label(__('filament/api_keys.new_api_key')),
        ];
    }
}
