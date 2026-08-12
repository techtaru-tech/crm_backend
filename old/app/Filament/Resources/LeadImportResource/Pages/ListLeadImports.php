<?php

namespace App\Filament\Resources\LeadImportResource\Pages;

use App\Filament\Resources\LeadImportResource;
use Filament\Actions\CreateAction;
use Filament\Resources\Pages\ListRecords;

class ListLeadImports extends ListRecords
{
    protected static string $resource = LeadImportResource::class;

    protected function getHeaderActions(): array
    {
        return [CreateAction::make()->label(__('filament/lead_imports.import_csv_excel'))];
    }
}
