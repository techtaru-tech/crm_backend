<?php

namespace App\Providers;

use Composer\Autoload\ClassLoader;
use Illuminate\Support\Facades\File;
use Illuminate\Support\ServiceProvider;

/**
 * Runtime PSR-4 registration for every module under base_path('Modules').
 *
 * nwidart/laravel-modules v12 relies on wikimedia/composer-merge-plugin
 * to merge each module's composer.json autoload into the host autoload
 * at `composer install` time. That is fine for modules that ship inside
 * the distribution zip, but breaks completely for modules uploaded at
 * runtime through the Super Admin UI on a plug-and-play deployment
 * where composer is not available.
 *
 * This provider plugs that hole: on every boot it walks Modules/*,
 * reads each module.json manifest, and calls $loader->addPsr4() on the
 * Composer ClassLoader instance that is already living in memory. No
 * shelling out to composer, no filesystem autoload map to regenerate,
 * no server-side CLI required.
 *
 * Registered in bootstrap/providers.php BEFORE the nwidart service
 * provider so nwidart can find module provider classes when it boots.
 */
class ModuleAutoloadServiceProvider extends ServiceProvider
{
    /**
     * Subpaths (relative to a module root) that may contain PHP classes
     * under the module's root namespace. Matches the default nwidart v12
     * layout plus a flat 'src' convention for simpler modules.
     */
    protected array $namespaceSubpaths = [
        'app',
        'src',
    ];

    public function register(): void
    {
        $modulesRoot = base_path('Modules');
        if (! File::isDirectory($modulesRoot)) {
            return;
        }

        $loader = $this->findComposerLoader();
        if (! $loader instanceof ClassLoader) {
            return;
        }

        foreach (File::directories($modulesRoot) as $moduleDir) {
            $this->registerModule($loader, $moduleDir);
        }
    }

    /**
     * Register one module's PSR-4 namespaces on the Composer loader.
     */
    protected function registerModule(ClassLoader $loader, string $moduleDir): void
    {
        $manifestPath = $moduleDir . DIRECTORY_SEPARATOR . 'module.json';
        if (! File::isFile($manifestPath)) {
            return;
        }

        $manifest = json_decode(File::get($manifestPath), true);
        if (! is_array($manifest) || empty($manifest['name'])) {
            return;
        }

        $name      = (string) $manifest['name'];
        $namespace = 'Modules\\' . $name . '\\';

        // Honor an explicit autoload block in module.json if present,
        // mirroring the shape of composer.json's autoload.psr-4 section:
        //   "autoload": { "psr-4": { "Modules\\Foo\\": "app/" } }
        $explicit = $manifest['autoload']['psr-4'] ?? null;
        if (is_array($explicit) && $explicit !== []) {
            foreach ($explicit as $prefix => $relative) {
                $loader->addPsr4(
                    rtrim($prefix, '\\') . '\\',
                    $moduleDir . DIRECTORY_SEPARATOR . ltrim($relative, '/\\')
                );
            }
            return;
        }

        // Otherwise register the conventional subpaths under the module's
        // root namespace. We register every subpath that exists on disk so
        // a module can use 'app/', 'src/', or both.
        foreach ($this->namespaceSubpaths as $sub) {
            $path = $moduleDir . DIRECTORY_SEPARATOR . $sub;
            if (is_dir($path)) {
                $loader->addPsr4($namespace, $path);
            }
        }

        // Also register the module root itself so a Providers/ directory
        // sitting directly under Modules/<Name>/Providers works (the
        // HelloWorld example module uses this flat layout).
        $loader->addPsr4($namespace, $moduleDir);
    }

    /**
     * Pull the already-instantiated Composer ClassLoader out of the
     * autoload.php bootstrap. Composer stores it on a registered SPL
     * autoload function as a closure-bound $this; the simplest stable
     * way to reach it is through Composer's own registered function.
     */
    protected function findComposerLoader(): ?ClassLoader
    {
        foreach (spl_autoload_functions() as $fn) {
            if (is_array($fn) && isset($fn[0]) && $fn[0] instanceof ClassLoader) {
                return $fn[0];
            }
        }

        // Fallback: re-require the Composer autoload bootstrap, which
        // returns the ClassLoader instance.
        $autoload = base_path('vendor/autoload.php');
        if (is_file($autoload)) {
            $loader = require $autoload;
            if ($loader instanceof ClassLoader) {
                return $loader;
            }
        }

        return null;
    }
}
