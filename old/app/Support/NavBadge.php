<?php

declare(strict_types=1);

namespace App\Support;

/**
 * Safe wrapper for Filament navigation-badge counts.
 *
 * getNavigationBadge() runs on EVERY panel page render (it builds the
 * sidebar). If its count query throws — most commonly because the
 * underlying table does not exist yet (new code copied by an update
 * before `php artisan migrate` ran) — the exception bubbles out of the
 * sidebar render and 500s the ENTIRE panel, including the System Health
 * page where the operator would actually run the missing migrations.
 *
 * Wrapping the count so any failure degrades to "no badge" keeps the
 * panel usable, so the fix (running migrations) stays reachable instead
 * of being locked behind a white screen.
 */
final class NavBadge
{
    /**
     * Resolve a nav-badge value, swallowing any failure to null.
     *
     * @param  \Closure():(?string)  $resolver
     */
    public static function safe(\Closure $resolver): ?string
    {
        try {
            return $resolver();
        } catch (\Throwable) {
            return null;
        }
    }
}
