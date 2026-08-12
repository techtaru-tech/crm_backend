<?php

namespace App\Http\Middleware;

use App\Settings\GeneralSettings;
use Carbon\Carbon;
use Carbon\CarbonImmutable;
use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

/**
 * Resolves the active UI language for the current request. Priority:
 *
 *   1. ?lang=xx query param (writes to session)
 *   2. session('locale') — set by the /locale/{code} switcher
 *   3. authenticated user's persisted preference column
 *   4. GeneralSettings->language (super admin default)
 *   5. config('locales.default')
 *
 * Anything we receive is validated against config('locales.supported')
 * so a hostile query string can't flip us to a missing lang file.
 *
 * On resolution, BOTH `app()->setLocale($locale)` AND
 * `Carbon::setLocale($locale)` are called.  Without the Carbon side,
 * `->diffForHumans()`, `->translatedFormat()`, `->isoFormat()`,
 * `->ago()` etc. all keep rendering English regardless of the active
 * UI locale — a silent leak class because the Filament UI shell
 * around the date IS translated, so the date string stands out
 * against an otherwise localized page.
 */
class SetLocale
{
    public function handle(Request $request, Closure $next): Response
    {
        $supported = array_keys(config('locales.supported', ['en' => []]));
        $locale    = null;

        // 1. Explicit ?lang=xx flip
        $queryLang = $request->query('lang');
        if ($queryLang && in_array($queryLang, $supported, true)) {
            session(['locale' => $queryLang]);
            $locale = $queryLang;
        }

        // 2. Session memory
        if (! $locale) {
            $sessionLang = $request->session()->get('locale');
            if ($sessionLang && in_array($sessionLang, $supported, true)) {
                $locale = $sessionLang;
            }
        }

        // 3. Authenticated user's preferred locale via HasLocalePreference.
        //    User model implements Illuminate\Contracts\Translation\HasLocalePreference
        //    and exposes preferredLocale() backed by the users.language column
        //    (added in 2026_05_16_120000_add_language_to_users_table migration).
        //    `property_exists()` doesn't see Eloquent dynamic attributes — //    flagged the prior check as dead code; this version uses the contract.
        $user = $request->user();
        if (! $locale && $user instanceof \Illuminate\Contracts\Translation\HasLocalePreference) {
            $userLocale = $user->preferredLocale();
            if ($userLocale && in_array($userLocale, $supported, true)) {
                $locale = $userLocale;
            }
        }

        // 4. Super admin's global default
        if (! $locale) {
            try {
                $default = app(GeneralSettings::class)->language ?? null;
                if ($default && in_array($default, $supported, true)) {
                    $locale = $default;
                }
            } catch (\Throwable) {
                // Settings migration may not have run yet.
            }
        }

        // 5. Hard config fallback
        if (! $locale) {
            $locale = config('locales.default', config('app.locale', 'en'));
        }

        app()->setLocale($locale);

        // Carbon needs its own locale call — Laravel's app()->setLocale()
        // only routes the translator.  Without this, every
        // ->diffForHumans() / ->translatedFormat() / ->isoFormat()
        // anywhere in the codebase (live lead feed, activity timeline,
        // billing portal events, notification timestamps, ...) stays
        // English even when the surrounding UI is Spanish / Arabic /
        // Hindi.  CarbonImmutable shares the locale registry with
        // Carbon, so one call covers both; we still call both
        // explicitly so a future Carbon refactor that splits them
        // doesn't quietly regress.
        try {
            Carbon::setLocale($locale);
            CarbonImmutable::setLocale($locale);
        } catch (\Throwable) {
            // Unknown locale (e.g. typo in the locales table) — keep
            // Carbon's previous locale rather than crashing the
            // request.  The validator above limits this to
            // supported locales, so a real-world miss requires the
            // Carbon translation pack for that locale to be missing
            // from carbon/translation-data.
        }

        return $next($request);
    }
}
