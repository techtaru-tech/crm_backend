<?php

namespace App\Filament\Resources\AutomationResource\Pages;

use App\Filament\Resources\AutomationResource;
use App\Models\Automation;
use App\Models\AutomationRun;
use Filament\Resources\Pages\Page;
use Illuminate\Support\Facades\Auth;

class AutomationRunHistory extends Page
{
    protected static string $resource = AutomationResource::class;

    public Automation $record;
    public array $runs = [];

    public function getView(): string
    {
        return 'filament.resources.automation-run-history';
    }

    public function mount(int|string $record): void
    {
        $tenantId = Auth::user()?->tenant_id;

        $this->record = Automation::where('id', $record)
            ->where('tenant_id', $tenantId)
            ->firstOrFail();

        $this->runs = AutomationRun::where('automation_id', $this->record->id)
            ->whereHas('lead', fn($q) => $q->where('tenant_id', $tenantId))
            ->with('lead')
            ->latest()
            ->limit(100)
            ->get()
            ->toArray();
    }

    protected function getHeaderActions(): array
    {
        return [
            \Filament\Actions\Action::make('back')
                ->label(__('filament/automations.back_to_automation'))
                ->icon('heroicon-o-arrow-left')
                ->url(AutomationResource::getUrl('edit', ['record' => $this->record])),
        ];
    }
}
