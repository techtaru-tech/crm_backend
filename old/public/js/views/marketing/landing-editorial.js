/*!
 |------------------------------------------------------------------
 | landing-editorial.js — interactive pointer-tracking accents
 |------------------------------------------------------------------
 |
 | Paired with resources/views/marketing/landing-editorial.blade.php.
 | Extracted from a previously-inline <script> block to satisfy
 | CodeCanyon's "no inline scripts unless dynamic" rule (the script
 | body contains zero Blade interpolation — pure static client-side
 | behaviour).
 |
 | What it does: on non-touch, non-reduced-motion devices, tracks
 | the pointer over `.js-reactive` elements and exposes the
 | normalised x/y position as the CSS custom properties --mx / --my,
 | which the matching stylesheet uses to drive a soft hover gradient.
 */
(function () {
    'use strict';

    if (window.matchMedia && window.matchMedia('(prefers-reduced-motion: reduce)').matches) {
        return;
    }
    if ('ontouchstart' in window) {
        return;
    }

    document.querySelectorAll('.js-reactive').forEach(function (el) {
        el.addEventListener('pointermove', function (e) {
            var r = el.getBoundingClientRect();
            el.style.setProperty('--mx', ((e.clientX - r.left) / r.width * 100) + '%');
            el.style.setProperty('--my', ((e.clientY - r.top) / r.height * 100) + '%');
        }, { passive: true });
    });
})();
