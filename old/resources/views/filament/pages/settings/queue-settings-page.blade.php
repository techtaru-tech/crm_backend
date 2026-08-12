<x-filament-panels::page>
    <link rel="stylesheet" href="{{ asset('css/views/filament/pages/settings/queue-settings-page.css') }}">

    @php $info = $this->getQueueInfo(); @endphp

    <div class="fi-section rounded-xl bg-white shadow-sm ring-1 ring-gray-950/5 dark:bg-gray-900 dark:ring-white/10 qs-section">
        <h3 class="qs-title">{{ __('filament/queue_settings.queue_configuration') }}</h3>

        <div class="qs-grid">
            <div>
                <dt class="qs-dt">{{ __('filament/queue_settings.connection') }}</dt>
                <dd class="qs-dd">{{ $info['connection'] }}</dd>
            </div>
            <div>
                <dt class="qs-dt">{{ __('filament/queue_settings.driver') }}</dt>
                <dd class="qs-dd">{{ $info['driver'] }}</dd>
            </div>
        </div>

        @if($info['horizon_installed'])
            <div class="qs-horizon">
                <p class="qs-horizon-lede">{{ __('filament/queue_settings.horizon_lede') }}</p>
                <a href="{{ $info['horizon_url'] }}" target="_blank" rel="noopener noreferrer" class="qs-horizon-link">
                    <svg class="qs-icon-sm" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" d="M13.5 6H5.25A2.25 2.25 0 003 8.25v10.5A2.25 2.25 0 005.25 21h10.5A2.25 2.25 0 0018 18.75V10.5m-10.5 6L21 3m0 0h-5.25M21 3v5.25"/></svg>
                    {{ __('filament/queue_settings.horizon_open_dashboard') }}
                </a>
            </div>
        @endif
    </div>

    {{-- Background workers are an infrastructure concern handled by the
         service operator (the person who installed LeadHub on the server).
         Tenant admins don't configure cron — this page is informational
         only, showing whether the service's queue layer is healthy. --}}
    <div class="qs-info-banner">
        <svg class="qs-info-icon" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.8" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" d="M11.25 11.25l.041-.02a.75.75 0 011.063.852l-.708 2.836a.75.75 0 001.063.853l.041-.021M21 12a9 9 0 11-18 0 9 9 0 0118 0zm-9-3.75h.008v.008H12V8.25z"/></svg>
        <div>
            <p class="qs-info-title">{{ __('filament/queue_settings.operator_notice_title') }}</p>
            <p class="qs-info-body">
                {{ __('filament/queue_settings.operator_notice_body') }}
            </p>
        </div>
    </div>
</x-filament-panels::page>
