@php $appName = config('leadhub.branding.app_name', 'LeadHub'); @endphp
<!DOCTYPE html>
<html lang="{{ app()->getLocale() }}">
<head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />
    <meta name="robots" content="noindex,nofollow" />
    <title>@yield('title', __('portal.default_title')) — {{ $appName }}</title>
    <link rel="icon" href="/favicon.svg" type="image/svg+xml">
    <link rel="stylesheet" href="{{ asset('css/views/portal/_layout.css') }}">
</head>
<body>
    @hasSection('topbar')@yield('topbar')@endif
    <div class="wrap">
        @if(session('success'))<div class="flash-success">{{ session('success') }}</div>@endif
        @if(session('error'))<div class="flash-error">{{ session('error') }}</div>@endif
        @yield('content')
    </div>
</body>
</html>
