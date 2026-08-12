@extends('marketing.layout')

@section('title', __('marketing.light_page_title', ['app' => $appName]))
@section('description', __('marketing.light_page_description'))

@push('head')
{{-- ?v=<filemtime> busts the browser + CDN cache automatically on
     every CSS edit.  Without this, the asset() helper renders a
     stable URL with no version hash, so static-file caches happily
     serve a stale copy even after the file changed on disk — which
     was the symptom of "I pulled the latest commit but the page
     still looks like the previous version".  filemtime() returns
     the unix timestamp the file was last modified, so the query
     string changes the moment the deploy lands. --}}
<link rel="stylesheet" href="{{ asset('css/marketing/landing-modern.css') }}?v={{ @filemtime(public_path('css/marketing/landing-modern.css')) ?: time() }}">
@endpush

@section('content')
@php
    // All text fields resolve via LandingContent::t() so the current
    // locale's translation (if any) overrides English automatically.
    $heroEyebrow      = $content->t('hero_eyebrow') ?: __('marketing.light.hero_eyebrow');
    $heroHeadline     = $content->t('hero_headline') ?: __('marketing.light.hero_headline');
    $heroHighlight    = $content->t('hero_headline_highlight') ?: __('marketing.light.hero_highlight');
    $heroSubtext      = $content->t('hero_subtext') ?: __('marketing.light.hero_subtext');
    $heroCtaLabel     = $content->t('hero_cta_label') ?: __('marketing.light.hero_cta', ['days' => $trialDays]);
    $heroNote         = $content->t('hero_note') ?: __('marketing.light.hero_note');

    $featuresHeadline = $content->t('features_headline') ?: __('marketing.light.features_headline');
    $featuresSubtext  = $content->t('features_subtext')  ?: __('marketing.light_features_subtext');

    $defaultFeatures = [
        ['title' => __('marketing.light_feat1_title'), 'body' => __('marketing.light_feat1_body')],
        ['title' => __('marketing.light_feat2_title'), 'body' => __('marketing.light_feat2_body')],
        ['title' => __('marketing.light_feat3_title'), 'body' => __('marketing.light_feat3_body')],
        ['title' => __('marketing.light_feat4_title'), 'body' => __('marketing.light_feat4_body')],
        ['title' => __('marketing.light_feat5_title'), 'body' => __('marketing.light_feat5_body')],
        ['title' => __('marketing.light_feat6_title'), 'body' => __('marketing.light_feat6_body')],
        ['title' => __('marketing.modern_feat7_title'), 'body' => __('marketing.modern_feat7_body')],
        ['title' => __('marketing.light_feat8_title'), 'body' => __('marketing.light_feat8_body')],
        ['title' => __('marketing.light_feat9_title'), 'body' => __('marketing.light_feat9_body')],
    ];
    $features = ! empty($content->features) ? $content->features : $defaultFeatures;

    $pricingHeadline = $content->t('pricing_headline') ?: __('marketing.light.pricing_headline');
    $pricingSubtext  = $content->t('pricing_subtext')  ?: __('marketing.light_pricing_subtext');

    $ctaHeadline = $content->t('cta_headline') ?: __('marketing.light.cta_headline');
    $ctaSubtext  = $content->t('cta_subtext')  ?: __('marketing.light_cta_subtext');
@endphp
{{-- ── Hero ─────────────────────────────────────────────────── --}}
<section class="hero-m">
    <div class="wrap">
        <div class="pill-m"><span class="dot"></span> {{ $heroEyebrow }}</div>
        <h1>{{ $heroHeadline }} <span class="grad">{{ $heroHighlight }}</span></h1>
        <p class="sub">{{ $heroSubtext }}</p>
        <div class="cta-row">
            @if($canSignUp)
                <a href="/register" class="btn-m btn-m-primary">{{ $heroCtaLabel }}</a>
            @else
                <a href="/admin/login" class="btn-m btn-m-primary">{{ __('marketing.light_signin') }}</a>
            @endif
            <a href="/pricing" class="btn-m btn-m-ghost">{{ __('marketing.light_see_pricing') }}</a>
        </div>
        <p class="trust">{{ $heroNote }}</p>
    </div>
</section>

{{-- ── Features ──────────────────────────────────────────── --}}
<section class="wrap">
    <div class="sh">
        <small>{{ __('marketing.light_everything_included') }}</small>
        <h2>{{ $featuresHeadline }}</h2>
        <p>{{ $featuresSubtext }}</p>
    </div>

    <div class="feat-grid">
        @foreach($features as $feature)
            <div class="feat-m">
                <div class="icon">
                    <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round">
                        <polyline points="20 6 9 17 4 12"/>
                    </svg>
                </div>
                <h3>{{ $feature['title'] ?? '' }}</h3>
                <p>{{ $feature['body'] ?? '' }}</p>
            </div>
        @endforeach
    </div>
</section>

{{-- ── Stats ────────────────────────────────────────────── --}}
<section class="wrap">
    <div class="stats">
        <div class="stat"><div class="n">{{ __('marketing.light_stat1_n') }}</div><div class="l">{{ __('marketing.light_stat1_l') }}</div></div>
        <div class="stat"><div class="n">{{ __('marketing.light_stat2_n') }}</div><div class="l">{{ __('marketing.light_stat2_l') }}</div></div>
        <div class="stat"><div class="n">∞</div><div class="l">{{ __('marketing.light_stat3_l') }}</div></div>
        <div class="stat"><div class="n">{{ __('marketing.light_stat4_n') }}</div><div class="l">{{ __('marketing.light_stat4_l') }}</div></div>
    </div>
</section>

{{-- ── Pricing ──────────────────────────────────────────── --}}
@if($plans && count($plans) > 0)
<section class="wrap">
    <div class="sh">
        <small>{{ __('marketing.light_transparent_pricing') }}</small>
        <h2>{{ $pricingHeadline }}</h2>
        <p>{{ $pricingSubtext }}</p>
    </div>

    <div class="price-grid">
        @foreach($plans as $key => $plan)
            @php $isPop = $plan['highlight'] ?? false; @endphp
            <div class="plan {{ $isPop ? 'popular' : '' }}">
                @if($isPop)<div class="ribbon">{{ __('marketing.light_most_popular') }}</div>@endif
                <h3>{{ $plan['name'] }}</h3>
                <p class="desc">{{ $plan['description'] ?? '' }}</p>
                @php
                    // Translator-first interval label so "/month" respects tenant locale.
                    $mdIntervalSlug = $plan['interval'] ?? 'month';
                    $mdKey          = 'marketing.interval_' . $mdIntervalSlug;
                    $mdTrans        = __($mdKey);
                    $mdIntervalLbl  = (is_string($mdTrans) && $mdTrans !== $mdKey)
                        ? $mdTrans
                        : __('marketing.light_per_month');
                @endphp
                <div class="price">{{ \App\Support\Currency::format((float) $plan['price'], $plan['currency'] ?? \App\Support\Currency::default()) }}<small> / {{ $mdIntervalLbl }}</small></div>
                <ul>
                    @foreach(array_slice($plan['features'] ?? [], 0, 6) as $feat => $on)
                        @if($on)
                            @php
                                // Translator-first feature label so the marketing landing
                                // respects locale. Reuses the billing_portal feature keys.
                                $mkFeatKey   = 'filament/billing_portal.feature_' . $feat;
                                $mkFeatTrans = __($mkFeatKey);
                                $mkFeatLabel = is_string($mkFeatTrans) && $mkFeatTrans !== $mkFeatKey
                                    ? $mkFeatTrans
                                    : ucwords(str_replace('_', ' ', (string) $feat));
                            @endphp
                            <li>{{ $mkFeatLabel }}</li>
                        @endif
                    @endforeach
                </ul>
                @if($canSignUp)
                    @php $modernIsPaid = ((float) ($plan['price'] ?? 0)) > 0; @endphp
                    <a href="/register?plan={{ $key }}" class="btn-m {{ $isPop ? 'btn-m-primary' : 'btn-m-ghost' }}">
                        {{ $modernIsPaid ? __('marketing.light_choose_plan') : __('marketing.light_start_trial') }}
                    </a>
                @else
                    <a href="/admin/login" class="btn-m btn-m-ghost">{{ __('marketing.light_signin') }}</a>
                @endif
            </div>
        @endforeach
    </div>
</section>
@endif

{{-- ── Closing CTA ─────────────────────────────────────── --}}
<section class="wrap">
    <div class="cta-m">
        <h2>{{ $ctaHeadline }}</h2>
        <p>{{ $ctaSubtext }}</p>
        @if($canSignUp)
            <a href="/register" class="btn-m btn-m-primary">{{ __('marketing.light_cta_create') }}</a>
        @else
            <a href="/admin/login" class="btn-m btn-m-primary">{{ __('marketing.light_signin') }}</a>
        @endif
    </div>
</section>

{{-- Footer removed: the marketing.layout already renders its own .foot
     below @yield('content'). Having both produced a duplicated footer. --}}
@endsection
