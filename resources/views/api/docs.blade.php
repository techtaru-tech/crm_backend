<!DOCTYPE html>
<html lang="{{ app()->getLocale() }}">
<head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />
    @php $appName = config('leadhub.branding.app_name', 'LeadHub'); @endphp
    <title>{{ __('api_docs.page_title', ['app' => $appName]) }}</title>
    <link rel="stylesheet" href="{{ asset('vendor/swagger-ui/swagger-ui.css') }}" />
    <link rel="stylesheet" href="{{ asset('css/views/api/docs.css') }}" />
</head>
{{-- The topbar label rendered into the Swagger UI pseudo-element is the
     only dynamic value on the page, passed as a CSS custom property
     (--api-label).  The label string itself flows through __() so the
     "API v1" suffix is localizable.  Other styles live in
     css/views/api/docs.css. --}}
<body style='--api-label: "{{ __('api_docs.topbar_label', ['app' => $appName]) }}";'>
<div id="swagger-ui"></div>
{{-- OpenAPI spec is exposed as a JSON island consumed by docs.js
     (which is loaded as a static asset with zero Blade interpolation). --}}
<script type="application/json" id="api-spec">{!! $spec !!}</script>
<script src="{{ asset('vendor/swagger-ui/swagger-ui-bundle.js') }}"></script>
<script src="{{ asset('js/views/api/docs.js') }}" defer></script>
</body>
</html>
