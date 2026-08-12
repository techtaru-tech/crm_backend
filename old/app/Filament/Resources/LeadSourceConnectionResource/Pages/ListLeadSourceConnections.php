<?php

namespace App\Filament\Resources\LeadSourceConnectionResource\Pages;

use App\Filament\Resources\LeadSourceConnectionResource;
use Filament\Actions\CreateAction;
use Filament\Resources\Pages\ListRecords;

class ListLeadSourceConnections extends ListRecords
{
    protected static string $resource = LeadSourceConnectionResource::class;

    protected function getHeaderActions(): array
    {
        return [CreateAction::make()];
    }
}
