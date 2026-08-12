@php
    $oneSignalAppId  = config('leadhub.onesignal.app_id', '');
    // Pre-build the OneSignal config array OUTSIDE the @json() directive.
    // Blade's @json paren-balancer mishandles `?->` chained against `()`
    // method calls inside the array literal, truncating the array early
    // and producing an unclosed `[` in the compiled view. Building the
    // array in a @php block first and passing the resulting variable
    // to @json sidesteps that compiler quirk.
    $oneSignalConfig = $oneSignalAppId ? [
        'appId'    => $oneSignalAppId,
        'userId'   => auth()->id(),
        'tenantId' => auth()->user()?->tenant_id,
        'i18n'     => [
            'banner_enable_message' => __('filament/push_sw.banner_enable_message'),
            'banner_allow_btn'      => __('filament/push_sw.banner_allow_btn'),
        ],
    ] : null;
@endphp

@if($oneSignalAppId)
{{-- OneSignal Web Push SDK: loads from cdn.onesignal.com per OneSignal's
     documented integration. Their service worker registration validates
     the SDK source URL and version against their backend, so self-hosting
     breaks push delivery because the SDK auto-updates from OneSignal's
     CDN and signs subscription requests against the served version.
     Only loads when the operator has configured a OneSignal app ID.

     Styles for the toast banner and its two buttons live in
     public/css/views/filament/push-sw.css and are targeted by id
     (#push-prompt-banner / #push-allow-btn / #push-dismiss-btn).
     The JS below builds the banner DOM with those ids; no inline
     style attributes remain in the Blade view. --}}
<link rel="stylesheet" href="{{ asset('css/views/filament/push-sw.css') }}">
<script src="https://cdn.onesignal.com/sdks/web/v16/OneSignalSDK.page.js" defer></script>
{{-- JSON island: the only dynamic payload for the OneSignal init.
     The matching static loader lives at public/js/views/filament/push-sw.js
     and reads this island via JSON.parse(). Keeping the dynamic values
     in a typed JSON block (instead of inline @js() in an executable
     <script>) keeps the "no inline scripts unless dynamic" rule
     satisfied while still passing tenant context through. --}}
<script type="application/json" id="onesignal-config">
@json($oneSignalConfig)
</script>
<script src="{{ asset('js/views/filament/push-sw.js') }}" defer></script>
@endif
