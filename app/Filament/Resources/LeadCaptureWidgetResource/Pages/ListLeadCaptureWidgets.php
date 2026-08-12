<?php

namespace App\Filament\Resources\LeadCaptureWidgetResource\Pages;

use App\Filament\Resources\LeadCaptureWidgetResource;
use Filament\Actions\CreateAction;
use Filament\Resources\Pages\ListRecords;

class ListLeadCaptureWidgets extends ListRecords
{
    protected static string $resource = LeadCaptureWidgetResource::class;

    protected function getHeaderActions(): array
    {
        return [CreateAction::make()];
    }
}
