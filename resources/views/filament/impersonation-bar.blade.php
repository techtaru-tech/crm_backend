{{--
    Full-width impersonation banner — sticky top, visible on every panel
    (tenant + super admin). Single source of truth for the "you are
    impersonating" state. Replaces the old pair of banners that both
    POSTed to the same stop route.
--}}
@if(session()->has('impersonating_from'))
@php
    $tenantId   = session('impersonating_tenant_id');
    $tenant     = $tenantId ? \App\Models\Tenant::find($tenantId) : null;
    $tenantName = $tenant?->name ?? __('filament/impersonation_bar.tenant_fallback_name');
    $ownerName  = $tenant?->owner?->name ?? null;
    $ownerEmail = $tenant?->owner?->email ?? null;
    $startedAt  = session('impersonating_started_at');
    $startedLabel = $startedAt ? \Carbon\Carbon::parse($startedAt)->diffForHumans() : '';
@endphp
{{-- Inline styles + bar. Uses :has() to push Filament notifications
     BELOW the bar so they're not obscured by it. JS fallback tags
     <body> with `has-impersonation-bar` for browsers that don't
     support :has() yet. --}}
<link rel="stylesheet" href="{{ asset('css/views/filament/impersonation-bar.css') }}">
<script src="{{ asset('js/views/filament/impersonation-bar.js') }}" defer></script>
<div id="impersonation-bar">
    {{-- Warning icon --}}
    <svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" viewBox="0 0 20 20" fill="currentColor" class="imp-icon">
        <path fill-rule="evenodd" d="M8.485 2.495c.673-1.167 2.357-1.167 3.03 0l6.28 10.875c.673 1.167-.168 2.625-1.516 2.625H3.72c-1.347 0-2.189-1.458-1.515-2.625L8.485 2.495zM10 5a.75.75 0 01.75.75v3.5a.75.75 0 01-1.5 0v-3.5A.75.75 0 0110 5zm0 9a1 1 0 100-2 1 1 0 000 2z" clip-rule="evenodd"/>
    </svg>
    <div class="imp-message">
        <strong class="imp-label">{{ __('filament/impersonation_bar.label_active') }}</strong>
        <span class="imp-muted">&nbsp;&middot;&nbsp;</span>
        {{ __('filament/impersonation_bar.viewing_prefix') }} <strong>{{ e($tenantName) }}</strong>@if($ownerName) {{ __('filament/impersonation_bar.as_connector') }} <strong>{{ e($ownerName) }}</strong>@if($ownerEmail) <span class="imp-faint">({{ e($ownerEmail) }})</span>@endif @endif
        @if($startedLabel)<span class="imp-faint">&middot; {{ __('filament/impersonation_bar.started_prefix', ['time' => $startedLabel]) }}</span>@endif
        <span class="imp-faint">&middot; {{ __('filament/impersonation_bar.actions_disclosure') }}</span>
    </div>
    <form method="POST" action="{{ route('impersonation.stop') }}" class="imp-form">
        @csrf
        <button type="submit" class="imp-stop-btn">
            <svg xmlns="http://www.w3.org/2000/svg" width="14" height="14" viewBox="0 0 20 20" fill="currentColor">
                <path fill-rule="evenodd" d="M3 4.25A2.25 2.25 0 015.25 2h5.5A2.25 2.25 0 0113 4.25v2a.75.75 0 01-1.5 0v-2a.75.75 0 00-.75-.75h-5.5a.75.75 0 00-.75.75v11.5c0 .414.336.75.75.75h5.5a.75.75 0 00.75-.75v-2a.75.75 0 011.5 0v2A2.25 2.25 0 0110.75 18h-5.5A2.25 2.25 0 013 15.75V4.25z" clip-rule="evenodd"/>
                <path fill-rule="evenodd" d="M19 10a.75.75 0 00-.75-.75H8.704l1.048-.943a.75.75 0 10-1.004-1.114l-2.5 2.25a.75.75 0 000 1.114l2.5 2.25a.75.75 0 101.004-1.114l-1.048-.943h9.546A.75.75 0 0019 10z" clip-rule="evenodd"/>
            </svg>
            {{ __('filament/impersonation_bar.stop_button') }}
        </button>
    </form>
</div>
@endif
