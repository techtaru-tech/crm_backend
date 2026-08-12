<?php

declare(strict_types=1);

namespace App\Filament\Resources\AiSdrAgentResource\Pages;

use App\Filament\Resources\AiSdrAgentResource;
use Filament\Actions\CreateAction;
use Filament\Resources\Pages\ListRecords;

class ListAiSdrAgents extends ListRecords
{
    protected static string $resource = AiSdrAgentResource::class;

    protected function getHeaderActions(): array
    {
        return [CreateAction::make()];
    }
}
