<?php

namespace App\Filament\Resources\LeadCaptureWidgetResource\Pages;

use App\Filament\Resources\LeadCaptureWidgetResource;
use Filament\Actions\DeleteAction;
use Filament\Resources\Pages\EditRecord;

class EditLeadCaptureWidget extends EditRecord
{
    protected static string $resource = LeadCaptureWidgetResource::class;

    protected function getHeaderActions(): array
    {
        return [DeleteAction::make()];
    }
}
