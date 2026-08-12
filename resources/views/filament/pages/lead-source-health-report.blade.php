<x-filament-panels::page>
    <link rel="stylesheet" href="{{ asset('css/views/filament/pages/lead-source-health-report.css') }}">

    @php
        $rows = $this->getRows();
        $statusColor = fn($s) => match ($s) {
            'connected'    => 'sh-green',
            'disconnected' => 'sh-gray',
            'error'        => 'sh-red',
            default        => 'sh-yellow',
        };
    @endphp

    <div class="fi-section rounded-xl bg-white shadow-sm ring-1 ring-gray-950/5 dark:bg-gray-900 dark:ring-white/10 sh-section-overflow">
        <div class="sh-section-header">
            <h3 class="sh-section-title">{{ __('filament/lead_source_health.source_connections') }}</h3>
            <p class="sh-section-sub">{{ __('filament/lead_source_health.section_sub') }}</p>
        </div>

        @if (count($rows) === 0)
            <div class="sh-empty">{{ __('filament/lead_source_health.empty') }}</div>
        @else
            <table class="sh-table">
                <thead>
                    <tr>
                        <th>{{ __('filament/lead_source_health.col_name') }}</th>
                        <th>{{ __('filament/lead_source_health.col_source') }}</th>
                        <th>{{ __('filament/lead_source_health.col_status') }}</th>
                        <th>{{ __('filament/lead_source_health.col_last_received') }}</th>
                        <th class="sh-th-right">{{ __('filament/lead_source_health.col_24h') }}</th>
                        <th class="sh-th-right">{{ __('filament/lead_source_health.col_7d') }}</th>
                        <th class="sh-th-right">{{ __('filament/lead_source_health.col_30d') }}</th>
                        <th>{{ __('filament/lead_source_health.col_success_7d') }}</th>
                        <th>{{ __('filament/lead_source_health.col_last_error') }}</th>
                        <th>{{ __('filament/lead_source_health.col_actions') }}</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach ($rows as $r)
                        <tr>
                            <td class="sh-cell-name">{{ $r['name'] }}</td>
                            <td><span class="sh-badge sh-gray">{{ $r['source_label'] }}</span></td>
                            @php
                                // Translator-first status label so the report respects tenant locale.
                                // Falls back to ucfirst() of the raw key for unknown/legacy statuses.
                                $statusKey        = 'filament/lead_source_health.status_' . $r['status'];
                                $statusTranslated = __($statusKey);
                                $statusLabel      = is_string($statusTranslated) && $statusTranslated !== $statusKey
                                    ? $statusTranslated
                                    : ucfirst((string) $r['status']);
                            @endphp
                            <td><span class="sh-badge {{ $statusColor($r['status']) }}">{{ $statusLabel }}</span></td>
                            <td class="sh-cell-time">{{ $r['last_received_at'] ? $r['last_received_at']->diffForHumans() : __('filament/lead_source_health.never') }}</td>
                            <td class="sh-cell-num">{{ number_format($r['leads_24h']) }}</td>
                            <td class="sh-cell-num">{{ number_format($r['leads_7d']) }}</td>
                            <td class="sh-cell-num">{{ number_format($r['leads_30d']) }}</td>
                            <td>
                                @if ($r['success_rate'] === null)
                                    <span class="sh-cell-nodata">{{ __('filament/lead_source_health.no_data') }}</span>
                                @else
                                    <div class="sh-cell-wrap">
                                        <div class="sh-bar-wrap sh-bar-fixed">
                                            <div class="sh-bar" style="width:{{ $r['success_rate'] }}%;background:{{ $r['success_rate'] >= 90 ? '#10b981' : ($r['success_rate'] >= 70 ? '#f59e0b' : '#ef4444') }}"></div>
                                        </div>
                                        <span class="sh-rate-text">{{ $r['success_rate'] }}%</span>
                                    </div>
                                @endif
                            </td>
                            <td><span class="sh-error">{{ $r['last_error'] ? \Illuminate\Support\Str::limit($r['last_error'], 80) : '—' }}</span></td>
                            <td class="sh-actions">
                                <a href="{{ $this->reconnectUrl($r['id']) }}">{{ __('filament/lead_source_health.action_edit') }}</a>
                            </td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        @endif
    </div>
</x-filament-panels::page>
