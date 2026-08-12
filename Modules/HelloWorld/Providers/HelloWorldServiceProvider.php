<?php

namespace Modules\HelloWorld\Providers;

use Illuminate\Support\ServiceProvider;

/**
 * Example LeadHub module service provider. Wires the module's
 * routes and views into the host application. Copy this file as
 * the starting point for your own modules.
 */
class HelloWorldServiceProvider extends ServiceProvider
{
    public function register(): void
    {
        //
    }

    public function boot(): void
    {
        $base = dirname(__DIR__);

        if (file_exists($base . '/Routes/web.php')) {
            $this->loadRoutesFrom($base . '/Routes/web.php');
        }

        if (is_dir($base . '/Resources/views')) {
            $this->loadViewsFrom($base . '/Resources/views', 'helloworld');
        }

        if (is_dir($base . '/Database/Migrations')) {
            $this->loadMigrationsFrom($base . '/Database/Migrations');
        }

        if (is_dir($base . '/Resources/lang')) {
            $this->loadTranslationsFrom($base . '/Resources/lang', 'helloworld');
        }
    }
}
