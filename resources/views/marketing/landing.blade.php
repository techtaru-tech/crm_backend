@extends('marketing.layout')

@section('title', __('marketing.landing_page_title', ['app' => $appName]))

@push('head')
<link rel="stylesheet" href="{{ asset('css/marketing/landing.css') }}">
@endpush

@section('content')
@php
    // Precedence: editor's per-locale translation → editor's English
    // scalar → lang/{locale}/marketing.php translation key.  Covered by
    // LandingContent::t() returning '' for empty locales, letting the
    // __() fallback fire.
    $heroHeadline = $content->t('hero_headline') ?: __('marketing.hero.headline');
    $heroHighlight = $content->t('hero_headline_highlight') ?: __('marketing.hero.highlight');
    $heroSubtext = $content->t('hero_subtext') ?: __('marketing.hero.subtext');
    $heroCtaLabel = $content->t('hero_cta_label') ?: __('marketing.hero.cta', ['days' => $trialDays]);
    $heroNote = $content->t('hero_note') ?: __('marketing.hero.note');

    $featuresHeadline = $content->t('features_headline') ?: __('marketing.features.headline');
    $featuresSubtext = $content->t('features_subtext') ?: __('marketing.features.subtext');

    $defaultFeatures = [
        ['title' => __('marketing.landing_feat1_title'), 'body' => __('marketing.landing_feat1_body')],
        ['title' => __('marketing.landing_feat2_title'), 'body' => __('marketing.landing_feat2_body')],
        ['title' => __('marketing.landing_feat3_title'), 'body' => __('marketing.landing_feat3_body')],
        ['title' => __('marketing.landing_feat4_title'), 'body' => __('marketing.landing_feat4_body')],
        ['title' => __('marketing.landing_feat5_title'), 'body' => __('marketing.landing_feat5_body')],
        ['title' => __('marketing.landing_feat6_title'), 'body' => __('marketing.landing_feat6_body')],
    ];
    $features = ! empty($content->features) ? $content->features : $defaultFeatures;

    $pricingHeadline = $content->t('pricing_headline') ?: __('marketing.pricing.headline');
    $pricingSubtext  = $content->t('pricing_subtext')  ?: __('marketing.pricing.subtext');

    $ctaHeadline = $content->t('cta_headline') ?: __('marketing.cta.headline');
    $ctaSubtext  = $content->t('cta_subtext')  ?: __('marketing.cta.subtext');
@endphp

<section class="hero">
    <div class="wrap hero-inner">
        @php $heroEyebrowText = $content->t('hero_eyebrow'); @endphp
        @if($heroEyebrowText !== '')
            <p class="hero-eyebrow">{{ $heroEyebrowText }}</p>
        @endif
        <h1>{{ $heroHeadline }}<br><span>{{ $heroHighlight }}</span></h1>
        <p class="lead">{{ $heroSubtext }}</p>
        <div class="hero-cta">
            @if($canSignUp)
                <a href="/register" class="btn btn-primary">{{ $heroCtaLabel }}</a>
            @else
                <a href="/admin/login" class="btn btn-primary">{{ __('marketing.landing_signin') }}</a>
            @endif
            <a href="/pricing" class="btn btn-ghost">{{ __('marketing.landing_see_pricing') }}</a>
        </div>
        <p class="hero-note">{{ $heroNote }}</p>
    </div>
</section>

<section class="features">
    <div class="wrap">
        <h2>{{ $featuresHeadline }}</h2>
        <p class="sub">{{ $featuresSubtext }}</p>

        <div class="features-grid">
            @foreach($features as $feature)
                <div class="feat">
                    <div class="icon">
                        <svg width="22" height="22" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M22 11.08V12a10 10 0 1 1-5.93-9.14"/><polyline points="22 4 12 14.01 9 11.01"/></svg>
                    </div>
                    <h3>{{ $feature['title'] ?? '' }}</h3>
                    <p>{{ $feature['body'] ?? '' }}</p>
                </div>
            @endforeach
        </div>
    </div>
</section>

<section class="checklist">
    <div class="wrap checklist-inner">
        <h2>{{ __('marketing.landing_checklist_heading') }}</h2>
        <div class="checklist-grid">
            @foreach([
                __('marketing.landing_check_19_sources'),
                __('marketing.landing_check_kanban'),
                __('marketing.landing_check_automation'),
                __('marketing.landing_check_scoring'),
                __('marketing.landing_check_form_builder'),
                __('marketing.landing_check_multitenant'),
                __('marketing.landing_check_white_label'),
                __('marketing.landing_check_rest_api'),
                __('marketing.landing_check_outbound_webhooks'),
                __('marketing.landing_check_scheduled_reports'),
                __('marketing.landing_check_2fa'),
                __('marketing.landing_check_imap'),
                __('marketing.landing_check_ai_composer'),
                __('marketing.landing_check_push'),
                __('marketing.landing_check_backup'),
                __('marketing.landing_check_modules'),
                __('marketing.landing_check_languages'),
                __('marketing.landing_check_installer'),
            ] as $item)
                <div class="item">
                    <svg width="16" height="16" viewBox="0 0 20 20" fill="currentColor"><path fill-rule="evenodd" d="M16.704 4.153a.75.75 0 01.143 1.052l-8 10.5a.75.75 0 01-1.127.075l-4.5-4.5a.75.75 0 011.06-1.06l3.894 3.893 7.48-9.817a.75.75 0 011.05-.143z" clip-rule="evenodd"/></svg>
                    {{ $item }}
                </div>
            @endforeach
        </div>
    </div>
</section>

@if($plans && count($plans) > 0)
<section class="pricing-preview">
    <div class="wrap">
        <h2>{{ $pricingHeadline }}</h2>
        <p class="sub">{{ $pricingSubtext }}</p>

        <div class="plans">
            @foreach($plans as $key => $plan)
                <div class="plan {{ ($plan['highlight'] ?? false) ? 'highlight' : '' }}">
                    @if($plan['highlight'] ?? false)
                        <div class="ribbon">{{ __('marketing.landing_most_popular') }}</div>
                    @endif
                    <h3>{{ $plan['name'] }}</h3>
                    <p class="desc">{{ $plan['description'] ?? '' }}</p>
                    @php
                        // Locale-aware currency symbol (was hardcoded "$") and
                        // translator-first interval label (was leaking raw slug).
                        $mlPlanCurrency = $plan['currency'] ?? \App\Support\Currency::default();
                        $mlIntervalSlug = $plan['interval'] ?? 'month';
                        $mlIntervalKey  = 'marketing.interval_' . $mlIntervalSlug;
                        $mlIntervalTr   = __($mlIntervalKey);
                        $mlIntervalLbl  = (is_string($mlIntervalTr) && $mlIntervalTr !== $mlIntervalKey)
                            ? $mlIntervalTr
                            : __('marketing.landing_per_month');
                    @endphp
                    <div class="price">{{ \App\Support\Currency::format((float) ($plan['price'] ?? 0), $mlPlanCurrency) }}<span> /{{ $mlIntervalLbl }}</span></div>

                    @php $limits = $plan['limits'] ?? []; @endphp
                    <ul>
                        <li>
                            <svg width="14" height="14" viewBox="0 0 20 20" fill="#10b981"><path fill-rule="evenodd" d="M16.704 4.153a.75.75 0 01.143 1.052l-8 10.5a.75.75 0 01-1.127.075l-4.5-4.5a.75.75 0 011.06-1.06l3.894 3.893 7.48-9.817a.75.75 0 011.05-.143z" clip-rule="evenodd"/></svg>
                            {{ ($limits['max_seats'] ?? 0) === -1 ? __('marketing.pricing.seats_unlimited') : __('marketing.pricing.seats_count', ['count' => $limits['max_seats'] ?? 0]) }}
                        </li>
                        <li>
                            <svg width="14" height="14" viewBox="0 0 20 20" fill="#10b981"><path fill-rule="evenodd" d="M16.704 4.153a.75.75 0 01.143 1.052l-8 10.5a.75.75 0 01-1.127.075l-4.5-4.5a.75.75 0 011.06-1.06l3.894 3.893 7.48-9.817a.75.75 0 011.05-.143z" clip-rule="evenodd"/></svg>
                            {{ ($limits['max_leads'] ?? 0) === -1 ? __('marketing.pricing.leads_unlimited') : __('marketing.pricing.leads_count', ['count' => number_format($limits['max_leads'] ?? 0)]) }}
                        </li>
                        <li>
                            <svg width="14" height="14" viewBox="0 0 20 20" fill="#10b981"><path fill-rule="evenodd" d="M16.704 4.153a.75.75 0 01.143 1.052l-8 10.5a.75.75 0 01-1.127.075l-4.5-4.5a.75.75 0 011.06-1.06l3.894 3.893 7.48-9.817a.75.75 0 011.05-.143z" clip-rule="evenodd"/></svg>
                            {{ ($limits['max_forms'] ?? 0) === -1 ? __('marketing.pricing.forms_unlimited') : __('marketing.pricing.forms_count', ['count' => $limits['max_forms'] ?? 0]) }}
                        </li>
                    </ul>

                    @if($canSignUp)
                        <a href="/register?plan={{ $key }}" class="btn btn-primary is-full-width">{{ __('marketing.pricing.start_with', ['plan' => $plan['name']]) }}</a>
                    @else
                        <a href="/admin/login" class="btn btn-primary is-full-width">{{ __('marketing.pricing.get_started') }}</a>
                    @endif
                </div>
            @endforeach
        </div>
    </div>
</section>
@endif

<section class="cta">
    <div class="wrap">
        <h2>{{ $ctaHeadline }}</h2>
        <p>{{ $ctaSubtext }}</p>
        @if($canSignUp)
            <a href="/register" class="btn btn-primary">{{ $heroCtaLabel }}</a>
        @else
            <a href="/admin/login" class="btn btn-primary">{{ __('marketing.landing_signin') }}</a>
        @endif
    </div>
</section>
@endsection
