/*!
 |------------------------------------------------------------------
 | landing-light.js — Scroll-reveal + nav-shadow for the light variant
 |------------------------------------------------------------------
 |
 | Paired with resources/views/marketing/landing-light.blade.php.
 | Extracted from a previously-inline <script> block to satisfy
 | CodeCanyon's "no inline scripts unless dynamic" rule.
 |
 | IntersectionObserver flips `.reveal` -> `.is-visible` as each
 | element enters the viewport (with a small stagger for visual
 | rhythm). The nav also gets a tighter shadow once the user
 | scrolls past the hero. The nav-shadow handler here is redundant
 | with the layout-level one in public/js/views/marketing/layout.js,
 | but is preserved verbatim per extraction rules.
 |
 | Zero Blade interpolation — selectors and thresholds are static.
 */
(function () {
    'use strict';

    // Scroll reveal
    if (!('IntersectionObserver' in window)) {
        document.querySelectorAll('.reveal').forEach(el => el.classList.add('is-visible'));
    } else {
        const io = new IntersectionObserver(entries => {
            entries.forEach(e => {
                if (e.isIntersecting) {
                    e.target.classList.add('is-visible');
                    io.unobserve(e.target);
                }
            });
        }, { threshold: 0.12, rootMargin: '0px 0px -60px 0px' });
        document.querySelectorAll('.reveal').forEach((el, i) => {
            // Stagger successive reveals slightly
            if (i % 3 === 1) el.setAttribute('data-delay', '1');
            if (i % 3 === 2) el.setAttribute('data-delay', '2');
            io.observe(el);
        });
    }

    // Nav elevation-on-scroll
    const nav = document.querySelector('.nav');
    if (nav) {
        const onScroll = () => {
            if (window.scrollY > 40) nav.classList.add('scrolled');
            else nav.classList.remove('scrolled');
        };
        window.addEventListener('scroll', onScroll, { passive: true });
        onScroll();
    }
})();
