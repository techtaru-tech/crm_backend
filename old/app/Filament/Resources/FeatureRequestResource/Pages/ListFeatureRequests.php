<?php

namespace App\Filament\Resources\FeatureRequestResource\Pages;

use App\Filament\Resources\FeatureRequestResource;
use Filament\Actions\CreateAction;
use Filament\Resources\Pages\ListRecords;

class ListFeatureRequests extends ListRecords
{
    protected static string $resource = FeatureRequestResource::class;

    protected function getHeaderActions(): array
    {
        return [CreateAction::make()->label(__('filament/feature_requests.submit_request'))];
    }
}
