<x-filament-panels::page>
    {{--
        Static styles live in public/css/views/filament/pages/subscription-required.css.
        DYNAMIC values kept inline: plan grid column count, plan card
        border highlight, feature-check icon color, payment-button
        gateway-position colors.
    --}}
    <div class="sr-container">
        {{-- Status banner --}}
        <div class="sr-status-banner">
            <div class="sr-status-icon-wrap">
                <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 20 20" fill="#dc2626">
                    <path fill-rule="evenodd" d="M8.485 2.495c.673-1.167 2.357-1.167 3.03 0l6.28 10.875c.673 1.167-.168 2.625-1.516 2.625H3.72c-1.347 0-2.189-1.458-1.515-2.625L8.485 2.495zM10 5a.75.75 0 01.75.75v3.5a.75.75 0 01-1.5 0v-3.5A.75.75 0 0110 5zm0 9a1 1 0 100-2 1 1 0 000 2z" clip-rule="evenodd"/>
                </svg>
            </div>
            <div class="sr-status-body">
                <p class="sr-status-label">{{ __('filament/subscription_required.current_status_prefix') }} {{ $statusLabel }}</p>
                <p class="sr-status-text">{{ $this->getSubheading() }}</p>
            </div>
        </div>

        {{-- Plan cards — grid column count is dynamic --}}
        @if($plans && count($plans) > 0)
        <div style="display:grid;grid-template-columns:repeat({{ min(count($plans), 3) }},1fr);gap:20px;margin-bottom:32px;">
            @foreach($plans as $key => $plan)
            {{-- plan card border is dynamic (highlights recommended plan) --}}
            <div class="sr-plan-card" style="border:{{ ($plan['highlight'] ?? false) ? '2px solid #4f46e5' : '1px solid #e5e7eb' }};">
                @if($plan['highlight'] ?? false)
                <div class="sr-plan-popular-tag">{{ __('filament/subscription_required.most_popular_tag') }}</div>
                @endif

                <h3 class="sr-plan-name">{{ $plan['name'] }}</h3>
                <p class="sr-plan-description">{{ $plan['description'] }}</p>

                @php
                    // Translator-first interval label so non-English locales render
                    // "/month" "/year" etc. instead of the raw slug stored in
                    // config/plans.php. Falls through to the raw value for any
                    // tenant-custom interval ("quarter", etc.) we haven't shipped a key for.
                    $intervalSlug  = $plan['interval'] ?? 'month';
                    $intervalKey   = 'filament/subscription_required.interval_' . $intervalSlug;
                    $intervalTrans = __($intervalKey);
                    $intervalLabel = (is_string($intervalTrans) && $intervalTrans !== $intervalKey)
                        ? $intervalTrans
                        : $intervalSlug;
                @endphp
                <div class="sr-plan-price-row">
                    <span class="sr-plan-price">{{ \App\Support\Currency::format((float) ($plan['price'] ?? 0), $plan['currency'] ?? \App\Support\Currency::default()) }}</span>
                    <span class="sr-plan-interval">{{ __('filament/subscription_required.price_per_interval', ['interval' => $intervalLabel]) }}</span>
                </div>

                <ul class="sr-feature-list">
                    @php
                        $limits = $plan['limits'] ?? [];
                        $features = $plan['features'] ?? [];
                    @endphp
                    <li class="sr-feature-item">
                        <svg width="14" height="14" viewBox="0 0 20 20" fill="#10b981"><path fill-rule="evenodd" d="M16.704 4.153a.75.75 0 01.143 1.052l-8 10.5a.75.75 0 01-1.127.075l-4.5-4.5a.75.75 0 011.06-1.06l3.894 3.893 7.48-9.817a.75.75 0 011.05-.143z" clip-rule="evenodd"/></svg>
                        {{ $limits['max_seats'] === -1 ? __('filament/subscription_required.seats_unlimited') : __('filament/subscription_required.seats_count', ['count' => $limits['max_seats']]) }}
                    </li>
                    <li class="sr-feature-item">
                        <svg width="14" height="14" viewBox="0 0 20 20" fill="#10b981"><path fill-rule="evenodd" d="M16.704 4.153a.75.75 0 01.143 1.052l-8 10.5a.75.75 0 01-1.127.075l-4.5-4.5a.75.75 0 011.06-1.06l3.894 3.893 7.48-9.817a.75.75 0 011.05-.143z" clip-rule="evenodd"/></svg>
                        {{ $limits['max_leads'] === -1 ? __('filament/subscription_required.leads_unlimited') : __('filament/subscription_required.leads_count', ['count' => number_format($limits['max_leads'])]) }}
                    </li>
                    <li class="sr-feature-item">
                        <svg width="14" height="14" viewBox="0 0 20 20" fill="#10b981"><path fill-rule="evenodd" d="M16.704 4.153a.75.75 0 01.143 1.052l-8 10.5a.75.75 0 01-1.127.075l-4.5-4.5a.75.75 0 011.06-1.06l3.894 3.893 7.48-9.817a.75.75 0 011.05-.143z" clip-rule="evenodd"/></svg>
                        {{ $limits['max_forms'] === -1 ? __('filament/subscription_required.forms_unlimited') : __('filament/subscription_required.forms_count', ['count' => $limits['max_forms']]) }}
                    </li>
                    {{-- icon fill is dynamic (green when enabled, grey when not) --}}
                    <li class="sr-feature-item">
                        <svg width="14" height="14" viewBox="0 0 20 20" fill="{{ ($features['integrations'] ?? false) ? '#10b981' : '#d1d5db' }}"><path fill-rule="evenodd" d="M16.704 4.153a.75.75 0 01.143 1.052l-8 10.5a.75.75 0 01-1.127.075l-4.5-4.5a.75.75 0 011.06-1.06l3.894 3.893 7.48-9.817a.75.75 0 011.05-.143z" clip-rule="evenodd"/></svg>
                        {{ __('filament/subscription_required.feature_integrations') }}
                    </li>
                    <li class="sr-feature-item">
                        <svg width="14" height="14" viewBox="0 0 20 20" fill="{{ ($features['api_access'] ?? false) ? '#10b981' : '#d1d5db' }}"><path fill-rule="evenodd" d="M16.704 4.153a.75.75 0 01.143 1.052l-8 10.5a.75.75 0 01-1.127.075l-4.5-4.5a.75.75 0 011.06-1.06l3.894 3.893 7.48-9.817a.75.75 0 011.05-.143z" clip-rule="evenodd"/></svg>
                        {{ __('filament/subscription_required.feature_api_access') }}
                    </li>
                    <li class="sr-feature-item">
                        <svg width="14" height="14" viewBox="0 0 20 20" fill="{{ ($features['custom_domain'] ?? false) ? '#10b981' : '#d1d5db' }}"><path fill-rule="evenodd" d="M16.704 4.153a.75.75 0 01.143 1.052l-8 10.5a.75.75 0 01-1.127.075l-4.5-4.5a.75.75 0 011.06-1.06l3.894 3.893 7.48-9.817a.75.75 0 011.05-.143z" clip-rule="evenodd"/></svg>
                        {{ __('filament/subscription_required.feature_custom_domain') }}
                    </li>
                </ul>

                @php
                    $gateways = app(\App\Billing\PaymentGatewayManager::class)->enabled();
                @endphp
                @if(count($gateways))
                    <div class="sr-gateway-list">
                        @foreach($gateways as $gid => $gw)
                            {{-- button colors vary by gateway loop position + plan highlight (dynamic) --}}
                            <a href="{{ route('billing.checkout', ['gateway' => $gid, 'plan' => $key]) }}"
                               class="sr-gateway-btn"
                               style="background:{{ $loop->first && ($plan['highlight'] ?? false) ? '#4f46e5' : ($loop->first ? '#111827' : '#fff') }};color:{{ $loop->first ? '#fff' : '#111827' }};border:1px solid {{ $loop->first ? 'transparent' : '#e5e7eb' }};">
                                {{ __('filament/subscription_required.pay_with_label', ['gateway' => $gw->label()]) }}
                            </a>
                        @endforeach
                    </div>
                @else
                    {{-- Sales-CTA background varies with plan highlight (dynamic).
                         Mailto subject routes through __() so non-English locales
                         get a localised "Upgrade to <plan>" subject line. --}}
                    <a href="mailto:{{ config('leadhub.billing.contact_email', 'sales@theleadhub.app') }}?subject={{ rawurlencode(__('filament/subscription_required.sales_mailto_subject', ['plan' => $plan['name']])) }}"
                       class="sr-sales-cta"
                       style="background:{{ ($plan['highlight'] ?? false) ? '#4f46e5' : '#111827' }};">
                        {{ __('filament/subscription_required.contact_sales_btn', ['plan' => $plan['name']]) }}
                    </a>
                @endif
            </div>
            @endforeach
        </div>
        @endif

        {{-- Logout option --}}
        <div class="sr-logout-row">
            {{ __('filament/subscription_required.switch_accounts_label') }}
            <form method="POST" action="{{ filament()->getLogoutUrl() }}" class="sr-logout-form">
                @csrf
                <button type="submit" class="sr-logout-btn">{{ __('filament/subscription_required.sign_out') }}</button>
            </form>
        </div>
    </div>

    <link rel="stylesheet" href="{{ asset('css/views/filament/pages/subscription-required.css') }}">
</x-filament-panels::page>
