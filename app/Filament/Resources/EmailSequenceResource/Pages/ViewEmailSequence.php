<?php

namespace App\Filament\Resources\EmailSequenceResource\Pages;

use App\Filament\Resources\EmailSequenceResource;
use Filament\Actions\DeleteAction;
use Filament\Actions\EditAction;
use Filament\Resources\Pages\ViewRecord;

class ViewEmailSequence extends ViewRecord
{
    protected static string $resource = EmailSequenceResource::class;

    protected function getHeaderActions(): array
    {
        return [
            EditAction::make(),
            DeleteAction::make(),
        ];
    }
}
