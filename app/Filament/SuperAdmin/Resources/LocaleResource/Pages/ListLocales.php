<?php

namespace App\Filament\SuperAdmin\Resources\LocaleResource\Pages;

use App\Filament\SuperAdmin\Resources\LocaleResource;
use Filament\Actions\CreateAction;
use Filament\Resources\Pages\ListRecords;

class ListLocales extends ListRecords
{
    protected static string $resource = LocaleResource::class;

    protected function getHeaderActions(): array
    {
        return [CreateAction::make()->label(__('filament/sa_locales.add_language'))];
    }
}
