<?php

/*
|--------------------------------------------------------------------------
| Application Service Providers
|--------------------------------------------------------------------------
|
| HorizonServiceProvider is only loaded when the queue connection is
| set to "redis". On shared hosting (database queue), Horizon is
| not used — queued jobs are processed via cron.php instead.
|
*/

$providers = [
    // Registers PSR-4 namespaces for every folder under Modules/ on the
    // already-loaded Composer ClassLoader. Runs before nwidart so the
    // package can discover module provider classes. This is what makes
    // runtime module uploads work without a composer binary on the host.
    App\Providers\ModuleAutoloadServiceProvider::class,

    App\Providers\AppServiceProvider::class,
    App\Providers\BroadcastServiceProvider::class,
    // Hydrates config('locales.*') from the DB `locales` table so
    // operator edits in /super-admin/locales propagate app-wide.
    App\Providers\LocaleServiceProvider::class,
    App\Providers\Filament\AdminPanelProvider::class,
    App\Providers\Filament\SuperAdminPanelProvider::class,
];

// Only register Horizon when Redis queue is configured and the package is installed.
if (
    env('QUEUE_CONNECTION', 'database') === 'redis'
    && class_exists(\Laravel\Horizon\HorizonServiceProvider::class)
) {
    $providers[] = App\Providers\HorizonServiceProvider::class;
}

return $providers;
