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
 | Zero Blade interpolation — the body class name is fixed.
 */
(function () {
    'use strict';

    if (document.getElementById('impersonation-bar')) {
        document.body.classList.add('has-impersonation-bar');
    }
})();
