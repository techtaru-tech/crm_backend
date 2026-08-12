@php
    $headline  = $content['headline']     ?? '';
    $body      = $content['body']         ?? '';
    $btnLabel  = $content['button_label'] ?? null;
    $btnUrl    = $content['button_url']   ?? '#';
    $bg        = $content['background']   ?? 'indigo';

    $bgStyle = match ($bg) {
        'gray'  => 'background:#111827;color:#fff;',
        'white' => 'background:#fff;color:#111827;border-top:1px solid #e5e7eb;',
        default => 'background:linear-gradient(135deg,#4f46e5 0%,#8b5cf6 100%);color:#fff;',
    };
@endphp
{{-- Note: inline style is genuinely DYNAMIC — $bgStyle is computed from
     the tenant's $content['background'] picker (indigo / gray / white).
     All static properties (padding, position, overflow) live in cta.css under
     the .lp-cta rule; only the dynamic background/color/border swap stays inline. --}}
<section class="lp-section lp-cta" style="{{ $bgStyle }}">
<link rel="stylesheet" href="{{ asset('css/views/public/landing/sections/cta.css') }}">
<div class="lp-wrap lp-cta-inner" data-bg="{{ $bg }}">
    @if($headline)<h2>{{ $headline }}</h2>@endif
    @if($body)<p>{{ $body }}</p>@endif
    @if($btnLabel)
        {{-- Fix note: btnUrl is tenant-typed; Blade {{ }}/e() HTML-escape
             but does NOT block javascript: scheme. Gate with UrlSafety. --}}
        <a href="{{ \App\Support\UrlSafety::isSafeExternalUrl($btnUrl) ? $btnUrl : '#' }}" class="lp-cta-btn">
            {{ $btnLabel }}
            <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round"><line x1="5" y1="12" x2="19" y2="12"/><polyline points="12 5 19 12 12 19"/></svg>
        </a>
    @endif
</div>
</section>
