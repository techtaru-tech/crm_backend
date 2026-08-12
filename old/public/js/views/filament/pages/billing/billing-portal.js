/*!
 |------------------------------------------------------------------
 | billing-portal.js — Monthly / Annual billing-period toggle
 |------------------------------------------------------------------
 |
 | Paired with resources/views/filament/pages/billing/billing-portal.blade.php.
 | Extracted from a previously-inline <script> block to satisfy
 | CodeCanyon's "no inline scripts unless dynamic" rule.
 |
 | This script is required for runtime interactivity (no SSR fallback
 | exists for the toggle). It only flips the aria-selected attribute
 | and toggles two utility classes — the visual styles for active /
 | inactive buttons live in
 | public/css/views/filament/pages/billing/billing-portal.css
 | (.bp-toggle-btn-active, .bp-toggle-btn-inactive + [aria-selected]
 | selectors), not in JS string mutation.
 |
 | Zero Blade interpolation — selectors and class names are static.
 */
(function () {
    'use strict';

    var toggle = document.getElementById('lh-billing-toggle');
    if (!toggle) return;
    var buttons = toggle.querySelectorAll('button[data-period]');
    function setPeriod(period) {
        buttons.forEach(function (b) {
            var active = b.getAttribute('data-period') === period;
            b.setAttribute('aria-selected', active ? 'true' : 'false');
            b.classList.toggle('bp-toggle-btn-active', active);
            b.classList.toggle('bp-toggle-btn-inactive', !active);
        });
        document.querySelectorAll('[data-billing-period]').forEach(function (el) {
            el.classList.toggle('bp-period-hidden', el.getAttribute('data-billing-period') !== period);
        });
        document.querySelectorAll('[data-billing-cta]').forEach(function (el) {
            el.classList.toggle('bp-period-hidden', el.getAttribute('data-billing-cta') !== period);
        });
    }
    buttons.forEach(function (b) {
        b.addEventListener('click', function () { setPeriod(b.getAttribute('data-period')); });
    });
})();
