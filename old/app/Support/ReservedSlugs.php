<?php

namespace App\Support;

/**
 * Reserved first-path-segment slugs that tenants cannot claim as
 * their workspace identifier.  Used by:
 *
 *   1. The /{workspace}/{slug} landing-page route constraint — so
 *      a workspace named "admin" can't intercept /admin/login.
 *   2. Tenant-create validation (SA form + self-service /register)
 *      — rejected at form submit so operators never get a
 *      workspace that routes conflict with the rest of the app.
 *
 * Keep this list in sync with the non-tenant route prefixes in
 * routes/web.php and the Filament panel prefixes (admin, super-
 * admin) registered via AdminPanelProvider / SuperAdminPanelProvider.
 */
class ReservedSlugs
{
    public const ALL = [
        // Filament panels
        'admin', 'super-admin',

        // Public marketing + legal
        'pages', 'docs', 'pricing', 'register', 'login',

        // Installer / legacy installer path
        'install', 'installer',

        // Public lead-capture endpoints
        'forms', 'widget', 'tracking', 'p',

        // Public quote / invoice / booking / portal
        'quote', 'invoice', 'book', 'portal',

        // Infrastructure / tooling
        'api', 'livewire', 'storage', 'vendor', 'up',
        'webhook', 'webhooks', 'oauth', 'integrations',

        // Auth / session / misc
        'invitation', 'locale', 'impersonation',
        'emails', 'email', 'voice', 'export', 'exports',
        'update-banner', 'mobile', 'billing',

        // Super-admin specific routes under /super-admin shortcut
        'super',

        // Static assets served directly by the web server, but
        // still good to reserve so nobody picks them as a slug.
        'favicon', 'robots', 'sitemap', 'apple-touch-icon',
        'site', 'humans',
    ];

    /** Combined regex pattern the route constraint can apply. */
    public static function routeConstraint(): string
    {
        $excluded = implode('|', array_map(preg_quote(...), self::ALL));
        return '^(?!(' . $excluded . ')$)[a-z0-9][a-z0-9\-]{0,62}$';
    }

    /** Quick membership check for validation rules. */
    public static function isReserved(string $slug): bool
    {
        return in_array(strtolower(trim($slug)), self::ALL, true);
    }
}
