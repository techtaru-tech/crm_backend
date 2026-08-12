<?php

namespace App\Filament\Resources\MeetingTypeResource\Pages;

use App\Filament\Resources\MeetingTypeResource;
use Filament\Actions\DeleteAction;
use Filament\Resources\Pages\EditRecord;

class EditMeetingType extends EditRecord
{
    protected static string $resource = MeetingTypeResource::class;

    protected function getHeaderActions(): array
    {
        return [DeleteAction::make()];
    }
}
