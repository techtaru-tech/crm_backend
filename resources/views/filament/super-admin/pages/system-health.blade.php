<x-filament-panels::page>
    @php
        $sysInfo = $this->getSystemInfo();
        $disk = $this->getDiskUsage();
    @endphp

    <div class="sh-grid">
        {{-- System Information --}}
        <div class="sh-card">
            <div class="sh-card-head">
                <h3 class="sh-card-title">{{ __('filament/sa_system_health.card_system_info') }}</h3>
            </div>
            <div class="sh-info-body">
                @foreach($sysInfo as $label => $value)
                <div class="sh-info-row">
                    <span class="sh-info-label">{{ $label }}</span>
                    <span class="sh-info-value">{{ $value }}</span>
                </div>
                @endforeach
            </div>
        </div>

        {{-- Disk Usage --}}
        <div class="sh-card">
            <div class="sh-card-head">
                <h3 class="sh-card-title">{{ __('filament/sa_system_health.card_disk_usage') }}</h3>
            </div>
            <div class="sh-disk-body">
                <div class="sh-disk-summary">
                    <span class="sh-disk-summary-text">{{ __('filament/sa_system_health.disk_used_of_total', ['used' => $disk['used'], 'total' => $disk['total']]) }}</span>
                    <span class="sh-disk-summary-pct">{{ $disk['pct'] }}%</span>
                </div>
                <div class="sh-disk-track">
                    {{-- Dynamic style retained: width + threshold-based background color are computed from $disk['pct'] each render. --}}
                    <div style="height:100%;width:{{ $disk['pct'] }}%;background:{{ $disk['pct'] > 90 ? '#ef4444' : ($disk['pct'] > 70 ? '#f59e0b' : '#10b981') }};border-radius:4px;"></div>
                </div>
                <div class="sh-stat-row">
                    <div class="sh-stat">
                        <p class="sh-stat-label">{{ __('filament/sa_system_health.stat_label_total') }}</p>
                        <p class="sh-stat-value">{{ $disk['total'] }}</p>
                    </div>
                    <div class="sh-stat">
                        <p class="sh-stat-label">{{ __('filament/sa_system_health.stat_label_used') }}</p>
                        <p class="sh-stat-value">{{ $disk['used'] }}</p>
                    </div>
                    <div class="sh-stat">
                        <p class="sh-stat-label">{{ __('filament/sa_system_health.stat_label_free') }}</p>
                        <p class="sh-stat-value-free">{{ $disk['free'] }}</p>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <link rel="stylesheet" href="{{ asset('css/views/filament/super-admin/pages/system-health.css') }}">
</x-filament-panels::page>
