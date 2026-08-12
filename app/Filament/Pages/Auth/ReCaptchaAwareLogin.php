<?php

namespace App\Filament\Pages\Auth;

use App\Filament\Auth\Pages\Login;
use App\Services\RecaptchaService;
use App\Settings\RecaptchaSettings;
use Filament\Auth\Http\Responses\Contracts\LoginResponse;
use Illuminate\Validation\ValidationException;

/**
 * Filament Login page with Google reCAPTCHA v3 pre-check.
 *
 * The base Filament Login is a Livewire component that renders
 * via the schema system, so there's no HTML form template to
 * splice.  Instead we:
 *
 *   1. Expose a public `$recaptchaToken` property on the Livewire
 *      component.  The panel provider injects a hidden input and
 *      the reCAPTCHA loader/executor script via the
 *      AUTH_LOGIN_FORM_BEFORE render hook — the input is bound
 *      via wire:model to this property, so by the time the user
 *      clicks Sign In, the latest token is in the component
 *      state.
 *   2. Override authenticate() to verify the token BEFORE the
 *      parent method runs its credentials check, so a failed
 *      captcha never even touches the password broker.
 *
 * Subclasses set the action string (admin_login, sa_login, …) so
 * server-side verification can match the action the client
 * actually executed.
 */
abstract class ReCaptchaAwareLogin extends Login
{
    public string $recaptchaToken = '';

    /** Which RecaptchaSettings guard this login obeys. */
    abstract protected function recaptchaGuardPredicate(): bool;

    /** The reCAPTCHA v3 action string for this login. */
    abstract protected function recaptchaAction(): string;

    public function authenticate(): ?LoginResponse
    {
        if ($this->recaptchaGuardPredicate()) {
            $ok = app(RecaptchaService::class)->verify(
                $this->recaptchaToken,
                $this->recaptchaAction(),
                request()->ip(),
            );

            if (! $ok) {
                throw ValidationException::withMessages([
                    'data.email' => __('auth_validation.recaptcha_failed_login'),
                ]);
            }
        }

        // Suspension gate — runs BEFORE credentials so a
        // suspended user learns their account is blocked without
        // us confirming their password is correct.  Lookup is
        // a single users-row query by the email the user typed.
        $email = (string) ($this->form->getRawState()['email'] ?? '');
        if ($email !== '') {
            $target = \App\Models\User::where('email', $email)->first();
            if ($target && $target->isSuspended()) {
                throw ValidationException::withMessages([
                    'data.email' => __('auth_validation.account_suspended'),
                ]);
            }
        }

        return parent::authenticate();
    }
}
