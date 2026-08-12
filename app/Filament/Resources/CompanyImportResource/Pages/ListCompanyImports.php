<?php

namespace App\Filament\Resources\CompanyImportResource\Pages;

use App\Filament\Resources\CompanyImportResource;
use Filament\Actions\CreateAction;
use Filament\Resources\Pages\ListRecords;

class ListCompanyImports extends ListRecords
{
    protected static string $resource = CompanyImportResource::class;

    protected function getHeaderActions(): array
    {
        return [
            CreateAction::make()->label(__('filament/company_imports.import_csv_excel')),
        ];
    }
}
