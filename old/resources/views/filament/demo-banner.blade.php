{{--
    Live-demo banner shown on both Filament panels (admin + super-admin)
    whenever DEMO_MODE is on.

    Loaded by app/Providers/AppServiceProvider.php via a global
    FilamentView::registerRenderHook on BODY_START.  All English strings
    flow through __() and all styling lives in the dedicated CSS file
    (no inline styles) per CodeCanyon Items 1 + 2.
--}}
@php
    $appName = config('leadhub.branding.app_name', config('app.name', 'LeadHub'));
@endphp
<link rel="stylesheet" href="{{ asset('css/views/filament/demo-banner.css') }}">

<div class="lh-demo-banner">
    <span>{{ __('filament/demo_banner.banner_message', ['app' => $appName]) }}</span>
    <a href="{{ \App\Support\DemoMode::purchaseUrl() }}" target="_blank" rel="noopener" class="lh-demo-banner-cta">
        {{ __('filament/demo_banner.get_app_link', ['app' => $appName]) }}
    </a>
</div>
