<?php

namespace App\Http\Middleware;

use App\Http\Responses\ApiErrorResponse;
use App\Models\ApiKey;
use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Cache;
use Symfony\Component\HttpFoundation\Response;

class ApiKeyAuthentication
{
    public function handle(Request $request, Closure $next, string ...$requiredScopes): Response
    {
        $token = $request->bearerToken();

        if (! $token) {
            return ApiErrorResponse::unauthorized('API key required. Provide Authorization: Bearer <key> header.');
        }

        $apiKey = ApiKey::findByKey($token);

        if (! $apiKey || ! $apiKey->isActive()) {
            return ApiErrorResponse::unauthorized('Invalid or expired API key.');
        }

        foreach ($requiredScopes as $scope) {
            if (! $apiKey->hasScope($scope)) {
                return ApiErrorResponse::forbidden("Insufficient scope. Required: {$scope}");
            }
        }

        $limit = $apiKey->rate_limit_per_hour;

        [$allowed, $remaining, $resetAt] = $this->checkRateLimit($apiKey->id, $limit);

        if (! $allowed) {
            return ApiErrorResponse::rateLimited("Rate limit exceeded. Limit: {$limit} requests/hour.")
                ->withHeaders([
                    'X-RateLimit-Limit'     => $limit,
                    'X-RateLimit-Remaining' => 0,
                    'X-RateLimit-Reset'     => $resetAt,
                    'Retry-After'           => max(0, $resetAt - time()),
                ]);
        }

        // Tenant-level cap on top of per-key limit.  Without this, a
        // tenant who creates 10 keys at 1000/hr each could pull
        // 10,000/hr — bypassing any meaningful per-tenant SLA.  The
        // tenant cap defaults to 5x the per-key limit so legitimate
        // multi-key fan-out (separate prod / staging / CI keys) keeps
        // working.  Override per-tenant via tenants.settings.api.
        $tenantLimit = $this->resolveTenantLimit($apiKey);
        [$tenantAllowed, , $tenantReset] =
            $this->checkTenantRateLimit($apiKey->tenant_id, $tenantLimit);

        if (! $tenantAllowed) {
            return ApiErrorResponse::rateLimited("Tenant rate limit exceeded. Limit: {$tenantLimit} requests/hour.")
                ->withHeaders([
                    'X-RateLimit-Limit-Tenant'     => $tenantLimit,
                    'X-RateLimit-Remaining-Tenant' => 0,
                    'X-RateLimit-Reset-Tenant'     => $tenantReset,
                    'Retry-After'                  => max(0, $tenantReset - time()),
                ]);
        }

        $apiKey->debounceMarkUsed();

        $request->attributes->set('api_key', $apiKey);
        $request->attributes->set('tenant_id', $apiKey->tenant_id);

        return $next($request)->withHeaders([
            'X-RateLimit-Limit'     => $limit,
            'X-RateLimit-Remaining' => max(0, $remaining),
            'X-RateLimit-Reset'     => $resetAt,
        ]);
    }

    /**
     * Atomic fixed-window rate limiter using Cache::add() + Cache::increment().
     *
     * Cache::add() establishes the bucket with a 1-hour TTL ONLY if it does
     * not already exist (= start of window). Cache::increment() is atomic in
     * Redis/Memcached, eliminating the race condition of the old get+put pattern.
     * The window resets at a deterministic hourly boundary (floor of Unix epoch
     * in 3600-second buckets), not on a per-user sliding schedule.
     *
     * @return array{bool, int, int} [allowed, remaining, resetTimestamp]
     */
    private function checkRateLimit(int $keyId, int $limit): array
    {
        $window    = 3600;
        $bucket    = (int) (time() / $window);
        $cacheKey  = "api_rl:{$keyId}:{$bucket}";
        $resetAt   = ($bucket + 1) * $window;

        Cache::add($cacheKey, 0, $window);

        $count     = (int) Cache::increment($cacheKey);
        $allowed   = $count <= $limit;
        $remaining = max(0, $limit - $count);

        return [$allowed, $remaining, $resetAt];
    }

    /**
     * Resolve the per-hour cap for the tenant who owns this key.
     * Order of precedence:
     *   1. tenants.settings.api.rate_limit_per_hour  (operator override)
     *   2. config('leadhub.api.tenant_rate_limit_multiplier') × per-key
     *   3. 5 × per-key (5,000/hr default for the standard 1,000/hr key)
     */
    private function resolveTenantLimit(\App\Models\ApiKey $apiKey): int
    {
        $tenant = $apiKey->tenant ?? null;
        $explicit = $tenant
            ? data_get($tenant->settings, 'api.rate_limit_per_hour')
            : null;
        if (is_numeric($explicit) && (int) $explicit > 0) {
            return (int) $explicit;
        }

        $multiplier = (int) config('leadhub.api.tenant_rate_limit_multiplier', 5);
        if ($multiplier < 1) $multiplier = 1;

        return max(1, $multiplier * (int) $apiKey->rate_limit_per_hour);
    }

    /**
     * Tenant-level fixed-window counter mirroring checkRateLimit().
     * Same hourly bucket boundary so the per-key and per-tenant
     * counters reset at the same wall-clock moment.
     *
     * @return array{bool, int, int} [allowed, remaining, resetTimestamp]
     */
    private function checkTenantRateLimit(int $tenantId, int $limit): array
    {
        $window   = 3600;
        $bucket   = (int) (time() / $window);
        $cacheKey = "api_rl:tenant:{$tenantId}:{$bucket}";
        $resetAt  = ($bucket + 1) * $window;

        Cache::add($cacheKey, 0, $window);

        $count     = (int) Cache::increment($cacheKey);
        $allowed   = $count <= $limit;
        $remaining = max(0, $limit - $count);

        return [$allowed, $remaining, $resetAt];
    }
}
