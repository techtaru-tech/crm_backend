/*!
 |------------------------------------------------------------------
 | impersonation-bar.js — Adds `has-impersonation-bar` class to <body>
 |------------------------------------------------------------------
 |
 | Paired with resources/views/filament/impersonation-bar.blade.php.
 | Extracted from a previously-inline <script> block to satisfy
 | CodeCanyon's "no inline scripts unless dynamic" rule.
 |
 | The bar uses :has() in CSS to push Filament notifications below
 | it on modern browsers. This file is the JS fallback that tags
 | <body> with `has-impersonation-bar` for browsers that don't
 | support :has() yet.
 |
 | It also keeps `--imp-bar-h` in sync with the bar's real height.
 | The stylesheet offsets Filament's sticky topbar and fixed sidebar
 | by that variable so the bar never paints over them (which hid the
 | notification bell and its dropdown).  The default 52px matches
 | the bar's min-height, but the bar is `flex-wrap: wrap` — on a
 | narrow viewport it grows to two lines and a hardcoded offset would
 | leave the topbar overlapped again.
 |
 | Zero Blade interpolation — the body class name is fixed.
 */
(function () {
    'use strict';

    var bar = document.getElementById('impersonation-bar');

    if (! bar) {
        return;
    }

    document.body.classList.add('has-impersonation-bar');

    function syncHeight() {
        var height = Math.round(bar.getBoundingClientRect().height);

        if (height > 0) {
            document.documentElement.style.setProperty('--imp-bar-h', height + 'px');
        }
    }

    syncHeight();

    if (typeof ResizeObserver !== 'undefined') {
        new ResizeObserver(syncHeight).observe(bar);
    } else {
        window.addEventListener('resize', syncHeight);
    }
})();
