<?php

namespace App\Filament\Resources\TrackingSnippetResource\Pages;

use App\Filament\Resources\TrackingSnippetResource;
use Filament\Actions\DeleteAction;
use Filament\Resources\Pages\EditRecord;

class EditTrackingSnippet extends EditRecord
{
    protected static string $resource = TrackingSnippetResource::class;

    protected function getHeaderActions(): array
    {
        return [DeleteAction::make()];
    }
}
