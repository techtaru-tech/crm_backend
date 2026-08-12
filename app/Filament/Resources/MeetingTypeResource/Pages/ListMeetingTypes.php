<?php

namespace App\Filament\Resources\MeetingTypeResource\Pages;

use App\Filament\Resources\MeetingTypeResource;
use Filament\Actions\CreateAction;
use Filament\Resources\Pages\ListRecords;

class ListMeetingTypes extends ListRecords
{
    protected static string $resource = MeetingTypeResource::class;

    protected function getHeaderActions(): array
    {
        return [CreateAction::make()];
    }
}
