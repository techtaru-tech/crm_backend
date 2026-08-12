/*!
 |------------------------------------------------------------------
 | clipboard.js — declarative copy-to-clipboard handler
 |------------------------------------------------------------------
 |
 | Replaces inline `onclick="navigator.clipboard.writeText(...)"`
 | event handlers (which trigger CodeCanyon's "no inline scripts"
 | review flag) with a single, top-level event delegation on the
 | document.
 |
 | Usage: add `data-copy-from="<value>"` to any clickable element.
 | On click, the value is copied to the clipboard.  The element's
 | text content briefly changes to `data-copied-label` (default:
 | "Copied!") for ~2 seconds before reverting.
 |
 | Loaded via <script src> from the Filament admin panel's render
 | hook (so it's available across all admin pages without each view
 | having to load it).
 */
(function () {
    'use strict';

    document.addEventListener('click', function (event) {
        var target = event.target.closest('[data-copy-from]');
        if (!target) {
            return;
        }

        var text = target.getAttribute('data-copy-from');
        if (text === null) {
            return;
        }

        var originalLabel = target.textContent;
        var copiedLabel = target.getAttribute('data-copied-label') || 'Copied!';

        var afterCopy = function () {
            target.textContent = copiedLabel;
            setTimeout(function () {
                target.textContent = originalLabel;
            }, 2000);
        };

        if (navigator.clipboard && navigator.clipboard.writeText) {
            navigator.clipboard.writeText(text).then(afterCopy).catch(function () {
                // Silent failure — clipboard API may be unavailable on
                // non-HTTPS or restricted contexts.  Leave the button
                // label untouched so the user can retry.
            });
            return;
        }

        // Fallback for browsers without async Clipboard API support:
        // use the legacy execCommand('copy') path.
        try {
            var hidden = document.createElement('textarea');
            hidden.value = text;
            hidden.style.position = 'absolute';
            hidden.style.left = '-9999px';
            document.body.appendChild(hidden);
            hidden.select();
            document.execCommand('copy');
            document.body.removeChild(hidden);
            afterCopy();
        } catch (e) {
            // Non-fatal — clipboard simply doesn't work in this context.
        }
    }, true);
})();
