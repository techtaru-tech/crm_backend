<?php

namespace App\Filament\Resources\IntegrationResource\Pages;

use App\Filament\Resources\IntegrationResource;
use App\Jobs\SyncLeadToIntegration;
use App\Models\Integration;
use App\Models\IntegrationSyncLog;
use Filament\Actions\Action;
use Filament\Notifications\Notification;
use Filament\Resources\Pages\Page;
use Illuminate\Support\Facades\Auth;

class IntegrationSyncLogs extends Page
{
    protected static string $resource = IntegrationResource::class;

    public Integration $record;
    public array $logs = [];
    public int $totalLogs = 0;

    public function getView(): string
    {
        return 'filament.resources.integration-sync-logs';
    }

    public function mount(int|string $record): void
    {
        $tenantId     = Auth::user()?->tenant_id;
        $this->record = Integration::where('id', $record)
            ->where('tenant_id', $tenantId)
            ->firstOrFail();

        $this->loadLogs();
    }

    public function loadLogs(): void
    {
        $query = IntegrationSyncLog::where('integration_id', $this->record->id)
            ->with('lead')
            ->latest();

        $this->totalLogs = $query->count();
        $this->logs      = $query->limit(100)->get()->toArray();
    }

    public function retryFailed(): void
    {
        $failed = IntegrationSyncLog::where('integration_id', $this->record->id)
            ->where('status', IntegrationSyncLog::STATUS_FAILED)
            ->get();

        foreach ($failed as $log) {
            $log->update(['status' => IntegrationSyncLog::STATUS_PENDING, 'attempts' => 0]);
            SyncLeadToIntegration::dispatch($this->record->id, $log->lead_id, $log->event, $log->id, $this->record->tenant_id);
        }

        Notification::make()->success()->title(__('filament/integrations.retrying_failed_syncs', ['count' => $failed->count()]))->send();
        $this->loadLogs();
    }

    protected function getHeaderActions(): array
    {
        return [
            Action::make('retry_failed')
                ->label(__('filament/integrations.retry_all_failed'))
                ->icon('heroicon-o-arrow-path')
                ->color('warning')
                ->action('retryFailed'),

            Action::make('back')
                ->label(__('filament/integrations.back_to_integrations'))
                ->icon('heroicon-o-arrow-left')
                ->url(IntegrationResource::getUrl()),
        ];
    }
}
