<?php

declare(strict_types=1);

namespace App\Models;

use Carbon\CarbonInterface;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\Cache;

/**
 * One row per failed authentication attempt.
 *
 * Drives:
 *   - Brute-force lockout (AuthAttemptListener)
 *   - SA "Recent suspicious logins" dashboard widget
 *   - Forensic trail when an account is reported compromised
 *
 * Reason codes used by AuthAttemptListener:
 *   - invalid_password   wrong credentials
 *   - unknown_email      email not on file (still recorded so we
 *                        catch enumeration attempts)
 *   - locked_out         attempt was rejected because lockout window
 *                        was active
 *   - two_factor_failed  TOTP code didn't match
 */
class LoginAttempt extends Model
{
    protected $table = 'login_attempts';

    protected $fillable = [
        'email',
        'ip',
        'user_agent',
        'success',
        'reason',
        'locked_until',
    ];

    protected $casts = [
        'success'      => 'boolean',
        'locked_until' => 'datetime',
    ];

    public const REASON_INVALID_PASSWORD   = 'invalid_password';
    public const REASON_UNKNOWN_EMAIL      = 'unknown_email';
    public const REASON_LOCKED_OUT         = 'locked_out';
    public const REASON_TWO_FACTOR_FAILED  = 'two_factor_failed';

    public const LOCKOUT_WINDOW_SEC   = 900;  // 15 min counter bucket
    public const LOCKOUT_DURATION_SEC = 900;  // 15 min cooldown
    public const LOCKOUT_THRESHOLD    = 5;

    /**
     * Atomically record a failed attempt and return the lockout
     * deadline if the failure puts this (email, ip) over the
     * threshold for the current 15-minute window.
     *
     * Mirrors the atomic pattern in ApiKeyAuthentication::checkRateLimit:
     * Cache::add() establishes the counter only if missing (start
     * of window); Cache::increment() is atomic in Redis/Memcached.
     * This eliminates the get-then-write race the previous DB-COUNT
     * implementation was exposed to under burst attacks.
     *
     * Bucket = intdiv(time(), 900) so the window resets at a
     * deterministic wall-clock boundary, not on a per-user sliding
     * schedule (matches ApiKeyAuthentication's design).
     *
     * Returns the lockout deadline (a Carbon instance) when the
     * threshold has just been crossed; null otherwise.  Callers
     * (AuthAttemptListener) write the DB audit row AFTER this
     * call so the lockout decision happens on the atomic counter,
     * not on a stale DB COUNT.
     */
    public static function recordFailureAndMaybeLock(
        ?string $email,
        ?string $ip,
        int $threshold = self::LOCKOUT_THRESHOLD,
        int $windowSec = self::LOCKOUT_WINDOW_SEC,
        int $lockoutSec = self::LOCKOUT_DURATION_SEC,
    ): ?CarbonInterface {
        $bucket = intdiv(time(), $windowSec);
        $tripped = false;

        $emailKey = $email !== null && $email !== ''
            ? 'login_fail:e:' . strtolower($email) . ':' . $bucket
            : null;
        $ipKey = $ip !== null && $ip !== ''
            ? 'login_fail:ip:' . $ip . ':' . $bucket
            : null;

        if ($emailKey !== null) {
            Cache::add($emailKey, 0, $windowSec);
            $count = (int) Cache::increment($emailKey);
            if ($count >= $threshold) {
                $tripped = true;
            }
        }

        if ($ipKey !== null) {
            Cache::add($ipKey, 0, $windowSec);
            $count = (int) Cache::increment($ipKey);
            if ($count >= $threshold) {
                $tripped = true;
            }
        }

        if (! $tripped) {
            return null;
        }

        $until = time() + $lockoutSec;
        if ($email !== null && $email !== '') {
            Cache::put('login_lockout:e:' . strtolower($email), $until, $lockoutSec);
        }
        if ($ip !== null && $ip !== '') {
            Cache::put('login_lockout:ip:' . $ip, $until, $lockoutSec);
        }

        return Carbon::createFromTimestamp($until);
    }

    /**
     * @deprecated REMOVED — was a get-then-write DB COUNT race that
     *             let burst attacks slip past the lockout threshold
     *             under concurrent load.  The signature is preserved
     *             so any out-of-tree caller fails LOUDLY at runtime
     *             instead of silently using stale data.  Use
     *             {@see recordFailureAndMaybeLock()} which applies
     *             the atomic Redis counter pattern.
     *
     * @throws \BadMethodCallException always
     */
    public static function shouldLock(?string $email, ?string $ip, int $threshold = 5, int $windowMinutes = 15): bool
    {
        throw new \BadMethodCallException(
            'shouldLock() is deprecated and unsafe under concurrent load. '
            . 'Use LoginAttempt::recordFailureAndMaybeLock() (atomic Redis counter) instead.'
        );
    }

    /**
     * Most recent active lockout for the given email/ip pair, if any.
     * Returns the unlock time or null when no lockout is active.
     *
     * Cache-first: the atomic counter sets a "login_lockout:*" key
     * with the unlock Unix timestamp.  We check those keys before
     * touching the DB so the hot path during a burst attack stays
     * O(1) Redis hits and never queries `login_attempts`.  DB query
     * is only used as a fallback for setups where the cache store
     * was rotated (e.g. `php artisan cache:clear` mid-attack) so
     * persistent locks recorded in the DB still apply.
     */
    public static function activeLockout(?string $email, ?string $ip): ?CarbonInterface
    {
        $now = time();
        $latest = 0;

        if ($email !== null && $email !== '') {
            $stamp = (int) Cache::get('login_lockout:e:' . strtolower($email), 0);
            if ($stamp > $now) {
                $latest = max($latest, $stamp);
            }
        }
        if ($ip !== null && $ip !== '') {
            $stamp = (int) Cache::get('login_lockout:ip:' . $ip, 0);
            if ($stamp > $now) {
                $latest = max($latest, $stamp);
            }
        }

        if ($latest > 0) {
            return Carbon::createFromTimestamp($latest);
        }

        // DB fallback — only consulted on cache miss.
        $row = static::query()
            ->where(function ($q) use ($email, $ip) {
                if ($email) $q->orWhere('email', strtolower($email));
                if ($ip)    $q->orWhere('ip', $ip);
            })
            ->whereNotNull('locked_until')
            ->where('locked_until', '>', now())
            ->orderByDesc('locked_until')
            ->first();

        return $row?->locked_until;
    }
}
