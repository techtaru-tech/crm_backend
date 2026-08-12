<?php

namespace App\Filament\Resources\MeetingTypeResource\Pages;

use App\Filament\Resources\MeetingTypeResource;
use Filament\Resources\Pages\CreateRecord;

class CreateMeetingType extends CreateRecord
{
    protected static string $resource = MeetingTypeResource::class;

    protected function mutateFormDataBeforeCreate(array $data): array
    {
        if (empty($data['user_id'])) {
            $data['user_id'] = auth()->id();
        }
        return $data;
    }
}
