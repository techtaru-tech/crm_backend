<x-filament-panels::page>
    {{--
        Static styles live in public/css/views/filament/super-admin/pages/billing.css.
        Inline style="" attributes are reserved for genuinely DYNAMIC
        values: status-card colors driven by $statusStyles, churn-rate
        threshold color, and tenant-growth bar heights (computed per row).

        The ?v=2 suffix on the stylesheet link at the bottom of this
        view is a cache-buster — browsers that already cached the v1
        CSS (where the MRR hero card text rendered dark on the indigo
        gradient because Filament's panel paragraph rules were winning
        the cascade) re-fetch the file on next load.
    --}}
    @php
        $m = $this->getMetrics();
        $reportingCurrency = $m['currency'];
        $fmt = fn ($v) => \App\Support\Currency::format((float) $v, $reportingCurrency);
    @endphp

    {{-- Hero metrics --}}
    <div class="sa-billing-grid-4">
        <div class="sa-billing-kpi-hero">
            <p class="sa-billing-kpi-label">{{ __('filament/sa_billing.kpi_mrr_label') }}</p>
            <p class="sa-billing-kpi-value">{{ $fmt($m['mrr']) }}</p>
            <p class="sa-billing-kpi-sub">{{ $m['paying_tenants'] === 1 ? __('filament/sa_billing.kpi_mrr_sub_singular', ['count' => $m['paying_tenants']]) : __('filament/sa_billing.kpi_mrr_sub_plural', ['count' => $m['paying_tenants']]) }}</p>
        </div>

        <div class="sa-billing-card">
            <p class="sa-billing-kpi-label">{{ __('filament/sa_billing.kpi_arr_label') }}</p>
            <p class="sa-billing-kpi-value">{{ $fmt($m['arr']) }}</p>
            <p class="sa-billing-kpi-sub">{{ __('filament/sa_billing.kpi_arr_sub') }}</p>
        </div>

        <div class="sa-billing-card">
            <p class="sa-billing-kpi-label">{{ __('filament/sa_billing.kpi_arpu_label') }}</p>
            <p class="sa-billing-kpi-value">{{ \App\Support\Currency::format((float) $m['arpu'], $reportingCurrency) }}</p>
            <p class="sa-billing-kpi-sub">{{ __('filament/sa_billing.kpi_arpu_sub', ['currency' => $reportingCurrency]) }}</p>
        </div>

        <div class="sa-billing-card">
            <p class="sa-billing-kpi-label">{{ __('filament/sa_billing.kpi_churn_label') }}</p>
            {{-- churn-rate value color is dynamic: red when above threshold, green otherwise --}}
            <p class="sa-billing-kpi-value" style="color:{{ $m['churn_rate'] > 5 ? '#dc2626' : '#059669' }};">{{ $m['churn_rate'] }}%</p>
            <p class="sa-billing-kpi-sub">{{ __('filament/sa_billing.kpi_churn_sub') }}</p>
        </div>
    </div>

    {{-- Status breakdown --}}
    <div class="sa-billing-section">
        <p class="sa-billing-section-title">{{ __('filament/sa_billing.section_status_breakdown') }}</p>
        <div class="sa-billing-grid-5">
            @php
                $statusStyles = [
                    'active'        => ['#059669','#d1fae5', __('filament/sa_billing.status_active')],
                    'trial'         => ['#d97706','#fef3c7', __('filament/sa_billing.status_trial')],
                    'trial_expired' => ['#dc2626','#fee2e2', __('filament/sa_billing.status_trial_expired')],
                    'cancelled'     => ['#6b7280','#f3f4f6', __('filament/sa_billing.status_cancelled')],
                    'expired'       => ['#dc2626','#fee2e2', __('filament/sa_billing.status_expired')],
                ];
            @endphp
            @foreach($statusStyles as $key => [$fg,$bg,$label])
                {{-- Per-status bg + fg colors are dynamic (status-keyed) --}}
                <div class="sa-status-card" style="background:{{ $bg }};">
                    <p class="sa-status-card-label" style="color:{{ $fg }};">{{ $label }}</p>
                    <p class="sa-status-card-value" style="color:{{ $fg }};">{{ $m['status_breakdown'][$key] ?? 0 }}</p>
                </div>
            @endforeach
        </div>
    </div>

    {{-- Revenue by plan + tenant growth --}}
    <div class="sa-billing-grid-21">

        <div class="sa-billing-section-small">
            <p class="sa-billing-section-title">{{ __('filament/sa_billing.section_revenue_by_plan') }}</p>
            <table class="sa-billing-table">
                <thead>
                    <tr>
                        <th>{{ __('filament/sa_billing.col_plan') }}</th>
                        <th class="right">{{ __('filament/sa_billing.col_price') }}</th>
                        <th class="right">{{ __('filament/sa_billing.col_customers') }}</th>
                        <th class="right">{{ __('filament/sa_billing.col_mrr') }}</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($m['revenue_by_plan'] as $row)
                        <tr>
                            <td class="strong">
                                {{ $row['name'] }}
                                @if($row['is_free'])
                                    <span class="sa-free-badge">{{ __('filament/sa_billing.free_badge') }}</span>
                                @endif
                            </td>
                            <td class="right">{{ \App\Support\Currency::format((float) $row['price'], $row['currency']) }}</td>
                            <td class="right">{{ $row['customers'] }}</td>
                            <td class="numeric-strong">{{ \App\Support\Currency::format((float) $row['mrr'], $row['currency']) }}</td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        </div>

        <div class="sa-billing-section-small">
            <p class="sa-billing-section-title">{{ __('filament/sa_billing.section_tenant_growth') }}</p>
            @php
                $maxTotal = max(array_map(fn($r) => $r['total'], $m['tenant_growth'])) ?: 1;
            @endphp
            <div class="sa-tg-chart">
                @foreach($m['tenant_growth'] as $row)
                    @php $h = max(8, round(($row['total'] / $maxTotal) * 140)); @endphp
                    <div class="sa-tg-bar-col">
                        {{-- bar height is dynamic (computed per row from $row['total'] / $maxTotal) --}}
                        <div class="sa-tg-bar" title="{{ __('filament/sa_billing.tenant_growth_title', ['total' => $row['total'], 'new' => $row['new_tenants']]) }}" style="height:{{ $h }}px;"></div>
                        <div class="sa-tg-label">{{ $row['label'] }}</div>
                        <div class="sa-tg-value">{{ $row['total'] }}</div>
                    </div>
                @endforeach
            </div>
        </div>
    </div>

    {{-- Recent billing events --}}
    <div class="sa-billing-section-small">
        <p class="sa-billing-section-title">{{ __('filament/sa_billing.section_recent_activity') }}</p>

        @if($m['recent_events']->isEmpty())
            <p class="sa-billing-empty">{{ __('filament/sa_billing.empty_recent_events') }}</p>
        @else
            <table class="sa-billing-table">
                <thead>
                    <tr>
                        <th>{{ __('filament/sa_billing.col_tenant') }}</th>
                        <th>{{ __('filament/sa_billing.col_plan') }}</th>
                        <th>{{ __('filament/sa_billing.col_status') }}</th>
                        <th class="right">{{ __('filament/sa_billing.col_updated') }}</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($m['recent_events'] as $t)
                        @php
                            // Translator-first plan label so the SA activity row
                            // respects locale.  The lang/<locale>/plans.php file
                            // mirrors the seeder catalog (trial/starter/pro/
                            // professional/business/enterprise) — anything else
                            // (custom PlanResource plans) gracefully falls back
                            // to a humanised slug.
                            $planSlug      = (string) ($t->plan ?? '');
                            $planLabelKey  = 'plans.' . $planSlug . '.name';
                            $planLabelTry  = $planSlug !== '' ? __($planLabelKey) : '';
                            $planLabel     = $planSlug === ''
                                ? '—'
                                : (is_string($planLabelTry) && $planLabelTry !== $planLabelKey
                                    ? $planLabelTry
                                    : ucfirst(str_replace(['_', '-'], ' ', $planSlug)));
                        @endphp
                        <tr>
                            <td class="strong">{{ $t->name }}</td>
                            <td class="capitalize">{{ $planLabel }}</td>
                            <td class="capitalize">{{ $t->subscription_status?->label() ?? '—' }}</td>
                            <td class="muted">{{ $t->updated_at?->diffForHumans() }}</td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        @endif
    </div>

    <link rel="stylesheet" href="{{ asset('css/views/filament/super-admin/pages/billing.css') }}?v=2">
</x-filament-panels::page>
