{{--
    Shown by EnforceLicense middleware when an install has run out of
    grace and the licensing server is not returning "valid".

    Intentionally renders OUTSIDE Filament's panel layout so a panel-
    level config issue can't lock the operator out of seeing the
    reason.  All copy flows through __() so non-English buyers see
    the localised explanation.
--}}
@php
    /** @var \App\Services\LicenseService $licenseSvc */
    $licenseSvc = app(\App\Services\LicenseService::class);
    $status     = $licenseSvc->getStatus();
    $verdict    = (string) ($status['status']  ?? 'unknown');
    $message    = (string) ($status['message'] ?? '');
    $itemId     = (string) config('leadhub.license.item_id', '');
    $appName    = config('leadhub.branding.app_name', config('app.name', 'LeadHub'));
@endphp
<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}" dir="{{ in_array(app()->getLocale(), ['ar','he','fa','ur']) ? 'rtl' : 'ltr' }}">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="robots" content="noindex, nofollow">
    <title>{{ __('messages.license_required_title', ['app' => $appName]) }}</title>
    <link rel="icon" type="image/svg+xml" href="{{ asset('favicon.svg') }}">
    <link rel="stylesheet" href="{{ asset('vendor/fonts/inter/inter.css') }}">
    <link rel="stylesheet" href="{{ asset('css/views/license-required.css') }}">
</head>
<body>
    <main class="lr-shell">
        <section class="lr-card">
            <div class="lr-icon" aria-hidden="true">
                <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                    <rect x="3" y="11" width="18" height="11" rx="2"/>
                    <path d="M7 11V7a5 5 0 0 1 10 0v4"/>
                </svg>
            </div>

            <h1>{{ __('messages.license_required_heading', ['app' => $appName]) }}</h1>

            <p class="lr-lead">
                {{ __('messages.license_required_lead', ['app' => $appName]) }}
            </p>

            @if($verdict !== 'valid' && $message !== '')
                <div class="lr-reason">
                    <strong>{{ __('messages.license_required_reason_label') }}:</strong>
                    {{ $message }}
                </div>
            @endif

            <ol class="lr-steps">
                <li>{{ __('messages.license_required_step_codecanyon') }}</li>
                <li>{{ __('messages.license_required_step_purchase_code') }}</li>
                <li>{{ __('messages.license_required_step_paste_settings') }}</li>
            </ol>

            <div class="lr-actions">
                <a href="{{ url('/super-admin/settings/license') }}" class="lr-btn lr-btn-primary">
                    {{ __('messages.license_required_cta_settings') }}
                </a>
                <a href="https://codecanyon.net/downloads" target="_blank" rel="noopener" class="lr-btn lr-btn-ghost">
                    {{ __('messages.license_required_cta_codecanyon') }}
                </a>
            </div>

            @if($itemId !== '')
                <p class="lr-meta">
                    {{ __('messages.license_required_item_label') }}:
                    <span class="lr-meta-strong">#{{ $itemId }}</span>
                </p>
            @endif
        </section>
    </main>
</body>
</html>
