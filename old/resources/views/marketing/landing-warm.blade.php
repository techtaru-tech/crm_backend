@extends('marketing.layout')

@section('title', $appName . ' — ' . ($content->t('hero_headline') ?: __('marketing.warm.page_title')))
@section('description', $content->t('hero_subtext') ?: __('marketing.warm.page_description'))

@push('head')
{{-- Fonts: Inter is provided by the parent marketing.layout via a
     self-hosted <link> (no CDN).  Instrument Serif was previously
     loaded from Google Fonts but has been removed for CodeCanyon
     compliance — the typography CSS below falls back to a system
     serif stack for display headings. --}}
<link rel="stylesheet" href="{{ asset('css/marketing/landing-warm.css') }}">
@endpush

@section('content')
@php
    $heroEyebrow      = $content->t('hero_eyebrow') ?: __('marketing.warm.hero_eyebrow');
    $heroHeadline     = $content->t('hero_headline') ?: __('marketing.warm.hero_headline');
    $heroHighlight    = $content->t('hero_headline_highlight') ?: __('marketing.warm.hero_highlight');
    $heroSubtext      = $content->t('hero_subtext') ?: __('marketing.warm.hero_subtext');
    $heroCtaLabel     = $content->t('hero_cta_label') ?: __('marketing.warm.hero_cta', ['days' => $trialDays]);
    $heroNote         = $content->t('hero_note') ?: __('marketing.warm.hero_note');

    $featuresHeadline = $content->t('features_headline') ?: __('marketing.warm.features_headline');
    $featuresSubtext  = $content->t('features_subtext')  ?: __('marketing.warm_features_subtext');

    $pricingHeadline = $content->t('pricing_headline') ?: __('marketing.warm.pricing_headline');
    $pricingSubtext  = $content->t('pricing_subtext')  ?: __('marketing.warm_pricing_subtext');

    $ctaHeadline = $content->t('cta_headline') ?: __('marketing.warm.cta_headline');
    $ctaSubtext  = $content->t('cta_subtext')  ?: __('marketing.warm_cta_subtext');
@endphp

<div class="w-shell">

    {{-- ─── HERO ──────────────────────────────────────────── --}}
    <section class="w-hero">
        <div class="w-wrap-narrow">
            <div class="w-eyebrow"><span class="dot"></span> {{ $heroEyebrow }}</div>
            <h1>{{ $heroHeadline }} <em>{{ $heroHighlight }}</em></h1>
            <p class="w-sub">{{ $heroSubtext }}</p>
            <div class="w-cta-row">
                @if($canSignUp)
                    <a href="/register" class="w-btn w-btn-fill">
                        {{ $heroCtaLabel }}<span class="arrow">→</span>
                    </a>
                @else
                    <a href="/admin/login" class="w-btn w-btn-fill">{{ __('marketing.warm_signin') }}<span class="arrow">→</span></a>
                @endif
                <a href="/pricing" class="w-btn w-btn-ghost">{{ __('marketing.warm_see_pricing') }}</a>
            </div>

            <div class="w-trust">
                <span class="w-trust-note">{{ $heroNote }}</span>
            </div>
        </div>
    </section>

    {{-- ─── SECTION HEAD ──────────────────────────────────── --}}
    <section class="w-wrap">
        <div class="w-section-head">
            <span class="w-section-eyebrow">{{ __('marketing.warm_what_it_does') }}</span>
            <h2>{{ $featuresHeadline }} <em>{!! __('marketing.warm_section_h2_tail') !!}</em></h2>
            <p>{{ $featuresSubtext }}</p>
        </div>
    </section>

    {{-- ─── FEATURE ROW 1 — Pipeline ─────────────────────────── --}}
    <section class="w-wrap">
        <div class="w-feat-row">
            <div class="w-feat-text">
                <span class="w-feat-mark">{{ __('marketing.warm_row1_mark') }}</span>
                <h3>{{ __('marketing.warm_row1_h3_lead') }} <em>{{ __('marketing.warm_row1_h3_em') }}</em> {{ __('marketing.warm_row1_h3_tail') }}</h3>
                <p>{{ __('marketing.warm_row1_p1') }}</p>
                <p>{{ __('marketing.warm_row1_p2') }}</p>
            </div>
            <div class="w-feat-visual">
                <div class="w-viz-pipeline">
                    <div class="row">{{ __('marketing.warm_viz1_acme') }} <span class="src">{{ __('marketing.warm_viz1_acme_src') }}</span></div>
                    <div class="row">{{ __('marketing.warm_viz1_jane') }} <span class="src">{{ __('marketing.warm_viz1_jane_src') }}</span></div>
                    <div class="row">{{ __('marketing.warm_viz1_globex') }} <span class="src">{{ __('marketing.warm_viz1_globex_src') }}</span></div>
                    <div class="row">{{ __('marketing.warm_viz1_wayne') }} <span class="src">{{ __('marketing.warm_viz1_wayne_src') }}</span></div>
                </div>
            </div>
        </div>
    </section>

    {{-- ─── FEATURE ROW 2 — Scoring (flipped) ────────────────── --}}
    <section class="w-wrap">
        <div class="w-feat-row flip">
            <div class="w-feat-text">
                <span class="w-feat-mark">{{ __('marketing.warm_row2_mark') }}</span>
                <h3>{{ __('marketing.warm_row2_h3_lead') }} <em>{{ __('marketing.warm_row2_h3_em') }}</em> {{ __('marketing.warm_row2_h3_tail') }}</h3>
                <p>{{ __('marketing.warm_row2_p1') }}</p>
                <p>{!! __('marketing.warm_row2_p2') !!}</p>
            </div>
            <div class="w-feat-visual">
                <div class="w-viz-score">
                    <div class="score-item">
                        <span class="label">{{ __('marketing.warm_viz2_lead_name') }}</span>
                        <div class="score-row"><span class="name">{{ __('marketing.warm_viz2_engagement') }}</span><div class="track"><div class="fill is-engagement"></div></div><span class="num">72</span></div>
                        <div class="score-row"><span class="name">{{ __('marketing.warm_viz2_recency') }}</span><div class="track"><div class="fill is-recency"></div></div><span class="num">48</span></div>
                        <div class="score-row"><span class="name">{{ __('marketing.warm_viz2_fit') }}</span><div class="track"><div class="fill is-fit"></div></div><span class="num">86</span></div>
                    </div>
                </div>
            </div>
        </div>
    </section>

    {{-- ─── FEATURE ROW 3 — Automations ──────────────────────── --}}
    <section class="w-wrap">
        <div class="w-feat-row">
            <div class="w-feat-text">
                <span class="w-feat-mark">{{ __('marketing.warm_row3_mark') }}</span>
                <h3>{{ __('marketing.warm_row3_h3_lead') }} <em>{{ __('marketing.warm_row3_h3_em') }}</em></h3>
                <p>{{ __('marketing.warm_row3_p1') }}</p>
                <p>{{ __('marketing.warm_row3_p2') }}</p>
            </div>
            <div class="w-feat-visual">
                <div class="w-viz-flow">
                    <div class="step">
                        <span class="mark">i.</span>
                        <span class="txt"><strong>{{ __('marketing.warm_viz3_step1_when') }}</strong> {{ __('marketing.warm_viz3_step1_text') }} <em>{{ __('marketing.warm_viz3_step1_em') }}</em></span>
                    </div>
                    <div class="step">
                        <span class="mark">ii.</span>
                        <span class="txt"><strong>{{ __('marketing.warm_viz3_step2_wait') }}</strong> {{ __('marketing.warm_viz3_step2_text') }}</span>
                    </div>
                    <div class="step">
                        <span class="mark">iii.</span>
                        <span class="txt"><strong>{{ __('marketing.warm_viz3_step3_send') }}</strong> {{ __('marketing.warm_viz3_step3_text') }}</span>
                    </div>
                    <div class="step">
                        <span class="mark">iv.</span>
                        <span class="txt"><strong>{{ __('marketing.warm_viz3_step4_create') }}</strong> {{ __('marketing.warm_viz3_step4_text') }}</span>
                    </div>
                </div>
            </div>
        </div>
    </section>

    {{-- ─── PULL QUOTE ────────────────────────────────────────── --}}
    <section class="w-wrap">
        <div class="w-quote">
            <blockquote>{!! __('marketing.warm_quote') !!}</blockquote>
            <cite>{{ __('marketing.warm_quote_cite') }}</cite>
        </div>
    </section>

    {{-- ─── FEATURE ROW 4 — Multi-tenant (flipped) ───────────── --}}
    <section class="w-wrap">
        <div class="w-feat-row flip">
            <div class="w-feat-text">
                <span class="w-feat-mark">{{ __('marketing.warm_row4_mark') }}</span>
                <h3>{{ __('marketing.warm_row4_h3_lead') }} <em>{{ __('marketing.warm_row4_h3_em') }}</em></h3>
                <p>{{ __('marketing.warm_row4_p1') }}</p>
                <p>{{ __('marketing.warm_row4_p2') }}</p>
            </div>
            <div class="w-feat-visual">
                <div class="w-viz-tenants">
                    <div class="tenant">
                        <div class="name">{{ __('marketing.warm_viz4_t1_name') }}</div>
                        <div class="meta">{{ __('marketing.warm_viz4_t1_meta') }}</div>
                    </div>
                    <div class="tenant">
                        <div class="name">{{ __('marketing.warm_viz4_t2_name') }}</div>
                        <div class="meta">{{ __('marketing.warm_viz4_t2_meta') }}</div>
                    </div>
                    <div class="tenant">
                        <div class="name">{{ __('marketing.warm_viz4_t3_name') }}</div>
                        <div class="meta">{{ __('marketing.warm_viz4_t3_meta') }}</div>
                    </div>
                    <div class="tenant">
                        <div class="name">{!! __('marketing.warm_viz4_t4_name') !!}</div>
                        <div class="meta">{{ __('marketing.warm_viz4_t4_meta') }}</div>
                    </div>
                </div>
            </div>
        </div>
    </section>

    {{-- ─── HOW IT WORKS ─────────────────────────────────────── --}}
    <section class="w-wrap">
        <div class="w-section-head">
            <span class="w-section-eyebrow">{{ __('marketing.warm_how_label') }}</span>
            <h2>{{ __('marketing.warm_how_h2_lead') }} <em>{{ __('marketing.warm_how_h2_em1') }}</em> {{ __('marketing.warm_how_h2_to') }} <em>{{ __('marketing.warm_how_h2_em2') }}</em> {{ __('marketing.warm_how_h2_tail') }}</h2>
        </div>

        <div class="w-steps">
            <div class="w-step">
                <div class="num">i.</div>
                <h4>{!! __('marketing.warm_how_step1_title') !!}</h4>
                <p>{{ __('marketing.warm_how_step1_body_pre') }} <code>/install.php</code>{{ __('marketing.warm_how_step1_body_post') }}</p>
            </div>
            <div class="w-step">
                <div class="num">ii.</div>
                <h4>{{ __('marketing.warm_how_step2_title') }}</h4>
                <p>{{ __('marketing.warm_how_step2_body') }}</p>
            </div>
            <div class="w-step">
                <div class="num">iii.</div>
                <h4>{{ __('marketing.warm_how_step3_title') }}</h4>
                <p>{{ __('marketing.warm_how_step3_body') }}</p>
            </div>
        </div>
    </section>

    {{-- ─── PRICING ─────────────────────────────────────────── --}}
    @if($plans && count($plans) > 0)
    <section class="w-wrap">
        <div class="w-section-head">
            <span class="w-section-eyebrow">{{ __('marketing.warm_pricing_label') }}</span>
            <h2>{{ $pricingHeadline }} <em>{{ __('marketing.warm_pricing_h2_tail') }}</em></h2>
            <p>{{ $pricingSubtext }}</p>
        </div>

        <div class="w-pricing">
            @foreach($plans as $key => $plan)
                @php $pop = $plan['highlight'] ?? false; @endphp
                <div class="w-plan {{ $pop ? 'popular' : '' }}">
                    @if($pop)<span class="ribbon">{{ __('marketing.warm_pricing_most_popular') }}</span>@endif
                    <h3>{{ $plan['name'] }}</h3>
                    <p class="desc">{{ \Illuminate\Support\Str::limit($plan['description'] ?? '', 140) }}</p>
                    @php
                        // Translator-first interval label so "/month" respects tenant locale.
                        $wmIntervalSlug = $plan['interval'] ?? 'month';
                        $wmKey          = 'marketing.interval_' . $wmIntervalSlug;
                        $wmTrans        = __($wmKey);
                        $wmIntervalLbl  = (is_string($wmTrans) && $wmTrans !== $wmKey)
                            ? $wmTrans
                            : __('marketing.warm_pricing_per');
                    @endphp
                    <div class="price-row">
                        <span class="price">{{ \App\Support\Currency::format((float) $plan['price'], $plan['currency'] ?? \App\Support\Currency::default()) }}</span>
                        <span class="price-unit">/ {{ $wmIntervalLbl }}</span>
                    </div>
                    <ul>
                        @php $feats = array_filter($plan['features'] ?? [], fn ($v) => (bool) $v); $i = 0; @endphp
                        @foreach($feats as $feat => $on)
                            @if($i++ >= 6) @break @endif
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
                        @endforeach
                    </ul>
                    @if($canSignUp)
                        @php $warmIsPaid = ((float) ($plan['price'] ?? 0)) > 0; @endphp
                        <a href="/register?plan={{ $key }}" class="w-btn {{ $pop ? 'w-btn-fill' : 'w-btn-ghost' }}">
                            {{ $warmIsPaid ? __('marketing.warm_pricing_choose_plan') : __('marketing.warm_pricing_start_trial') }}<span class="arrow">→</span>
                        </a>
                    @else
                        <a href="/admin/login" class="w-btn w-btn-ghost">{{ __('marketing.warm_pricing_signin') }}</a>
                    @endif
                </div>
            @endforeach
        </div>
    </section>
    @endif

    {{-- ─── CLOSING CTA ────────────────────────────────────── --}}
    <section class="w-wrap">
        <div class="w-cta">
            <span class="w-section-eyebrow">{{ __('marketing.warm_cta_label') }}</span>
            <h2 class="w-cta-headline">{{ $ctaHeadline }}</h2>
            <p>{{ $ctaSubtext }}</p>
            @if($canSignUp)
                <a href="/register" class="w-btn w-btn-fill">{{ __('marketing.warm_cta_create') }}<span class="arrow">→</span></a>
            @else
                <a href="/admin/login" class="w-btn w-btn-fill">{{ __('marketing.warm_signin') }}<span class="arrow">→</span></a>
            @endif
        </div>
    </section>
</div>
@endsection
