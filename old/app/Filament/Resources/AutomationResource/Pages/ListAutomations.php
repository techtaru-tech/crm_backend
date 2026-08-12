<?php

namespace App\Filament\Resources\AutomationResource\Pages;

use App\Filament\Pages\AutomationTemplateBrowser;
use App\Filament\Resources\AutomationResource;
use Filament\Actions\Action;
use Filament\Actions\CreateAction;
use Filament\Resources\Pages\ListRecords;

class ListAutomations extends ListRecords
{
    protected static string $resource = AutomationResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Action::make('browse_templates')
                ->label(__('filament/automations.browse_templates'))
                ->icon('heroicon-o-sparkles')
                ->color('gray')
                ->url(fn () => AutomationTemplateBrowser::getUrl()),
            CreateAction::make(),
        ];
    }
}
