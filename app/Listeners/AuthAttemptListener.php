<?php

declare(strict_types=1);

namespace App\Listeners;

use App\Models\AuditLog;
use App\Models\LoginAttempt;
use App\Models\User;
use Illuminate\Auth\Events\Failed;

/**
 * Records each failed authentication attempt and triggers a 15-minute
 * lockout when the threshold is hit.  Wired in AppServiceProvider's
 * boot() via Event::listen(Failed::class, AuthAttemptListener::class).
 *
 * The Filament panel's login flow uses the standard Laravel auth
 * provider so Failed events fire whether the user lands on the tenant
 * panel or the super-admin panel.  EnforceLoginLockout middleware
 * reads activeLockout() to short-circuit any further attempt while
 * the cooldown window is still active.
 *
 * Trade-offs:
 *   - 5 failures / 15 min covers the "forgot my password" case
 *     without locking out a typo-prone user, while still raising the
 *     bar for credential-stuffing automation.
 *   - We log unknown-email attempts too (not just known-user
 *     failures) so attackers can't use slow enumeration to bypass
 *     the lockout by burning through one address per minute.
 */
class AuthAttemptListener
{
    public const LOCKOUT_THRESHOLD     = 5;
    public const LOCKOUT_WINDOW_MIN    = 15;
    public const LOCKOUT_DURATION_MIN  = 15;

    public function handle(Failed $event): void
    {
        $email = strtolower((string) ($event->credentials['email'] ?? ''));
        // Strip CR/LF so a crafted "victim@example.com\nFAKE LOG ENTRY"
        // can't inject forged lines into our audit logs.
        $email = str_replace(["\r", "\n"], '', $email);
        $ip    = request()?->ip();
        $ua    = (string) substr((string) request()?->userAgent(), 0, 255);

        $reason = $event->user instanceof User
            ? LoginAttempt::REASON_INVALID_PASSWORD
            : LoginAttempt::REASON_UNKNOWN_EMAIL;

        // Atomic Cache-based counter: Cache::add + Cache::increment
        // races safely under concurrent burst attempts, unlike the
        // previous DB-COUNT path which could let multiple workers
        // observe a stale sub-threshold count and slip past lockout.
        // The DB row below is then written for audit purposes only.
        $lockedUntil = LoginAttempt::recordFailureAndMaybeLock(
            $email !== '' ? $email : null,
            $ip,
            self::LOCKOUT_THRESHOLD,
            self::LOCKOUT_WINDOW_MIN * 60,
            self::LOCKOUT_DURATION_MIN * 60,
        );

        try {
            LoginAttempt::create([
                'email'        => $email !== '' ? $email : null,
                'ip'           => $ip,
                'user_agent'   => $ua !== '' ? $ua : null,
                'success'      => false,
                'reason'       => $reason,
                'locked_until' => $lockedUntil,
            ]);
        } catch (\Throwable) {
            // Failing to write the audit row should not break login —
            // the cache-based lockout decision has already been made
            // and persisted by recordFailureAndMaybeLock().
        }

        if ($lockedUntil) {
            try {
                AuditLog::record('auth.lockout_triggered', null, [], [
                    'email'        => $email !== '' ? $email : null,
                    'ip'           => $ip,
                    'locked_until' => $lockedUntil->toIso8601String(),
                    'threshold'    => self::LOCKOUT_THRESHOLD,
                    'window_min'   => self::LOCKOUT_WINDOW_MIN,
                ], tags: 'security,auth,lockout');
            } catch (\Throwable) {
                // Audit log is best-effort.
            }
        }
    }
}
