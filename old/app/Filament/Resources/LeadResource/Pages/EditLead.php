<?php

namespace App\Filament\Resources\LeadResource\Pages;

use App\Filament\Resources\LeadResource;
use Filament\Actions\DeleteAction;
use Filament\Resources\Pages\EditRecord;

class EditLead extends EditRecord
{
    protected static string $resource = LeadResource::class;

    protected function getHeaderActions(): array
    {
        return [
            \Filament\Actions\Action::make('view_detail')
                ->label(__('filament/leads.full_detail_view'))
                ->icon('heroicon-o-eye')
                ->url(fn() => route('filament.admin.resources.leads.view', $this->record))
                ->color('gray'),
            DeleteAction::make(),
        ];
    }
}
