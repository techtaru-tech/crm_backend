<?php

declare(strict_types=1);

namespace App\Filament\Resources\AutomationResource\Pages;

use App\Filament\Resources\AutomationResource;
use App\Models\Automation;
use App\Models\AutomationStep;
use App\Services\Automations\AutomationFlowGraph;
use Filament\Actions\Action;
use Filament\Notifications\Notification;
use Filament\Resources\Pages\Page;
use Illuminate\Support\Facades\Auth;
use InvalidArgumentException;

/**
 * Visual Flow Builder — a drag-drop node canvas over the existing automation
 * engine. It reads/writes the SAME automation_steps the classic editor produces
 * (via AutomationFlowGraph), so the execution engine never changes; node
 * positions live in the presentation-only automations.nodes_layout column.
 */
class AutomationFlowBuilder extends Page
{
    protected static string $resource = AutomationResource::class;

    public Automation $record;

    /** Node graph handed to the canvas on load (and refreshed after a save). */
    public array $graph = [];

    public function getView(): string
    {
        return 'filament.resources.automation-flow-builder';
    }

    public function getTitle(): string
    {
        return 'Flow Builder — ' . $this->record->name;
    }

    public function mount(int|string $record): void
    {
        $tenantId = Auth::user()?->tenant_id;

        $this->record = Automation::query()
            ->where('id', $record)
            ->where('tenant_id', $tenantId)
            ->firstOrFail();

        $this->graph = app(AutomationFlowGraph::class)->toGraph($this->record);
    }

    /**
     * Persist the canvas. A malformed/unsupported graph surfaces a friendly
     * error and changes nothing (AutomationFlowGraph::apply is atomic and
     * throws InvalidArgumentException before touching the DB).
     */
    public function saveGraph(array $graph): void
    {
        try {
            app(AutomationFlowGraph::class)->apply($this->record, $graph);
        } catch (InvalidArgumentException $e) {
            Notification::make()
                ->danger()
                ->title('Could not save the flow')
                ->body($e->getMessage())
                ->send();

            return;
        }

        $this->record->refresh();
        $this->graph = app(AutomationFlowGraph::class)->toGraph($this->record);

        Notification::make()
            ->success()
            ->title('Flow saved')
            ->send();
    }

    /**
     * Catalogs that drive the node palette + per-node config editor, so the
     * canvas can only ever produce node kinds/types the engine understands.
     */
    protected function getViewData(): array
    {
        return [
            'catalog' => [
                'triggers'   => Automation::TRIGGERS,
                'actions'    => AutomationStep::ACTION_TYPES,
                'conditions' => AutomationStep::CONDITION_TYPES,
            ],
        ];
    }

    protected function getHeaderActions(): array
    {
        return [
            Action::make('back')
                ->label(__('filament/automations.back_to_automation'))
                ->icon('heroicon-o-arrow-left')
                ->url(AutomationResource::getUrl('edit', ['record' => $this->record])),
        ];
    }
}
