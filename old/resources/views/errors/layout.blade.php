{{--
    Shared minimal error-page layout, used by errors/{404,403,419,500,503}.
    Self-contained inline CSS so it renders on a fresh install before any
    Vite asset is built and on every host (shared cPanel through to S3).
    Branding pulls from config so a buyer's app_name + primary colour
    surface immediately.
--}}
@php
    $appName  = config('leadhub.branding.app_name', config('app.name', 'LeadHub'));
    $primary  = config('leadhub.branding.primary_color', '#4f46e5');
    $homeUrl  = url('/');
@endphp
<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="robots" content="noindex,nofollow">
    <title>@yield('code') &middot; {{ $appName }}</title>
    <link rel="stylesheet" href="{{ asset('css/views/errors/layout.css') }}">
</head>
{{-- Per-tenant brand color is passed as the --err-primary CSS custom
     property (single dynamic style declaration; everything else is in
     the per-view CSS file). --}}
<body style="--err-primary: {{ $primary }};">
    <main class="card" role="alert">
        <p class="code">{{ __('errors.error_code_prefix', ['code' => '']) }}@yield('code')</p>
        <h1>@yield('heading')</h1>
        <p class="lead">@yield('message')</p>
        <div class="actions">
            <a class="btn btn-primary" href="{{ $homeUrl }}">{{ __('errors.back_to_home') }}</a>
            @hasSection('extra-action')
                @yield('extra-action')
            @endif
        </div>
        <p class="brand">{{ $appName }}</p>
    </main>
</body>
</html>
