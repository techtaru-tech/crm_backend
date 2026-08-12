<?php

namespace App\Filament\Resources\LeadScoringRuleResource\Pages;

use App\Filament\Resources\LeadScoringRuleResource;
use Filament\Resources\Pages\CreateRecord;

class CreateLeadScoringRule extends CreateRecord
{
    protected static string $resource = LeadScoringRuleResource::class;

    protected function mutateFormDataBeforeCreate(array $data): array
    {
        $data['tenant_id'] = \App\Support\TenantContext::currentId();
        return $data;
    }
}
