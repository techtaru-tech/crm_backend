<?php

namespace App\Filament\Pages;

use App\Filament\Resources\AutomationResource;
use App\Models\Automation;
use App\Services\AutomationTemplateService;
use Filament\Notifications\Notification;
use Filament\Pages\Page;
use Illuminate\Contracts\Support\Htmlable;

class AutomationTemplateBrowser extends Page
{
    protected static string|\BackedEnum|null $navigationIcon = 'heroicon-o-sparkles';

    // Hidden from the sidebar — accessed via button on the Automations list.
    protected static bool $shouldRegisterNavigation = false;

    protected static ?string $slug  = 'automation-templates';

    /**
     * Method-form title override.  Replaces the static $title property
     * because Filament resolves static properties before the translator
     * is fully bound, leaving English strings baked into the page header
     * for non-English locales.  getTitle() runs per-request so it picks
     * up the active locale via __().
     */
    public function getTitle(): string|Htmlable
    {
        return __('filament/automation_template_browser.title');
    }

    public function getView(): string
    {
        return 'filament.pages.automation-template-browser';
    }

    public function templates(): array
    {
        return app(AutomationTemplateService::class)->templates();
    }

    public function useTemplate(string $key): void
    {
        $tenantId = \App\Support\TenantContext::currentId();
        if (! $tenantId) {
            Notification::make()->danger()->title(__('filament/automation_template_browser.notif_no_tenant'))->send();
            return;
        }

        try {
            /** @var Automation $automation */
            $automation = app(AutomationTemplateService::class)->install($key, $tenantId);
        } catch (\Throwable $e) {
            Notification::make()->danger()
                ->title(__('filament/automation_template_browser.notif_install_failed_title'))
                // Defense-in-depth: e() on exception message
                // (Filament Notification::body() allows raw HTML).
                ->body(e($e->getMessage()))
                ->send();
            return;
        }

        Notification::make()->success()
            ->title(__('filament/automation_template_browser.notif_installed_title'))
            ->body(__('filament/automation_template_browser.notif_installed_body'))
            ->send();

        $this->redirect(AutomationResource::getUrl('edit', ['record' => $automation->id]));
    }

    protected function getViewData(): array
    {
        $templates = $this->templates();

        return [
            'templates' => array_map(function (array $tpl) {
                return [
                    'key'          => $tpl['key'],
                    'name'         => $tpl['name'],
                    'description'  => $tpl['description'] ?? '',
                    'trigger_type' => Automation::triggerLabels()[$tpl['trigger_type']] ?? $tpl['trigger_type'],
                    'step_count'   => count($tpl['steps'] ?? []),
                ];
            }, $templates),
        ];
    }
}
