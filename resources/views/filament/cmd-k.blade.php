<div x-data="cmdKPalette()"
     x-init="init()"
     @keydown.window.prevent.cmd.k="open()"
     @keydown.window.prevent.ctrl.k="open()"
     @keydown.window.escape="close()">
    {{--
        Static styles for the Cmd-K command palette live in
        public/css/filament/cmd-k.css (alongside the .cmdk-active +
        [x-cloak] helpers already there).  Translated UI strings and
        the command catalogue are emitted via a JSON island below and
        consumed by public/js/views/filament/cmd-k.js. Keeping the
        translations server-side lets the palette be fully localised
        without inline script blocks.

        The i18n payload is pre-built in a PHP block and emitted via a
        single variable. Inlining a large nested array literal directly
        inside the json directive triggers a paren-balancer bug in the
        Blade compiler when the literal contains many translation calls
        and chained nullsafe operators, producing an "Unclosed '['"
        parse error in the compiled view.
    --}}
    @php
        $cmdkData = [
            'placeholder'  => __('filament/cmd_k.placeholder'),
            'no_matches'   => __('filament/cmd_k.no_matches_for'),
            'kbd_navigate' => __('filament/cmd_k.kbd_navigate'),
            'kbd_open'     => __('filament/cmd_k.kbd_open'),
            'kbd_toggle'   => __('filament/cmd_k.kbd_toggle'),
            'sections'     => [
                'pages'    => __('filament/cmd_k.section_pages'),
                'account'  => __('filament/cmd_k.section_account'),
                'settings' => __('filament/cmd_k.section_settings'),
                'actions'  => __('filament/cmd_k.section_actions'),
            ],
            'commands' => [
                ['title' => __('filament/cmd_k.cmd_leads'),             'url' => '/admin/leads',             'icon' => '👥', 'section' => 'pages',    'keywords' => __('filament/cmd_k.kw_leads')],
                ['title' => __('filament/cmd_k.cmd_kanban_board'),      'url' => '/admin/kanban-board',      'icon' => '🗂️', 'section' => 'pages',    'keywords' => __('filament/cmd_k.kw_kanban')],
                ['title' => __('filament/cmd_k.cmd_pipelines'),         'url' => '/admin/pipelines',         'icon' => '📊', 'section' => 'pages',    'keywords' => __('filament/cmd_k.kw_pipelines')],
                ['title' => __('filament/cmd_k.cmd_forms'),             'url' => '/admin/forms',             'icon' => '📝', 'section' => 'pages',    'keywords' => __('filament/cmd_k.kw_forms')],
                ['title' => __('filament/cmd_k.cmd_landing_pages'),     'url' => '/admin/landing-pages',     'icon' => '🌐', 'section' => 'pages',    'keywords' => __('filament/cmd_k.kw_landing_pages')],
                ['title' => __('filament/cmd_k.cmd_automations'),       'url' => '/admin/automations',       'icon' => '⚡', 'section' => 'pages',    'keywords' => __('filament/cmd_k.kw_automations')],
                ['title' => __('filament/cmd_k.cmd_email_templates'),   'url' => '/admin/email-templates',   'icon' => '✉️', 'section' => 'pages',    'keywords' => __('filament/cmd_k.kw_email_templates')],
                ['title' => __('filament/cmd_k.cmd_companies'),         'url' => '/admin/companies',         'icon' => '🏢', 'section' => 'pages',    'keywords' => __('filament/cmd_k.kw_companies')],
                ['title' => __('filament/cmd_k.cmd_quotes'),            'url' => '/admin/quotes',            'icon' => '📃', 'section' => 'pages',    'keywords' => __('filament/cmd_k.kw_quotes')],
                ['title' => __('filament/cmd_k.cmd_invoices'),          'url' => '/admin/invoices',          'icon' => '💵', 'section' => 'pages',    'keywords' => __('filament/cmd_k.kw_invoices')],
                ['title' => __('filament/cmd_k.cmd_calendar'),          'url' => '/admin/calendar',          'icon' => '📅', 'section' => 'pages',    'keywords' => __('filament/cmd_k.kw_calendar')],
                ['title' => __('filament/cmd_k.cmd_conversations'),     'url' => '/admin/conversations',     'icon' => '💬', 'section' => 'pages',    'keywords' => __('filament/cmd_k.kw_conversations')],
                ['title' => __('filament/cmd_k.cmd_marketplace'),       'url' => '/admin/marketplace',       'icon' => '🛍️', 'section' => 'pages',    'keywords' => __('filament/cmd_k.kw_marketplace')],
                ['title' => __('filament/cmd_k.cmd_industry_packs'),    'url' => '/admin/industry-packs',    'icon' => '📦', 'section' => 'pages',    'keywords' => __('filament/cmd_k.kw_industry_packs')],
                ['title' => __('filament/cmd_k.cmd_reports'),           'url' => '/admin/reports',           'icon' => '📈', 'section' => 'pages',    'keywords' => __('filament/cmd_k.kw_reports')],
                ['title' => __('filament/cmd_k.cmd_help_faq'),          'url' => '/admin/help',              'icon' => '❓', 'section' => 'pages',    'keywords' => __('filament/cmd_k.kw_help_faq')],
                ['title' => __('filament/cmd_k.cmd_billing'),           'url' => '/admin/billing',           'icon' => '💳', 'section' => 'account',  'keywords' => __('filament/cmd_k.kw_billing')],
                ['title' => __('filament/cmd_k.cmd_privacy_data'),      'url' => '/admin/privacy',           'icon' => '🛡️', 'section' => 'account',  'keywords' => __('filament/cmd_k.kw_privacy_data')],
                ['title' => __('filament/cmd_k.cmd_two_factor_auth'),   'url' => '/admin/two-factor-setup',  'icon' => '🔒', 'section' => 'account',  'keywords' => __('filament/cmd_k.kw_two_factor_auth')],
                ['title' => __('filament/cmd_k.cmd_profile'),           'url' => '/admin/profile',           'icon' => '👤', 'section' => 'account',  'keywords' => __('filament/cmd_k.kw_profile')],
                ['title' => __('filament/cmd_k.cmd_settings_branding'), 'url' => '/admin/settings/branding', 'icon' => '🎨', 'section' => 'settings', 'keywords' => __('filament/cmd_k.kw_settings_branding')],
                ['title' => __('filament/cmd_k.cmd_settings_email'),    'url' => '/admin/settings/email',    'icon' => '✉️', 'section' => 'settings', 'keywords' => __('filament/cmd_k.kw_settings_email')],
                ['title' => __('filament/cmd_k.cmd_settings_team'),     'url' => '/admin/settings/team',     'icon' => '👥', 'section' => 'settings', 'keywords' => __('filament/cmd_k.kw_settings_team')],
                ['title' => __('filament/cmd_k.cmd_new_lead'),          'url' => '/admin/leads/create',      'icon' => '➕', 'section' => 'actions',  'keywords' => __('filament/cmd_k.kw_new_lead')],
                ['title' => __('filament/cmd_k.cmd_new_form'),          'url' => '/admin/forms/create',      'icon' => '➕', 'section' => 'actions',  'keywords' => __('filament/cmd_k.kw_new_form')],
                ['title' => __('filament/cmd_k.cmd_new_automation'),    'url' => '/admin/automations/create','icon' => '➕', 'section' => 'actions',  'keywords' => __('filament/cmd_k.kw_new_automation')],
            ],
        ];
    @endphp

    {{-- JSON island — i18n strings + command catalogue.
         <script type="application/json"> is not executable JavaScript,
         so this satisfies the "no inline scripts unless dynamic" rule
         while still passing translated strings into the static JS. --}}
    <script type="application/json" id="lh-cmdk-i18n">@json($cmdkData)</script>

    {{-- Modal --}}
    <div x-show="isOpen"
         x-transition.opacity
         @click.self="close()"
         class="cmdk-backdrop"
         x-cloak>

        <div @click.stop class="cmdk-panel">

            {{-- Search input --}}
            <div class="cmdk-search-row">
                <span class="cmdk-search-icon">🔍</span>
                <input type="text"
                       x-ref="search"
                       x-model="query"
                       @keydown.arrow-down.prevent="moveCursor(1)"
                       @keydown.arrow-up.prevent="moveCursor(-1)"
                       @keydown.enter.prevent="execute()"
                       :placeholder="i18n.placeholder"
                       class="cmdk-search-input">
                <kbd class="cmdk-kbd">{{ __('filament/cmd_k.kbd_escape_key') }}</kbd>
            </div>

            {{-- Results list --}}
            <div class="cmdk-results">
                <template x-for="(item, i) in filtered" :key="item.url">
                    <a :href="item.url"
                       :class="i === cursor ? 'cmdk-item cmdk-active' : 'cmdk-item'"
                       @mouseover="cursor = i">
                        <span class="cmdk-item-icon" x-text="item.icon"></span>
                        <span class="cmdk-item-title" x-text="item.title"></span>
                        <span class="cmdk-item-section" x-text="sectionLabel(item.section)"></span>
                    </a>
                </template>

                <div x-show="filtered.length === 0" class="cmdk-empty" x-text="noMatchesText"></div>
            </div>

            {{-- Footer hint --}}
            <div class="cmdk-footer">
                <span><kbd class="cmdk-footer-kbd">↑↓</kbd> <span x-text="i18n.kbd_navigate"></span></span>
                <span><kbd class="cmdk-footer-kbd">↵</kbd> <span x-text="i18n.kbd_open"></span></span>
                <span><kbd class="cmdk-footer-kbd">⌘K</kbd> <span x-text="i18n.kbd_toggle"></span></span>
            </div>
        </div>
    </div>

    <link rel="stylesheet" href="{{ asset('css/filament/cmd-k.css') }}">
</div>

<script src="{{ asset('js/views/filament/cmd-k.js') }}" defer></script>
