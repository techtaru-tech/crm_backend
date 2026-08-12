@php
    $headline = $content['headline']    ?? '';
    $sub      = $content['subheadline'] ?? '';
    $plans    = $content['plans']       ?? [];
@endphp
<section class="lp-section lp-pricing" id="pricing">
<link rel="stylesheet" href="{{ asset('css/views/public/landing/sections/pricing.css') }}">
<div class="lp-wrap">
    @if($headline)<h2>{{ $headline }}</h2>@endif
    @if($sub)<p class="lp-sub">{{ $sub }}</p>@endif
    <div class="lp-plans">
        @foreach($plans as $plan)
            @php
                $highlight = ! empty($plan['highlight']);
                $features  = is_array($plan['features'] ?? null)
                    ? $plan['features']
                    : array_filter(array_map('trim', explode("\n", (string) ($plan['features'] ?? ''))));
            @endphp
            <div class="lp-plan {{ $highlight ? 'lp-plan--highlight' : '' }}">
                @if($highlight)<div class="lp-plan-ribbon">{{ __('marketing.public_pricing_most_popular') }}</div>@endif
                <h3>{{ $plan['name'] ?? '' }}</h3>
                <div class="lp-plan-price">
                    <span class="amount">{{ $plan['price'] ?? '' }}</span>
                    @if(! empty($plan['interval']))<span class="interval">/ {{ $plan['interval'] }}</span>@endif
                </div>
                <ul>
                    @foreach($features as $feature)
                        <li>
                            <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round"><polyline points="20 6 9 17 4 12"/></svg>
                            <span>{{ $feature }}</span>
                        </li>
                    @endforeach
                </ul>
                @if(! empty($plan['cta_label']))
                    {{-- Fix note: per-plan cta_url is tenant-typed; gate
                         with UrlSafety to block javascript: scheme. --}}
                    <a href="{{ \App\Support\UrlSafety::isSafeExternalUrl($plan['cta_url'] ?? null) ? ($plan['cta_url'] ?? '#') : '#' }}" class="lp-plan-cta">{{ $plan['cta_label'] }}</a>
                @endif
            </div>
        @endforeach
    </div>
</div>
</section>
