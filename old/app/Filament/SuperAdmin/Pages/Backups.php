<?php

namespace App\Filament\SuperAdmin\Pages;

use App\Services\BackupService;
use Filament\Actions\Action;
use Filament\Notifications\Notification;
use Filament\Pages\Page;
use Illuminate\Contracts\Support\Htmlable;
use Symfony\Component\HttpFoundation\BinaryFileResponse;
use UnitEnum;

/**
 * Super Admin backup dashboard. Creates on-demand backups, lists every
 * archive on disk, and lets the owner download / restore / delete
 * individual files from a single view. Sits under System so it's one
 * click away from System Health.
 */
class Backups extends Page
{
    protected static string|\BackedEnum|null $navigationIcon = 'heroicon-o-archive-box';
    protected string $view = 'filament.super-admin.pages.backups';
    protected static ?int $navigationSort = 85;
    protected static string|UnitEnum|null $navigationGroup = 'System';

    public static function getNavigationLabel(): string
    {
        return __('filament/sa_backups.navigation_label');
    }

    public function getTitle(): string|Htmlable
    {
        return __('filament/sa_backups.title');
    }

    public function getBackups(): array
    {
        return app(BackupService::class)->list();
    }

    public function createBackup(): void
    {
        \App\Support\DemoMode::guard(__('filament/demo_mode.backups_create_method_blocked'));

        try {
            $path = app(BackupService::class)->create([
                'database' => true,
                'files'    => true,
            ]);

            Notification::make()
                ->title(__('filament/sa_backups.backup_created_title'))
                ->body(basename($path))
                ->success()
                ->send();
        } catch (\Throwable $e) {
            Notification::make()
                ->title(__('filament/sa_backups.backup_failed_title'))
                // Defense-in-depth: e() on exception message.
                ->body(e($e->getMessage()))
                ->danger()
                ->send();
        }
    }

    public function deleteBackup(string $name): void
    {
        \App\Support\DemoMode::guard(__('filament/demo_mode.backups_delete_method_blocked'));

        if (app(BackupService::class)->delete($name)) {
            Notification::make()
                ->title(__('filament/sa_backups.backup_deleted_title'))
                ->body($name)
                ->success()
                ->send();
        } else {
            Notification::make()
                ->title(__('filament/sa_backups.backup_delete_failed_title'))
                ->body($name)
                ->danger()
                ->send();
        }
    }

    public function restoreBackup(string $name): void
    {
        \App\Support\DemoMode::guard(__('filament/demo_mode.backups_restore_method_blocked'));

        try {
            $summary = app(BackupService::class)->restore($name);

            Notification::make()
                ->title(__('filament/sa_backups.restore_complete_title'))
                ->body(__('filament/sa_backups.restore_complete_body', [
                    'files'  => $summary['files_restored'],
                    'rows'   => $summary['rows_imported'],
                    'backup' => $summary['backup'],
                ]))
                ->success()
                ->persistent()
                ->send();
        } catch (\Throwable $e) {
            Notification::make()
                ->title(__('filament/sa_backups.restore_failed_title'))
                // Defense-in-depth: e() on exception message.
                ->body(e($e->getMessage()))
                ->danger()
                ->send();
        }
    }

    public function downloadBackup(string $name): BinaryFileResponse
    {
        $path = app(BackupService::class)->path($name);
        abort_unless($path && file_exists($path), 404);

        return response()->download($path);
    }

    /**
     * Open the archive and confirm it is a valid, restorable backup
     * without actually doing the restore. Surfaces the SQL-statement
     * count and any structural errors to the operator inline.
     */
    public function verifyBackup(string $name): void
    {
        $svc  = app(BackupService::class);
        $path = $svc->path($name);

        if (! $path || ! file_exists($path)) {
            Notification::make()
                ->title(__('filament/sa_backups.backup_not_found_title'))
                ->body($name)
                ->danger()
                ->send();
            return;
        }

        $result = $svc->verify($path);

        if ($result['ok']) {
            Notification::make()
                ->title(__('filament/sa_backups.backup_healthy_title'))
                ->body(__('filament/sa_backups.backup_healthy_body', [
                    'name'  => $name,
                    'count' => $result['sql_statements'],
                ]))
                ->success()
                ->send();
            return;
        }

        Notification::make()
            ->title(__('filament/sa_backups.backup_verify_failed_title'))
            ->body($name . ' — ' . implode('; ', $result['errors']))
            ->danger()
            ->persistent()
            ->send();
    }

    protected function getHeaderActions(): array
    {
        return [
            Action::make('create')
                ->label(__('filament/sa_backups.action_create'))
                ->before(fn () => \App\Support\DemoMode::guard(__('filament/demo_mode.backups_create_action_blocked')))
                ->icon('heroicon-o-plus')
                ->color('primary')
                ->action('createBackup'),
        ];
    }

    /**
     * Filament Action wrapping the restore call with a styled
     * confirmation modal.  Triggered from the table row via
     * `$wire.mountAction('restoreBackup', { name: '...' })` so the
     * existing CSS-styled buttons keep their look while the
     * confirmation experience is the proper Filament dialog (heading,
     * description, danger icon, explicit submit label) rather than the
     * native browser `confirm()` dialog Livewire's `wire:confirm`
     * produces.
     */
    public function restoreBackupAction(): Action
    {
        return Action::make('restoreBackup')
            ->before(fn () => \App\Support\DemoMode::guard(__('filament/demo_mode.backups_restore_action_blocked')))
            ->requiresConfirmation()
            ->modalHeading(__('filament/sa_backups.restore_modal_heading'))
            ->modalDescription(__('filament/sa_backups.restore_modal_description'))
            ->modalIcon('heroicon-o-exclamation-triangle')
            ->modalIconColor('warning')
            ->modalSubmitActionLabel(__('filament/sa_backups.restore_modal_submit'))
            ->color('warning')
            ->action(fn (array $arguments) => $this->restoreBackup((string) ($arguments['name'] ?? '')));
    }

    /**
     * Filament Action wrapping the delete call with a styled
     * confirmation modal.  Same trigger pattern as the restore action.
     */
    public function deleteBackupAction(): Action
    {
        return Action::make('deleteBackup')
            ->before(fn () => \App\Support\DemoMode::guard(__('filament/demo_mode.backups_delete_action_blocked')))
            ->requiresConfirmation()
            ->modalHeading(__('filament/sa_backups.delete_modal_heading'))
            ->modalDescription(__('filament/sa_backups.delete_modal_description'))
            ->modalIcon('heroicon-o-trash')
            ->modalIconColor('danger')
            ->modalSubmitActionLabel(__('filament/sa_backups.delete_modal_submit'))
            ->color('danger')
            ->action(fn (array $arguments) => $this->deleteBackup((string) ($arguments['name'] ?? '')));
    }

    public static function humanSize(int $bytes): string
    {
        $units = ['B', 'KB', 'MB', 'GB'];
        $i = 0;
        while ($bytes >= 1024 && $i < count($units) - 1) {
            $bytes /= 1024;
            $i++;
        }
        return number_format($bytes, $i === 0 ? 0 : 2) . ' ' . $units[$i];
    }
}
