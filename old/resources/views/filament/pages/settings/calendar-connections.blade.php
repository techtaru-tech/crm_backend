<x-filament-panels::page>
    <link rel="stylesheet" href="{{ asset('css/views/filament/pages/settings/calendar-connections.css') }}">

    @if (session('success'))
        <div class="cc-flash cc-flash-success">
            ✅ {{ session('success') }}
        </div>
    @endif

    @if ($errors->has('calendar'))
        <div class="cc-flash cc-flash-error">
            ⚠️ {{ $errors->first('calendar') }}
        </div>
    @endif

    <div class="cc-grid">
        @foreach ($providers as $providerKey => $provider)
            @php $conn = $connections->get($providerKey); @endphp

            <div class="cc-card">
                <div class="cc-card-head">
                    {{-- NOTE: background is DYNAMIC — uses $provider['color'] --}}
                    <div class="cc-card-icon" style="background:{{ $provider['color'] }};">
                        {{ $provider['icon'] }}
                    </div>
                    <div class="cc-card-body">
                        <h3 class="cc-card-title">{{ $provider['label'] }}</h3>
                        @if ($conn)
                            <p class="cc-card-email">{{ $conn->external_account_email }}</p>
                        @endif
                    </div>
                </div>

                @if ($conn)
                    @php
                        $statusColor = match ($conn->status) {
                            'active'       => ['bg' => '#dcfce7', 'fg' => '#14532d', 'border' => '#bbf7d0'],
                            'needs_reauth' => ['bg' => '#fef3c7', 'fg' => '#78350f', 'border' => '#fde68a'],
                            'error'        => ['bg' => '#fee2e2', 'fg' => '#7f1d1d', 'border' => '#fecaca'],
                            'disabled'     => ['bg' => '#f3f4f6', 'fg' => '#374151', 'border' => '#e5e7eb'],
                            default        => ['bg' => '#f3f4f6', 'fg' => '#374151', 'border' => '#e5e7eb'],
                        };
                    @endphp

                    {{-- NOTE: bg/border/color are DYNAMIC — keyed on $conn->status --}}
                    <div class="cc-status" style="background:{{ $statusColor['bg'] }};border:1px solid {{ $statusColor['border'] }};color:{{ $statusColor['fg'] }};">
                        <strong>{{ $statuses[$conn->status] ?? $conn->status }}</strong>
                        @if ($conn->last_synced_at)
                            <div class="cc-status-sync">{{ __('filament/calendar_connections.last_synced', ['when' => $conn->last_synced_at->diffForHumans()]) }}</div>
                        @endif
                        @if ($conn->last_sync_error)
                            <div class="cc-status-err">{{ \Illuminate\Support\Str::limit($conn->last_sync_error, 140) }}</div>
                        @endif
                    </div>

                    <div class="cc-actions">
                        @if ($conn->status === 'needs_reauth')
                            <a href="{{ $provider['connect_url'] }}" class="cc-reconnect">
                                {{ __('filament/calendar_connections.reconnect') }}
                            </a>
                        @endif

                        {{-- `data-confirm` is consumed by public/js/views/shared/confirm-form.js
                             (declarative replacement for the legacy onsubmit handler). --}}
                        <form method="POST" action="/admin/calendar/disconnect/{{ $conn->id }}" class="cc-disconnect-form" data-confirm="{{ __('filament/calendar_connections.confirm_disconnect', ['provider' => $provider['label']]) }}">
                            @csrf
                            <button type="submit" class="cc-disconnect-btn">
                                {{ __('filament/calendar_connections.disconnect') }}
                            </button>
                        </form>
                    </div>
                @else
                    <p class="cc-empty-msg">
                        {{ __('filament/calendar_connections.not_connected_note', ['provider' => $provider['label']]) }}
                    </p>

                    @if ($provider['available'])
                        {{-- NOTE: background is DYNAMIC — uses $provider['color'] --}}
                        <a href="{{ $provider['connect_url'] }}" class="cc-connect-cta" style="background:{{ $provider['color'] }};">
                            {{ __('filament/calendar_connections.connect_prefix') }} {{ $provider['label'] }}
                        </a>
                    @else
                        <button type="button" disabled class="cc-coming-soon">
                            {{ __('filament/calendar_connections.coming_soon') }}
                        </button>
                    @endif
                @endif
            </div>
        @endforeach
    </div>

    <p class="cc-footnote">
        {{ __('filament/calendar_connections.footnote') }}
    </p>

    {{-- Wires up `data-confirm` attributes on disconnect forms above. --}}
    <script src="{{ asset('js/views/shared/confirm-form.js') }}" defer></script>
</x-filament-panels::page>
