<?php

namespace App\Filament\SuperAdmin\Pages;

use App\Services\ModuleManagerService;
use Filament\Actions\Action;
use Filament\Forms\Components\FileUpload;
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
 * Super Admin Modules manager. Lists every nwidart module the
 * installation knows about, lets the script owner enable, disable
 * or delete a module, and uploads brand-new modules as a zip file
 * that contains a module.json manifest.
 *
 * The underlying ModuleManagerService degrades gracefully when the
 * nwidart package has not yet been installed on the server, so a
 * fresh deploy always renders a helpful state instead of crashing.
 */
class Modules extends Page implements HasForms
{
    use InteractsWithForms;

    protected static string|\BackedEnum|null $navigationIcon = 'heroicon-o-squares-plus';
    protected string $view = 'filament.super-admin.pages.modules';
    protected static ?int $navigationSort = 85;
    protected static string|UnitEnum|null $navigationGroup = 'System';

    public ?array $data = [];

    public static function getNavigationLabel(): string
    {
        return __('filament/sa_modules.navigation_label');
    }

    public function getTitle(): string|Htmlable
    {
        return __('filament/sa_modules.title');
    }

    public function mount(): void
    {
        $this->form->fill([
            'package' => null,
        ]);
    }

    public function form(Schema $form): Schema
    {
        return $form
            ->schema([
                Section::make(__('sections.install_a_module'))
                    ->description(__('filament/sa_modules.install_section_description'))
                    ->schema([
                        FileUpload::make('package')
                            ->label(__('filament/sa_modules.module_zip_label'))
                            ->disk('local')
                            ->directory('modules/uploads')
                            ->acceptedFileTypes(['application/zip', 'application/x-zip-compressed'])
                            ->maxSize(102400) // 100 MB
                            ->required()
                            ->helperText(__('filament/sa_modules.module_zip_helper')),
                    ])
                    ->columns(1),
            ])
            ->statePath('data');
    }

    public function getModules(): array
    {
        return app(ModuleManagerService::class)->all();
    }

    public function isAvailable(): bool
    {
        return app(ModuleManagerService::class)->isAvailable();
    }

    public function enable(string $name): void
    {
        // Demo lockdown: enabling a module mutates filesystem state
        // under Modules/ and can run service-provider boot code that
        // touches the DB or queue.  Hard-stop on demo.  No-op in prod.
        \App\Support\DemoMode::guard();

        try {
            app(ModuleManagerService::class)->enable($name);
            Notification::make()->title(__('notifications.module_enabled', ['name' => $name]))->success()->send();
        } catch (\Throwable $e) {
            // Defense-in-depth: e() on exception message (Filament Notification::body() allows raw HTML).
            Notification::make()->title(__('notifications.module_enable_failed'))->body(e($e->getMessage()))->danger()->send();
        }
    }

    public function disable(string $name): void
    {
        // Demo lockdown: matches enable() — same filesystem + DI
        // mutation risk in reverse.
        \App\Support\DemoMode::guard();

        try {
            app(ModuleManagerService::class)->disable($name);
            Notification::make()->title(__('notifications.module_disabled', ['name' => $name]))->success()->send();
        } catch (\Throwable $e) {
            Notification::make()->title(__('notifications.module_disable_failed'))->body(e($e->getMessage()))->danger()->send();
        }
    }

    public function remove(string $name): void
    {
        // Demo lockdown: deleting a module is the most destructive
        // of the three (filesystem rm -r under Modules/$name).
        // Hard-stop on demo.
        \App\Support\DemoMode::guard();

        try {
            app(ModuleManagerService::class)->delete($name);
            Notification::make()->title(__('notifications.module_deleted', ['name' => $name]))->success()->send();
        } catch (\Throwable $e) {
            Notification::make()->title(__('notifications.module_delete_failed'))->body(e($e->getMessage()))->danger()->send();
        }
    }

    public function install(): void
    {
        // Demo lockdown: installing a module unzips a buyer-uploaded
        // archive into the filesystem and executes the module's
        // service provider on next boot.  Letting random visitors run
        // arbitrary code is the worst possible demo footgun.
        \App\Support\DemoMode::guard();

        $state = $this->form->getState();
        $path  = $state['package'] ?? null;
        if (! $path) {
            Notification::make()->title(__('notifications.module_upload_zip_first'))->warning()->send();
            return;
        }

        $absolute = storage_path('app/private/' . $path);
        if (! file_exists($absolute)) {
            $absolute = storage_path('app/' . $path);
        }

        try {
            $name = app(ModuleManagerService::class)->installFromZip($absolute);
            Notification::make()
                ->title(__('filament/sa_modules.module_installed_title'))
                ->body(__('filament/sa_modules.module_installed_body', ['name' => $name]))
                ->success()
                ->persistent()
                ->send();
            $this->form->fill(['package' => null]);
        } catch (\Throwable $e) {
            Notification::make()->title(__('notifications.module_install_failed'))->body(e($e->getMessage()))->danger()->persistent()->send();
        }
    }

    public function regenerate(): void
    {
        try {
            // The host's ModuleAutoloadServiceProvider walks Modules/ on
            // every boot, so clearing caches is sufficient to pick up a
            // module that was added outside the admin UI (e.g. dropped
            // on disk by SFTP). We intentionally skip nwidart's
            // `module:dump` command because it shells out to composer.
            \Illuminate\Support\Facades\Cache::lock('modules:cache-clear', 10)->block(10, function () {
                Artisan::call('config:clear');
                Artisan::call('route:clear');
                Artisan::call('view:clear');
            });
            Notification::make()->title(__('notifications.module_caches_cleared'))->success()->send();
        } catch (\Throwable $e) {
            Notification::make()->title(__('notifications.module_regenerate_failed'))->body(e($e->getMessage()))->danger()->send();
        }
    }

    protected function getHeaderActions(): array
    {
        return [
            Action::make('regenerate')
                ->label(__('filament/sa_modules.action_regenerate'))
                ->icon('heroicon-o-arrow-path')
                ->color('gray')
                ->action('regenerate'),

            Action::make('install')
                ->label(__('filament/sa_modules.action_install'))
                ->icon('heroicon-o-arrow-up-tray')
                ->color('primary')
                ->requiresConfirmation()
                ->modalDescription(__('filament/sa_modules.action_install_confirmation'))
                ->action('install'),
        ];
    }
}
