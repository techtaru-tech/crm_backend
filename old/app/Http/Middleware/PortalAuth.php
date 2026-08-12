<?php

namespace App\Http\Middleware;

use App\Models\PortalSession;
use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

/**
 * Validates the portal_session cookie on customer-portal routes.
 * Binds the resolved Lead onto the request via `portal_lead` attribute
 * so controllers don't have to re-resolve it.
 */
class PortalAuth
{
    public function handle(Request $request, Closure $next): Response
    {
        $sessionToken = $request->cookie('portal_session');

        if (! $sessionToken) {
            return redirect()->route('portal.login');
        }

        $session = PortalSession::where('session_token', $sessionToken)->first();

        if (! $session || ! $session->isValid()) {
            return redirect()->route('portal.login')
                ->cookie('portal_session', '', -1, '/');
        }

        $lead = $session->lead;

        if (! $lead) {
            $session->delete();
            return redirect()->route('portal.login')
                ->cookie('portal_session', '', -1, '/');
        }

        // Touch last_active_at so idle sessions prune naturally.
        $session->update(['last_active_at' => now()]);

        // Bind current_tenant so BelongsToTenant-scoped relations
        // ($lead->pipeline->stages, $lead->activities, etc.) resolve
        // correctly on the public portal routes — which otherwise
        // run with no tenant context and fail-closed.
        if ($lead->tenant_id) {
            $tenant = $lead->tenant()->withoutGlobalScope('tenant')->first();
            if ($tenant) {
                app()->instance('current_tenant', $tenant);
            }
        }

        $request->attributes->set('portal_lead', $lead);
        $request->attributes->set('portal_session', $session);

        return $next($request);
    }
}
