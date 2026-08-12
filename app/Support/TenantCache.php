<?php

declare(strict_types=1);

namespace App\Support;

use Closure;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Log;
use Throwable;

/**
 * Per-tenant cache helper.
 *
 * On a tag-capable driver (redis / memcached / array), every cached
 * value is tagged with `tenant:{$id}` so a single
 * TenantCache::flushTenant($id) invalidates ALL of that tenant's
 * cached widgets / nav badges / settings the moment a write happens.
 *
 * On a non-tagged driver (file / database), tags are silently
 * skipped; the underlying TTL is what keeps data fresh. flushTenant()
 * becomes a no-op. This means buyers on shared hosting see exactly
 * the same behavior as before this helper existed - they're just
 * not getting the tag-invalidation win until they upgrade to redis.
 *
 * The wrap is best-effort: any cache backend exception falls through
 * to a direct callback evaluation so a broken cache layer can never
 * 500 the request. Same defensive pattern as TrackUserSession.
 */
final class TenantCache
{
    /**
     * Cache stores that support tagged operations. Laravel throws
     * BadMethodCallException on Cache::tags() with file / database /
     * dynamodb stores, so we explicitly opt in.
     *
     * @var array<int, string>
     */
    private const TAG_CAPABLE = ['redis', 'memcached', 'array', 'apc'];

    /**
     * Tag-aware Cache::remember(). Same signature you would expect,
     * with $tenantId as the leading scope arg so the helper can
     * compose the `tenant:{$id}` tag and (incidentally) prefix the
     * key for human-readability in `redis-cli MONITOR`.
     */
    public static function remember(int $tenantId, string $key, int $ttlSeconds, Closure $callback): mixed
    {
        try {
            if (self::supportsTags()) {
                return Cache::tags(['tenant:' . $tenantId])
                    ->remember($key, $ttlSeconds, $callback);
            }

            return Cache::remember($key, $ttlSeconds, $callback);
        } catch (Throwable $e) {
            Log::warning(
                '[tenant-cache] remember failed (non-fatal, evaluating callback directly): ' . $e->getMessage(),
                ['key' => $key, 'tenant' => $tenantId],
            );

            return $callback();
        }
    }

    /**
     * Invalidate every cached value tagged for this tenant. No-op on
     * non-tagged drivers - the TTL keeps things fresh there instead.
     *
     * Call this from observers (LeadObserver::saved,
     * AutomationRun::saved, etc.) so a write to a tenant's data
     * surfaces immediately on the dashboard rather than waiting up to
     * 60 s for the cache TTL to expire.
     */
    public static function flushTenant(int $tenantId): void
    {
        if (! self::supportsTags()) {
            return;
        }

        try {
            Cache::tags(['tenant:' . $tenantId])->flush();
        } catch (Throwable $e) {
            Log::warning(
                '[tenant-cache] flushTenant failed (non-fatal): ' . $e->getMessage(),
                ['tenant' => $tenantId],
            );
        }
    }

    private static function supportsTags(): bool
    {
        return in_array((string) config('cache.default'), self::TAG_CAPABLE, true);
    }
}
