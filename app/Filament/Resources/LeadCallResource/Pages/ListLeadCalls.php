<?php

namespace App\Filament\Resources\LeadCallResource\Pages;

use App\Filament\Resources\LeadCallResource;
use Filament\Resources\Pages\ListRecords;

class ListLeadCalls extends ListRecords
{
    protected static string $resource = LeadCallResource::class;

    protected function getHeaderActions(): array
    {
        return [];
    }
}
