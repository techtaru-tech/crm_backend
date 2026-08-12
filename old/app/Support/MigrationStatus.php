<?php

declare(strict_types=1);

namespace App\Support;

use Illuminate\Support\Facades\Cache;

/**
 * Lightweight "are there unrun migrations?" probe for the admin banner.
 *
 * Result is cached briefly because it runs on every Super-Admin page
 * render, and the whole thing is fully defensive: any failure (cache
 * table missing, migrator quirk on a stripped-down install, DB down)
 * resolves to false so the probe itself can never break a page.
 */
final class MigrationStatus
{
    private const CACHE_KEY = 'leadhub:pending-migrations:v1';

    private const TTL_SECONDS = 60;

    /**
     * True when the schema has migration files that have not run yet.
     */
    public static function hasPending(): bool
    {
        try {
            return (bool) Cache::remember(self::CACHE_KEY, self::TTL_SECONDS, static function (): bool {
                /** @var \Illuminate\Database\Migrations\Migrator $migrator */
                $migrator = app('migrator');

                // No migrations table => fresh / not-yet-installed. The
                // installer owns that flow, not this banner.
                if (! $migrator->repositoryExists()) {
                    return false;
                }

                $ran   = $migrator->getRepository()->getRan();
                $paths = array_unique(array_merge([database_path('migrations')], $migrator->paths()));
                $files = $migrator->getMigrationFiles($paths);

                return count(array_diff(array_keys($files), $ran)) > 0;
            });
        } catch (\Throwable) {
            return false;
        }
    }

    /**
     * Drop the cached result so the next call re-checks immediately.
     * Called right after migrations are applied so the banner clears
     * without waiting out the TTL.
     */
    public static function forget(): void
    {
        try {
            Cache::forget(self::CACHE_KEY);
        } catch (\Throwable) {
            // Best-effort; the short TTL clears it anyway.
        }
    }
}
