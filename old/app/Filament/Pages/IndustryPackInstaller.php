<?php

namespace App\Filament\Pages;

use App\Services\IndustryPackService;
use Filament\Notifications\Notification;
use Filament\Pages\Page;
use Illuminate\Contracts\Support\Htmlable;

class IndustryPackInstaller extends Page
{
    protected static string|\BackedEnum|null $navigationIcon = 'heroicon-o-building-storefront';

    protected static string|\UnitEnum|null $navigationGroup = 'Settings';
    protected static ?int    $navigationSort  = 30;
    protected static ?string $slug            = 'industry-packs';

    public static function getNavigationLabel(): string
    {
        return __('filament/industry_pack_installer.nav_label');
    }

    /**
     * Method-form title override.  Replaces the static $title property
     * because static initializers run before the translator is bound for
     * the active locale.  getTitle() resolves per-request so the page
     * header tracks the current locale via __().
     */
    public function getTitle(): string|Htmlable
    {
        return __('filament/industry_pack_installer.title');
    }

    public function getView(): string
    {
        return 'filament.pages.industry-pack-installer';
    }

    /**
     * Called from Blade with wire:click="install('real_estate')".
     * A confirmation modal is triggered client-side via confirm() before this fires.
     */
    public function install(string $key): void
    {
        $tenantId = \App\Support\TenantContext::currentId();

        if (! $tenantId) {
            Notification::make()->danger()->title(__('filament/industry_pack_installer.notif_no_tenant'))->send();
            return;
        }

        try {
            $counts = app(IndustryPackService::class)->install($key, $tenantId);
        } catch (\Throwable $e) {
            Notification::make()
                ->danger()
                ->title(__('filament/industry_pack_installer.notif_install_failed_title'))
                // Defense-in-depth: Filament Notification::body() allows
                // raw HTML per Filament docs; e() ensures future exceptions
                // carrying user-controlled data can't render as HTML in the toast.
                ->body(e($e->getMessage()))
                ->send();
            return;
        }

        $summary = __('filament/industry_pack_installer.notif_summary_body', [
            'pipelines'       => $counts['pipelines']       ?? 0,
            'stages'          => $counts['stages']          ?? 0,
            'custom_fields'   => $counts['custom_fields']   ?? 0,
            'tags'            => $counts['tags']            ?? 0,
            'email_templates' => $counts['email_templates'] ?? 0,
            'automations'     => $counts['automations']     ?? 0,
            'forms'           => $counts['forms']           ?? 0,
        ]);

        Notification::make()
            ->success()
            ->title(__('filament/industry_pack_installer.notif_installed_title'))
            ->body($summary)
            ->send();
    }

    protected function getViewData(): array
    {
        $packs = app(IndustryPackService::class)->packs();

        $rows = [];
        foreach ($packs as $pack) {
            $rows[] = [
                'key'         => $pack['key'],
                'name'        => $pack['name'],
                'description' => $pack['description'] ?? '',
                'icon'        => $pack['icon'] ?? 'heroicon-o-square-3-stack-3d',
                'pipelines'        => count($pack['pipelines'] ?? []),
                'custom_fields'    => count($pack['custom_fields'] ?? []),
                'tags'             => count($pack['tags'] ?? []),
                'email_templates'  => count($pack['email_templates'] ?? []),
                'automations'      => count($pack['automation_templates'] ?? []),
                'forms'            => count($pack['forms'] ?? []),
            ];
        }

        return ['packs' => $rows];
    }
}
