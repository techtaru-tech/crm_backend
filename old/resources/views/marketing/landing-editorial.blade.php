@extends('marketing.layout')

@section('title', $appName . ' — ' . ($content->t('hero_headline') ?: __('marketing.editorial.page_title')))
@section('description', $content->t('hero_subtext') ?: __('marketing.editorial.page_description'))

@push('head')
{{-- Fonts: Inter is provided by the parent marketing.layout via a
     self-hosted <link> (no CDN).  Inter Tight and JetBrains Mono were
     previously loaded from Google Fonts but have been removed for
     CodeCanyon compliance — the typography CSS below falls back to
     Inter / system-ui / monospace stacks. --}}
<link rel="stylesheet" href="{{ asset('css/marketing/landing-editorial.css') }}">
@endpush

@section('content')
@php
    $heroEyebrow      = $content->t('hero_eyebrow') ?: __('marketing.editorial.hero_eyebrow') . ' · v' . config('leadhub.version', '1.0.0');
    $heroHeadline     = $content->t('hero_headline') ?: __('marketing.editorial.hero_headline');
    $heroHighlight    = $content->t('hero_headline_highlight') ?: __('marketing.editorial.hero_highlight');
    $heroSubtext      = $content->t('hero_subtext') ?: __('marketing.editorial.hero_subtext');
    $heroCtaLabel     = $content->t('hero_cta_label') ?: __('marketing.editorial.hero_cta', ['days' => $trialDays]);
    $heroNote         = $content->t('hero_note') ?: __('marketing.editorial.hero_note');

    $featuresHeadline = $content->t('features_headline') ?: __('marketing.editorial.features_headline');
    $featuresSubtext  = $content->t('features_subtext')  ?: __('marketing.editorial_features_subtext');

    $pricingHeadline = $content->t('pricing_headline') ?: __('marketing.editorial.pricing_headline');
    $pricingSubtext  = $content->t('pricing_subtext')  ?: __('marketing.editorial_pricing_subtext');

    $ctaHeadline = $content->t('cta_headline') ?: __('marketing.editorial.cta_headline');
    $ctaSubtext  = $content->t('cta_subtext')  ?: __('marketing.editorial_cta_subtext');
@endphp

<div class="ed-shell">

    {{-- ─── HERO ──────────────────────────────────────────── --}}
    <section class="ed-hero">
        <div class="ed-wrap">
            <div class="ed-eyebrow">
                <span class="dot"></span>
                {{ $heroEyebrow }}
            </div>
            <h1>
                {{ $heroHeadline }}
                <br>
                <span class="soft">{{ $heroHighlight }}</span>
            </h1>
            <p class="ed-sub">{{ $heroSubtext }}</p>
            <div class="ed-cta-row">
                @if($canSignUp)
                    <a href="/register" class="ed-btn ed-btn-fill">
                        {{ $heroCtaLabel }}
                        <span class="arrow">→</span>
                    </a>
                @else
                    <a href="/admin/login" class="ed-btn ed-btn-fill">{{ __('marketing.editorial_signin') }}<span class="arrow">→</span></a>
                @endif
                <a href="/pricing" class="ed-btn ed-btn-ghost">{{ __('marketing.editorial_see_pricing') }}</a>
            </div>

            <div class="ed-trust">
                <span>{{ $heroNote }}</span>
            </div>
        </div>
    </section>

    {{-- ─── BENTO FEATURE GRID ──────────────────────────── --}}
    <section class="ed-wrap">
        <div class="ed-section-head">
            <span class="ed-section-mono">{{ __('marketing.editorial_capabilities_label') }}</span>
            <h2>{{ $featuresHeadline }}</h2>
            <p>{{ $featuresSubtext }}</p>
        </div>

        <div class="ed-bento" id="ed-bento">

            {{-- Cell 1: wide — Lead capture --}}
            <article class="ed-cell ed-cell-wide js-reactive">
                <span class="ed-cell-num">01</span>
                <div>
                    <h3 class="ed-cell-title">{{ __('marketing.editorial_cell1_title') }}</h3>
                    <p class="ed-cell-desc">{{ __('marketing.editorial_cell1_desc') }}</p>
                </div>
                <div class="ed-viz ed-viz-sources">
                    {{-- Brand-name pills (Facebook Ads, Google Ads, TikTok,
                         Zapier) and protocol tokens (Webhooks, IMAP) stay
                         literal across locales — international users
                         recognise them. The two category labels (Forms,
                         Widgets) route through __() so translators can
                         localise them. --}}
                    <span>Facebook Ads</span>
                    <span>Google Ads</span>
                    <span>TikTok</span>
                    <span>Webhooks</span>
                    <span>IMAP</span>
                    <span>{{ __('marketing.editorial_cell1_source_forms') }}</span>
                    <span>{{ __('marketing.editorial_cell1_source_widgets') }}</span>
                    <span>Zapier</span>
                    <span>{{ __('marketing.editorial_cell1_more') }}</span>
                </div>
            </article>

            {{-- Cell 2: regular — AI scoring --}}
            <article class="ed-cell ed-cell-regular js-reactive">
                <span class="ed-cell-num">02</span>
                <div>
                    <h3 class="ed-cell-title">{{ __('marketing.editorial_cell2_title') }}</h3>
                    <p class="ed-cell-desc">{{ __('marketing.editorial_cell2_desc') }}</p>
                </div>
                <div class="ed-viz ed-viz-score">
                    <div class="bar"><span>{{ __('marketing.editorial_cell2_engagement') }}</span><div class="track"><div class="fill is-engagement"></div></div><span>72</span></div>
                    <div class="bar"><span>{{ __('marketing.editorial_cell2_recency') }}</span><div class="track"><div class="fill is-recency"></div></div><span>48</span></div>
                    <div class="bar"><span>{{ __('marketing.editorial_cell2_fit') }}</span><div class="track"><div class="fill is-fit"></div></div><span>86</span></div>
                </div>
            </article>

            {{-- Cell 3: regular — Automations --}}
            <article class="ed-cell ed-cell-regular js-reactive">
                <span class="ed-cell-num">03</span>
                <div>
                    <h3 class="ed-cell-title">{{ __('marketing.editorial_cell3_title') }}</h3>
                    <p class="ed-cell-desc">{{ __('marketing.editorial_cell3_desc') }}</p>
                </div>
                <div class="ed-viz ed-viz-flow">
                    <span class="node">{{ __('marketing.editorial_cell3_node1') }}</span>
                    <span class="arrow">→</span>
                    <span class="node">{{ __('marketing.editorial_cell3_node2') }}</span>
                    <span class="arrow">→</span>
                    <span class="node">{{ __('marketing.editorial_cell3_node3') }}</span>
                </div>
            </article>

            {{-- Cell 4: half — Pipelines --}}
            <article class="ed-cell ed-cell-half js-reactive">
                <span class="ed-cell-num">04</span>
                <div>
                    <h3 class="ed-cell-title">{{ __('marketing.editorial_cell4_title') }}</h3>
                    <p class="ed-cell-desc">{{ __('marketing.editorial_cell4_desc') }}</p>
                </div>
                <div class="ed-viz ed-viz-kanban">
                    <div class="col">
                        <span class="label">{{ __('marketing.editorial_cell4_col_new') }}</span>
                        <div class="card">Acme Corp</div>
                        <div class="card">Jane D.</div>
                    </div>
                    <div class="col">
                        <span class="label">{{ __('marketing.editorial_cell4_col_qualified') }}</span>
                        <div class="card">Stark Inc</div>
                    </div>
                    <div class="col">
                        <span class="label">{{ __('marketing.editorial_cell4_col_won') }}</span>
                        <div class="card">Globex</div>
                        <div class="card">Wayne</div>
                    </div>
                </div>
            </article>

            {{-- Cell 5: half — Multi-tenant --}}
            <article class="ed-cell ed-cell-half js-reactive">
                <span class="ed-cell-num">05</span>
                <div>
                    <h3 class="ed-cell-title">{{ __('marketing.editorial_cell5_title') }}</h3>
                    <p class="ed-cell-desc">{{ __('marketing.editorial_cell5_desc') }}</p>
                </div>
                <div class="ed-viz ed-viz-tenants">
                    <div class="node">A</div>
                    <div class="node">B</div>
                    <div class="node">C</div>
                    <div class="node is-overflow">+n</div>
                </div>
            </article>

            {{-- Cell 6: regular — Zip install --}}
            <article class="ed-cell ed-cell-regular js-reactive">
                <span class="ed-cell-num">06</span>
                <div>
                    <h3 class="ed-cell-title">{{ __('marketing.editorial_cell6_title') }}</h3>
                    <p class="ed-cell-desc">{!! __('marketing.editorial_cell6_desc') !!}</p>
                </div>
                <div class="ed-viz ed-viz-zip">
                    <span class="prompt">$</span> unzip leadhub.zip<br>
                    <span class="prompt">&rarr;</span> browse /install.php<br>
                    <span class="prompt">✓</span> {{ __('marketing.editorial_cell6_ready') }}
                </div>
            </article>

            {{-- Cell 7: regular — GDPR --}}
            <article class="ed-cell ed-cell-regular js-reactive">
                <span class="ed-cell-num">07</span>
                <div>
                    <h3 class="ed-cell-title">{{ __('marketing.editorial_cell7_title') }}</h3>
                    <p class="ed-cell-desc">{{ __('marketing.editorial_cell7_desc') }}</p>
                </div>
            </article>

            {{-- Cell 8: regular — API + webhooks --}}
            <article class="ed-cell ed-cell-regular js-reactive">
                <span class="ed-cell-num">08</span>
                <div>
                    <h3 class="ed-cell-title">{{ __('marketing.editorial_cell8_title') }}</h3>
                    <p class="ed-cell-desc">{{ __('marketing.editorial_cell8_desc') }}</p>
                </div>
            </article>

            {{-- Cell 9: regular — Quote-to-invoice --}}
            <article class="ed-cell ed-cell-regular js-reactive">
                <span class="ed-cell-num">09</span>
                <div>
                    <h3 class="ed-cell-title">{{ __('marketing.editorial_cell9_title') }}</h3>
                    <p class="ed-cell-desc">{{ __('marketing.editorial_cell9_desc') }}</p>
                </div>
            </article>
        </div>
    </section>

    {{-- ─── HOW IT WORKS (three tight steps) ──────────────── --}}
    <section class="ed-wrap">
        <div class="ed-section-head">
            <span class="ed-section-mono">{{ __('marketing.editorial_workflow_label') }}</span>
            <h2>{{ __('marketing.editorial_workflow_h2_lead') }} <span class="soft">{{ __('marketing.editorial_workflow_h2_tail') }}</span></h2>
        </div>

        <div class="ed-steps">
            <div class="ed-step">
                <div class="num">{{ __('marketing.editorial_step1_num') }}</div>
                <h3>{{ __('marketing.editorial_step1_title') }}</h3>
                <p>{!! __('marketing.editorial_step1_body') !!}</p>
            </div>
            <div class="ed-step">
                <div class="num">{{ __('marketing.editorial_step2_num') }}</div>
                <h3>{{ __('marketing.editorial_step2_title') }}</h3>
                <p>{!! __('marketing.editorial_step2_body') !!}</p>
            </div>
            <div class="ed-step">
                <div class="num">{{ __('marketing.editorial_step3_num') }}</div>
                <h3>{{ __('marketing.editorial_step3_title') }}</h3>
                <p>{{ __('marketing.editorial_step3_body') }}</p>
            </div>
        </div>
    </section>

    {{-- ─── PRICING ─────────────────────────────────────────── --}}
    @if($plans && count($plans) > 0)
    <section class="ed-wrap">
        <div class="ed-section-head">
            <span class="ed-section-mono">{{ __('marketing.editorial_pricing_label') }}</span>
            <h2>{{ $pricingHeadline }}</h2>
            <p>{{ $pricingSubtext }}</p>
        </div>

        <div class="ed-pricing">
            @foreach($plans as $key => $plan)
                @php $pop = $plan['highlight'] ?? false; @endphp
                <div class="ed-plan js-reactive {{ $pop ? 'popular' : '' }}">
                    @if($pop)<span class="ribbon">{{ __('marketing.editorial_pricing_most_popular') }}</span>@endif
                    <h3>{{ $plan['name'] }}</h3>
                    <p class="desc">{{ \Illuminate\Support\Str::limit($plan['description'] ?? '', 120) }}</p>
                    @php
                        // Translator-first interval label so "/month" respects tenant locale.
                        $edIntervalSlug = $plan['interval'] ?? 'month';
                        $edKey          = 'marketing.interval_' . $edIntervalSlug;
                        $edTrans        = __($edKey);
                        $edIntervalLbl  = (is_string($edTrans) && $edTrans !== $edKey)
                            ? $edTrans
                            : __('marketing.editorial_pricing_per');
                    @endphp
                    <div class="price-row">
                        <span class="price">{{ \App\Support\Currency::format((float) $plan['price'], $plan['currency'] ?? \App\Support\Currency::default()) }}</span>
                        <span class="price-unit">/ {{ $edIntervalLbl }}</span>
                    </div>
                    <ul>
                        @php $feats = array_filter($plan['features'] ?? [], fn ($v) => (bool) $v); $i = 0; @endphp
                        @foreach($feats as $feat => $on)
                            @if($i++ >= 6) @break @endif
                            @php
                                // Translator-first feature label so the marketing landing
                                // respects locale. Reuses the billing_portal feature keys
                                // so the marketing and admin sides stay in sync.
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
                        @php $edIsPaid = ((float) ($plan['price'] ?? 0)) > 0; @endphp
                        <a href="/register?plan={{ $key }}" class="ed-btn {{ $pop ? 'ed-btn-fill' : 'ed-btn-ghost' }}">
                            {{ $edIsPaid ? __('marketing.editorial_pricing_choose_plan') : __('marketing.editorial_pricing_start_trial') }}<span class="arrow">→</span>
                        </a>
                    @else
                        <a href="/admin/login" class="ed-btn ed-btn-ghost">{{ __('marketing.editorial_pricing_signin') }}</a>
                    @endif
                </div>
            @endforeach
        </div>
    </section>
    @endif

    {{-- ─── CLOSING CTA ────────────────────────────────────── --}}
    <section class="ed-wrap">
        <div class="ed-cta">
            <span class="ed-section-mono">{{ __('marketing.editorial_cta_label') }}</span>
            <h2 class="ed-cta-headline">{{ $ctaHeadline }}</h2>
            <p>{{ $ctaSubtext }}</p>
            @if($canSignUp)
                <a href="/register" class="ed-btn ed-btn-fill">{{ __('marketing.editorial_cta_create') }}<span class="arrow">→</span></a>
            @else
                <a href="/admin/login" class="ed-btn ed-btn-fill">{{ __('marketing.editorial_signin') }}<span class="arrow">→</span></a>
            @endif
        </div>
    </section>

</div>

{{-- Pointer-tracked signature interaction. Opt-out on touch +
     reduced motion.  Extracted to external JS per CodeCanyon's
     "no inline scripts" rule (script body had no Blade interpolation). --}}
<script src="{{ asset('js/views/marketing/landing-editorial.js') }}" defer></script>
@endsection
