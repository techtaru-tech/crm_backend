<?php

namespace App\Filament\Resources\ScheduledReportResource\Pages;

use App\Filament\Resources\ScheduledReportResource;
use Filament\Actions\DeleteAction;
use Filament\Resources\Pages\EditRecord;

class EditScheduledReport extends EditRecord
{
    protected static string $resource = ScheduledReportResource::class;

    protected function getHeaderActions(): array
    {
        return [DeleteAction::make()];
    }

    protected function mutateFormDataBeforeSave(array $data): array
    {
        $data['recipient_emails'] = array_column($data['recipient_emails'] ?? [], 'email');
        return $data;
    }

    protected function mutateFormDataBeforeFill(array $data): array
    {
        $data['recipient_emails'] = array_map(
            fn($email) => ['email' => $email],
            $data['recipient_emails'] ?? []
        );
        return $data;
    }
}
