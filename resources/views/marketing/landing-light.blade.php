@extends('marketing.layout')

@section('title', __('marketing.light_page_title', ['app' => $appName]))
@section('description', __('marketing.light_page_description'))

@push('head')
<link rel="stylesheet" href="{{ asset('css/marketing/landing-light.css') }}">
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
        ['title' => __('marketing.light_feat7_title'), 'body' => __('marketing.light_feat7_body')],
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

        {{-- ── Above-the-fold product mockup ────────────────────
             CSS-drawn workspace preview.  Gives the visitor an
             immediate "this is what it looks like" without needing
             a real screenshot. --}}
        <div class="lp-mockup-wrap reveal">
            <div class="lp-mockup">
                <div class="lp-chrome">
                    <span class="dot r"></span>
                    <span class="dot y"></span>
                    <span class="dot g"></span>
                    <span class="url"><span class="dim">https://</span>app.{{ parse_url(config('app.url'), PHP_URL_HOST) ?: 'leadhub.com' }}<span class="dim">/leads</span></span>
                </div>
                <div class="lp-app">
                    <aside class="lp-sidebar">
                        <div class="logo"><span class="logo-mark"></span> {{ $appName }}</div>
                        <ul>
                            <li class="active"><span class="sb-dot"></span> {{ __('marketing.light_mockup_nav_leads') }}</li>
                            <li><span class="sb-dot"></span> {{ __('marketing.light_mockup_nav_pipeline') }}</li>
                            <li><span class="sb-dot"></span> {{ __('marketing.light_mockup_nav_automations') }}</li>
                            <li><span class="sb-dot"></span> {{ __('marketing.light_mockup_nav_forms') }}</li>
                            <li><span class="sb-dot"></span> {{ __('marketing.light_mockup_nav_landing') }}</li>
                            <li><span class="sb-dot"></span> {{ __('marketing.light_mockup_nav_reports') }}</li>
                            <li><span class="sb-dot"></span> {{ __('marketing.light_mockup_nav_settings') }}</li>
                        </ul>
                    </aside>
                    <main class="lp-main">
                        <div class="lp-main-top">
                            <h5>{{ __('marketing.light_mockup_nav_leads') }} <span class="count">{{ __('marketing.light_mockup_count_active') }}</span></h5>
                            <span class="btn">{{ __('marketing.light_mockup_btn_new_lead') }}</span>
                        </div>
                        <div class="lp-kanban">
                            <div class="lp-k-col">
                                <div class="lp-k-head"><span class="label">{{ __('marketing.light_mockup_col_new') }}</span><span class="pill">12</span></div>
                                <div class="lp-card">
                                    <span class="name">{{ __('marketing.light_mockup_card_acme') }}</span>
                                    <span class="meta"><span class="av"></span> {{ __('marketing.light_mockup_card_acme_meta') }}</span>
                                    <div class="tags"><span class="tag hot">{{ __('marketing.light_mockup_tag_hot') }}</span></div>
                                </div>
                                <div class="lp-card">
                                    <span class="name">{{ __('marketing.light_mockup_card_globex') }}</span>
                                    <span class="meta"><span class="av a2"></span> {{ __('marketing.light_mockup_card_globex_meta') }}</span>
                                </div>
                                <div class="lp-card">
                                    <span class="name">{{ __('marketing.light_mockup_card_initech') }}</span>
                                    <span class="meta"><span class="av a3"></span> {{ __('marketing.light_mockup_card_initech_meta') }}</span>
                                    <div class="tags"><span class="tag warm">{{ __('marketing.light_mockup_tag_warm') }}</span></div>
                                </div>
                            </div>
                            <div class="lp-k-col">
                                <div class="lp-k-head"><span class="label">{{ __('marketing.light_mockup_col_qualified') }}</span><span class="pill">8</span></div>
                                <div class="lp-card">
                                    <span class="name">{{ __('marketing.light_mockup_card_stark') }}</span>
                                    <span class="meta"><span class="av"></span> {{ __('marketing.light_mockup_card_stark_meta') }}</span>
                                    <div class="tags"><span class="tag enterprise">{{ __('marketing.light_mockup_tag_enterprise') }}</span></div>
                                </div>
                                <div class="lp-card">
                                    <span class="name">{{ __('marketing.light_mockup_card_wayne') }}</span>
                                    <span class="meta"><span class="av a2"></span> {{ __('marketing.light_mockup_card_wayne_meta') }}</span>
                                </div>
                            </div>
                            <div class="lp-k-col">
                                <div class="lp-k-head"><span class="label">{{ __('marketing.light_mockup_col_won') }}</span><span class="pill">5</span></div>
                                <div class="lp-card">
                                    <span class="name">{{ __('marketing.light_mockup_card_umbrella') }}</span>
                                    <span class="meta"><span class="av a3"></span> {{ __('marketing.light_mockup_card_umbrella_meta') }}</span>
                                    <div class="tags"><span class="tag enterprise">$48K</span></div>
                                </div>
                                <div class="lp-card">
                                    <span class="name">{{ __('marketing.light_mockup_card_piedpiper') }}</span>
                                    <span class="meta"><span class="av"></span> {{ __('marketing.light_mockup_card_piedpiper_meta') }}</span>
                                </div>
                            </div>
                        </div>
                    </main>
                </div>
            </div>
        </div>
    </div>
</section>

{{-- ── Features ──────────────────────────────────────────── --}}
<section class="wrap reveal">
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

{{-- ── Stats ──────────────────────────────────────────────
     Each tile prefers the SA-edited override from LandingContent
     when present; falls back to the translator key shipped in
     lang/<locale>/marketing.php (which existing installs see).
     SuperAdmin edits live at /super-admin/landing-page-editor →
     Stats tab and persist via the `landing` settings group. --}}
@php
    $stat1n = filled($content->stat1_number) ? $content->stat1_number : __('marketing.light_stat1_n');
    $stat1l = filled($content->stat1_label)  ? $content->stat1_label  : __('marketing.light_stat1_l');
    $stat2n = filled($content->stat2_number) ? $content->stat2_number : __('marketing.light_stat2_n');
    $stat2l = filled($content->stat2_label)  ? $content->stat2_label  : __('marketing.light_stat2_l');
    // Stat 3 has no light_stat3_n lang key — the original blade
    // hard-coded "∞".  Keep that as the SA-overrideable fallback.
    $stat3n = filled($content->stat3_number) ? $content->stat3_number : '∞';
    $stat3l = filled($content->stat3_label)  ? $content->stat3_label  : __('marketing.light_stat3_l');
    $stat4n = filled($content->stat4_number) ? $content->stat4_number : __('marketing.light_stat4_n');
    $stat4l = filled($content->stat4_label)  ? $content->stat4_label  : __('marketing.light_stat4_l');
@endphp
<section class="wrap reveal">
    <div class="stats">
        <div class="stat"><div class="n">{{ $stat1n }}</div><div class="l">{{ $stat1l }}</div></div>
        <div class="stat"><div class="n">{{ $stat2n }}</div><div class="l">{{ $stat2l }}</div></div>
        <div class="stat"><div class="n">{{ $stat3n }}</div><div class="l">{{ $stat3l }}</div></div>
        <div class="stat"><div class="n">{{ $stat4n }}</div><div class="l">{{ $stat4l }}</div></div>
    </div>
</section>

{{-- ── Pricing ──────────────────────────────────────────── --}}
@if($plans && count($plans) > 0)
<section id="pricing" class="wrap reveal">
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
                    $ltIntervalSlug = $plan['interval'] ?? 'month';
                    $ltKey          = 'marketing.interval_' . $ltIntervalSlug;
                    $ltTrans        = __($ltKey);
                    $ltIntervalLbl  = (is_string($ltTrans) && $ltTrans !== $ltKey)
                        ? $ltTrans
                        : __('marketing.light_per_month');
                @endphp
                <div class="price">{{ \App\Support\Currency::format((float) $plan['price'], $plan['currency'] ?? \App\Support\Currency::default()) }}<small> / {{ $ltIntervalLbl }}</small></div>
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
                    @php $lightIsPaid = ((float) ($plan['price'] ?? 0)) > 0; @endphp
                    <a href="/register?plan={{ $key }}" class="btn-m {{ $isPop ? 'btn-m-primary' : 'btn-m-ghost' }}">
                        {{ $lightIsPaid ? __('marketing.light_choose_plan') : __('marketing.light_start_trial') }}
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
<section class="wrap reveal">
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

{{-- Rich footer moved into marketing/layout.blade.php so every
     public page (home, pricing, docs, static pages) shares the
     same dark-card footer treatment.  All toggles (show brand,
     show static pages, show social, individual URL overrides)
     still live on LandingContent and are read by the layout. --}}

{{-- ── Scroll-reveal + nav-shadow JS ──────────────────────
     IntersectionObserver flips .reveal → .is-visible as each
     element enters the viewport.  Nav gets a tighter shadow
     once the user scrolls past the hero. --}}
<script src="{{ asset('js/views/marketing/landing-light.js') }}" defer></script>
@endsection
