/*!
 |------------------------------------------------------------------
 | cmd-k.js — Cmd+K command palette factory for the admin panel
 |------------------------------------------------------------------
 |
 | Paired with resources/views/filament/cmd-k.blade.php. Extracted
 | from a previously-inline <script> block to satisfy CodeCanyon's
 | "no inline scripts unless dynamic" rule.
 |
 | Translated UI strings + the command catalogue are emitted by the
 | view as a JSON island (<script type="application/json"
 | id="lh-cmdk-i18n">) and parsed once at factory-init time. This
 | keeps the JS free of hardcoded English while still allowing the
 | file to be served as a fully-static asset.
 |
 | Public surface:
 |   cmdKPalette() — Alpine factory referenced by x-data on the
 |   wrapper element. MUST remain a window-global so Alpine can
 |   resolve it during x-init, which is why this file does NOT
 |   wrap the factory in an IIFE.
 */
function cmdKPalette() {
    var i18n = { placeholder: '', no_matches: '', kbd_navigate: '', kbd_open: '', kbd_toggle: '', sections: {}, commands: [] };
    try {
        var el = document.getElementById('lh-cmdk-i18n');
        if (el) {
            i18n = JSON.parse(el.textContent || '{}');
        }
    } catch (_) { /* fall through with empty defaults */ }

    return {
        isOpen: false,
        query: '',
        cursor: 0,
        i18n: i18n,
        commands: i18n.commands || [],
        init() {
            // No-op for now — could fetch dynamic items via AJAX in a future polish.
        },
        sectionLabel(sectionKey) {
            return (this.i18n.sections && this.i18n.sections[sectionKey]) || sectionKey;
        },
        get noMatchesText() {
            var template = this.i18n.no_matches || '';
            return template.replace(':query', this.query);
        },
        open() {
            this.isOpen = true;
            this.query = '';
            this.cursor = 0;
            this.$nextTick(() => this.$refs.search?.focus());
        },
        close() {
            this.isOpen = false;
        },
        get filtered() {
            const q = this.query.trim().toLowerCase();
            if (q === '') return this.commands;
            const sectionsMap = this.i18n.sections || {};
            return this.commands.filter(c => {
                const sectionLabel = (sectionsMap[c.section] || c.section || '').toLowerCase();
                return c.title.toLowerCase().includes(q) ||
                    sectionLabel.includes(q) ||
                    (c.keywords ?? '').toLowerCase().includes(q);
            });
        },
        moveCursor(delta) {
            const max = this.filtered.length - 1;
            if (max < 0) { this.cursor = 0; return; }
            this.cursor = Math.max(0, Math.min(max, this.cursor + delta));
        },
        execute() {
            const item = this.filtered[this.cursor];
            if (item?.url) {
                window.location.href = item.url;
            }
        },
    };
}
