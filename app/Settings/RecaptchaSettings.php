<?php

namespace App\Settings;

use Spatie\LaravelSettings\Settings;

/**
 * Google reCAPTCHA v3 configuration, operator-editable from the
 * Super Admin panel.  Version 3 (invisible / score-based) chosen
 * over v2 (checkbox / puzzle) to keep the UX clean on login and
 * registration — users never see a challenge unless their score
 * is below the configured threshold.
 *
 * Fields:
 *   enabled            Master switch.  When false, every guard
 *                      below is a no-op regardless of its own
 *                      toggle.
 *   site_key           Google reCAPTCHA site key (public).  Sent
 *                      to the browser in the script tag.
 *   secret_key         Google reCAPTCHA secret key (server-only).
 *                      Used to verify submitted tokens.
 *   guard_register     Protect the public /register form.
 *   guard_admin_login  Protect the tenant /admin login page.
 *   guard_sa_login     Protect the super-admin login page.
 *   min_score          Score threshold below which the token is
 *                      rejected (0.0 = definitely bot, 1.0 =
 *                      definitely human).  Google recommends 0.5.
 *
 * See https://developers.google.com/recaptcha/docs/v3 for setup
 * and score-tuning guidance.
 */
class RecaptchaSettings extends Settings
{
    public bool    $enabled           = false;
    public ?string $site_key          = null;
    public ?string $secret_key        = null;
    public bool    $guard_register    = true;
    public bool    $guard_admin_login = true;
    public bool    $guard_sa_login    = true;
    public float   $min_score         = 0.5;

    public static function group(): string
    {
        return 'recaptcha';
    }

    /**
     * Failure-tolerant factory.  Returns null if Spatie cannot resolve
     * the settings row — partial install, migration not yet run,
     * settings table missing, etc.  Public auth surfaces (register,
     * invitation accept, /admin login, /super-admin login) MUST use
     * this instead of `app(self::class)` directly: a fresh install with
     * no recaptcha row would otherwise 500 every login attempt and
     * lock the buyer out before they can configure.
     *
     * Usage:
     *     $captcha = RecaptchaSettings::resolveSafe();
     *     $needs   = $captcha?->shouldGuardRegister() ?? false;
     */
    public static function resolveSafe(): ?self
    {
        try {
            return app(self::class);
        } catch (\Throwable) {
            return null;
        }
    }

    /**
     * Effective enable check: master toggle + keys present.  A
     * caller that wants "really enabled" (not just half-configured)
     * calls this instead of reading ->enabled directly.
     */
    public function isActive(): bool
    {
        return $this->enabled
            && filled($this->site_key)
            && filled($this->secret_key);
    }

    public function shouldGuardRegister(): bool
    {
        return $this->isActive() && $this->guard_register;
    }

    public function shouldGuardAdminLogin(): bool
    {
        return $this->isActive() && $this->guard_admin_login;
    }

    public function shouldGuardSaLogin(): bool
    {
        return $this->isActive() && $this->guard_sa_login;
    }
}
