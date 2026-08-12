/*!
 |------------------------------------------------------------------
 | confirm-form.js — declarative form-confirm handler
 |------------------------------------------------------------------
 |
 | Replaces inline `onsubmit="return confirm(...)"` attribute handlers
 | (which trigger CodeCanyon's "no inline scripts" review flag) with
 | a single, top-level event delegation on the document.
 |
 | Usage: add `data-confirm="Are you sure?"` to any <form>.  On submit,
 | the user gets a native confirm() dialog; cancelling aborts the form
 | submission.  Server-side flow is unchanged.
 |
 | Loaded via <script src="{{ asset('js/views/shared/confirm-form.js') }}">
 | by the public booking views, the calendar-connections settings
 | page, and any other view that needs a confirm-on-submit prompt
 | without an inline handler.
 */
(function () {
    'use strict';

    document.addEventListener('submit', function (event) {
        var form = event.target;
        if (!(form instanceof HTMLFormElement)) {
            return;
        }
        var message = form.getAttribute('data-confirm');
        if (!message) {
            return;
        }
        // Trim whitespace and confirm.  Cancel aborts the submission.
        if (!window.confirm(message.trim())) {
            event.preventDefault();
            event.stopPropagation();
        }
    }, true);
})();
