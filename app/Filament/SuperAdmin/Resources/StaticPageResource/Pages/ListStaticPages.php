<?php

namespace App\Filament\SuperAdmin\Resources\StaticPageResource\Pages;

use App\Filament\SuperAdmin\Resources\StaticPageResource;
use Filament\Actions\CreateAction;
use Filament\Resources\Pages\ListRecords;

class ListStaticPages extends ListRecords
{
    protected static string $resource = StaticPageResource::class;

    protected function getHeaderActions(): array
    {
        return [CreateAction::make()->label(__('filament/sa_static_pages.new_static_page'))];
    }
}
