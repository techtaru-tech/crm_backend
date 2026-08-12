/*!
 |------------------------------------------------------------------
 | layout.js — Shared nav-scroll + mobile hamburger for marketing
 |------------------------------------------------------------------
 |
 | Paired with resources/views/marketing/layout.blade.php. Extracted
 | from a previously-inline <script> block to satisfy CodeCanyon's
 | "no inline scripts unless dynamic" rule.
 |
 | Two independent IIFEs:
 |   1. Nav elevation-on-scroll — adds `.scrolled` to `header.nav`
 |      once the user scrolls past 40px.
 |   2. Mobile hamburger — opens/closes the `.nav-mobile-panel`
 |      below the pill on narrow screens; closes on ESC, on link
 |      tap, on outside click, and on resize above the breakpoint.
 |
 | Zero Blade interpolation — all selectors and breakpoints are
 | static.
 */

/* Nav elevation on scroll — shared across every public page. */
(function () {
    'use strict';
    var nav = document.querySelector('header.nav');
    if (!nav) return;
    var onScroll = function () {
        if (window.scrollY > 40) nav.classList.add('scrolled');
        else nav.classList.remove('scrolled');
    };
    window.addEventListener('scroll', onScroll, { passive: true });
    onScroll();
})();

/* Mobile hamburger — toggles the nav-mobile-panel below the pill.
   Closes on ESC, on link click, and on any click outside the nav. */
(function () {
    'use strict';
    var toggle = document.querySelector('.nav-toggle');
    var panel  = document.getElementById('nav-mobile-panel');
    if (!toggle || !panel) return;

    var setOpen = function (open) {
        panel.classList.toggle('is-open', open);
        toggle.classList.toggle('is-open', open);
        toggle.setAttribute('aria-expanded', open ? 'true' : 'false');
        if (open) panel.removeAttribute('hidden');
        else      panel.setAttribute('hidden', '');
        /* Lock body scroll only while menu is open on narrow
           screens — lets the user scroll the menu freely without
           the page behind scrolling with them. */
        document.body.style.overflow = open ? 'hidden' : '';
    };

    toggle.addEventListener('click', function (e) {
        e.stopPropagation();
        setOpen(!panel.classList.contains('is-open'));
    });
    /* Close on any link tap inside the panel so SPA-style
       anchors (#pricing) don't leave the menu open. */
    panel.addEventListener('click', function (e) {
        if (e.target.closest('a')) setOpen(false);
    });
    /* Outside tap closes. */
    document.addEventListener('click', function (e) {
        if (!panel.classList.contains('is-open')) return;
        if (e.target.closest('.nav-mobile-panel') || e.target.closest('.nav-toggle')) return;
        setOpen(false);
    });
    /* ESC closes. */
    document.addEventListener('keydown', function (e) {
        if (e.key === 'Escape' && panel.classList.contains('is-open')) setOpen(false);
    });
    /* Resizing above the breakpoint closes the panel so the
       desktop nav doesn't paint the open mobile menu underneath. */
    window.addEventListener('resize', function () {
        if (window.innerWidth > 768 && panel.classList.contains('is-open')) setOpen(false);
    });
})();
