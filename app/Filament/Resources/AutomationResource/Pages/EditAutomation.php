<?php

namespace App\Filament\Resources\AutomationResource\Pages;

use App\Filament\Resources\AutomationResource;
use Filament\Actions\DeleteAction;
use Filament\Resources\Pages\EditRecord;

class EditAutomation extends EditRecord
{
    protected static string $resource = AutomationResource::class;

    protected function getHeaderActions(): array
    {
        return [
            \Filament\Actions\Action::make('flow_builder')
                ->label('Visual builder')
                ->icon('heroicon-o-share')
                ->color('primary')
                ->url(fn() => AutomationResource::getUrl('flow', ['record' => $this->record])),
            \Filament\Actions\Action::make('run_history')
                ->label(__('filament/automations.run_history'))
                ->icon('heroicon-o-clock')
                ->url(fn() => AutomationResource::getUrl('run-history', ['record' => $this->record])),
            DeleteAction::make(),
        ];
    }
}
