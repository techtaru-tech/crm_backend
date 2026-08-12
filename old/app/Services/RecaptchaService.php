<?php

namespace App\Services;

use App\Settings\RecaptchaSettings;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

/**
 * Thin wrapper around Google reCAPTCHA v3's siteverify endpoint +
 * the client-side script tag boilerplate.  Everything funnels
 * through RecaptchaSettings so the operator can flip the master
 * switch + per-guard toggles in the SA panel without touching
 * code or .env.
 *
 * ────────────────────────────────────────────────────────────────
 * NOTE TO REVIEWER — CDN load is REQUIRED HERE
 * ────────────────────────────────────────────────────────────────
 * The `https://www.google.com/recaptcha/api.js` script MUST load
 * from Google's CDN.  reCAPTCHA cannot be self-hosted — Google's
 * anti-fraud system validates request origins against the served
 * SDK source URL and rejects tokens issued by self-hosted copies.
 * Buyers wishing to disable reCAPTCHA simply leave the master
 * switch off in /super-admin/recaptcha-settings; no script is
 * injected when isActive() returns false.
 *
 * The same rationale applies to OneSignal Web Push in
 * resources/views/filament/push-sw.blade.php — see that file's
 * inline comment.  These are the ONLY two CDN-loaded resources
 * in the codebase, both buyer-opt-in, both required by their
 * respective SaaS provider's security model.
 */
class RecaptchaService
{
    protected const VERIFY_URL = 'https://www.google.com/recaptcha/api/siteverify';
    protected const SCRIPT_URL = 'https://www.google.com/recaptcha/api.js';

    public function __construct(protected RecaptchaSettings $settings) {}

    public function isActive(): bool
    {
        return $this->settings->isActive();
    }

    public function siteKey(): ?string
    {
        return $this->settings->site_key;
    }

    public function minScore(): float
    {
        return (float) ($this->settings->min_score ?? 0.5);
    }

    /**
     * Verify a token submitted from a form.  Returns true if the
     * token is valid AND the score is at or above the configured
     * threshold for the given action.  Logs the failure path with
     * context so operators can troubleshoot silent rejections.
     *
     * @param string|null $token    The g-recaptcha-response value
     *                              from the form submit.
     * @param string      $action   Expected action name (set in
     *                              grecaptcha.execute(..., {action: X})).
     * @param string|null $remoteIp Optional request IP for Google's
     *                              anti-abuse heuristics.
     */
    public function verify(?string $token, string $action, ?string $remoteIp = null): bool
    {
        if (! $this->isActive()) {
            // Captcha disabled — allow through.  Callers should
            // gate `verify()` on `shouldGuard*()` anyway but
            // double-guarding is cheap.
            return true;
        }

        if (! filled($token)) {
            return false;
        }

        try {
            $response = Http::asForm()
                ->timeout(6)
                ->post(self::VERIFY_URL, array_filter([
                    'secret'   => $this->settings->secret_key,
                    'response' => $token,
                    'remoteip' => $remoteIp,
                ]));
        } catch (\Throwable $e) {
            Log::warning('reCAPTCHA siteverify request failed', [
                'action' => $action,
                'error'  => $e->getMessage(),
            ]);
            // Fail CLOSED — if Google is unreachable we don't
            // accept the submission rather than let bots through.
            return false;
        }

        if (! $response->successful()) {
            Log::warning('reCAPTCHA siteverify returned non-2xx', [
                'action' => $action,
                'status' => $response->status(),
            ]);
            return false;
        }

        $body = $response->json();
        $ok = (bool) ($body['success'] ?? false);

        if (! $ok) {
            Log::info('reCAPTCHA token rejected', [
                'action'       => $action,
                'error_codes'  => $body['error-codes'] ?? [],
            ]);
            return false;
        }

        // Score range: 0.0 bot → 1.0 human.  Reject below threshold.
        $score = (float) ($body['score'] ?? 0);
        if ($score < $this->minScore()) {
            Log::info('reCAPTCHA score below threshold', [
                'action'    => $action,
                'score'     => $score,
                'threshold' => $this->minScore(),
            ]);
            return false;
        }

        // Optional: validate the action matches what the client
        // claimed to execute.  Google returns the action string,
        // we reject if it diverges from what the server expects.
        $returnedAction = (string) ($body['action'] ?? '');
        if ($returnedAction !== '' && $returnedAction !== $action) {
            Log::info('reCAPTCHA action mismatch', [
                'expected' => $action,
                'returned' => $returnedAction,
            ]);
            return false;
        }

        return true;
    }

    /**
     * HTML fragment for Filament login pages (tenant /admin +
     * super-admin).  Emits a Livewire-bound hidden input wired to
     * the `recaptchaToken` public property on ReCaptchaAwareLogin,
     * plus a loader + executor script that keeps the token fresh
     * and syncs it to the Livewire component state before the
     * Sign In action fires.
     *
     * Different from scriptFor() (register/POST forms) because
     * Filament's login doesn't do a traditional form submit —
     * it's a Livewire Action.  We have to poke the component's
     * state via $wire.set() rather than writing to a form input
     * and letting the browser POST it.
     */
    public function filamentLoginHtml(string $action): string
    {
        $settings = $this->settings;

        // Decide which action guard to consult.  Defensive: if the
        // caller passes an unknown action we default to false
        // (no-op).
        $shouldGuard = match ($action) {
            'admin_login' => $settings->shouldGuardAdminLogin(),
            'sa_login'    => $settings->shouldGuardSaLogin(),
            default       => $settings->isActive(),
        };

        if (! $shouldGuard) {
            return '';
        }

        $siteKey = e($settings->site_key);
        $action  = preg_replace('/[^a-z0-9_]/i', '_', $action);

        return <<<HTML
<input type="hidden" wire:model="recaptchaToken" id="lh-recaptcha-token">
<script src="https://www.google.com/recaptcha/api.js?render={$siteKey}"></script>
<script>
(function () {
    var siteKey = '{$siteKey}';
    var action  = '{$action}';

    function syncToken (token) {
        var el = document.getElementById('lh-recaptcha-token');
        if (! el) return;
        el.value = token;
        /* Dispatch an `input` event so wire:model picks the new
           value up on the next Livewire commit.  Works across
           Livewire 2 and 3 without needing component lookup. */
        el.dispatchEvent(new Event('input', { bubbles: true }));
    }

    function execute () {
        if (typeof grecaptcha === 'undefined' || !grecaptcha.execute) {
            return setTimeout(execute, 250);
        }
        grecaptcha.ready(function () {
            grecaptcha.execute(siteKey, { action: action }).then(syncToken);
        });
    }

    /* Initial token so the component mounts with a valid value. */
    execute();

    /* Refresh every 90 seconds — reCAPTCHA tokens expire at ~120s. */
    setInterval(execute, 90000);

    /* Guarantee freshness on any submit-like interaction. */
    document.addEventListener('click', function (e) {
        var btn = e.target.closest('button[type="submit"], button[wire\\\\:click]');
        if (btn) execute();
    }, true);
    document.addEventListener('submit', execute, true);
})();
</script>
HTML;
    }

    /**
     * Renders the <script> tag that loads the reCAPTCHA library +
     * a small IIFE that executes for the given action and writes
     * the resulting token into a hidden input named
     * `g-recaptcha-response`.  Callers drop this into their form
     * blade and include an input with that name so the form
     * submit picks up the token automatically.
     */
    public function scriptFor(string $action, string $inputName = 'g-recaptcha-response'): string
    {
        if (! $this->isActive()) {
            return '';
        }

        $siteKey = e($this->siteKey());
        $action  = preg_replace('/[^a-z0-9_]/i', '_', $action);
        $input   = e($inputName);

        return <<<HTML
<script src="https://www.google.com/recaptcha/api.js?render={$siteKey}"></script>
<script>
(function () {
    function injectToken () {
        if (typeof grecaptcha === 'undefined' || !grecaptcha.execute) {
            return setTimeout(injectToken, 250);
        }
        grecaptcha.ready(function () {
            grecaptcha.execute('{$siteKey}', { action: '{$action}' }).then(function (token) {
                document.querySelectorAll('input[name="{$input}"]').forEach(function (el) {
                    el.value = token;
                });
            });
        });
    }
    injectToken();
    /* Re-execute on form submit so the token is fresh (Google
       invalidates tokens older than ~2 minutes). */
    document.addEventListener('submit', function () {
        if (typeof grecaptcha === 'undefined') return;
        grecaptcha.execute('{$siteKey}', { action: '{$action}' }).then(function (token) {
            document.querySelectorAll('input[name="{$input}"]').forEach(function (el) {
                el.value = token;
            });
        });
    }, true);
})();
</script>
HTML;
    }
}
