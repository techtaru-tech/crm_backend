<?php

declare(strict_types=1);

namespace App\Filament\Resources\AiSdrAgentResource\Pages;

use App\Filament\Resources\AiSdrAgentResource;
use Filament\Actions\DeleteAction;
use Filament\Resources\Pages\EditRecord;

class EditAiSdrAgent extends EditRecord
{
    protected static string $resource = AiSdrAgentResource::class;

    protected function getHeaderActions(): array
    {
        return [DeleteAction::make()];
    }
}
