<?php

namespace App\Providers;

use App\Models\Locale;
use Illuminate\Database\QueryException;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\ServiceProvider;

/**
 * Reads the operator-managed `locales` table once per boot, caches
 * the result, and mirrors it into config('locales.supported') so
 * every existing caller (marketing layout, editor tabs, middleware)
 * keeps working without any change of API.
 *
 * Fails safely when the table doesn't exist yet (fresh install
 * mid-migration) or DB is unreachable — the file-backed
 * config/locales.php default survives as a bootstrap fallback.
 */
class LocaleServiceProvider extends ServiceProvider
{
    public function boot(): void
    {
        try {
            if (! Schema::hasTable('locales')) {
                return;
            }

            $registry = Locale::registry();
            if (empty($registry)) {
                return;
            }

            config()->set('locales.supported', $registry);
            config()->set('locales.default',   Locale::defaultCode());
        } catch (QueryException) {
            // DB unreachable — keep the file-backed defaults.
        } catch (\Throwable) {
            // Cache driver down, etc.  Silently keep defaults.
        }

        // Seed Carbon with the framework's configured locale at boot.
        // SetLocale middleware re-applies the per-request value, but
        // console commands, scheduled tasks, and queue workers fire
        // before any middleware — they need a sane non-English Carbon
        // default when the operator has configured a non-English
        // global locale (config/app.php `locale`).  Without this,
        // `Carbon::now()->diffForHumans()` in a `php artisan
        // notifications:digest` run renders English even though the
        // surrounding mail body is __()-translated.
        try {
            $currentLocale = app()->getLocale();
            if (is_string($currentLocale) && $currentLocale !== '') {
                \Carbon\Carbon::setLocale($currentLocale);
                \Carbon\CarbonImmutable::setLocale($currentLocale);
            }
        } catch (\Throwable) {
            // Carbon translation pack missing — keep its English
            // fallback rather than crashing the boot.
        }
    }
}
