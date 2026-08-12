<?php

namespace App\Http\Middleware;

use App\Models\Tenant;
use App\Models\TenantDomain;
use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Symfony\Component\HttpFoundation\Response;

class ResolveTenant
{
    protected function skipPaths(): array
    {
        $paths = [
            'install',
            'super-admin',
            'impersonation',
            'auth/two-factor-challenge',
            'invitation',
        ];

        if (app()->environment('local', 'testing')) {
            $paths[] = '_ignition';
            $paths[] = '_debugbar';
        }

        return $paths;
    }

    public function handle(Request $request, Closure $next): Response
    {
        if ($this->shouldSkip($request)) {
            return $next($request);
        }

        $tenant = $this->resolveTenant($request);

        // Always bind the key so app('current_tenant') never throws
        app()->instance('current_tenant', $tenant);

        if ($tenant) {
            config(['app.tenant_id' => $tenant->id]);
            return $next($request);
        }

        if ($this->isCustomOrSubdomain($request)) {
            return $this->redirectToSplash($request);
        }

        return $next($request);
    }

    protected function shouldSkip(Request $request): bool
    {
        foreach ($this->skipPaths() as $path) {
            if ($request->is($path) || $request->is($path . '/*')) {
                return true;
            }
        }
        return false;
    }

    protected function isCustomOrSubdomain(Request $request): bool
    {
        $host       = $request->getHost();
        $appDomain  = config('leadhub.domain', parse_url(config('app.url'), PHP_URL_HOST));

        if (! $appDomain || $host === $appDomain) {
            return false;
        }

        if (str_ends_with($host, '.' . $appDomain)) {
            return true;
        }

        return false;
    }

    protected function resolveTenant(Request $request): ?Tenant
    {
        // Custom domain + subdomain resolution paths are retained
        // as DEPRECATED fallbacks — LeadHub's current architecture
        // is path-based (/{workspace}/{slug}), so the active
        // tenants.subdomain + tenants.domain columns should be
        // NULL on new installs.  Kept here so any existing
        // production tenant that still points DNS at a
        // tenant-subdomain keeps resolving until the operator
        // migrates them.
        if ($tenant = $this->resolveFromCustomDomain($request)) {
            return $tenant;
        }

        if ($tenant = $this->resolveFromSubdomain($request)) {
            return $tenant;
        }

        // Primary path: authenticated user's tenant.  For
        // unauthenticated requests against path-based
        // /{workspace}/{slug} URLs, the controller resolves
        // the tenant explicitly from the first path segment —
        // this middleware intentionally doesn't do URL parsing.
        if ($user = Auth::user()) {
            return $user->tenant;
        }

        return null;
    }

    protected function redirectToSplash(Request $request): Response
    {
        $host = $request->getHost();

        if ($request->expectsJson()) {
            return response()->json([
                'error'   => __('middleware.resolve_tenant.not_found'),
                'message' => __('middleware.resolve_tenant.not_found_detail', ['host' => $host]),
            ], 404);
        }

        return redirect()->away(config('app.url'))->with('error', __('messages.no_workspace_found', ['host' => $host]));
    }

    protected function resolveFromCustomDomain(Request $request): ?Tenant
    {
        $host = $request->getHost();

        $tenant = Tenant::where('domain', $host)
            ->where('active', true)
            ->first();

        if ($tenant) {
            return $tenant;
        }

        $tenantDomain = TenantDomain::where('domain', $host)
            ->whereNotNull('verified_at')
            ->with('tenant')
            ->first();

        if ($tenantDomain && $tenantDomain->tenant?->active) {
            return $tenantDomain->tenant;
        }

        return null;
    }

    protected function resolveFromSubdomain(Request $request): ?Tenant
    {
        $host       = $request->getHost();
        $appDomain  = config('leadhub.domain', parse_url(config('app.url'), PHP_URL_HOST));

        if (! $appDomain || $host === $appDomain) {
            return null;
        }

        if (! str_ends_with($host, '.' . $appDomain)) {
            return null;
        }

        $subdomain = str_replace('.' . $appDomain, '', $host);

        if (empty($subdomain)) {
            return null;
        }

        return Tenant::where('subdomain', $subdomain)
            ->where('active', true)
            ->first();
    }
}
