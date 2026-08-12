/*!
 |------------------------------------------------------------------
 | cookie-consent.js — GDPR / ePrivacy banner handler
 |------------------------------------------------------------------
 |
 | Paired with resources/views/marketing/partials/cookie-consent.blade.php.
 | Extracted from a previously-inline <script> block to satisfy
 | CodeCanyon's "no inline scripts unless dynamic" rule.
 |
 | Storage contract (unchanged from the inline original):
 |   localStorage[lh_cookie_consent] = "granted" -> hide banner,
 |                                                  allow analytics
 |   localStorage[lh_cookie_consent] = "denied"  -> hide banner,
 |                                                  suppress analytics
 |
 | Integrations: pages that load Plausible / GA / Hotjar / etc. gate
 | their loaders on `window.__lhCookieConsent === 'granted'` and
 | listen for the `lh:cookie-consent` window event to retro-boot
 | trackers once the user accepts.
 |
 | Zero Blade interpolation — all selectors and storage keys are
 | static.
 */
(function () {
    'use strict';

    var KEY = 'lh_cookie_consent';
    var banner = document.getElementById('lh-cookie-banner');
    if (!banner) return;

    var stored;
    try { stored = window.localStorage.getItem(KEY); } catch (_) { stored = null; }
    if (stored === 'granted' || stored === 'denied') {
        window.__lhCookieConsent = stored;
        return;
    }

    banner.classList.remove('is-hidden');

    function decide(value) {
        try { window.localStorage.setItem(KEY, value); } catch (_) {}
        window.__lhCookieConsent = value;
        try {
            window.dispatchEvent(new CustomEvent('lh:cookie-consent', { detail: { value: value } }));
        } catch (_) {}
        banner.classList.add('is-hidden');
    }

    banner.addEventListener('click', function (e) {
        var t = e.target.closest('[data-cookie-action]');
        if (!t) return;
        decide(t.getAttribute('data-cookie-action') === 'accept' ? 'granted' : 'denied');
    });
})();
