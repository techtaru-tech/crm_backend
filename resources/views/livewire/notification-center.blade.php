{{-- Styling lives in public/css/views/filament/components/notification-center.css,
     linked from filament/notification-bell.blade.php (a Livewire component may
     only have ONE root element, so the <link> cannot live here).  The panel
     loads only Filament's compiled app.css and there is no app-level Tailwind
     build behind it, so utility classes written here resolve to nothing —
     see the header comment in that stylesheet. --}}
<div
    class="lh-nc"
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
        type="button"
        wire:click="toggle"
        class="lh-nc-bell"
        aria-label="{{ __('notifications.aria_label') }}"
    >
        <svg class="lh-nc-bell-icon" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 17h5l-1.405-1.405A2.032 2.032 0 0118 14.158V11a6.002 6.002 0 00-4-5.659V5a2 2 0 10-4 0v.341C7.67 6.165 6 8.388 6 11v3.159c0 .538-.214 1.055-.595 1.436L4 17h5m6 0v1a3 3 0 11-6 0v-1m6 0H9"/>
        </svg>
        @if($unreadCount > 0)
        <span class="lh-nc-badge">
            {{ $unreadCount > 99 ? '99+' : $unreadCount }}
        </span>
        @endif
    </button>

    {{-- Slide-over panel --}}
    @if($open)
    <div
        class="lh-nc-panel"
        wire:click.outside="$set('open', false)"
    >
        {{-- Header --}}
        <div class="lh-nc-header">
            <h3 class="lh-nc-title">{{ __('notifications.panel_title') }}</h3>
            <div class="lh-nc-header-actions">
                @if($unreadCount > 0)
                <button type="button" wire:click="markAllRead" class="lh-nc-link">{{ __('notifications.mark_all_read') }}</button>
                @endif
                <button type="button" wire:click="$set('open', false)" class="lh-nc-close">
                    <svg class="lh-nc-close-icon" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/></svg>
                </button>
            </div>
        </div>

        {{-- Notification list --}}
        <div class="lh-nc-list">
            @forelse($notifications as $notification)
            @php
                // export_ready rows carry a signed download URL instead of a
                // lead to open, so fall back to it — otherwise the row renders
                // as an inert <div> and the finished file is unreachable.
                $notifHref = $notification['lead_id']
                    ? url('/admin/leads/' . $notification['lead_id'])
                    : ($notification['download_url'] ?? null);
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
                $iconModifier = match($notification['type']) {
                    'integration_sync_failed' => ' lh-nc-icon--danger',
                    'export_ready' => ' lh-nc-icon--success',
                    default => '',
                };
            @endphp
            {{-- Key the rows by notification id.  Rows alternate between <a>
                 (has a lead / download URL) and <div> (has neither), so after a
                 dismiss every row below shifts onto a different tag; without a
                 key Livewire morphs this list by position and has to tear down
                 and rebuild the tail on each change. --}}
            <{{ $itemTag }}
                wire:key="notification-{{ $notification['id'] }}"
                @if($notifHref) href="{{ $notifHref }}" @endif
                wire:click="markRead('{{ $notification['id'] }}')"
                class="lh-nc-item {{ !$notification['read'] ? 'lh-nc-item--unread' : '' }} {{ $notifHref ? 'lh-nc-item--link' : '' }}"
            >
                <div class="lh-nc-item-icon-wrap">
                    <x-dynamic-component :component="$icon" class="lh-nc-icon{{ $iconModifier }}" />
                </div>
                <div class="lh-nc-item-body">
                    <p class="lh-nc-message">
                        {{ $notification['message'] }}
                    </p>
                    <p class="lh-nc-time">{{ $notification['timestamp'] }}</p>
                </div>
                {{-- .prevent as well as .stop: this button sits INSIDE the row,
                     which is an <a href> whenever the notification has a lead or
                     download URL.  .stop only halts propagation — the anchor's
                     default activation still fired, so dismissing a row
                     navigated the browser away instead of deleting it. --}}
                <div class="lh-nc-item-actions" @click.stop.prevent>
                    <button type="button" wire:click.prevent.stop="dismiss('{{ $notification['id'] }}')" title="{{ __('notifications.dismiss') }}" class="lh-nc-dismiss">
                        <svg class="lh-nc-dismiss-icon" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/></svg>
                    </button>
                </div>
            </{{ $itemTag }}>
            @empty
            <div class="lh-nc-empty">
                {{ __('notifications.empty_state') }}
            </div>
            @endforelse
        </div>

        @if($hasMore)
        <div class="lh-nc-footer">
            <button type="button" wire:click="loadMore" class="lh-nc-more">
                {{ __('notifications.load_more') }}
            </button>
        </div>
        @endif
    </div>
    @endif
</div>
