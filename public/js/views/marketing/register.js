/*!
 |------------------------------------------------------------------
 | register.js — Live slug preview for workspace registration
 |------------------------------------------------------------------
 |
 | Paired with resources/views/marketing/register.blade.php.
 | Extracted from a previously-inline <script> block to satisfy
 | CodeCanyon's "no inline scripts unless dynamic" rule.
 |
 | The Blade view ships dynamic data via a JSON island:
 |   <script type="application/json" id="lh-register-cfg">
 |     {"base":"https://example.com","reserved":["admin",…],
 |      "warnTemplate":"\":slug\" is reserved — pick a different name.",
 |      "placeholderSlug":"your-workspace"}
 |   </script>
 |
 | Static-DOM ids consumed:
 |   #workspace_name   <input> the user types into
 |   #ws-url-preview   span shown under the input
 |   #ws-url-warn      span that surfaces reserved-slug warnings
 |
 | Mirrors Str::slug() semantics on the server: lowercase, alphanumerics
 | + dashes, collapse whitespace, strip diacritics.
 */
(function () {
    'use strict';

    var cfgEl = document.getElementById('lh-register-cfg');
    if (!cfgEl) return;

    var cfg;
    try {
        cfg = JSON.parse(cfgEl.textContent || '{}');
    } catch (_) {
        return;
    }

    var input   = document.getElementById('workspace_name');
    var preview = document.getElementById('ws-url-preview');
    var warn    = document.getElementById('ws-url-warn');
    if (!input || !preview || !warn) return;

    var base            = (cfg.base || '').replace(/\/$/, '');
    var reserved        = Array.isArray(cfg.reserved) ? cfg.reserved : [];
    var warnTemplate    = String(cfg.warnTemplate || '":slug" is reserved.');
    var placeholderSlug = String(cfg.placeholderSlug || 'your-workspace');

    function slugify(s) {
        return (s || '')
            .toString()
            .toLowerCase()
            .trim()
            .normalize('NFD').replace(/[̀-ͯ]/g, '')
            .replace(/[^a-z0-9]+/g, '-')
            .replace(/^-+|-+$/g, '');
    }

    function render() {
        var slug = slugify(input.value) || placeholderSlug;
        preview.textContent = base + '/' + slug;
        if (reserved.indexOf(slug) !== -1) {
            warn.style.display = 'block';
            warn.textContent = warnTemplate.replace(':slug', slug);
        } else {
            warn.style.display = 'none';
            warn.textContent = '';
        }
    }

    input.addEventListener('input', render);
    // Run once on page load to handle validation bounce-backs.
    render();
})();
