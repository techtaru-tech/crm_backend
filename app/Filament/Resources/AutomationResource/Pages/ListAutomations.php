<?php

namespace App\Filament\Resources\AutomationResource\Pages;

use App\Filament\Pages\AutomationTemplateBrowser;
use App\Filament\Resources\AutomationResource;
use Filament\Actions\Action;
use Filament\Actions\CreateAction;
use App\Support\QueueHealth;
use Filament\Notifications\Notification;
use Filament\Resources\Pages\ListRecords;

class ListAutomations extends ListRecords
{
    protected static string $resource = AutomationResource::class;

    /**
     * Warn when automations are being queued but nothing is draining the
     * queue.  Every automation runs as a job, so a stopped worker makes them
     * all look broken while the automations themselves are fine — and there
     * is no error anywhere to lead you to the real cause.  This is the screen
     * someone opens when "my automation didn't run", so the answer belongs here.
     */
    public function mount(): void
    {
        parent::mount();

        if ($backlog = QueueHealth::backlog()) {
            Notification::make()
                ->warning()
                ->persistent()
                ->title(__('filament/automations.queue_stalled_title'))
                ->body(__('filament/automations.queue_stalled_body', $backlog))
                ->send();
        }
    }

    protected function getHeaderActions(): array
    {
        return [
            Action::make('browse_templates')
                ->label(__('filament/automations.browse_templates'))
                ->icon('heroicon-o-sparkles')
                ->color('gray')
                ->url(fn () => AutomationTemplateBrowser::getUrl()),
            CreateAction::make(),
        ];
    }
}
