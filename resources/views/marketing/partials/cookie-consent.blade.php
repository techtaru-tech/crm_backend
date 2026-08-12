{{-- GDPR / ePrivacy cookie consent banner.
     Displayed on first visit until the user picks Accept or Reject.
     Choice persists in localStorage so it survives page navigation
     and full reloads.

     Decision keys:
       lh_cookie_consent = "granted"  →  hide banner, allow analytics
       lh_cookie_consent = "denied"   →  hide banner, suppress analytics

     Framework integrations: pages that load Plausible / GA / Hotjar /
     etc. should gate their script tags on:
         window.__lhCookieConsent === 'granted'
     and listen for the `lh:cookie-consent` window event to retroactively
     boot trackers when the user accepts. --}}
<link rel="stylesheet" href="{{ asset('css/views/marketing/partials/cookie-consent.css') }}">
<div id="lh-cookie-banner" class="lh-cookie-banner is-hidden" role="dialog" aria-live="polite" aria-label="{{ __('marketing.cookie_dialog_aria') }}">
    <p>
        {{ __('marketing.cookie_message') }}
        <a href="/pages/privacy" rel="noopener">{{ __('marketing.cookie_privacy_link') }}</a>.
    </p>
    <div class="actions">
        <button type="button" class="reject" data-cookie-action="reject">{{ __('marketing.cookie_reject') }}</button>
        <button type="button" class="accept" data-cookie-action="accept">{{ __('marketing.cookie_accept') }}</button>
    </div>
</div>
<script src="{{ asset('js/views/marketing/cookie-consent.js') }}" defer></script>
