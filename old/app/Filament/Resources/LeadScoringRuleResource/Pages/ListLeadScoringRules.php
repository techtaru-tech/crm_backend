<?php

namespace App\Filament\Resources\LeadScoringRuleResource\Pages;

use App\Filament\Resources\LeadScoringRuleResource;
use Filament\Actions\CreateAction;
use Filament\Resources\Pages\ListRecords;

class ListLeadScoringRules extends ListRecords
{
    protected static string $resource = LeadScoringRuleResource::class;

    protected function getHeaderActions(): array
    {
        return [CreateAction::make()];
    }
}
