<?php

declare(strict_types=1);

namespace App\Http\Middleware;

use App\Models\LoginAttempt;
use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

/**
 * Manual-application middleware alias (`login.lockout`) for
 * NON-FILAMENT login routes.
 *
 * IMPORTANT: this middleware is NOT registered on the global web
 * stack.  Filament 4 + Livewire submit credentials via
 * /livewire/update, never the panel's /admin/login URL — a
 * path-based middleware never sees the Livewire request body, so
 * registering it globally would only create the illusion of
 * protection.  Filament panels enforce the lockout directly via
 * {@see \App\Filament\Auth\Pages\Login} which splices the gate into
 * Livewire's `authenticate()` lifecycle.
 *
 * Use this middleware ONLY when you build a custom non-Filament
 * login route (an old-school `<form method="post">` that hits a
 * controller); attach it via the `login.lockout` alias defined in
 * bootstrap/app.php.  In that case it short-circuits the POST when
 * the email or IP is in an active lockout window, returns a
 * redirect-back with a friendly "try again in N minutes" flash,
 * and writes a `LoginAttempt` row with reason=locked_out so the SA
 * dashboard sees attempted bypasses during a cooldown.
 *
 * Pairs with AuthAttemptListener which sets the lockout marker on
 * the 5th failure within 15 minutes via the atomic Redis counter
 * ({@see \App\Models\LoginAttempt::recordFailureAndMaybeLock()}).
 */
class EnforceLoginLockout
{
    /**
     * URI patterns we treat as a login form post.  Filament's default
     * tenant panel exposes /admin/login; the SA panel uses /super/login.
     * If the buyer customises the slug they can override this list
     * via config/auth.php (login_lockout_paths).  Anything not in this
     * set passes through untouched so we don't accidentally lock out
     * normal POST endpoints (forms, API webhooks, etc).
     */
    private function loginPaths(Request $request): array
    {
        $configured = config('auth.login_lockout_paths');
        if (is_array($configured) && ! empty($configured)) {
            return array_map('strval', $configured);
        }
        return [
            'admin/login',
            'super/login',
            'portal/login',
            'login',
        ];
    }

    public function handle(Request $request, Closure $next): Response
    {
        if (! $request->isMethod('post')) {
            return $next($request);
        }

        $path = ltrim($request->path(), '/');
        if (! in_array($path, $this->loginPaths($request), true)) {
            return $next($request);
        }

        $email = strtolower((string) $request->input('email', ''));
        $ip    = $request->ip();

        $lockedUntil = LoginAttempt::activeLockout(
            $email !== '' ? $email : null,
            $ip,
        );

        if ($lockedUntil) {
            try {
                LoginAttempt::create([
                    'email'      => $email !== '' ? $email : null,
                    'ip'         => $ip,
                    'user_agent' => substr((string) $request->userAgent(), 0, 255),
                    'success'    => false,
                    'reason'     => LoginAttempt::REASON_LOCKED_OUT,
                ]);
            } catch (\Throwable) {
                // best-effort
            }

            $minutes = max(1, (int) ceil(now()->diffInMinutes($lockedUntil, false)));
            $message = trans_choice(
                'middleware.login_lockout.too_many_attempts',
                $minutes,
                ['count' => $minutes],
            );

            return back()
                ->withInput($request->only('email'))
                ->withErrors(['email' => $message]);
        }

        return $next($request);
    }
}
