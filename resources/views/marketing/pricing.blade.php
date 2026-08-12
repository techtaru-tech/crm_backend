@extends('marketing.layout')

@section('title', __('marketing.pricing_page_title', ['app' => $appName]))
@section('description', __('marketing.pricing_page_description', ['days' => $trialDays]))

@push('head')
<link rel="stylesheet" href="{{ asset('css/marketing/pricing.css') }}">
@endpush

@section('content')
<section class="hdr">
    <div class="wrap">
        <h1>{{ __('marketing.pricing_h1') }}</h1>
        <p>{{ __('marketing.pricing_intro', ['days' => $trialDays]) }}</p>
    </div>
</section>

<section class="plans-section">
    <div class="wrap">
        <div class="plans">
            @foreach($plans as $key => $plan)
                @php
                    $limits   = $plan['limits'] ?? [];
                    $features = $plan['features'] ?? [];
                    $planCurrency = strtoupper($plan['currency'] ?? \App\Support\Currency::default());
                    $planPrice    = (float) ($plan['price'] ?? 0);
                @endphp
                <div class="plan {{ ($plan['highlight'] ?? false) ? 'highlight' : '' }}">
                    @if($plan['highlight'] ?? false)
                        <div class="ribbon">{{ __('marketing.pricing_most_popular') }}</div>
                    @endif

                    <h3>{{ $plan['name'] }}</h3>
                    <p class="desc">{{ $plan['description'] ?? '' }}</p>

                    @php
                        // Translator-first interval label so "/month" and
                        // "billed monthly" render in tenant locale instead of
                        // leaking the raw slug. Two lookups: short form for the
                        // price suffix, "ly" form for the billing-cadence line.
                        $prIntervalSlug = $plan['interval'] ?? 'month';
                        $prKeyShort     = 'marketing.interval_' . $prIntervalSlug;
                        $prTransShort   = __($prKeyShort);
                        $prLabelShort   = (is_string($prTransShort) && $prTransShort !== $prKeyShort)
                            ? $prTransShort
                            : __('marketing.pricing_per');

                        $prKeyLong      = 'marketing.interval_' . $prIntervalSlug . 'ly';
                        $prTransLong    = __($prKeyLong);
                        $prLabelLong    = (is_string($prTransLong) && $prTransLong !== $prKeyLong)
                            ? $prTransLong
                            : __('marketing.interval_monthly');
                    @endphp
                    <div class="price">{{ \App\Support\Currency::format($planPrice, $planCurrency) }}<span> /{{ $prLabelShort }}</span></div>
                    <div class="interval">{{ __('marketing.pricing_interval_billed', ['interval' => $prLabelLong]) }}</div>

                    <ul>
                        <li>
                            <svg width="14" height="14" viewBox="0 0 20 20" fill="#10b981"><path fill-rule="evenodd" d="M16.704 4.153a.75.75 0 01.143 1.052l-8 10.5a.75.75 0 01-1.127.075l-4.5-4.5a.75.75 0 011.06-1.06l3.894 3.893 7.48-9.817a.75.75 0 011.05-.143z" clip-rule="evenodd"/></svg>
                            {{ ($limits['max_seats'] ?? 0) === -1 ? __('marketing.pricing_unlimited_seats') : __('marketing.pricing_seats_count', ['count' => $limits['max_seats'] ?? 0]) }}
                        </li>
                        <li>
                            <svg width="14" height="14" viewBox="0 0 20 20" fill="#10b981"><path fill-rule="evenodd" d="M16.704 4.153a.75.75 0 01.143 1.052l-8 10.5a.75.75 0 01-1.127.075l-4.5-4.5a.75.75 0 011.06-1.06l3.894 3.893 7.48-9.817a.75.75 0 011.05-.143z" clip-rule="evenodd"/></svg>
                            {{ ($limits['max_leads'] ?? 0) === -1 ? __('marketing.pricing_unlimited_leads') : __('marketing.pricing_leads_count', ['count' => number_format($limits['max_leads'] ?? 0)]) }}
                        </li>
                        <li>
                            <svg width="14" height="14" viewBox="0 0 20 20" fill="#10b981"><path fill-rule="evenodd" d="M16.704 4.153a.75.75 0 01.143 1.052l-8 10.5a.75.75 0 01-1.127.075l-4.5-4.5a.75.75 0 011.06-1.06l3.894 3.893 7.48-9.817a.75.75 0 011.05-.143z" clip-rule="evenodd"/></svg>
                            {{ ($limits['max_forms'] ?? 0) === -1 ? __('marketing.pricing_unlimited_forms') : __('marketing.pricing_forms_count', ['count' => $limits['max_forms'] ?? 0]) }}
                        </li>
                        <li>
                            <svg width="14" height="14" viewBox="0 0 20 20" fill="#10b981"><path fill-rule="evenodd" d="M16.704 4.153a.75.75 0 01.143 1.052l-8 10.5a.75.75 0 01-1.127.075l-4.5-4.5a.75.75 0 011.06-1.06l3.894 3.893 7.48-9.817a.75.75 0 011.05-.143z" clip-rule="evenodd"/></svg>
                            {{ ($limits['max_pipelines'] ?? 0) === -1 ? __('marketing.pricing_unlimited_pipelines') : __('marketing.pricing_pipelines_count', ['count' => $limits['max_pipelines'] ?? 0]) }}
                        </li>
                        <li class="{{ ($features['integrations'] ?? false) ? '' : 'dim' }}">
                            <svg width="14" height="14" viewBox="0 0 20 20" fill="{{ ($features['integrations'] ?? false) ? '#10b981' : '#d1d5db' }}"><path fill-rule="evenodd" d="M16.704 4.153a.75.75 0 01.143 1.052l-8 10.5a.75.75 0 01-1.127.075l-4.5-4.5a.75.75 0 011.06-1.06l3.894 3.893 7.48-9.817a.75.75 0 011.05-.143z" clip-rule="evenodd"/></svg>
                            {{ __('marketing.pricing_feat_integrations') }}
                        </li>
                        <li class="{{ ($features['api_access'] ?? false) ? '' : 'dim' }}">
                            <svg width="14" height="14" viewBox="0 0 20 20" fill="{{ ($features['api_access'] ?? false) ? '#10b981' : '#d1d5db' }}"><path fill-rule="evenodd" d="M16.704 4.153a.75.75 0 01.143 1.052l-8 10.5a.75.75 0 01-1.127.075l-4.5-4.5a.75.75 0 011.06-1.06l3.894 3.893 7.48-9.817a.75.75 0 011.05-.143z" clip-rule="evenodd"/></svg>
                            {{ __('marketing.pricing_feat_api_access') }}
                        </li>
                        {{-- Custom-domain feature row temporarily removed: the custom-domain
                             page is disabled until host-based serving is implemented (see
                             DomainSettingsPage::canAccess). Re-add this <li> with
                             __('marketing.pricing_feat_custom_domain') to restore it. --}}
                        <li class="{{ ($features['white_label'] ?? false) ? '' : 'dim' }}">
                            <svg width="14" height="14" viewBox="0 0 20 20" fill="{{ ($features['white_label'] ?? false) ? '#10b981' : '#d1d5db' }}"><path fill-rule="evenodd" d="M16.704 4.153a.75.75 0 01.143 1.052l-8 10.5a.75.75 0 01-1.127.075l-4.5-4.5a.75.75 0 011.06-1.06l3.894 3.893 7.48-9.817a.75.75 0 011.05-.143z" clip-rule="evenodd"/></svg>
                            {{ __('marketing.pricing_feat_white_label') }}
                        </li>
                        <li class="{{ ($features['priority_support'] ?? false) ? '' : 'dim' }}">
                            <svg width="14" height="14" viewBox="0 0 20 20" fill="{{ ($features['priority_support'] ?? false) ? '#10b981' : '#d1d5db' }}"><path fill-rule="evenodd" d="M16.704 4.153a.75.75 0 01.143 1.052l-8 10.5a.75.75 0 01-1.127.075l-4.5-4.5a.75.75 0 011.06-1.06l3.894 3.893 7.48-9.817a.75.75 0 011.05-.143z" clip-rule="evenodd"/></svg>
                            {{ __('marketing.pricing_feat_priority_support') }}
                        </li>
                    </ul>

                    @if($canSignUp)
                        <a href="/register?plan={{ $key }}" class="btn btn-primary is-full-width">{{ __('marketing.pricing_start_trial') }}</a>
                    @else
                        <a href="/admin/login" class="btn btn-primary is-full-width">{{ __('marketing.pricing_signin') }}</a>
                    @endif
                </div>
            @endforeach
        </div>
    </div>
</section>

<section class="faq">
    <h2>{{ __('marketing.pricing_faq_heading') }}</h2>

    <div class="q">
        <h4>{{ __('marketing.pricing_faq1_q') }}</h4>
        <p>{{ __('marketing.pricing_faq1_a', ['days' => $trialDays]) }}</p>
    </div>

    <div class="q">
        <h4>{{ __('marketing.pricing_faq2_q') }}</h4>
        <p>{{ __('marketing.pricing_faq2_a') }}</p>
    </div>

    <div class="q">
        <h4>{{ __('marketing.pricing_faq3_q') }}</h4>
        <p>{{ __('marketing.pricing_faq3_a') }}</p>
    </div>

    <div class="q">
        <h4>{{ __('marketing.pricing_faq4_q') }}</h4>
        <p>{{ __('marketing.pricing_faq4_a') }}</p>
    </div>
</section>
@endsection
