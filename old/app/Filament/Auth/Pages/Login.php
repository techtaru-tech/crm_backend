<?php

declare(strict_types=1);

namespace App\Filament\Auth\Pages;

use App\Models\LoginAttempt;
use Filament\Auth\Http\Responses\Contracts\LoginResponse;
use Filament\Auth\Pages\Login as FilamentLogin;
use Illuminate\Validation\ValidationException;

/**
 * Lockout-aware base Login page for Filament panels.
 *
 * Filament 4 + Livewire submit credentials via /livewire/update,
 * NOT via the panel's /admin/login URL — so a path-based middleware
 * never sees the request body.  We instead splice the lockout
 * gate directly into the Livewire component's authenticate() so it
 * runs BEFORE Filament hands credentials to the auth broker.
 *
 * Pairs with AuthAttemptListener which records each Failed auth
 * event and writes the lockout marker.  This page reads the same
 * marker (via LoginAttempt::activeLockout) and short-circuits with
 * a friendly "try again in N minutes" validation error.
 *
 * Subclasses (WhiteLabelLogin, SuperAdminLogin via ReCaptchaAware-
 * Login) keep their additional pre-checks intact — they call
 * parent::authenticate() which arrives here, so the lockout
 * decision is the last gate before the password broker.
 */
abstract class Login extends FilamentLogin
{
    public function authenticate(): ?LoginResponse
    {
        $email = strtolower((string) data_get($this->form->getState(), 'email', ''));
        $ip    = request()?->ip();

        $lockedUntil = LoginAttempt::activeLockout(
            $email !== '' ? $email : null,
            $ip,
        );

        if ($lockedUntil) {
            // Audit row so the SA "Recent suspicious logins" widget
            // shows attempted bypasses during a cooldown window.
            try {
                LoginAttempt::create([
                    'email'      => $email !== '' ? $email : null,
                    'ip'         => $ip,
                    'user_agent' => substr((string) request()?->userAgent(), 0, 255),
                    'success'    => false,
                    'reason'     => LoginAttempt::REASON_LOCKED_OUT,
                ]);
            } catch (\Throwable) {
                // Audit row is best-effort — never break login.
            }

            $minutes = max(1, (int) ceil(now()->diffInMinutes($lockedUntil, false)));

            throw ValidationException::withMessages([
                'data.email' => trans_choice('auth_validation.too_many_attempts', $minutes, ['minutes' => $minutes]),
            ]);
        }

        return parent::authenticate();
    }
}
