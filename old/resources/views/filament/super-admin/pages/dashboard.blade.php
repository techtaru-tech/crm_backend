<x-filament-panels::page>
    {{-- Soft brand wash above the widgets — Option B palette
         (indigo → violet) used on hero areas across the marketing
         site.  Inline so we don't depend on Tailwind JIT compilation
         on shared-hosting builds. --}}
    <link rel="stylesheet" href="{{ asset('css/filament/sa-dashboard.css') }}">

    {{-- "Outbound email is not configured" warning removed entirely.
         The SA setup guide covers SMTP configuration step-by-step, so
         a persistent yellow banner on every page was redundant and
         visually noisy. Tenant admins never saw a useful version of
         this anyway since SMTP is a platform-level setting only the
         Super Admin can change. --}}

    {{-- Hero strip --}}
    <div class="lh-sa-hero">
        <div>
            <h2>{{ __('filament/sa_dashboard.hero_heading') }}</h2>
            <p>{{ __('filament/sa_dashboard.hero_subheading') }}</p>
        </div>
        <span class="lh-sa-refresh-badge">{{ __('filament/sa_dashboard.refresh_badge') }}</span>
    </div>

    {{-- Top: KPI overview + revenue trend + signup bar chart --}}
    <x-filament-widgets::widgets
        :widgets="$this->headerWidgetsList()"
        :columns="$this->headerWidgetsColumns()"
    />

    @php $stats = $this->getStats(); @endphp

    {{-- Secondary metrics strip --}}
    <div class="lh-sa-secondary">
        <div class="lh-sa-card">
            <p class="lh-sa-card-label">{{ __('filament/sa_dashboard.card_total_leads') }}</p>
            <p class="lh-sa-card-value">{{ number_format($stats['total_leads']) }}</p>
        </div>
        <div class="lh-sa-card">
            <p class="lh-sa-card-label">{{ __('filament/sa_dashboard.card_leads_this_month') }}</p>
            <p class="lh-sa-card-value lh-sa-card-value--success">{{ number_format($stats['leads_this_month']) }}</p>
        </div>
        <div class="lh-sa-card">
            <p class="lh-sa-card-label">{{ __('filament/sa_dashboard.card_new_tenants_30d') }}</p>
            <p class="lh-sa-card-value lh-sa-card-value--accent">{{ number_format($stats['new_tenants_30d']) }}</p>
        </div>
        <div class="lh-sa-card">
            <p class="lh-sa-card-label">{{ __('filament/sa_dashboard.card_avg_leads_per_tenant') }}</p>
            <p class="lh-sa-card-value lh-sa-card-value--amber">{{ number_format($stats['avg_leads_per_tenant']) }}</p>
        </div>
    </div>

    {{-- Bottom: status donut + lead-source mix --}}
    <x-filament-widgets::widgets
        :widgets="$this->footerWidgetsList()"
        :columns="$this->footerWidgetsColumns()"
    />

    {{-- Activity feeds --}}
    <div class="lh-sa-feeds">
        <div class="lh-sa-feed">
            <div class="lh-sa-feed-head">
                <h3>{{ __('filament/sa_dashboard.recent_workspaces') }}</h3>
                <a href="/super-admin/tenants" class="lh-sa-feed-link">{{ __('filament/sa_dashboard.view_all_tenants') }}</a>
            </div>
            @forelse ($this->getRecentTenants() as $tenant)
                @php
                    // H8: subscription_status is now a SubscriptionStatus enum cast.
                    // Match on enum cases so a future case rename only needs to land
                    // in the enum file; the unknown-default arm keeps rendering safe
                    // even if a row holds a value not yet in the enum.
                    $status = $tenant->subscription_status;
                    $pillClass = match (true) {
                        $status === \App\Enums\SubscriptionStatus::Active        => 'lh-sa-pill lh-sa-pill--active',
                        $status === \App\Enums\SubscriptionStatus::Trial         => 'lh-sa-pill lh-sa-pill--trial',
                        $status === \App\Enums\SubscriptionStatus::Cancelled     => 'lh-sa-pill lh-sa-pill--cancelled',
                        $status === \App\Enums\SubscriptionStatus::Expired,
                        $status === \App\Enums\SubscriptionStatus::TrialExpired  => 'lh-sa-pill lh-sa-pill--expired',
                        default                                                  => 'lh-sa-pill lh-sa-pill--cancelled',
                    };
                @endphp
                <div class="lh-sa-feed-row">
                    <div class="lh-sa-feed-row-text">
                        <p class="lh-sa-feed-name">{{ $tenant->name }}</p>
                        <p class="lh-sa-feed-meta">
                            {{ $tenant->owner?->email ?? '—' }} · {{ $tenant->created_at?->diffForHumans() }}
                        </p>
                    </div>
                    <div class="lh-sa-feed-row-meta">
                        <span class="{{ $pillClass }}">{{ $status?->label() ?? '—' }}</span>
                        <span class="lh-sa-feed-seats">{{ __('filament/sa_dashboard.seats_count', ['used' => $tenant->seat_count, 'max' => $tenant->max_seats]) }}</span>
                    </div>
                </div>
            @empty
                <div class="lh-sa-feed-empty">{{ __('filament/sa_dashboard.feed_empty_workspaces') }}</div>
            @endforelse
        </div>

        <div class="lh-sa-feed">
            <div class="lh-sa-feed-head">
                <h3>{{ __('filament/sa_dashboard.recent_payments') }}</h3>
                <a href="/super-admin/billing" class="lh-sa-feed-link">{{ __('filament/sa_dashboard.view_billing') }}</a>
            </div>
            @forelse ($this->getRecentReceipts() as $receipt)
                @php
                    // Translator-first plan label so the SA recent-payments feed
                    // respects locale.  Mirrors plans.<slug>.name (PlanService
                    // pattern) with a humanised-slug fallback for custom plans.
                    $recPlanSlug  = (string) ($receipt->plan_key ?? '');
                    $recPlanKey   = 'plans.' . $recPlanSlug . '.name';
                    $recPlanTry   = $recPlanSlug !== '' ? __($recPlanKey) : '';
                    $recPlanLabel = $recPlanSlug === ''
                        ? '—'
                        : (is_string($recPlanTry) && $recPlanTry !== $recPlanKey
                            ? $recPlanTry
                            : ucfirst(str_replace(['_', '-'], ' ', $recPlanSlug)));
                @endphp
                <div class="lh-sa-feed-row">
                    <div class="lh-sa-feed-row-text">
                        <p class="lh-sa-feed-name">
                            {{ $receipt->tenant?->name ?? __('filament/sa_dashboard.removed_workspace') }}
                            <span class="lh-sa-feed-plan">· {{ $recPlanLabel }}</span>
                        </p>
                        <p class="lh-sa-feed-meta">
                            {{ strtoupper($receipt->gateway) }} · {{ $receipt->receipt_number }} · {{ $receipt->issued_at?->diffForHumans() }}
                        </p>
                    </div>
                    <div class="lh-sa-feed-amount">
                        {{ \App\Support\Currency::format((float) $receipt->amount, $receipt->currency ?: 'USD') }}
                    </div>
                </div>
            @empty
                <div class="lh-sa-feed-empty">{{ __('filament/sa_dashboard.feed_empty_payments') }}</div>
            @endforelse
        </div>
    </div>
</x-filament-panels::page>
