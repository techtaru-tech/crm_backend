<?php

declare(strict_types=1);

namespace App\Support;

/**
 * Centralised URL builder for the tenant-facing Filament admin panel.
 *
 * Lets every Mail/Notification/Console message link to /admin/* through
 * a single point, instead of 35+ scattered `url('/admin/whatever')`
 * calls.  If a CodeCanyon buyer ever wants to rebrand the panel slug
 * from `/admin` to `/workspace` (or anything else), they only have to
 * update the panel id in AdminPanelProvider AND the constant here.
 */
final class AdminUrl
{
    /**
     * Must match the value passed to `->id(...)` in AdminPanelProvider's
     * panel() builder.  Using a const (rather than reading the panel
     * registry) so this works in queue workers + scheduled commands
     * without booting the full Filament panel stack.
     */
    public const PANEL_PATH = '/admin';

    /**
     * Build an absolute URL inside the admin panel.
     *
     * Examples:
     *   AdminUrl::for()                   → https://app.example.com/admin
     *   AdminUrl::for('billing')          → https://app.example.com/admin/billing
     *   AdminUrl::for('/leads/42')        → https://app.example.com/admin/leads/42
     *   AdminUrl::for('leads/'.$lead->id) → https://app.example.com/admin/leads/<id>
     *
     * Leading slash on `$path` is optional — both `'leads'` and
     * `'/leads'` produce the same URL.
     */
    public static function for(string $path = ''): string
    {
        if ($path === '') {
            return url(self::PANEL_PATH);
        }
        return url(self::PANEL_PATH . '/' . ltrim($path, '/'));
    }
}
