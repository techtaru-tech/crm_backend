{{--
    Conversations page — two-panel chat UI.

    Uses fully-inline CSS because custom Filament pages don't trigger
    Tailwind JIT on shared-hosting builds (no asset pipeline).  Icons
    are forced to 16px via SVG width/height attributes so they can't
    render at their native (huge) size when utility classes are absent.
--}}
<x-filament-panels::page>
    <link rel="stylesheet" href="{{ asset('css/filament/conversations.css') }}">

    <div class="lh-conv-root">

        {{-- ── Left panel: leads with messages ─────────────────────── --}}
        <aside class="lh-conv-panel lh-conv-left">
            <div class="lh-conv-head">
                <span>{{ __('filament/conversations.panel_conversations') }}</span>
                @if(count($this->leadsList))
                    <span class="lh-conv-count">{{ count($this->leadsList) }}</span>
                @endif
            </div>
            <div class="lh-conv-list">
                @forelse($this->leadsList as $item)
                    @php
                        $channelIcon = match($item['channel']) {
                            'email'    => 'envelope',
                            'whatsapp' => 'whatsapp',
                            'sms'      => 'phone',
                            'telegram' => 'paper-airplane',
                            'viber'    => 'chat',
                            default    => 'chat',
                        };
                    @endphp
                    <button type="button"
                            wire:key="conv-row-{{ $item['lead_id'] }}"
                            wire:click="selectLead({{ $item['lead_id'] }})"
                            class="lh-conv-row {{ $selectedLeadId === $item['lead_id'] ? 'lh-conv-row--active' : '' }}">
                        <div class="lh-conv-row-top">
                            {{-- Channel icon — fixed 14px so Tailwind absence doesn't explode its size --}}
                            <svg class="lh-conv-channel-icon" width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true">
                                @switch($channelIcon)
                                    @case('envelope')
                                        <rect x="3" y="5" width="18" height="14" rx="2"/><path d="m3 7 9 6 9-6"/>
                                        @break
                                    @case('whatsapp')
                                        <path d="M21 11.5a8.38 8.38 0 0 1-.9 3.8 8.5 8.5 0 0 1-7.6 4.7 8.38 8.38 0 0 1-3.8-.9L3 21l1.9-5.7a8.38 8.38 0 0 1-.9-3.8 8.5 8.5 0 0 1 4.7-7.6 8.38 8.38 0 0 1 3.8-.9h.5a8.48 8.48 0 0 1 8 8v.5z"/>
                                        @break
                                    @case('phone')
                                        <path d="M22 16.92v3a2 2 0 0 1-2.18 2 19.79 19.79 0 0 1-8.63-3.07 19.5 19.5 0 0 1-6-6 19.79 19.79 0 0 1-3.07-8.67A2 2 0 0 1 4.11 2h3a2 2 0 0 1 2 1.72 12.84 12.84 0 0 0 .7 2.81 2 2 0 0 1-.45 2.11L8.09 9.91a16 16 0 0 0 6 6l1.27-1.27a2 2 0 0 1 2.11-.45 12.84 12.84 0 0 0 2.81.7A2 2 0 0 1 22 16.92z"/>
                                        @break
                                    @case('paper-airplane')
                                        <line x1="22" y1="2" x2="11" y2="13"/><polygon points="22 2 15 22 11 13 2 9 22 2"/>
                                        @break
                                    @default
                                        <path d="M21 15a2 2 0 0 1-2 2H7l-4 4V5a2 2 0 0 1 2-2h14a2 2 0 0 1 2 2z"/>
                                @endswitch
                            </svg>
                            <span class="lh-conv-name">{{ $item['name'] }}</span>
                            @if(($item['unread'] ?? 0) > 0)
                                <span class="lh-conv-unread">{{ $item['unread'] }}</span>
                            @endif
                            <span class="lh-conv-time">{{ $item['time_ago'] }}</span>
                        </div>
                        <div class="lh-conv-preview">
                            @if($item['direction'] === 'outbound')<em class="lh-conv-out-marker">{{ __('filament/conversations.out_marker') }}</em>@endif{{ $item['snippet'] }}
                        </div>
                    </button>
                @empty
                    <div class="lh-conv-empty">
                        <svg class="lh-conv-empty-icon" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round"><path d="M21 15a2 2 0 0 1-2 2H7l-4 4V5a2 2 0 0 1 2-2h14a2 2 0 0 1 2 2z"/></svg>
                        <p>{{ __('filament/conversations.empty_no_conversations_p1') }}<br>{{ __('filament/conversations.empty_no_conversations_p2') }}</p>
                    </div>
                @endforelse
            </div>
        </aside>

        {{-- ── Right panel: thread + composer ─────────────────────── --}}
        <section class="lh-conv-panel lh-conv-right">
            @if($this->selectedLead)
                <div class="lh-conv-head">
                    <span>{{ $this->selectedLead->full_name ?: ($this->selectedLead->email ?: __('filament/conversations.lead_prefix') . $this->selectedLead->id) }}</span>
                    <a href="{{ \App\Filament\Resources\LeadResource::getUrl('view', ['record' => $this->selectedLead->id]) }}"
                       class="lh-conv-open-link">
                        {{ __('filament/conversations.open_lead') }}
                    </a>
                </div>

                <div id="lh-chat-scroll" class="lh-msg-scroll">
                    @forelse($this->thread as $msg)
                        @php
                            // Translator-first message-status label. The keys
                            // `filament/leads.message_status_*` already exist for the
                            // admin LeadResource message table — reuse them so this
                            // conversation thread bubble respects tenant locale too.
                            $msgStatusKey   = 'filament/leads.message_status_' . (string) $msg->status;
                            $msgStatusTrans = $msg->status ? __($msgStatusKey) : '';
                            $msgStatusLabel = ($msgStatusTrans !== '' && $msgStatusTrans !== $msgStatusKey)
                                ? (string) $msgStatusTrans
                                : ucfirst((string) $msg->status);
                        @endphp
                        <div class="lh-msg-bubble {{ $msg->direction === 'outbound' ? 'lh-msg-bubble--out' : 'lh-msg-bubble--in' }}">
                            <div>{{ $msg->body ?: ($msg->media_url ? __('filament/conversations.media_label') : '—') }}</div>
                            <div class="lh-msg-meta">
                                {{ strtoupper($msg->channel) }} · {{ $msg->created_at?->diffForHumans() }}
                                @if($msg->direction === 'outbound' && $msg->status) · {{ $msgStatusLabel }}@endif
                            </div>
                        </div>
                    @empty
                        <div class="lh-conv-empty">
                            <p>{{ __('filament/conversations.empty_thread') }}</p>
                        </div>
                    @endforelse
                </div>

                {{-- Composer --}}
                <form class="lh-conv-composer" wire:submit.prevent="sendMessage">
                    <div class="lh-conv-composer-row">
                        @if(count($this->availableChannels) > 1)
                            <select wire:model.live="selectedChannel">
                                @foreach($this->availableChannels as $value => $label)
                                    <option value="{{ $value }}">{{ $label }}</option>
                                @endforeach
                            </select>
                        @elseif(count($this->availableChannels) === 1)
                            <input type="hidden" wire:model="selectedChannel">
                            <span class="lh-conv-channel-hint">
                                {{ __('filament/conversations.compose_via', ['channel' => array_values($this->availableChannels)[0]]) }}
                            </span>
                        @endif
                        <textarea wire:model="newMessage" rows="1" placeholder="{{ __('filament/conversations.compose_placeholder') }}"></textarea>
                        <button type="submit" wire:loading.attr="disabled" wire:target="sendMessage">
                            <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round"><line x1="22" y1="2" x2="11" y2="13"/><polygon points="22 2 15 22 11 13 2 9 22 2"/></svg>
                            <span wire:loading.remove wire:target="sendMessage">{{ __('filament/conversations.compose_send') }}</span>
                            <span wire:loading wire:target="sendMessage">{{ __('filament/conversations.compose_sending') }}</span>
                        </button>
                    </div>
                    @if(count($this->availableChannels) === 0)
                        <p class="lh-conv-warning">
                            {{ __('filament/conversations.warning_no_channel') }}
                            <a href="/admin/messaging-settings-page">{{ __('filament/conversations.warning_no_channel_link') }}</a>
                            {{ __('filament/conversations.warning_no_channel_suffix') }}
                        </p>
                    @endif
                </form>
            @else
                <div class="lh-conv-empty lh-conv-empty-state">
                    <svg class="lh-conv-empty-icon" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round"><path d="M21 15a2 2 0 0 1-2 2H7l-4 4V5a2 2 0 0 1 2-2h14a2 2 0 0 1 2 2z"/></svg>
                    <p class="lh-conv-empty-msg">{{ __('filament/conversations.empty_select_msg') }}</p>
                    <p class="lh-conv-empty-sub">{{ __('filament/conversations.empty_select_sub') }}</p>
                </div>
            @endif
        </section>
    </div>

    <script>
        (function () {
            function scrollBottom() {
                var el = document.getElementById('lh-chat-scroll');
                if (el) el.scrollTop = el.scrollHeight;
            }
            scrollBottom();
            document.addEventListener('livewire:updated', scrollBottom);

            // Real-time updates via Laravel Echo if configured.
            try {
                var tenantId = @json(auth()->user()?->tenant_id);
                if (tenantId && window.Echo && typeof window.Echo.private === 'function' && !window.__lhMessagesSubscribed) {
                    window.__lhMessagesSubscribed = true;
                    window.Echo.private('tenant.' + tenantId + '.messages')
                        .listen('.lead.message.new', function () {
                            if (window.Livewire) window.Livewire.dispatch('$refresh');
                        });
                }
            } catch (e) { /* polling covers it */ }
        })();
    </script>
</x-filament-panels::page>
