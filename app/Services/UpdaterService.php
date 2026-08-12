<?php

namespace App\Services;

use App\Support\ZipSecurity;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Log;
use ZipArchive;

/**
 * Applies update packages uploaded from the Super Admin UI. An update
 * package is a plain zip where the top-level layout mirrors the
 * CodeCanyon distribution (app/, database/, resources/, routes/,
 * public/, composer.json, …). The service:
 *
 *   1. Creates a pre-update backup via BackupService.
 *   2. Extracts the zip to a staging dir.
 *   3. Copies files into base_path(), skipping protected paths.
 *   4. Runs migrations and clears every cache.
 *   5. Appends an entry to storage/app/updates/history.json.
 *
 * The whole thing is wrapped in a try/catch so a half-applied upgrade
 * surfaces as a visible error in the UI rather than a white-screen
 * crash. A restore button next to the pre-update backup gives the
 * owner a one-click rollback path.
 */
class UpdaterService
{
    /**
     * Paths we never overwrite — losing any of these would nuke a
     * live install's configuration or runtime data.
     *
     * @var array<int, string>
     */
    protected array $protectedPaths = [
        '.env',
        '.env.example',
        'storage',
        'bootstrap/cache',
        'public/tenant',
        'public/storage',
        'vendor',
        'node_modules',
    ];

    public function __construct(
        protected BackupService $backups,
    ) {
    }

    /**
     * Apply an update zip. Returns a detailed summary the UI can show.
     */
    public function apply(string $zipPath, array $options = []): array
    {
        if (! file_exists($zipPath)) {
            throw new \RuntimeException(__('services/updater.package_not_found', ['path' => $zipPath]));
        }

        $summary = [
            'started_at'      => now()->toIso8601String(),
            'package'         => basename($zipPath),
            'from_version'    => config('leadhub.version', '1.0.0'),
            'backup'          => null,
            'files_written'   => 0,
            'migrations_run'  => false,
            'caches_cleared'  => false,
            'to_version'      => null,
            'finished_at'     => null,
            'error'           => null,
        ];

        try {
            // 1. Pre-update safety backup
            if (! ($options['skip_backup'] ?? false)) {
                $backupPath = $this->backups->create(['database' => true, 'files' => true]);
                $summary['backup'] = basename($backupPath);
            }

            // 2. Extract
            $staging = storage_path('app/updates/staging_' . uniqid());
            File::ensureDirectoryExists($staging);
            $zip = new ZipArchive();
            if ($zip->open($zipPath) !== true) {
                throw new \RuntimeException(__('services/updater.unable_to_open'));
            }
            ZipSecurity::validateEntries($zip);
            $zip->extractTo($staging);
            $zip->close();

            // Unwrap single top-level folder if the packager nested the
            // release inside e.g. leadhub-1.2.0/.
            $entries = array_values(array_diff(scandir($staging), ['.', '..']));
            if (count($entries) === 1 && is_dir($staging . '/' . $entries[0])) {
                $staging = $staging . '/' . $entries[0];
            }

            // 2b. Check update.json for vendor-refresh opt-in.
            // When the distributor ships a release that updates dependencies,
            // they include {"refresh_vendor": true} in update.json so the
            // updater replaces the committed vendor/ with the new one.
            $updateMeta = [];
            $updateMetaFile = $staging . '/update.json';
            if (file_exists($updateMetaFile)) {
                $updateMeta = json_decode(file_get_contents($updateMetaFile), true) ?: [];
            }

            $protected = $this->protectedPaths;
            if (! empty($updateMeta['refresh_vendor'])) {
                $protected = array_values(array_diff($protected, ['vendor']));
            }

            // Capture the pre-update composer.lock fingerprint BEFORE the
            // copy overwrites it, so we can tell whether dependencies
            // changed in this release (used by the autoload refresh below).
            $preUpdateLockHash = is_file(base_path('composer.lock'))
                ? @md5_file(base_path('composer.lock'))
                : null;

            // 3. Copy files into place
            $summary['files_written'] = $this->copyIntoApp($staging, $protected);

            // 3b. Refresh Composer's autoload metadata when we did NOT
            // replace the whole vendor/ tree. Installs ship with
            // --classmap-authoritative (build.sh), so the ClassLoader does
            // no filesystem PSR-4 fallback: a class absent from the classmap
            // never loads. Copying the new app/ files above is therefore not
            // enough — a release's brand-new classes (e.g. a new Filament
            // resource) stay invisible until the classmap lists them. The
            // fresh authoritative classmap already sits in the zip's
            // vendor/composer/, so we copy just that small metadata set
            // instead of the whole vendor/. Only safe when dependencies are
            // unchanged (same composer.lock); dependency-changing releases
            // set refresh_vendor:true and copy the full vendor/ above.
            if (empty($updateMeta['refresh_vendor'])) {
                $stagingLock = $staging . '/composer.lock';
                $depsUnchanged = ! is_file($stagingLock)
                    || $preUpdateLockHash === null
                    || @md5_file($stagingLock) === $preUpdateLockHash;

                if ($depsUnchanged) {
                    $summary['autoload_refreshed'] = $this->refreshAutoloadMetadata($staging);
                } else {
                    $summary['autoload_refreshed'] = 0;
                    Log::warning('[updater] composer.lock changed but refresh_vendor not set; new classes may not autoload until vendor/ is refreshed.');
                }
            }

            // 4. Read new version from leadhub config if present
            $versionFile = $staging . '/config/leadhub.php';
            if (file_exists($versionFile)) {
                $cfg = @include $versionFile;
                $summary['to_version'] = $cfg['version'] ?? null;
            }

            // 5. Delete stale bootstrap caches BEFORE rebuilding —
            // ensures new service providers, packages, and configs are
            // discovered correctly (mirrors public/install.php:128).
            foreach (glob(base_path('bootstrap/cache/*.php')) as $cacheFile) {
                @unlink($cacheFile);
            }

            // 5b. Invalidate the PHP OPcache.  We just overwrote this app's
            // own .php files on disk, but any worker running with
            // opcache.validate_timestamps=0 (the production default on most
            // shared hosts) keeps serving the OLD compiled bytecode for files
            // it already cached while reading the NEW bytecode for files it
            // hadn't — a mix that fatals the very next request when a class's
            // method signature changed between versions.  That fatal is what
            // surfaced as Filament's "Error while loading page" on the reload
            // immediately after a successful update.  A full reset forces
            // every worker to recompile from the new source on its next
            // request.  No-op when the extension is absent or locked down via
            // opcache.restrict_api (opcache_reset() then returns false).
            if (function_exists('opcache_reset')) {
                $summary['opcache_reset'] = (bool) @opcache_reset();
            }

            // 6. Migrate
            Artisan::call('migrate', ['--force' => true]);
            $summary['migrations_run'] = true;
            \App\Support\MigrationStatus::forget();

            // 7. Clear caches
            Artisan::call('config:clear');
            Artisan::call('cache:clear');
            Artisan::call('view:clear');
            Artisan::call('route:clear');
            $summary['caches_cleared'] = true;

            // 7b. Republish Filament's compiled CSS / JS into public/.
            // A zip-upload update never runs composer's post-autoload-dump
            // hook (filament:upgrade → filament:assets), so without this
            // the public/css/filament + public/js/filament files stay at
            // the OLD version after an update that bumped Filament —
            // the admin panel + tenant pages then render unstyled with
            // missing icons ("broken elements").  Feature-detected and
            // individually try/caught so a stripped-down install or a
            // read-only public/ dir doesn't abort the update.
            if (array_key_exists('filament:assets', Artisan::all())) {
                try {
                    Artisan::call('filament:assets');
                    $summary['filament_assets_published'] = true;
                } catch (\Throwable $e) {
                    Log::warning(
                        '[updater] filament:assets republish failed (non-fatal)',
                        ['error' => $e->getMessage()],
                    );
                }
            }

            // 8. Rebuild the production caches AFTER the HTTP response is
            // sent.  Running config:cache / route:cache / view:cache while
            // this (Livewire) request is still executing corrupts the
            // in-flight response — most visibly config:cache, after which
            // env() returns null for the remainder of the request — so the
            // operator saw a false "something went wrong" toast even though
            // the update had fully succeeded (files written, migrations run,
            // version bumped, history recorded).  The clears in step 7
            // already leave the app working (just uncached); deferring the
            // RE-cache to terminate() keeps this response clean and still
            // warms the caches for the next request.  Each step is
            // individually try/caught (best effort) and feature-detected so
            // a stripped-down install (no Filament / blade-icons) is fine.
            $summary['caches_rebuilt'] = true;
            app()->terminating(function (): void {
                $registered   = Artisan::all();
                $rebuildSteps = ['config:cache', 'route:cache', 'view:cache', 'event:cache'];
                foreach (['filament:cache-components', 'icons:cache'] as $optional) {
                    if (array_key_exists($optional, $registered)) {
                        $rebuildSteps[] = $optional;
                    }
                }
                foreach ($rebuildSteps as $rebuildStep) {
                    try {
                        Artisan::call($rebuildStep);
                    } catch (\Throwable $e) {
                        Log::warning(
                            '[updater] post-response cache rebuild step failed (non-fatal)',
                            ['step' => $rebuildStep, 'error' => $e->getMessage()],
                        );
                    }
                }
            });

            $summary['finished_at'] = now()->toIso8601String();
            $this->recordHistory($summary);

            // Clean up the staging folder (but not its parent `updates/`)
            if (is_dir($staging)) {
                File::deleteDirectory($staging);
            }

            return $summary;
        } catch (\Throwable $e) {
            Log::error('[updater] apply failed: ' . $e->getMessage(), ['exception' => $e]);
            $summary['error']       = $e->getMessage();
            $summary['finished_at'] = now()->toIso8601String();
            $this->recordHistory($summary);
            throw $e;
        }
    }

    /**
     * Return newest-first history of previous updates.
     */
    public function history(): array
    {
        $file = $this->historyFile();
        if (! file_exists($file)) return [];

        $rows = json_decode(file_get_contents($file), true) ?: [];
        return array_reverse($rows);
    }

    /* --------------------------------------------------------------- */

    protected function copyIntoApp(string $stagingRoot, ?array $protected = null): int
    {
        $protectedPaths = $protected ?? $this->protectedPaths;
        $appRoot = base_path();
        $count = 0;

        $iterator = new \RecursiveIteratorIterator(
            new \RecursiveDirectoryIterator($stagingRoot, \FilesystemIterator::SKIP_DOTS),
            \RecursiveIteratorIterator::SELF_FIRST,
        );

        foreach ($iterator as $item) {
            $relative = ltrim(str_replace('\\', '/', substr($item->getPathname(), strlen($stagingRoot) + 1)), '/');
            if ($this->isProtected($relative, $protectedPaths)) {
                continue;
            }

            $target = $appRoot . DIRECTORY_SEPARATOR . $relative;

            if ($item->isDir()) {
                File::ensureDirectoryExists($target);
                continue;
            }

            File::ensureDirectoryExists(dirname($target));
            copy($item->getPathname(), $target);
            $count++;
        }

        return $count;
    }

    /**
     * Copy Composer's freshly-generated autoload metadata from the update
     * staging tree into the live install, bypassing the vendor/ protection.
     *
     * These ~dozen small files (vendor/autoload.php + vendor/composer/*)
     * hold the optimized, authoritative classmap. Refreshing them registers
     * the release's brand-new classes WITHOUT copying the entire vendor/
     * tree. They are regenerated together by composer and share one init
     * hash, so the whole set is copied to keep autoload.php / autoload_real
     * / autoload_static internally consistent.
     *
     * @return int number of metadata files copied
     */
    protected function refreshAutoloadMetadata(string $stagingRoot): int
    {
        $files = [
            'vendor/autoload.php',
            'vendor/composer/autoload_real.php',
            'vendor/composer/autoload_static.php',
            'vendor/composer/autoload_classmap.php',
            'vendor/composer/autoload_psr4.php',
            'vendor/composer/autoload_namespaces.php',
            'vendor/composer/autoload_files.php',
            'vendor/composer/ClassLoader.php',
            'vendor/composer/InstalledVersions.php',
            'vendor/composer/installed.php',
            'vendor/composer/installed.json',
            'vendor/composer/platform_check.php',
        ];

        $copied = 0;
        foreach ($files as $relative) {
            $source = $stagingRoot . '/' . $relative;
            if (! is_file($source)) {
                continue; // a lean zip may omit some entries — copy what exists
            }
            $target = base_path($relative);
            File::ensureDirectoryExists(dirname($target));
            if (@copy($source, $target)) {
                $copied++;
            }
        }

        return $copied;
    }

    protected function isProtected(string $relative, ?array $protectedPaths = null): bool
    {
        $paths = $protectedPaths ?? $this->protectedPaths;
        $normalised = str_replace('\\', '/', $relative);
        foreach ($paths as $protected) {
            if ($normalised === $protected || str_starts_with($normalised, $protected . '/')) {
                return true;
            }
        }
        return false;
    }

    protected function historyFile(): string
    {
        $dir = storage_path('app/updates');
        File::ensureDirectoryExists($dir);
        return $dir . DIRECTORY_SEPARATOR . 'history.json';
    }

    protected function recordHistory(array $summary): void
    {
        $file = $this->historyFile();
        $rows = file_exists($file) ? (json_decode(file_get_contents($file), true) ?: []) : [];
        $rows[] = $summary;
        file_put_contents($file, json_encode($rows, JSON_PRETTY_PRINT));
    }
}
