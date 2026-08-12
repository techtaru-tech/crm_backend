<?php

declare(strict_types=1);

namespace App\Filament\Resources\ChatbotConfigResource\Pages;

use App\Filament\Resources\ChatbotConfigResource;
use Filament\Actions\CreateAction;
use Filament\Resources\Pages\ListRecords;

class ListChatbotConfigs extends ListRecords
{
    protected static string $resource = ChatbotConfigResource::class;

    protected function getHeaderActions(): array
    {
        return [CreateAction::make()];
    }
}
