<?php

namespace App\Filament\Resources\MeetingBookingResource\Pages;

use App\Filament\Resources\MeetingBookingResource;
use Filament\Resources\Pages\ListRecords;

class ListMeetingBookings extends ListRecords
{
    protected static string $resource = MeetingBookingResource::class;

    protected function getHeaderActions(): array
    {
        return [];
    }
}
