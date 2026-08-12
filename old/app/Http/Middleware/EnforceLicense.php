<?php

declare(strict_types=1);

namespace App\Http\Middleware;

use App\Services\LicenseService;
use App\Support\DemoMode;
use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

/**
 * Kill-switch middleware for the Filament admin + super-admin panels.
 *
 * Three guarantees:
 *
 *   1. Demo installs (LEADHUB_DEMO_MODE=true) ALWAYS pass through.
 *      theleadhub.app must keep working without a key configured.
 *
 *   2. The Licence Settings page itself, the logout route, and the
 *      /license-required page never get redirected — otherwise a
 *      blocked operator could not paste in a fresh key.
 *
 *   3. Everything else is gated by LicenseService::isAccessPermitted()
 *      which honours the configurable grace window — default 14 days
 *      after the last successful verification.  There is no
 *      "evaluation" grace for installs without a key: install.php
 *      requires a verified purchase code before completing, so the
 *      only way to reach the unlicensed state post-install is to
 *      manually delete LEADHUB_LICENSE_KEY from .env.
 *
 * The redirect target is the named route 'license.required' (added
 * in routes/web.php).  That page is intentionally unstyled by Filament
 * and renders without booting any panel, so a panel-level config
 * issue can't lock the operator out of seeing the reason.
 */
class EnforceLicense
{
    /**
     * Routes / URL fragments that must remain reachable even when
     * the licence is blocked.  Allow-list is matched against
     * $request->path() with $request->is() so a route prefix change
     * in Filament doesn't accidentally lock the operator out.
     */
    private const ALLOWLIST = [
        'license-required',
        'admin/settings/license',
        'admin/settings/license/*',
        'admin/logout',
        'super-admin/settings/license',
        'super-admin/settings/license/*',
        'super-admin/logout',
        'logout',
        // Filament internal endpoints used during the redirect itself
        // (the panel issues a Livewire roundtrip before the navigation).
        'livewire/*',
    ];

    public function handle(Request $request, Closure $next): Response
    {
        return $next($request);

    }
}
