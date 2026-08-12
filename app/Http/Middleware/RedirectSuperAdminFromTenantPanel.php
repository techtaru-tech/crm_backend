<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

/**
 * Redirect super-admins away from the tenant admin panel.
 *
 * Super-admins should live in `/super-admin` and only reach the
 * tenant-facing `/admin` surface via the Impersonate flow (which sets
 * `impersonating_from` in the session).  Direct access to `/admin`
 * while not impersonating lands them back on their own panel — this
 * avoids the confusing UX of a super-admin seeing a real tenant's
 * data as if they owned it.
 *
 * Registered inside `AdminPanelProvider::authMiddleware()` so it only
 * runs for authenticated users on tenant-panel routes.
 */
class RedirectSuperAdminFromTenantPanel
{
    public function handle(Request $request, Closure $next): Response
    {
        $user = $request->user();

        // Not authed yet (e.g. /admin/login) or not a super admin — pass through.
        if (! $user || ! $user->is_super_admin) {
            return $next($request);
        }

        // Super admin is actively impersonating a tenant user — allow.
        if (session()->has('impersonating_from')) {
            return $next($request);
        }

        // Avoid redirect loop: if somehow routed here by the super-admin
        // panel itself, let it through.
        if ($request->is('super-admin*')) {
            return $next($request);
        }

        return redirect('/super-admin')
            ->with('info', __('messages.signed_in_as_super_admin_info'));
    }
}
