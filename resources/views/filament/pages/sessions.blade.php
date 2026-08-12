<x-filament-panels::page>
    <link rel="stylesheet" href="{{ asset('css/views/filament/pages/sessions.css') }}">
    <x-filament::section :heading="__('filament/sessions.section_heading')">
        <div class="ses-list">
            @forelse($this->getSessions() as $session)
                @php
                    $isCurrent = $session->session_id === request()->session()->getId();
                @endphp
                <div class="ses-row">
                    <div class="ses-row-inner">
                        @if($session->device_type === 'mobile')
                            <svg class="ses-icon" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" d="M10.5 1.5H8.25A2.25 2.25 0 006 3.75v16.5a2.25 2.25 0 002.25 2.25h7.5A2.25 2.25 0 0018 20.25V3.75a2.25 2.25 0 00-2.25-2.25H13.5m-3 0V3h3V1.5m-3 0h3m-3 18.75h3"/></svg>
                        @elseif($session->device_type === 'tablet')
                            <svg class="ses-icon" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" d="M10.5 19.5h3m-6.75 2.25h10.5a2.25 2.25 0 002.25-2.25V4.5a2.25 2.25 0 00-2.25-2.25H6.75A2.25 2.25 0 004.5 4.5v15a2.25 2.25 0 002.25 2.25z"/></svg>
                        @else
                            <svg class="ses-icon" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" d="M9 17.25v1.007a3 3 0 01-.879 2.122L7.5 21h9l-.621-.621A3 3 0 0115 18.257V17.25m6-12V15a2.25 2.25 0 01-2.25 2.25H5.25A2.25 2.25 0 013 15V5.25A2.25 2.25 0 015.25 3h13.5A2.25 2.25 0 0121 5.25z"/></svg>
                        @endif
                        <div>
                            <p class="ses-meta-title">
                                {{ __('filament/sessions.meta_browser_on', ['browser' => $session->browser, 'platform' => $session->platform]) }}
                                @if($isCurrent)
                                    <span class="ses-current-pill">{{ __('filament/sessions.pill_current') }}</span>
                                @endif
                            </p>
                            <p class="ses-meta-sub">
                                {{ __('filament/sessions.meta_last_active', ['ip' => $session->ip_address, 'ago' => $session->last_active_at?->diffForHumans()]) }}
                            </p>
                        </div>
                    </div>
                    @if(!$isCurrent)
                        <x-filament::button
                            wire:click="revokeSession({{ $session->id }})"
                            color="danger"
                            size="sm"
                            wire:confirm="{{ __('filament/sessions.confirm_revoke') }}"
                        >
                            {{ __('filament/sessions.btn_revoke') }}
                        </x-filament::button>
                    @endif
                </div>
            @empty
                <p class="ses-empty">{{ __('filament/sessions.empty_no_sessions') }}</p>
            @endforelse
        </div>
    </x-filament::section>
</x-filament-panels::page>
