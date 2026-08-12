<x-filament-panels::page>
    <link rel="stylesheet" href="{{ asset('css/views/filament/resources/outbound-webhook-resource/pages/webhook-delivery-log.css') }}">

    <div class="fi-section rounded-xl bg-white shadow-sm ring-1 ring-gray-950/5 dark:bg-gray-900 dark:ring-white/10 wdl-card">
        <div class="wdl-header">
            <h3 class="wdl-title">{{ __('filament/outbound_webhooks.webhook_title_prefix', ['name' => $this->record->name]) }}</h3>
            <p class="wdl-url">{{ $this->record->url }}</p>
        </div>

        @php
            $total   = $this->record->deliveries()->count();
            $success = $this->record->deliveries()->where('status', 'success')->count();
            $failed  = $this->record->deliveries()->where('status', 'failed')->count();
        @endphp

        <div class="wdl-stats">
            <div class="wdl-stat wdl-stat-total">
                <p class="wdl-stat-num">{{ number_format($total) }}</p>
                <p class="wdl-stat-label">{{ __('filament/outbound_webhooks.stat_total_deliveries') }}</p>
            </div>
            <div class="wdl-stat wdl-stat-success">
                <p class="wdl-stat-num-success">{{ number_format($success) }}</p>
                <p class="wdl-stat-label">{{ __('filament/outbound_webhooks.stat_successful') }}</p>
            </div>
            <div class="wdl-stat wdl-stat-failed">
                <p class="wdl-stat-num-failed">{{ number_format($failed) }}</p>
                <p class="wdl-stat-label">{{ __('filament/outbound_webhooks.stat_failed') }}</p>
            </div>
        </div>
    </div>

    {{ $this->table }}
</x-filament-panels::page>
