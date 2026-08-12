<div
    class="relative"
    x-data="{
        userId: @js((string) auth()->id()),
        initEcho() {
            if (typeof window.Echo === 'undefined') return;
            window.Echo.private('user.' + this.userId + '.notifications')
                .listen('.notification.new', () => {
                    $wire.refreshCount();
                });
        }
    }"
    x-init="initEcho()"
>
    {{-- Bell button --}}
    <button
        wire:click="toggle"
        class="relative flex items-center justify-center w-9 h-9 rounded-lg text-gray-500 dark:text-gray-400 hover:bg-gray-100 dark:hover:bg-white/10 hover:text-gray-700 dark:hover:text-gray-200 transition"
        aria-label="{{ __('notifications.aria_label') }}"
    >
        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 17h5l-1.405-1.405A2.032 2.032 0 0118 14.158V11a6.002 6.002 0 00-4-5.659V5a2 2 0 10-4 0v.341C7.67 6.165 6 8.388 6 11v3.159c0 .538-.214 1.055-.595 1.436L4 17h5m6 0v1a3 3 0 11-6 0v-1m6 0H9"/>
        </svg>
        @if($unreadCount > 0)
        <span class="absolute -top-0.5 -right-0.5 flex items-center justify-center min-w-[1.1rem] h-4.5 px-1 text-[10px] font-bold text-white bg-danger-500 rounded-full leading-none">
            {{ $unreadCount > 99 ? '99+' : $unreadCount }}
        </span>
        @endif
    </button>

    {{-- Slide-over panel --}}
    @if($open)
    <div
        class="absolute right-0 top-12 z-50 w-96 max-w-[calc(100vw-2rem)] bg-white dark:bg-gray-900 border border-gray-200 dark:border-white/10 rounded-xl shadow-xl"
        wire:click.outside="$set('open', false)"
    >
        {{-- Header --}}
        <div class="flex items-center justify-between px-4 py-3 border-b border-gray-100 dark:border-white/10">
            <h3 class="text-sm font-semibold text-gray-900 dark:text-white">{{ __('notifications.panel_title') }}</h3>
            <div class="flex items-center gap-2">
                @if($unreadCount > 0)
                <button wire:click="markAllRead" class="text-xs text-primary-600 dark:text-primary-400 hover:underline">{{ __('notifications.mark_all_read') }}</button>
                @endif
                <button wire:click="$set('open', false)" class="text-gray-400 hover:text-gray-600 dark:hover:text-gray-200">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/></svg>
                </button>
            </div>
        </div>

        {{-- Notification list --}}
        <div class="max-h-96 overflow-y-auto divide-y divide-gray-50 dark:divide-white/5">
            @forelse($notifications as $notification)
            @php
                $notifHref = $notification['lead_id'] ? url('/admin/leads/' . $notification['lead_id']) : null;
                $itemTag   = $notifHref ? 'a' : 'div';
                $icon = match($notification['type']) {
                    'lead_received' => 'heroicon-o-user-plus',
                    'lead_assigned' => 'heroicon-o-user',
                    'lead_stage_changed' => 'heroicon-o-arrow-right',
                    'integration_sync_failed' => 'heroicon-o-exclamation-circle',
                    'export_ready' => 'heroicon-o-arrow-down-tray',
                    'team_mentioned' => 'heroicon-o-at-symbol',
                    default => 'heroicon-o-bell',
                };
                $color = match($notification['type']) {
                    'integration_sync_failed' => 'text-danger-500',
                    'export_ready' => 'text-success-500',
                    default => 'text-primary-500',
                };
            @endphp
            <{{ $itemTag }}
                @if($notifHref) href="{{ $notifHref }}" @endif
                wire:click="markRead('{{ $notification['id'] }}')"
                class="flex items-start gap-3 px-4 py-3 {{ !$notification['read'] ? 'bg-primary-50/40 dark:bg-primary-900/10' : '' }} hover:bg-gray-50 dark:hover:bg-white/5 transition-colors group {{ $notifHref ? 'cursor-pointer' : '' }}"
            >
                <div class="flex-shrink-0 mt-0.5">
                    <x-dynamic-component :component="$icon" class="w-4 h-4 {{ $color }}" />
                </div>
                <div class="flex-1 min-w-0">
                    <p class="text-sm text-gray-800 dark:text-gray-200 leading-snug">
                        {{ $notification['message'] }}
                    </p>
                    <p class="text-xs text-gray-400 dark:text-gray-500 mt-0.5">{{ $notification['timestamp'] }}</p>
                </div>
                <div class="flex items-center gap-1 opacity-0 group-hover:opacity-100 transition-opacity" @click.stop>
                    <button wire:click.stop="dismiss('{{ $notification['id'] }}')" title="{{ __('notifications.dismiss') }}" class="text-gray-400 hover:text-danger-500">
                        <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/></svg>
                    </button>
                </div>
            </{{ $itemTag }}>
            @empty
            <div class="px-4 py-8 text-center text-sm text-gray-400 dark:text-gray-500">
                {{ __('notifications.empty_state') }}
            </div>
            @endforelse
        </div>

        @if($hasMore)
        <div class="px-4 py-3 border-t border-gray-100 dark:border-white/10">
            <button wire:click="loadMore" class="w-full text-xs text-center text-primary-600 dark:text-primary-400 hover:underline">
                {{ __('notifications.load_more') }}
            </button>
        </div>
        @endif
    </div>
    @endif
</div>
