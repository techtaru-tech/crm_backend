<?php

namespace App\Filament\SuperAdmin\Pages;

use App\Support\DemoMode;
use Filament\Actions\Action;
use Filament\Notifications\Notification;
use Filament\Pages\Page;
use Illuminate\Contracts\Support\Htmlable;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\Cache;
use UnitEnum;

class SystemHealth extends Page
{
    protected static string|\BackedEnum|null $navigationIcon = 'heroicon-o-server-stack';
    protected string $view = 'filament.super-admin.pages.system-health';
    protected static ?int $navigationSort = 90;
    protected static string|UnitEnum|null $navigationGroup = 'System';

    public function getTitle(): string|Htmlable
    {
        return __('filament/sa_system_health.title');
    }

    /**
     * Header maintenance buttons.  Whole array returns empty in demo
     * mode so the buttons are not visible on the public demo (they
     * would otherwise let any visitor wipe caches / break the box).
     * Each method also calls DemoMode::guard() as belt-and-braces in
     * case demo mode flips ON between render and POST.
     */
    protected function getHeaderActions(): array
    {
        if (DemoMode::isOn()) {
            return [];
        }

        return [
            // NOTE: there is intentionally NO "Finalize update" button here.
            // Applying a release on the Updates page (UpdaterService::apply())
            // already runs the full finalize sequence automatically — there is
            // nothing left for the operator to press afterwards.  The granular
            // actions below remain for the edge case where files were replaced
            // OUTSIDE the app (cPanel File Manager / SFTP overwrite) and the
            // caches/migrations need a manual nudge.
            Action::make('clear_caches')
                ->label(__('filament/sa_system_health.action_clear_caches'))
                ->icon('heroicon-o-trash')
                ->color('gray')
                ->requiresConfirmation()
                ->modalDescription(__('filament/sa_system_health.action_clear_caches_confirm'))
                ->action('clearCaches'),

            Action::make('run_migrations')
                ->label(__('filament/sa_system_health.action_run_migrations'))
                ->icon('heroicon-o-circle-stack')
                ->color('gray')
                ->requiresConfirmation()
                ->modalDescription(__('filament/sa_system_health.action_run_migrations_confirm'))
                ->action('runMigrations'),

            Action::make('rebuild_caches')
                ->label(__('filament/sa_system_health.action_rebuild_caches'))
                ->icon('heroicon-o-arrow-path')
                ->color('gray')
                ->requiresConfirmation()
                ->modalDescription(__('filament/sa_system_health.action_rebuild_caches_confirm'))
                ->action('rebuildCaches'),

            Action::make('storage_link')
                ->label(__('filament/sa_system_health.action_storage_link'))
                ->icon('heroicon-o-link')
                ->color('gray')
                ->requiresConfirmation()
                ->modalDescription(__('filament/sa_system_health.action_storage_link_confirm'))
                ->action('linkStorage'),

            Action::make('restart_queue')
                ->label(__('filament/sa_system_health.action_restart_queue'))
                ->icon('heroicon-o-bolt')
                ->color('gray')
                ->requiresConfirmation()
                ->modalDescription(__('filament/sa_system_health.action_restart_queue_confirm'))
                ->action('restartQueue'),
        ];
    }

    /**
     * `php artisan optimize:clear` — flushes config + route + view +
     * event + compiled caches in one shot.  Most common need for
     * CodeCanyon buyers on shared hosting (no SSH) right after they
     * deploy a new release or edit settings.
     *
     * Wrapped in try/catch so a read-only cache dir on a broken
     * shared host surfaces a danger toast instead of a 500.  A
     * Cache::lock prevents two operators (or two tabs) from clearing
     * concurrently, which on some hosts causes "file not found"
     * mid-truncate races.
     */
    /**
     * One-click full post-update sequence.  Mirrors the exact step
     * order that UpdaterService::apply() runs at the end of a zip
     * upload, for buyers who replaced files OUTSIDE the SA Updates
     * page (e.g. cPanel File Manager, SFTP).
     *
     *   1. Delete stale bootstrap/cache/*.php so new providers /
     *      packages get re-discovered (mirrors install.php:128 +
     *      UpdaterService::apply() step 5).
     *   2. migrate --force — applies pending schema + settings
     *      migrations from the new files.
     *   3. config:clear + cache:clear + view:clear + route:clear —
     *      flush every Laravel cache so the new code reads fresh
     *      values.
     *   4. config:cache + route:cache + view:cache + event:cache
     *      (+ filament:cache-components + icons:cache when those
     *      artisan commands are registered) — re-warm the prod
     *      caches so the first post-update request isn't slow.
     *
     * Each step is wrapped individually so a single failure (e.g.
     * read-only bootstrap/cache/ on a broken host) doesn't abort
     * the rest — the buyer gets a per-step summary in the toast
     * body telling them what worked, what didn't, and what to look
     * at.  Cache::lock TTL bumped to 300s because the rebuild phase
     * can take a while on slow shared hosts.
     */
    public function finalizeUpdate(): void
    {
        DemoMode::guard();

        $steps     = [];
        $failures  = [];
        $artisanAll = Artisan::all();

        $run = function (string $label, callable $thunk) use (&$steps, &$failures): void {
            try {
                $thunk();
                $steps[] = '✓ ' . $label;
            } catch (\Throwable $e) {
                $failures[] = $label . ' — ' . $e->getMessage();
                $steps[] = '✗ ' . $label;
            }
        };

        try {
            Cache::lock('system-health:maintenance', 300)->block(15, function () use (&$steps, &$run, $artisanAll): void {
                // Step 1: delete stale bootstrap caches.
                $run('Stale bootstrap caches removed', function (): void {
                    foreach (glob(base_path('bootstrap/cache/*.php')) ?: [] as $cacheFile) {
                        @unlink($cacheFile);
                    }
                });

                // Step 2: migrate.
                $run('Database migrations applied', fn () => Artisan::call('migrate', ['--force' => true]));

                // Step 3: clear every cache.
                foreach (['config:clear', 'cache:clear', 'view:clear', 'route:clear'] as $clearStep) {
                    $run($clearStep, fn () => Artisan::call($clearStep));
                }

                // Step 3b: republish Filament's compiled CSS / JS into
                // public/.  THIS is the actual fix for "broken / unstyled
                // admin panel (or tenant profile) after an update".  A
                // CodeCanyon zip-deploy never runs composer's
                // post-autoload-dump hook — which is what normally runs
                // filament:upgrade → filament:assets — so the published
                // files under public/css/filament + public/js/filament go
                // stale after a Filament version bump and the panel
                // renders unstyled with missing icons.  Feature-detected
                // so a stripped-down install (Filament removed) doesn't
                // fail this step.
                if (array_key_exists('filament:assets', $artisanAll)) {
                    $run('filament:assets (republish panel CSS / JS)', fn () => Artisan::call('filament:assets'));
                }

                // Step 4: rebuild production caches.  Filament + icons
                // commands feature-detected so a stripped-down install
                // doesn't fail this step.
                $rebuildSteps = ['config:cache', 'route:cache', 'view:cache', 'event:cache'];
                foreach (['filament:cache-components', 'icons:cache'] as $optional) {
                    if (array_key_exists($optional, $artisanAll)) {
                        $rebuildSteps[] = $optional;
                    }
                }
                foreach ($rebuildSteps as $rebuildStep) {
                    $run($rebuildStep, fn () => Artisan::call($rebuildStep));
                }
            });
        } catch (\Throwable $e) {
            // The Cache::lock outer try/catch handles the case where
            // we couldn't even acquire the lock (e.g. another tab is
            // running this exact sequence concurrently).
            Notification::make()
                ->title(__('filament/sa_system_health.notif_finalize_update_failed_title'))
                ->body(e($e->getMessage()))
                ->danger()
                ->persistent()
                ->send();
            return;
        }

        $body = implode("\n", $steps);
        if ($failures !== []) {
            $body .= "\n\n" . __('filament/sa_system_health.notif_finalize_update_failures_label') . "\n" . implode("\n", $failures);
            Notification::make()
                ->title(__('filament/sa_system_health.notif_finalize_update_partial_title'))
                ->body(e($body))
                ->warning()
                ->persistent()
                ->send();
        } else {
            Notification::make()
                ->title(__('filament/sa_system_health.notif_finalize_update_success_title'))
                ->body(__('filament/sa_system_health.notif_finalize_update_success_body'))
                ->success()
                ->send();
        }
    }

    /**
     * `php artisan migrate --force` — applies every pending migration
     * (schema DDL + seeders called inside up() methods) in one shot.
     *
     * Targets the very common buyer trap: they update LeadHub files
     * via cPanel File Manager (or via the SA Updates page when their
     * upload zip fits under the Livewire cap) but forget the artisan
     * migrate step, so newly-added settings rows / table columns don't
     * exist in their DB.  Symptom is usually a 500 the moment they try
     * to save a settings page that touches a property added in the
     * skipped migration (e.g. BillingSettings.affiliate_commission_percent
     * — buyer at topleadcrm.com hit that exactly).
     *
     * Wrapped in try/catch so a bad migration surfaces a danger toast
     * with the exception message — far more actionable than a raw 500.
     * Cache::lock TTL bumped to 120s because migrations on slow shared
     * hosts can legitimately take a while.
     */
    public function runMigrations(): void
    {
        DemoMode::guard();

        try {
            Cache::lock('system-health:maintenance', 120)->block(15, function (): void {
                Artisan::call('migrate', ['--force' => true]);
            });
            \App\Support\MigrationStatus::forget();
            Notification::make()
                ->title(__('filament/sa_system_health.notif_run_migrations_success_title'))
                ->body(__('filament/sa_system_health.notif_run_migrations_success_body'))
                ->success()
                ->send();
        } catch (\Throwable $e) {
            Notification::make()
                ->title(__('filament/sa_system_health.notif_run_migrations_failed_title'))
                // Defense-in-depth: e() on exception message because
                // Filament Notification::body() allows raw HTML.
                ->body(e($e->getMessage()))
                ->danger()
                ->persistent()
                ->send();
        }
    }

    public function clearCaches(): void
    {
        DemoMode::guard();

        try {
            Cache::lock('system-health:maintenance', 30)->block(15, function (): void {
                Artisan::call('optimize:clear');
            });
            Notification::make()
                ->title(__('filament/sa_system_health.notif_clear_caches_success_title'))
                ->body(__('filament/sa_system_health.notif_clear_caches_success_body'))
                ->success()
                ->send();
        } catch (\Throwable $e) {
            Notification::make()
                ->title(__('filament/sa_system_health.notif_clear_caches_failed_title'))
                // Defense-in-depth: e() on exception message because
                // Filament Notification::body() allows raw HTML.
                ->body(e($e->getMessage()))
                ->danger()
                ->persistent()
                ->send();
        }
    }

    /**
     * Clear-then-rebuild config + route + view caches in sequence.
     * Slower than clearCaches() but leaves the box with warm caches
     * so the next request is fast — useful on slow shared hosts
     * where the first-request cache miss is visibly painful.
     *
     * We do NOT cache events or compiled here; those are usually
     * fine to leave to Laravel's automatic compilation per request.
     */
    public function rebuildCaches(): void
    {
        DemoMode::guard();

        try {
            Cache::lock('system-health:maintenance', 60)->block(15, function (): void {
                Artisan::call('config:clear');
                Artisan::call('route:clear');
                Artisan::call('view:clear');
                Artisan::call('config:cache');
                Artisan::call('route:cache');
                Artisan::call('view:cache');
            });
            Notification::make()
                ->title(__('filament/sa_system_health.notif_rebuild_caches_success_title'))
                ->body(__('filament/sa_system_health.notif_rebuild_caches_success_body'))
                ->success()
                ->send();
        } catch (\Throwable $e) {
            Notification::make()
                ->title(__('filament/sa_system_health.notif_rebuild_caches_failed_title'))
                ->body(e($e->getMessage()))
                ->danger()
                ->persistent()
                ->send();
        }
    }

    /**
     * `php artisan storage:link` — creates the public/storage →
     * storage/app/public symlink that Laravel relies on for serving
     * uploaded user assets.  Fresh installers sometimes miss this
     * step (especially on hosts where the install.php script can't
     * `symlink()` due to open_basedir restrictions), so the buyer
     * lands on a working dashboard but every uploaded logo / avatar
     * 404s.  This button gives them a one-click fix.
     *
     * Idempotency: we check for an existing symlink first because
     * `storage:link` throws when public/storage already exists.
     */
    public function linkStorage(): void
    {
        DemoMode::guard();

        try {
            $publicStorage = public_path('storage');
            if (file_exists($publicStorage) || is_link($publicStorage)) {
                Notification::make()
                    ->title(__('filament/sa_system_health.notif_storage_link_already_title'))
                    ->info()
                    ->send();
                return;
            }

            Artisan::call('storage:link');
            Notification::make()
                ->title(__('filament/sa_system_health.notif_storage_link_success_title'))
                ->success()
                ->send();
        } catch (\Throwable $e) {
            Notification::make()
                ->title(__('filament/sa_system_health.notif_storage_link_failed_title'))
                ->body(e($e->getMessage()))
                ->danger()
                ->persistent()
                ->send();
        }
    }

    /**
     * `php artisan queue:restart` — signals running queue workers to
     * gracefully restart after the current job, so they pick up new
     * code on the next iteration.  No-op (and explicit warning toast)
     * when the queue driver is `sync`, where there are no background
     * workers and the command would silently do nothing.
     */
    public function restartQueue(): void
    {
        DemoMode::guard();

        if (config('queue.default') === 'sync') {
            Notification::make()
                ->title(__('filament/sa_system_health.notif_restart_queue_skipped_title'))
                ->body(__('filament/sa_system_health.notif_restart_queue_skipped_body'))
                ->warning()
                ->send();
            return;
        }

        try {
            Artisan::call('queue:restart');
            Notification::make()
                ->title(__('filament/sa_system_health.notif_restart_queue_success_title'))
                ->success()
                ->send();
        } catch (\Throwable $e) {
            Notification::make()
                ->title(__('filament/sa_system_health.notif_restart_queue_failed_title'))
                ->body(e($e->getMessage()))
                ->danger()
                ->persistent()
                ->send();
        }
    }

    public function getSystemInfo(): array
    {
        return [
            __('filament/sa_system_health.label_leadhub_version') => config('leadhub.version', '1.0.0'),
            __('filament/sa_system_health.label_laravel')         => app()->version(),
            __('filament/sa_system_health.label_php')             => PHP_VERSION,
            __('filament/sa_system_health.label_environment')     => app()->environment(),
            __('filament/sa_system_health.label_debug_mode')      => config('app.debug') ? __('filament/sa_system_health.value_on') : __('filament/sa_system_health.value_off'),
            __('filament/sa_system_health.label_queue_driver')    => config('queue.default'),
            __('filament/sa_system_health.label_cache_driver')    => config('cache.default'),
            __('filament/sa_system_health.label_session_driver')  => config('session.driver'),
            __('filament/sa_system_health.label_mail_driver')     => config('mail.default'),
            __('filament/sa_system_health.label_database')        => config('database.default'),
            __('filament/sa_system_health.label_timezone')        => config('app.timezone'),
            __('filament/sa_system_health.label_billing')         => config('leadhub.billing.enabled') ? __('filament/sa_system_health.value_enabled') : __('filament/sa_system_health.value_disabled'),
        ];
    }

    public function getDiskUsage(): array
    {
        $storagePath = storage_path();
        $totalSpace  = @disk_total_space($storagePath);
        $freeSpace   = @disk_free_space($storagePath);

        return [
            'total' => $totalSpace ? $this->formatBytes($totalSpace) : __('filament/sa_system_health.value_not_available'),
            'free'  => $freeSpace ? $this->formatBytes($freeSpace) : __('filament/sa_system_health.value_not_available'),
            'used'  => ($totalSpace && $freeSpace) ? $this->formatBytes($totalSpace - $freeSpace) : __('filament/sa_system_health.value_not_available'),
            'pct'   => ($totalSpace && $freeSpace) ? round((($totalSpace - $freeSpace) / $totalSpace) * 100) : 0,
        ];
    }

    private function formatBytes(int $bytes): string
    {
        $units = ['B', 'KB', 'MB', 'GB', 'TB'];
        $i     = 0;
        while ($bytes >= 1024 && $i < count($units) - 1) {
            $bytes /= 1024;
            $i++;
        }
        return round($bytes, 1) . ' ' . $units[$i];
    }
}
