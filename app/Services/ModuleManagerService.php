<?php

namespace App\Services;

use App\Support\ZipSecurity;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Log;
use ZipArchive;

/**
 * Thin wrapper around nwidart/laravel-modules that gives the
 * Super Admin panel a stable, framework-agnostic API for listing,
 * enabling, disabling, deleting and uploading HMVC modules.
 *
 * Every public method degrades gracefully when nwidart is not
 * installed yet (e.g. on a fresh deploy where composer has not
 * run) so the Modules admin page can still render a helpful
 * message instead of crashing.
 */
class ModuleManagerService
{
    /**
     * True when nwidart/laravel-modules is installed and bootable.
     */
    public function isAvailable(): bool
    {
        return class_exists(\Nwidart\Modules\Facades\Module::class);
    }

    /**
     * List every registered module with its current status.
     *
     * @return array<int, array{name:string,alias:string,description:?string,version:?string,enabled:bool,path:string}>
     */
    public function all(): array
    {
        if (! $this->isAvailable()) {
            return $this->scanFilesystem();
        }

        $out = [];
        foreach (\Nwidart\Modules\Facades\Module::all() as $module) {
            /** @var \Nwidart\Modules\Module $module */
            // nwidart/laravel-modules v12 removed Module::getAlias().
            // Fall back to the lowercased name (which is what getAlias()
            // historically returned) and let the module.json manifest's
            // `alias` override it if explicitly set.
            $alias = (string) ($module->get('alias') ?: $module->getLowerName());

            $out[] = [
                'name'        => $module->getName(),
                'alias'       => $alias,
                'description' => $module->getDescription() ?: null,
                'version'     => $module->get('version') ?: null,
                'enabled'     => $module->isEnabled(),
                'path'        => $module->getPath(),
            ];
        }

        // Stable alphabetical order.
        usort($out, fn ($a, $b) => strcmp($a['name'], $b['name']));

        return $out;
    }

    public function enable(string $name): void
    {
        if (! $this->isAvailable()) {
            throw new \RuntimeException(__('module_manager.error_system_unavailable'));
        }
        \Nwidart\Modules\Facades\Module::findOrFail($name)->enable();
    }

    public function disable(string $name): void
    {
        if (! $this->isAvailable()) {
            throw new \RuntimeException(__('module_manager.error_system_unavailable'));
        }
        \Nwidart\Modules\Facades\Module::findOrFail($name)->disable();
    }

    public function delete(string $name): void
    {
        if (! $this->isAvailable()) {
            // Still allow disk delete even when the package is missing,
            // so broken installs remain recoverable.
            $path = base_path('Modules/' . $name);
            if (File::isDirectory($path)) {
                File::deleteDirectory($path);
            }
            return;
        }

        /** @var \Nwidart\Modules\Module $module */
        $module = \Nwidart\Modules\Facades\Module::findOrFail($name);
        $module->delete();
    }

    /**
     * Install a module from an uploaded zip file. Expects the archive
     * top-level to be either <ModuleName>/... or a single wrapping
     * folder containing <ModuleName>/.
     *
     * Returns the installed module name.
     */
    public function installFromZip(string $zipPath): string
    {
        if (! file_exists($zipPath)) {
            throw new \RuntimeException(__('module_manager.error_upload_not_found', ['path' => $zipPath]));
        }

        $zip = new ZipArchive();
        if ($zip->open($zipPath) !== true) {
            throw new \RuntimeException(__('module_manager.error_cannot_open_zip'));
        }

        $stagingDir = storage_path('app/modules-staging/' . uniqid('mod_', true));
        File::ensureDirectoryExists($stagingDir);

        try {
            ZipSecurity::validateEntries($zip);
            $zip->extractTo($stagingDir);
            $zip->close();

            // Find the module.json to determine the real module root.
            $moduleRoot = $this->findModuleRoot($stagingDir);
            if (! $moduleRoot) {
                throw new \RuntimeException(__('module_manager.error_missing_module_json'));
            }

            $manifest = json_decode(File::get($moduleRoot . '/module.json'), true);
            if (! is_array($manifest) || empty($manifest['name'])) {
                throw new \RuntimeException(__('module_manager.error_module_json_missing_name'));
            }

            $name   = preg_replace('/[^A-Za-z0-9]/', '', (string) $manifest['name']);
            if (! $name) {
                throw new \RuntimeException(__('module_manager.error_invalid_module_name'));
            }

            $target = base_path('Modules/' . $name);
            File::ensureDirectoryExists(base_path('Modules'));

            if (File::isDirectory($target)) {
                File::deleteDirectory($target);
            }

            File::copyDirectory($moduleRoot, $target);

            // Clear runtime caches so the module's routes, config, and
            // service providers are discovered on the next request. We
            // deliberately do NOT call nwidart's `module:dump` command
            // because it shells out to the composer binary, which is not
            // available on shared-hosting CodeCanyon deployments. The
            // ModuleAutoloadServiceProvider picks up the new module's
            // PSR-4 namespaces on the very next HTTP boot.
            try {
                \Illuminate\Support\Facades\Cache::lock('modules:cache-clear', 10)->block(10, function () {
                    \Artisan::call('config:clear');
                    \Artisan::call('route:clear');
                    \Artisan::call('view:clear');
                });
            } catch (\Throwable $e) {
                Log::warning('Cache clear after module install failed', ['error' => $e->getMessage()]);
            }

            return $name;
        } finally {
            if (File::isDirectory($stagingDir)) {
                File::deleteDirectory($stagingDir);
            }
        }
    }

    /**
     * Walk the staging directory looking for the first module.json.
     */
    protected function findModuleRoot(string $dir): ?string
    {
        $iter = new \RecursiveIteratorIterator(
            new \RecursiveDirectoryIterator($dir, \FilesystemIterator::SKIP_DOTS),
            \RecursiveIteratorIterator::SELF_FIRST
        );

        foreach ($iter as $item) {
            /** @var \SplFileInfo $item */
            if ($item->isFile() && $item->getFilename() === 'module.json') {
                return dirname($item->getPathname());
            }
        }

        return null;
    }

    /**
     * Fallback discovery when nwidart is not installed — just lists
     * folders under base_path('Modules') so the admin page can
     * render them with a "module system unavailable" banner.
     *
     * @return array<int, array{name:string,alias:string,description:?string,version:?string,enabled:bool,path:string}>
     */
    protected function scanFilesystem(): array
    {
        $root = base_path('Modules');
        if (! File::isDirectory($root)) {
            return [];
        }

        $out = [];
        foreach (File::directories($root) as $dir) {
            $name     = basename($dir);
            $manifest = [];
            if (File::isFile($dir . '/module.json')) {
                $manifest = json_decode(File::get($dir . '/module.json'), true) ?: [];
            }
            $out[] = [
                'name'        => $manifest['name'] ?? $name,
                'alias'       => $manifest['alias'] ?? strtolower($name),
                'description' => $manifest['description'] ?? null,
                'version'     => $manifest['version'] ?? null,
                'enabled'     => (bool) ($manifest['active'] ?? false),
                'path'        => $dir,
            ];
        }

        usort($out, fn ($a, $b) => strcmp($a['name'], $b['name']));
        return $out;
    }
}
