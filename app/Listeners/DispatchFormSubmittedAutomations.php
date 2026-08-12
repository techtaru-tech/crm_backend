<?php

namespace App\Listeners;

use App\Events\FormSubmitted;
use App\Services\Automations\AutomationDispatcher;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Queue\InteractsWithQueue;

class DispatchFormSubmittedAutomations implements ShouldQueue
{
    use InteractsWithQueue, Queueable;

    public $tries = 3;

    public function __construct(private AutomationDispatcher $dispatcher)
    {
        $this->onQueue('automations');
    }

    public function handle(FormSubmitted $event): void
    {
        if ($event->lead) {
            $this->dispatcher->dispatch('form_submitted', $event->lead);
        }
    }
}
