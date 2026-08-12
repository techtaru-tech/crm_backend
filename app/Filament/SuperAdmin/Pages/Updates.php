<?php

namespace App\Filament\SuperAdmin\Pages;

use App\Services\UpdaterService;
use App\Settings\UpdateSettings;
use Filament\Actions\Action;
use Filament\Forms\Components\FileUpload;
use Filament\Forms\Components\Toggle;
use Filament\Forms\Concerns\InteractsWithForms;
use Filament\Forms\Contracts\HasForms;
use Filament\Notifications\Notification;
use Filament\Pages\Page;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Schema;
use Illuminate\Contracts\Support\Htmlable;
use Illuminate\Support\Facades\Artisan;
use UnitEnum;

/**
 * Super Admin update center. Shows the current version, the latest
 * version reported by the update server (from leadhub:check-updates),
 * the full history of previously applied upgrades, and a zip upload
 * form that runs UpdaterService to apply a release package in place.
 */
class Updates extends Page implements HasForms
{
    use InteractsWithForms;

    protected static string|\BackedEnum|null $navigationIcon = 'heroicon-o-arrow-up-tray';
    protected string $view = 'filament.super-admin.pages.updates';
    protected static ?int $navigationSort = 86;
    protected static string|UnitEnum|null $navigationGroup = 'System';

    public ?array $data = [];

    public static function getNavigationLabel(): string
    {
        return __('filament/sa_updates.navigation_label');
    }

    public function getTitle(): string|Htmlable
    {
        return __('filament/sa_updates.title');
    }

    public function mount(): void
    {
        $this->form->fill([
            'skip_backup' => false,
            'package'     => null,
        ]);
    }

    public function form(Schema $form): Schema
    {
        return $form
            ->schema([
                Section::make(__('sections.apply_an_update_package'))
                    ->description(__('filament/sa_updates.apply_section_description'))
                    ->schema([
                        FileUpload::make('package')
                            ->label(__('filament/sa_updates.package_label'))
                            ->disk('local')
                            ->directory('updates/uploads')
                            ->acceptedFileTypes(['application/zip', 'application/x-zip-compressed'])
                            ->maxSize(204800) // 200 MB
                            ->required()
                            ->helperText(__('filament/sa_updates.package_helper')),

                        Toggle::make('skip_backup')
                            ->label(__('filament/sa_updates.skip_backup_label'))
                            ->helperText(__('filament/sa_updates.skip_backup_helper')),
                    ])
                    ->columns(1),
            ])
            ->statePath('data');
    }

    public function getVersionInfo(): array
    {
        $current = config('leadhub.version', '1.0.0');
        $update  = null;

        try {
            $us = app(UpdateSettings::class);
            $update = [
                'latest_version' => $us->latest_version,
                'update_available' => $us->update_available,
                'changelog_url'  => $us->changelog_url,
                'last_checked_at' => $us->last_checked_at,
            ];
        } catch (\Throwable $e) {
            \Illuminate\Support\Facades\Log::warning('[updates] version-info load failed', ['error' => $e->getMessage()]);
        }

        return [
            'current' => $current,
            'update'  => $update,
        ];
    }

    public function getHistory(): array
    {
        // Render-time call: never let a history-file read (corrupt JSON, a
        // permission error, or a directory the just-applied update touched)
        // crash the whole page with Filament's "Error while loading page".
        // getVersionInfo() above is already guarded the same way.
        try {
            return app(UpdaterService::class)->history();
        } catch (\Throwable $e) {
            \Illuminate\Support\Facades\Log::warning('[updates] history load failed', ['error' => $e->getMessage()]);

            return [];
        }
    }

    public function checkForUpdates(): void
    {
        try {
            Artisan::call('leadhub:check-updates');
            Notification::make()
                ->title(__('filament/sa_updates.check_complete_title'))
                ->body(trim(Artisan::output()) ?: __('filament/sa_updates.check_complete_default_body'))
                ->success()
                ->send();
        } catch (\Throwable $e) {
            Notification::make()
                ->title(__('filament/sa_updates.check_failed_title'))
                // Defense-in-depth: e() on exception message.
                ->body(e($e->getMessage()))
                ->danger()
                ->send();
        }
    }

    public function apply(): void
    {
        \App\Support\DemoMode::guard(__('filament/demo_mode.updates_blocked'));

        $state = $this->form->getState();
        $path  = $state['package'] ?? null;

        if (! $path) {
            Notification::make()->title(__('notifications.module_upload_zip_first'))->warning()->send();
            return;
        }

        // Filament stores the upload under the local disk root.
        $absolute = storage_path('app/private/' . $path);
        if (! file_exists($absolute)) {
            $absolute = storage_path('app/' . $path);
        }

        try {
            $summary = app(UpdaterService::class)->apply($absolute, [
                'skip_backup' => (bool) ($state['skip_backup'] ?? false),
            ]);

            $body = __('filament/sa_updates.apply_summary_files_written', ['count' => $summary['files_written']]);
            if ($summary['to_version']) {
                $body .= __('filament/sa_updates.apply_summary_version', ['version' => $summary['to_version']]);
            }
            if ($summary['backup']) {
                $body .= __('filament/sa_updates.apply_summary_backup', ['backup' => $summary['backup']]);
            }

            Notification::make()
                ->title(__('filament/sa_updates.apply_success_title'))
                ->body($body)
                ->success()
                ->persistent()
                ->send();

            // A self-applied update overwrites this very app's PHP files
            // mid-request.  Re-rendering the page in place (Livewire/Filament
            // SPA) afterwards runs the browser's existing component against a
            // now-swapped backend (changed class signatures, freshly cleared
            // caches) and surfaces Filament's "Error while loading page"
            // overlay even though the update fully succeeded.  Force a
            // FULL-PAGE reload (navigate: false) so the browser re-bootstraps
            // cleanly against the new code.  The persistent success
            // notification above is flashed to the session and shows on the
            // reloaded page (the same mechanism Filament uses after
            // create/edit).  mount() re-fills the form, so no manual reset.
            $this->redirect(static::getUrl(), navigate: false);
        } catch (\Throwable $e) {
            Notification::make()
                ->title(__('filament/sa_updates.apply_failed_title'))
                // Defense-in-depth: e() on exception message.
                ->body(e($e->getMessage()))
                ->danger()
                ->persistent()
                ->send();
        }
    }

    protected function getHeaderActions(): array
    {
        return [
            Action::make('check')
                ->label(__('filament/sa_updates.action_check'))
                ->icon('heroicon-o-arrow-path')
                ->color('gray')
                ->action('checkForUpdates'),

            Action::make('apply')
                ->label(__('filament/sa_updates.action_apply'))
                ->icon('heroicon-o-arrow-up-tray')
                ->color('primary')
                ->requiresConfirmation()
                ->modalDescription(__('filament/sa_updates.action_apply_confirmation'))
                ->action('apply'),
        ];
    }
}
