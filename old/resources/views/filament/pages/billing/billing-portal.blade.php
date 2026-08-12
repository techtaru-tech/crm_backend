@php
    $stateColorMap = [
        'active'        => ['bg' => '#dcfce7', 'fg' => '#14532d', 'border' => '#bbf7d0'],
        'trial'         => ['bg' => '#fef3c7', 'fg' => '#78350f', 'border' => '#fde68a'],
        'trial_expired' => ['bg' => '#fee2e2', 'fg' => '#7f1d1d', 'border' => '#fecaca'],
        'expired'       => ['bg' => '#f3f4f6', 'fg' => '#374151', 'border' => '#e5e7eb'],
        'cancelled'     => ['bg' => '#f3f4f6', 'fg' => '#374151', 'border' => '#e5e7eb'],
        'suspended'     => ['bg' => '#fee2e2', 'fg' => '#7f1d1d', 'border' => '#fecaca'],
        'unknown'       => ['bg' => '#f3f4f6', 'fg' => '#374151', 'border' => '#e5e7eb'],
    ];
    $colors  = $stateColorMap[$state_key] ?? $stateColorMap['unknown'];
    $seatPct = $seat_max > 0 ? min(100, (int) round(($seat_used / $seat_max) * 100)) : 0;
@endphp

<x-filament-panels::page>
    {{--
        Static styles live in public/css/views/filament/pages/billing/billing-portal.css.
        Inline style="" attributes are reserved for genuinely DYNAMIC
        values (status banner colors derived from $state_key, seat-usage
        progress-bar width + threshold color, per-plan card border/bg).
        Buyers override by adding a higher-specificity rule in a custom
        stylesheet — the panel render hook loads tenant stylesheets
        after this file.
    --}}
    @if (! $tenant)
        <div class="bp-error-card">
            {{ __('filament/billing_portal.error_no_workspace') }}
        </div>
    @else
        {{-- Status Banner — bg + border are dynamic (state-keyed) --}}
        <div class="bp-status-banner" style="background:{{ $colors['bg'] }};border:1px solid {{ $colors['border'] }};">
            <div class="bp-status-row">
                <div>
                    <div class="bp-status-pill-wrap">
                        <span class="bp-status-pill" style="color:{{ $colors['fg'] }};border:1px solid {{ $colors['border'] }};">
                            {{ $state_label }}
                        </span>
                        @if ($plan)
                            @php
                                // Translator-first interval label so the price line
                                // and "/ month" suffix respect tenant locale instead
                                // of leaking the raw slug stored in config/plans.php.
                                // Falls through to the raw value for any tenant-custom
                                // interval not shipped as a lang key.
                                $bpIntervalSlug  = $plan['interval'] ?? 'month';
                                $bpIntervalKey   = 'filament/billing_portal.interval_' . $bpIntervalSlug;
                                $bpIntervalTrans = __($bpIntervalKey);
                                $bpIntervalLabel = (is_string($bpIntervalTrans) && $bpIntervalTrans !== $bpIntervalKey)
                                    ? $bpIntervalTrans
                                    : $bpIntervalSlug;
                            @endphp
                            <strong class="bp-plan-headline">{{ $plan['name'] ?? __('filament/billing_portal.section_current_plan') }}</strong>
                            @if (($plan['price'] ?? 0) > 0)
                                <span class="bp-plan-price-line">
                                    {{ number_format((float) $plan['price'], 2) }} {{ $plan['currency'] ?? 'USD' }}
                                    / {{ $bpIntervalLabel }}
                                </span>
                            @endif
                        @endif
                    </div>
                    @if ($next_event && $next_event_at)
                        <div class="bp-next-event-line">
                            {{ $next_event }}: <strong class="bp-next-event-strong">{{ $next_event_at->translatedFormat('M j, Y') }}</strong>
                            <span class="bp-next-event-rel">({{ $next_event_at->diffForHumans() }})</span>
                        </div>
                    @endif
                </div>
                <div class="bp-actions-wrap">
                    @if (in_array($state_key, ['trial', 'trial_expired', 'expired', 'cancelled']))
                        <a href="/admin/subscription-required" class="bp-cta-primary">
                            {{ __('filament/billing_portal.cta_choose_plan') }}
                        </a>
                    @endif
                </div>
            </div>
        </div>

        {{-- Two-column main layout --}}
        <div class="bp-grid lh-billing-grid">
            {{-- LEFT: Current plan + seats --}}
            <div class="bp-card">
                <h3 class="bp-section-title">{{ __('filament/billing_portal.section_current_plan') }}</h3>

                @if ($plan)
                    <div class="bp-plan-row">
                        <div>
                            <div class="bp-plan-name-large">{{ $plan['name'] ?? '—' }}</div>
                            @if (! empty($plan['description']))
                                <p class="bp-plan-desc">{{ $plan['description'] }}</p>
                            @endif
                        </div>
                        <div class="bp-price-block">
                            <div class="bp-price-large">
                                @if (($plan['price'] ?? 0) > 0)
                                    {{ number_format((float) $plan['price'], 2) }}
                                @else
                                    {{ __('filament/billing_portal.price_free') }}
                                @endif
                            </div>
                            @if (($plan['price'] ?? 0) > 0)
                                @php
                                    // Same translator-first interval lookup as the hero strip.
                                    // Reused inside the LEFT-card price block where $bpIntervalLabel
                                    // from the hero scope isn't accessible (different @if branch).
                                    $bpCardIntervalSlug  = $plan['interval'] ?? 'month';
                                    $bpCardIntervalKey   = 'filament/billing_portal.interval_' . $bpCardIntervalSlug;
                                    $bpCardIntervalTrans = __($bpCardIntervalKey);
                                    $bpCardIntervalLabel = (is_string($bpCardIntervalTrans) && $bpCardIntervalTrans !== $bpCardIntervalKey)
                                        ? $bpCardIntervalTrans
                                        : __('filament/billing_portal.interval_month_fallback');
                                @endphp
                                <div class="bp-price-period">{{ $plan['currency'] ?? 'USD' }} / {{ $bpCardIntervalLabel }}</div>
                            @endif
                        </div>
                    </div>

                    {{-- Seat usage — width % and threshold color are dynamic --}}
                    <div class="bp-seat-card">
                        <div class="bp-seat-header">
                            <span>{{ __('filament/billing_portal.seat_team_seats') }}</span>
                            <span><strong class="bp-seat-header-strong">{{ $seat_used }}</strong> {{ __('filament/billing_portal.of_connector') }} {{ $seat_max ?: '∞' }}</span>
                        </div>
                        <div class="bp-seat-bar-track">
                            <div class="bp-seat-bar-fill" style="width:{{ $seatPct }}%;background:{{ $seatPct >= 90 ? '#ef4444' : ($seatPct >= 70 ? '#f59e0b' : '#10b981') }};"></div>
                        </div>
                        @if ($seat_max > 0 && $seat_used >= $seat_max)
                            <p class="bp-seat-warning">{{ __('filament/billing_portal.seat_limit_reached') }}</p>
                        @endif
                    </div>

                    {{-- Plan features --}}
                    @if (! empty($plan['features']))
                        <h4 class="bp-features-title">{{ __('filament/billing_portal.features_whats_included') }}</h4>
                        <ul class="bp-features-grid">
                            @foreach ($plan['features'] as $featureKey => $enabled)
                                @if ($enabled)
                                    @php
                                        // Translator-first feature label so the "What's included"
                                        // bullet respects tenant locale. Tenant-custom features
                                        // fall back to a humanised key for legibility.
                                        $featKey   = 'filament/billing_portal.feature_' . $featureKey;
                                        $featTrans = __($featKey);
                                        $featLabel = is_string($featTrans) && $featTrans !== $featKey
                                            ? $featTrans
                                            : \Illuminate\Support\Str::title(str_replace('_', ' ', (string) $featureKey));
                                    @endphp
                                    <li class="bp-feature-item">
                                        <span class="bp-feature-check">✓</span>
                                        {{ $featLabel }}
                                    </li>
                                @endif
                            @endforeach
                        </ul>
                    @endif
                @else
                    <p class="bp-empty">{{ __('filament/billing_portal.no_plan_information') }}</p>
                @endif
            </div>

            {{-- RIGHT: Quick actions --}}
            <div class="bp-card">
                <h3 class="bp-section-title">{{ __('filament/billing_portal.section_manage_subscription') }}</h3>

                @if ($gateway_label)
                    <div class="bp-gateway-info">
                        {{ __('filament/billing_portal.gateway_paying_via_prefix') }} <strong class="bp-gateway-info-strong">{{ $gateway_label }}</strong>
                    </div>
                @endif

                <div class="bp-actions-col">
                    <a href="/admin/subscription-required" class="bp-action-link">
                        <span>{{ __('filament/billing_portal.action_change_plan') }}</span>
                        <span class="bp-action-arrow">→</span>
                    </a>
                    @if (! empty($has_stripe_portal))
                        <a href="{{ route('billing.portal') }}" class="bp-action-link">
                            <span>{{ __('filament/billing_portal.action_update_payment_method') }}</span>
                            <span class="bp-action-arrow">→</span>
                        </a>
                    @endif
                    @if (in_array($state_key, ['active', 'trial']))
                        <a href="/admin/billing/cancel" class="bp-action-link-danger">
                            <span>{{ __('filament/billing_portal.action_cancel_subscription') }}</span>
                            <span>→</span>
                        </a>
                    @endif
                </div>

                <p class="bp-support-hint">
                    {{ __('filament/billing_portal.support_hint') }}
                </p>
            </div>
        </div>

        {{-- Recent events --}}
        @if (! empty($recent_events))
            <div class="bp-events-card">
                <h3 class="bp-section-title">{{ __('filament/billing_portal.section_recent_activity') }}</h3>
                <div class="bp-actions-col">
                    @foreach ($recent_events as $event)
                        @php
                            $label = match ($event['action']) {
                                'subscription.activated'          => '✅ ' . __('filament/billing_portal.event_subscription_activated'),
                                'subscription.cancelled'          => '🛑 ' . __('filament/billing_portal.event_subscription_cancelled'),
                                'subscription.payment_failed'     => '⚠️ ' . __('filament/billing_portal.event_payment_failed'),
                                'subscription.plan_changed'       => '🔄 ' . __('filament/billing_portal.event_plan_changed'),
                                'subscription.notification_sent'  => '✉️ ' . __('filament/billing_portal.event_notification_sent'),
                                'tenant.suspended'                => '⏸ ' . __('filament/billing_portal.event_workspace_suspended'),
                                'tenant.reactivated'              => '▶ ' . __('filament/billing_portal.event_workspace_reactivated'),
                                'tenant.auto_suspended'           => '⏸ ' . __('filament/billing_portal.event_auto_suspended'),
                                default                           => $event['action'],
                            };
                        @endphp
                        <div class="bp-event-row">
                            <span class="bp-event-label">{{ $label }}</span>
                            <span class="bp-event-time">{{ $event['created_at']?->diffForHumans() }}</span>
                        </div>
                    @endforeach
                </div>
            </div>
        @endif

        {{-- Available plans grid --}}
        @if (! empty($plans) && count($plans) > 1)
            <div class="bp-plans-section">
                <div class="bp-plans-header">
                    <h3 class="bp-plans-section-title">{{ __('filament/billing_portal.section_available_plans') }}</h3>
                    {{-- Monthly / annual billing toggle.  JS-only — when
                         the user picks "annual" we swap which price block
                         is visible per card.  Cards with no annual_price
                         set just keep showing the monthly figure. --}}
                    <div id="lh-billing-toggle" role="tablist" class="bp-billing-toggle">
                        <button type="button" data-period="month" role="tab" aria-selected="true" class="bp-toggle-btn-active">
                            {{ __('filament/billing_portal.toggle_monthly') }}
                        </button>
                        <button type="button" data-period="year" role="tab" aria-selected="false" class="bp-toggle-btn-inactive">
                            {{ __('filament/billing_portal.toggle_annual') }} <span class="bp-save-badge">{{ __('filament/billing_portal.toggle_annual_save_badge') }}</span>
                        </button>
                    </div>
                </div>
                <div class="bp-plans-grid">
                    @foreach ($plans as $key => $availablePlan)
                        @php
                            $isCurrent = $plan && ($plan['key'] ?? null) === $key;
                            $highlight = ! empty($availablePlan['highlight']);
                            $preview = collect($upgrade_previews ?? [])
                                ->firstWhere('plan_key', $key);
                            $monthlyPrice = (float) ($availablePlan['price'] ?? 0);
                            $annualPrice  = $availablePlan['annual_price'] !== null
                                ? (float) $availablePlan['annual_price']
                                : ($monthlyPrice > 0 ? round($monthlyPrice * 12, 2) : 0.0);
                            $annualDiscountPct = (int) ($availablePlan['annual_discount_percent'] ?? 0);
                        @endphp
                        {{-- Border + background are dynamic (current vs highlight vs neutral) --}}
                        <div class="bp-plan-card" style="border:2px solid {{ $isCurrent ? '#4f46e5' : ($highlight ? '#a78bfa' : '#e5e7eb') }};background:{{ $isCurrent ? '#eef2ff' : '#ffffff' }};">
                            @if ($highlight && ! $isCurrent)
                                <span class="bp-plan-card-tag-recommended">{{ __('filament/billing_portal.plan_tag_recommended') }}</span>
                            @endif
                            @if ($isCurrent)
                                <span class="bp-plan-card-tag-current">{{ __('filament/billing_portal.plan_tag_current') }}</span>
                            @endif

                            <div class="bp-plan-card-name">{{ $availablePlan['name'] ?? $key }}</div>
                            <div data-billing-period="month" class="bp-plan-card-price">
                                @if ($monthlyPrice > 0)
                                    {{ number_format($monthlyPrice, 2) }}
                                    <span class="bp-plan-card-price-suffix">{{ $availablePlan['currency'] ?? 'USD' }}{{ __('filament/billing_portal.price_suffix_per_month') }}</span>
                                @else
                                    <span class="bp-plan-card-price-free">{{ __('filament/billing_portal.price_free') }}</span>
                                @endif
                            </div>
                            {{-- data-billing-period="year" hidden by default; billing-portal.js
                                 toggles .bp-period-hidden when the user activates the annual toggle. --}}
                            <div data-billing-period="year" class="bp-plan-card-price bp-period-hidden">
                                @if ($annualPrice > 0)
                                    {{ number_format($annualPrice, 2) }}
                                    <span class="bp-plan-card-price-suffix">{{ $availablePlan['currency'] ?? 'USD' }}{{ __('filament/billing_portal.price_suffix_per_year') }}</span>
                                    @if ($annualDiscountPct > 0)
                                        <div class="bp-plan-card-save">{{ __('filament/billing_portal.plan_save_vs_monthly', ['pct' => $annualDiscountPct]) }}</div>
                                    @elseif ($monthlyPrice > 0 && $annualPrice < $monthlyPrice * 12)
                                        @php $saving = (int) round((1 - ($annualPrice / ($monthlyPrice * 12))) * 100); @endphp
                                        @if ($saving > 0)
                                            <div class="bp-plan-card-save">{{ __('filament/billing_portal.plan_save_vs_monthly', ['pct' => $saving]) }}</div>
                                        @endif
                                    @endif
                                @else
                                    <span class="bp-plan-card-price-free">{{ __('filament/billing_portal.price_free') }}</span>
                                @endif
                            </div>
                            @if (! empty($availablePlan['description']))
                                <p class="bp-plan-card-description">{{ $availablePlan['description'] }}</p>
                            @endif

                            @if (! $isCurrent && $preview)
                                <div class="bp-preview-box">
                                    @if ($preview['direction'] === 'upgrade')
                                        <strong class="bp-preview-upgrade">{{ __('filament/billing_portal.preview_upgrade_strong') }}</strong><br>
                                        {{ __('filament/billing_portal.preview_charge_now_label') }} <strong>{{ number_format((float) $preview['charge'], 2) }} {{ $preview['currency'] }}</strong><br>
                                        {{ __('filament/billing_portal.preview_credit_applied_label') }} {{ number_format((float) $preview['credit'], 2) }} {{ $preview['currency'] }}
                                        <span class="bp-preview-meta">({{ $preview['prorated_days'] === 1 ? __('filament/billing_portal.preview_prorated_days_one', ['count' => $preview['prorated_days']]) : __('filament/billing_portal.preview_prorated_days_other', ['count' => $preview['prorated_days']]) }})</span>
                                    @elseif ($preview['direction'] === 'downgrade')
                                        <strong class="bp-preview-downgrade">{{ __('filament/billing_portal.preview_downgrade_strong') }}</strong><br>
                                        {{ __('filament/billing_portal.preview_account_credit_label') }} <strong>{{ number_format(abs((float) $preview['net']), 2) }} {{ $preview['currency'] }}</strong><br>
                                        <span class="bp-preview-meta">{{ __('filament/billing_portal.preview_applied_next_invoice') }}</span>
                                    @endif
                                </div>
                            @endif

                            @if (! $isCurrent)
                                <a href="/admin/subscription-required?plan={{ $key }}"
                                   data-billing-cta="month"
                                   class="bp-switch-btn">
                                    {{ __('filament/billing_portal.plan_action_switch') }}
                                </a>
                                {{-- data-billing-cta="year" hidden by default; billing-portal.js
                                     toggles .bp-period-hidden when annual toggle activates. --}}
                                <a href="/admin/subscription-required?plan={{ $key }}&interval=year"
                                   data-billing-cta="year"
                                   class="bp-switch-btn bp-period-hidden">
                                    {{ __('filament/billing_portal.plan_action_switch_annual') }}
                                </a>
                            @else
                                <div class="bp-active-label">{{ __('filament/billing_portal.plan_active_label') }}</div>
                            @endif
                        </div>
                    @endforeach
                </div>
            </div>
        @endif

        {{-- Billing details (VAT id, business name, postal address) --}}
        <div class="bp-billing-details">
            <h3 class="bp-billing-details-title">{{ __('filament/billing_portal.section_billing_details') }}</h3>
            <p class="bp-billing-details-hint">{{ __('filament/billing_portal.billing_details_hint') }}</p>
            <form wire:submit="saveBillingDetails" class="bp-billing-form">
                <label class="bp-form-label bp-form-label-full">
                    {{ __('filament/billing_portal.form_business_name_label') }}
                    <input type="text" maxlength="200" wire:model.lazy="billingDetails.business_name" class="bp-form-input">
                    @if(!empty($billingDetailsErrors['business_name']))
                        <span class="bp-form-error">{{ $billingDetailsErrors['business_name'] }}</span>
                    @endif
                </label>
                <label class="bp-form-label">
                    {{ __('filament/billing_portal.form_vat_number_label') }}
                    <input type="text" maxlength="30" wire:model.lazy="billingDetails.vat_number" class="bp-form-input bp-form-input-mono">
                    @if(!empty($billingDetailsErrors['vat_number']))
                        <span class="bp-form-error">{{ $billingDetailsErrors['vat_number'] }}</span>
                    @endif
                </label>
                <label class="bp-form-label">
                    {{ __('filament/billing_portal.form_country_label') }}
                    <input type="text" maxlength="2" wire:model.lazy="billingDetails.billing_country" placeholder="{{ __('filament/billing_portal.form_country_placeholder') }}" class="bp-form-input bp-form-input-mono bp-form-input-uppercase">
                    @if(!empty($billingDetailsErrors['billing_country']))
                        <span class="bp-form-error">{{ $billingDetailsErrors['billing_country'] }}</span>
                    @endif
                </label>
                <label class="bp-form-label bp-form-label-full">
                    {{ __('filament/billing_portal.form_billing_address_label') }}
                    <textarea rows="3" maxlength="2000" wire:model.lazy="billingDetails.billing_address" class="bp-form-input bp-form-input-textarea"></textarea>
                    @if(!empty($billingDetailsErrors['billing_address']))
                        <span class="bp-form-error">{{ $billingDetailsErrors['billing_address'] }}</span>
                    @endif
                </label>
                <label class="bp-form-label bp-form-label-full">
                    {{ __('filament/billing_portal.form_billing_email_label') }}
                    <input type="email" maxlength="200" wire:model.lazy="billingDetails.billing_email" placeholder="{{ __('filament/billing_portal.form_billing_email_placeholder') }}" class="bp-form-input">
                    @if(!empty($billingDetailsErrors['billing_email']))
                        <span class="bp-form-error">{{ $billingDetailsErrors['billing_email'] }}</span>
                    @endif
                </label>
                <div class="bp-form-submit-row">
                    <button type="submit" class="bp-form-submit">{{ __('filament/billing_portal.form_save_button') }}</button>
                </div>
            </form>
        </div>
    @endif

    <script src="{{ asset('js/views/filament/pages/billing/billing-portal.js') }}" defer></script>

    {{-- Per-view static styles.  The global filament/billing-portal.css
         hook (theme-level Filament overrides) remains for backwards
         compatibility — buyers who customised it keep their changes. --}}
    <link rel="stylesheet" href="{{ asset('css/views/filament/pages/billing/billing-portal.css') }}">
    <link rel="stylesheet" href="{{ asset('css/filament/billing-portal.css') }}">
</x-filament-panels::page>
