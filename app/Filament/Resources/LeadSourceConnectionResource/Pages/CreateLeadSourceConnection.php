<?php

namespace App\Filament\Resources\LeadSourceConnectionResource\Pages;

use App\Filament\Resources\LeadSourceConnectionResource;
use Filament\Resources\Pages\CreateRecord;
use Illuminate\Database\Eloquent\Model;

class CreateLeadSourceConnection extends CreateRecord
{
    protected static string $resource = LeadSourceConnectionResource::class;

    protected function handleRecordCreation(array $data): Model
    {
        $tenantId = \App\Support\TenantContext::currentId();
        $data['tenant_id'] = $tenantId;
        return parent::handleRecordCreation($data);
    }
}
