@extends('marketing.layout')

@php
    // Resolve translated values (English is the fallback).
    $pTitle   = $page->t('title');
    $pExcerpt = $page->t('excerpt');
    $pMeta    = $page->t('meta_description');
    $pBody    = $page->t('content_html');

    // Hardening: $pBody is SuperAdmin-authored TinyMCE HTML
    // rendered on a public-internet page (no auth gate, every visitor
    // sees it).  Trust boundary is "SA-only", BUT a stored-XSS via a
    // compromised SA account would reach every site visitor.  Apply the
    // same multi-layer regex sanitizer that landing/sections/html.blade
    // uses so an SA-pasted <iframe src=javascript:>/<svg onload=>/etc.
    // is defanged even if HTMLPurifier isn't a dependency yet.
    if (is_string($pBody) && $pBody !== '') {
        for ($pass = 0; $pass < 2; $pass++) {
            $pBody = preg_replace('#<script\b[^>]*>.*?</script>#is', '', $pBody);
            $pBody = preg_replace('#<script\b[^>]*/?>#is', '', $pBody);
        }
        $dangerousTags = '(?:iframe|frame|frameset|object|embed|applet|base|meta|form|input|button|svg|math|template|portal|xmp|plaintext|noembed|noscript)';
        for ($pass = 0; $pass < 2; $pass++) {
            $pBody = preg_replace('#<' . $dangerousTags . '\b[^>]*>.*?</' . $dangerousTags . '>#is', '', $pBody);
            $pBody = preg_replace('#<' . $dangerousTags . '\b[^>]*/?>#is', '', $pBody);
        }
        $pBody = preg_replace('#\son[a-z]+\s*=\s*"[^"]*"#i', '', $pBody);
        $pBody = preg_replace("#\son[a-z]+\s*=\s*'[^']*'#i", '', $pBody);
        $pBody = preg_replace('#\son[a-z]+\s*=\s*[^\s>]+#i', '', $pBody);
        $pBody = preg_replace(
            '#\s+(href|src|action|formaction|background|poster|cite|longdesc|srcset|data|manifest|ping|archive)\s*=\s*("|\')?\s*(?:javascript|vbscript|data|file)\s*:#i',
            ' data-blocked-uri-$1=$2',
            $pBody
        );
    }
@endphp

@section('title', $pTitle . ' — ' . $appName)
@section('description', $pMeta ?: ($pExcerpt ?: $pTitle))

@push('head')
<link rel="stylesheet" href="{{ asset('css/views/public/static-page/show.css') }}">
@endpush

@section('content')
<article class="sp-shell">
    <header class="sp-header">
        <span class="sp-eyebrow">{{ $appName }}</span>
        <h1 class="sp-title">{{ $pTitle }}</h1>
        @if($pExcerpt !== '')
            <p class="sp-excerpt">{{ $pExcerpt }}</p>
        @endif
    </header>

    <div class="sp-body">
        {!! $pBody !!}
    </div>

    <p class="sp-updated">{{ __('marketing.public_static_last_updated') }} {{ $page->updated_at?->translatedFormat('M j, Y') }}</p>
</article>
@endsection
