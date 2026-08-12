<?php

namespace App\Http\Middleware;

use App\Models\UserSession;
use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Log;
use Symfony\Component\HttpFoundation\Response;
use Throwable;

class TrackUserSession
{
    private const UPDATE_DEBOUNCE_SECONDS = 60;

    public function handle(Request $request, Closure $next): Response
    {
        if (Auth::check()) {
            $sessionId = $request->session()->getId();

            // Revocation status is read on EVERY authenticated request -
            // hot enough to be worth a 30 s cache. Tradeoff: an admin-
            // revoked session may take up to 30 s to log out; in
            // exchange we drop a `user_sessions` SELECT per request.
            // Cache failures fall through to the DB so revocation can
            // never be silently bypassed by a broken cache backend.
            $isRevoked = $this->isSessionRevoked($sessionId);

            if ($isRevoked) {
                Auth::logout();
                $request->session()->invalidate();
                $request->session()->regenerateToken();

                return redirect('/admin/login')
                    ->withErrors(['email' => __('messages.session_revoked')]);
            }

            // Session tracking is observability, not a hard dependency.
            // A broken cache backend (e.g. wiped storage/framework/cache/data
            // dir, missing 0775 perms after a manual `cache:clear`, or a
            // Redis hiccup) must not take down the actual request the
            // tenant is trying to serve.
            try {
                $this->maybeUpsertSession($request, $sessionId);
            } catch (Throwable $e) {
                Log::warning(
                    '[track-session] upsert failed (non-fatal): ' . $e->getMessage(),
                    ['session_id' => $sessionId, 'exception' => $e],
                );
            }
        }

        return $next($request);
    }

    /**
     * Cached revocation lookup. Cache miss path always queries the DB,
     * so a broken cache backend degrades to "one DB read per request"
     * rather than ever silently treating revoked sessions as live.
     *
     * 30 s TTL is a deliberate balance between admin responsiveness
     * (a revoked session is forced out within 30 s) and per-request
     * DB load (hot path drops from N reads/min to 2/min per session).
     */
    protected function isSessionRevoked(string $sessionId): bool
    {
        $cacheKey = 'session-revoke:' . $sessionId;

        try {
            return (bool) Cache::remember(
                $cacheKey,
                30,
                fn () => optional(
                    UserSession::select(['revoked_at'])
                        ->where('session_id', $sessionId)
                        ->first()
                )?->isRevoked() ?? false,
            );
        } catch (Throwable $e) {
            // Cache backend is unhealthy. Fall back to a direct DB
            // read so revocation enforcement is never bypassed.
            Log::warning(
                '[track-session] revocation cache read failed (non-fatal): ' . $e->getMessage(),
                ['key' => $cacheKey],
            );

            return optional(
                UserSession::select(['revoked_at'])
                    ->where('session_id', $sessionId)
                    ->first()
            )?->isRevoked() ?? false;
        }
    }

    /**
     * Only writes to the DB at most once per minute per session.
     * The revocation check above always reads, but the expensive upsert
     * is gated behind a cheap cache key so we don't hammer the sessions
     * table on every page load.
     *
     * Cache reads/writes are individually wrapped: if the file-cache
     * shard directory cannot be written (a common cPanel symptom after
     * `cache:clear` wipes storage/framework/cache/data and the PHP-FPM
     * user lacks write perms on the recreated path) we fall back to
     * touching the DB on every request rather than 500'ing the page.
     * The handler-level catch above is the final safety net.
     */
    protected function maybeUpsertSession(Request $request, string $sessionId): void
    {
        $cacheKey = 'session_updated:' . $sessionId;

        try {
            if (Cache::has($cacheKey)) {
                return;
            }
        } catch (Throwable $e) {
            // Cache backend unhealthy - fall through to upsert. Worst case:
            // we hit `user_sessions` once per request until the cache layer
            // is fixed. That's a perf regression, not a correctness bug.
            Log::warning(
                '[track-session] cache read failed (non-fatal): ' . $e->getMessage(),
                ['key' => $cacheKey],
            );
        }

        $userAgent = $request->userAgent() ?? '';
        $info      = UserSession::parseDeviceInfo($userAgent);

        UserSession::updateOrCreate(
            ['session_id' => $sessionId],
            [
                'user_id'        => Auth::id(),
                'ip_address'     => $request->ip(),
                'user_agent'     => $userAgent,
                'device_type'    => $info['device'],
                'browser'        => $info['browser'],
                'platform'       => $info['platform'],
                'last_active_at' => now(),
            ]
        );

        try {
            Cache::put($cacheKey, 1, self::UPDATE_DEBOUNCE_SECONDS);
        } catch (Throwable $e) {
            Log::warning(
                '[track-session] cache write failed (non-fatal): ' . $e->getMessage(),
                ['key' => $cacheKey],
            );
        }
    }
}
