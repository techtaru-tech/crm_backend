<?php

namespace App\Filament\Resources\TrackingSnippetResource\Pages;

use App\Filament\Resources\TrackingSnippetResource;
use Filament\Actions\CreateAction;
use Filament\Resources\Pages\ListRecords;

class ListTrackingSnippets extends ListRecords
{
    protected static string $resource = TrackingSnippetResource::class;

    protected function getHeaderActions(): array
    {
        return [CreateAction::make()];
    }
}
