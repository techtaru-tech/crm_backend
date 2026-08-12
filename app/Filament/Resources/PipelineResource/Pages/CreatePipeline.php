<?php

namespace App\Filament\Resources\PipelineResource\Pages;

use App\Filament\Resources\PipelineResource;
use App\Models\PipelineStage;
use Filament\Resources\Pages\CreateRecord;

class CreatePipeline extends CreateRecord
{
    protected static string $resource = PipelineResource::class;

    // After "Create": return to the pipelines list.
    protected function getRedirectUrl(): string
    {
        return $this->getResource()::getUrl('index');
    }

    protected function mutateFormDataBeforeCreate(array $data): array
    {
        $data['tenant_id'] = \App\Support\TenantContext::currentId();
        return $data;
    }

    protected function afterCreate(): void
    {
        $tenantId = \App\Support\TenantContext::currentId();
        foreach ($this->record->stages as $stage) {
            $stage->update(['tenant_id' => $tenantId]);
        }
    }
}
