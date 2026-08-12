<?php

namespace App\Filament\Resources\ScheduledReportResource\Pages;

use App\Filament\Resources\ScheduledReportResource;
use Filament\Resources\Pages\CreateRecord;

class CreateScheduledReport extends CreateRecord
{
    protected static string $resource = ScheduledReportResource::class;

    protected function mutateFormDataBeforeCreate(array $data): array
    {
        $data['tenant_id']        = \App\Support\TenantContext::currentId();
        $data['recipient_emails'] = array_column($data['recipient_emails'] ?? [], 'email');
        $data['next_due_at']      = (new \App\Models\ScheduledReport(array_merge($data, ['recipient_emails' => []])))->calculateNextDue();
        return $data;
    }
}
