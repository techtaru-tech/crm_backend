<?php

namespace App\Filament\Resources\ScheduledReportResource\Pages;

use App\Filament\Resources\ScheduledReportResource;
use Filament\Actions\CreateAction;
use Filament\Resources\Pages\ListRecords;

class ListScheduledReports extends ListRecords
{
    protected static string $resource = ScheduledReportResource::class;

    protected function getHeaderActions(): array
    {
        return [CreateAction::make()];
    }
}
