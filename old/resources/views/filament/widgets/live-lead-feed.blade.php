<x-filament-widgets::widget>
    <link rel="stylesheet" href="{{ asset('css/views/filament/widgets/live-lead-feed.css') }}">

    <div class="fi-section rounded-xl bg-white shadow-sm ring-1 ring-gray-950/5 dark:bg-gray-900 dark:ring-white/10 llf-section">
        <div class="llf-header">
            <span class="llf-ping">
                <span class="llf-ping-ring"></span>
                <span class="llf-ping-dot"></span>
            </span>
            <span class="llf-title">{{ __('filament/live_lead_feed.title') }}</span>
        </div>

        @php
            // i18n dictionaries injected as JSON so the Alpine handler
            // can resolve raw slug→localized label client-side when a
            // realtime broadcast unshifts a new row into the feed.
            // Mirror of the PHP-side resolution in LiveLeadFeed::mount()
            // (translator-first, ucfirst() last-resort fallback).
            $sourceDict = trans('lead_sources');
            $statusDict = trans('enums/lead_status');
            $sourceDictArr = is_array($sourceDict) ? $sourceDict : [];
            $statusDictArr = is_array($statusDict) ? $statusDict : [];
            $i18nJustNow = (string) __('filament/live_lead_feed.just_now');
            $i18nStatusUnknown = (string) __('filament/live_lead_feed.fallback_status_unknown');
            $i18nSourceManual = (string) __('filament/live_lead_feed.fallback_source_manual');
        @endphp
        <div
            x-data="{
                leads: @js($leads),
                tenantId: @js((string) auth()->user()?->tenant_id),
                sourceDict: @js($sourceDictArr),
                statusDict: @js($statusDictArr),
                i18n: {
                    justNow: @js($i18nJustNow),
                    statusUnknown: @js($i18nStatusUnknown),
                    sourceManual: @js($i18nSourceManual),
                },
                resolveSourceLabel(key) {
                    if (!key) return this.i18n.sourceManual;
                    if (this.sourceDict && this.sourceDict[key]) return this.sourceDict[key];
                    return key.charAt(0).toUpperCase() + key.slice(1).replace(/_/g, ' ');
                },
                resolveStatusLabel(key) {
                    if (!key) return this.i18n.statusUnknown;
                    if (this.statusDict && this.statusDict[key]) return this.statusDict[key];
                    return key.charAt(0).toUpperCase() + key.slice(1).replace(/_/g, ' ');
                },
                initEcho() {
                    if (typeof window.Echo === 'undefined') return;
                    window.Echo.private('tenant.' + this.tenantId + '.leads')
                        .listen('.lead.received', (e) => {
                            const newLead = {
                                id: e.id,
                                name: e.name,
                                email: e.email,
                                source: this.resolveSourceLabel(e.source),
                                status: this.resolveStatusLabel(e.status),
                                status_key: e.status,
                                created_at: this.i18n.justNow,
                                isNew: true,
                            };
                            this.leads.unshift(newLead);
                            if (this.leads.length > 20) this.leads.pop();
                        });
                },
                statusClass(statusKey) {
                    const map = {
                        'new': 'llf-badge-status-new',
                        'contacted': 'llf-badge-status-contacted',
                        'qualified': 'llf-badge-status-qualified',
                        'lost': 'llf-badge-status-lost',
                    };
                    return map[statusKey] || 'llf-badge-status-default';
                }
            }"
            x-init="initEcho()"
        >
            @if(count($leads) === 0)
                <div class="llf-empty">
                    {{ __('filament/live_lead_feed.empty') }}
                </div>
            @else
            <table class="llf-table">
                <thead>
                    <tr>
                        <th>{{ __('filament/live_lead_feed.col_name') }}</th>
                        <th>{{ __('filament/live_lead_feed.col_email') }}</th>
                        <th>{{ __('filament/live_lead_feed.col_source') }}</th>
                        <th>{{ __('filament/live_lead_feed.col_status') }}</th>
                        <th>{{ __('filament/live_lead_feed.col_received') }}</th>
                    </tr>
                </thead>
                <tbody>
                    <template x-for="(lead, index) in leads" :key="lead.id">
                        <tr>
                            <td>
                                <a :href="'/admin/leads/' + lead.id" class="llf-name" x-text="lead.name || '—'"></a>
                                <span x-show="lead.isNew" class="llf-new-tag">{{ __('filament/live_lead_feed.new_tag') }}</span>
                            </td>
                            <td><span class="llf-email" x-text="lead.email || '—'"></span></td>
                            <td><span class="llf-badge llf-badge-source" x-text="lead.source || i18n.sourceManual"></span></td>
                            <td><span class="llf-badge" :class="statusClass(lead.status_key)" x-text="lead.status || i18n.statusUnknown"></span></td>
                            <td class="llf-time" x-text="lead.created_at"></td>
                        </tr>
                    </template>
                </tbody>
            </table>
            @endif
        </div>
    </div>
</x-filament-widgets::widget>
