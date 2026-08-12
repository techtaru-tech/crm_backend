/*!
 |------------------------------------------------------------------
 | hide-on-error.js — declarative replacement for onerror="" handlers
 |------------------------------------------------------------------
 |
 | Attaches an `error` listener to every <img data-hide-on-error>
 | element so the image is hidden if the source URL fails to load.
 |
 | Use:  <img src="…" data-hide-on-error="1">
 |
 | Replaces the legacy inline pattern
 |   <img onerror="this.style.display='none'">
 | which is not allowed by CodeCanyon's "no inline scripts" rule.
 */
(function () {
    'use strict';

    function hideOnError(ev) {
        var el = ev.target;
        if (el && el.tagName === 'IMG') {
            el.style.display = 'none';
        }
    }

    function bind(el) {
        if (!el || el.dataset.hoeBound === '1') return;
        el.dataset.hoeBound = '1';
        el.addEventListener('error', hideOnError);
        // If the image already failed before this script ran, hide now.
        if (el.complete && el.naturalWidth === 0) {
            el.style.display = 'none';
        }
    }

    function bindAll(root) {
        var imgs = (root || document).querySelectorAll('img[data-hide-on-error]');
        for (var i = 0; i < imgs.length; i++) bind(imgs[i]);
    }

    if (document.readyState === 'loading') {
        document.addEventListener('DOMContentLoaded', function () { bindAll(); });
    } else {
        bindAll();
    }

    // Re-bind for Livewire / Alpine / tenant-injected DOM updates.
    document.addEventListener('livewire:navigated', function () { bindAll(); });
})();
