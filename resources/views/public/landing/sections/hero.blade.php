@php
    $eyebrow    = $content['eyebrow']     ?? null;
    $headline   = $content['headline']    ?? '';
    $sub        = $content['subheadline'] ?? '';
    $ctaLabel   = $content['cta_label']   ?? null;
    $ctaUrl     = $content['cta_url']     ?? '#';
    $image      = $content['image_url']   ?? null;
    $alignment  = $content['alignment']   ?? 'center';
    $background = $content['background']  ?? 'gradient';

    // The template below references $safeCtaHref (HTML attr context,
    // line ~54) and $safeImageCssUrl (CSS url() context, line ~18).
    // Both were previously undefined — a tenant who configured a CTA
    // label or an image background would trigger a 500 (production
    // logs: "Undefined variable $safeCtaHref ...").  Compute the
    // sanitised values here so the view never echoes raw editor
    // input into either context.
    //
    //   href:  allow http/https external URLs, anchor links (#foo),
    //          and same-app relative paths (/contact).  Reject
    //          protocol-relative `//evil/x`, `javascript:`, `data:`,
    //          and anything else — fall back to '#' so a broken CTA
    //          renders inert instead of crashing the page.
    //   CSS:   App\Support\UrlSafety::cssSafeUrl applies the strict
    //          http/https allowlist + CSS-special-char strip.  Empty
    //          string when unsafe; the @elseif below short-circuits
    //          on '!=== '' so an unsafe background falls back to the
    //          solid var(--bg) branch instead of breaking the page.
    if (\App\Support\UrlSafety::isSafeExternalUrl($ctaUrl)) {
        $safeCtaHref = $ctaUrl;
    } elseif (is_string($ctaUrl) && $ctaUrl !== '' && ! str_starts_with($ctaUrl, '//')
        && (str_starts_with($ctaUrl, '/') || str_starts_with($ctaUrl, '#'))) {
        $safeCtaHref = $ctaUrl;
    } else {
        $safeCtaHref = '#';
    }
    $safeImageCssUrl = \App\Support\UrlSafety::cssSafeUrl($image);
@endphp
{{-- Note: the inline style="" on this <section> below stays
     inline because its background varies per landing-page section
     record (gradient / per-record image URL / solid var(--bg)).  Same
     applies to the inline <style> block that follows — its values
     depend on $background, $alignment, and $image at render time. --}}
<section class="lp-hero lp-hero-{{ $background }}" style="padding:96px 0 80px;position:relative;overflow:hidden;
    @if($background==='gradient') background:radial-gradient(ellipse 900px 500px at 50% 0%,#eef2ff 0%,var(--bg) 65%);
    @elseif($background==='image' && $safeImageCssUrl !== '') background:linear-gradient(rgba(15,23,42,.55),rgba(15,23,42,.55)),url('{{ $safeImageCssUrl }}') center/cover no-repeat;color:#fff;
    @else background:var(--bg); @endif">
<style>
    .lp-hero::before{
        @if($background==='gradient')
            content:'';position:absolute;top:-200px;left:50%;transform:translateX(-50%);
            width:720px;height:720px;pointer-events:none;
            background:radial-gradient(circle,rgba(79,70,229,.12) 0%,transparent 60%);
        @endif
    }
    .lp-hero-inner{max-width:880px;margin:0 auto;text-align:{{ $alignment === 'left' ? 'left' : 'center' }};position:relative;z-index:1}
    .lp-hero h1{font-size:56px;line-height:1.08;font-weight:800;letter-spacing:-.025em;margin:0 0 22px;
        {{ $background==='gradient' ? 'background:linear-gradient(135deg,#0f172a 0%,#4f46e5 100%);-webkit-background-clip:text;background-clip:text;color:transparent;' : '' }}}
    .lp-hero .lp-hero-lead{font-size:20px;color:{{ $background === 'image' ? 'rgba(255,255,255,.92)' : '#4b5563' }};margin:0 0 34px;max-width:680px;line-height:1.55;{{ $alignment === 'center' ? 'margin-left:auto;margin-right:auto;' : '' }}}
    .lp-hero .lp-eyebrow{display:inline-flex;align-items:center;gap:6px;background:rgba(79,70,229,.1);color:var(--primary);padding:7px 16px;border-radius:100px;font-size:13px;font-weight:600;letter-spacing:.2px;margin-bottom:20px;border:1px solid rgba(79,70,229,.18)}
    .lp-hero-cta{display:flex;gap:12px;{{ $alignment === 'center' ? 'justify-content:center;' : '' }}flex-wrap:wrap}
    .lp-hero .lp-btn{display:inline-flex;align-items:center;gap:8px;background:var(--primary);color:#fff;padding:14px 28px;border-radius:10px;font-weight:600;font-size:15px;text-decoration:none;box-shadow:0 6px 20px rgba(79,70,229,.25);transition:transform .15s,box-shadow .15s}
    .lp-hero .lp-btn:hover{transform:translateY(-2px);box-shadow:0 10px 28px rgba(79,70,229,.35)}
    .lp-hero-trust{margin-top:28px;font-size:13px;color:{{ $background==='image' ? 'rgba(255,255,255,.75)' : '#9ca3af' }};{{ $alignment==='center' ? 'text-align:center;' : '' }}}
    @media(max-width:640px){ .lp-hero h1{font-size:36px} .lp-hero{padding:64px 0 56px} .lp-hero .lp-hero-lead{font-size:17px} }
</style>
<div class="lp-wrap lp-hero-inner">
    @if($eyebrow)<span class="lp-eyebrow">{{ $eyebrow }}</span>@endif
    @if($headline)<h1>{{ $headline }}</h1>@endif
    @if($sub)<p class="lp-hero-lead">{!! nl2br(e($sub)) !!}</p>@endif
    @if($ctaLabel)
        <div class="lp-hero-cta">
            <a href="{{ $safeCtaHref }}" class="lp-btn">
                {{ $ctaLabel }}
                <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true"><line x1="5" y1="12" x2="19" y2="12"/><polyline points="12 5 19 12 12 19"/></svg>
            </a>
        </div>
        <p class="lp-hero-trust">{{ __('marketing.public_hero_trust_line') }}</p>
    @endif
    @if($image && $background !== 'image')
        <div class="pls-hero-illustration">
            <img src="{{ e($image) }}" alt="">
        </div>
    @endif
</div>
{{-- The <section> above keeps its inline style="" because its padding
     and background are driven by the editor-chosen $background and
     $image values.  Static rules (illustration spacing, image
     treatment) are extracted to the linked stylesheet below. --}}
<link rel="stylesheet" href="{{ asset('css/views/public/landing/sections/hero.css') }}">
</section>
