<?php

namespace App\Filament\Resources\LeadScoringRuleResource\Pages;

use App\Filament\Resources\LeadScoringRuleResource;
use Filament\Actions\DeleteAction;
use Filament\Resources\Pages\EditRecord;

class EditLeadScoringRule extends EditRecord
{
    protected static string $resource = LeadScoringRuleResource::class;

    protected function getHeaderActions(): array
    {
        return [DeleteAction::make()];
    }
}
