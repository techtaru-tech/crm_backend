<?php

declare(strict_types=1);

namespace App\Filament\Resources\ChatbotConfigResource\Pages;

use App\Filament\Resources\ChatbotConfigResource;
use Filament\Actions\DeleteAction;
use Filament\Resources\Pages\EditRecord;

class EditChatbotConfig extends EditRecord
{
    protected static string $resource = ChatbotConfigResource::class;

    protected function getHeaderActions(): array
    {
        return [DeleteAction::make()];
    }
}
