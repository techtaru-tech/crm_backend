/*!
 |------------------------------------------------------------------
 | sw-register.js — Service-worker registration for the admin panel
 |------------------------------------------------------------------
 |
 | Replaces a previously-inline <script> in
 | AdminPanelProvider::renderHook(HEAD_END, …) that registered the
 | /sw.js service worker.  Extracted to satisfy CodeCanyon's
 | "no inline scripts unless dynamic" rule.
 |
 | Behaviour: registers /sw.js on `window.load` if the browser
 | supports the Service Worker API.  Failure (older browser,
 | restricted context, non-HTTPS) is silently ignored — the admin
 | continues to function without PWA install / web-push.
 */
(function () {
    'use strict';

    if (!('serviceWorker' in navigator)) {
        return;
    }

    window.addEventListener('load', function () {
        navigator.serviceWorker.register('/sw.js').catch(function () {
            // Silent failure — non-fatal.
        });
    });
})();
