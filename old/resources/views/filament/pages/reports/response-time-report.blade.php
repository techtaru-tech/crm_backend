<x-filament-panels::page>
    <link rel="stylesheet" href="{{ asset('css/filament/response-time-report.css') }}">

    <div class="rt-page">

        {{-- Filters --}}
        <div class="fi-section rounded-xl bg-white shadow-sm ring-1 ring-gray-950/5 dark:bg-gray-900 dark:ring-white/10 rt-section-pad">
            <x-report-date-range
                dateRangeModel="dateRange"
                dateFromModel="dateFrom"
                dateToModel="dateTo"
                :currentRange="$dateRange"
            >
                <div>
                    <label class="rt-label">{{ __('filament/reports.rt_source_label') }}</label>
                    <input
                        type="text"
                        wire:model.live.debounce.400ms="source"
                        placeholder="{{ __('filament/reports.rt_source_placeholder') }}"
                        class="rt-input"
                    >
                </div>
                <div>
                    <label class="rt-label">{{ __('filament/reports.rt_agent_label') }}</label>
                    <select wire:model.live="userId" class="rt-select">
                        <option value="">{{ __('filament/reports.rt_all_agents') }}</option>
                        @foreach($this->getAgents() as $id => $name)
                            <option value="{{ $id }}">{{ $name }}</option>
                        @endforeach
                    </select>
                </div>
            </x-report-date-range>
        </div>

        @php $report = $this->getReportData(); @endphp

        {{-- KPI Cards --}}
        <div class="rt-kpi-grid">
            <div class="fi-section rounded-xl bg-white shadow-sm ring-1 ring-gray-950/5 dark:bg-gray-900 dark:ring-white/10 rt-kpi">
                <p class="rt-kpi-label">{{ __('filament/reports.rt_total_leads_analysed') }}</p>
                <p class="rt-kpi-value">{{ number_format($report['total']) }}</p>
            </div>
            <div class="fi-section rounded-xl bg-white shadow-sm ring-1 ring-gray-950/5 dark:bg-gray-900 dark:ring-white/10 rt-kpi">
                <p class="rt-kpi-label">{{ __('filament/reports.rt_median_response_time') }}</p>
                {{-- duration suffix routed through __() so non-English locales render their own abbreviation. --}}
                <p class="rt-kpi-value-indigo">
                    {{ $report['median'] < 60
                        ? __('filament/reports.duration_minutes_short', ['n' => $report['median']])
                        : __('filament/reports.duration_hours_short', ['n' => round($report['median'] / 60, 1)]) }}
                </p>
            </div>
            <div class="fi-section rounded-xl bg-white shadow-sm ring-1 ring-gray-950/5 dark:bg-gray-900 dark:ring-white/10 rt-kpi">
                <p class="rt-kpi-label">{{ __('filament/reports.rt_p90_label') }}</p>
                <p class="rt-kpi-value-orange">
                    {{ $report['p90'] < 60
                        ? __('filament/reports.duration_minutes_short', ['n' => $report['p90']])
                        : __('filament/reports.duration_hours_short', ['n' => round($report['p90'] / 60, 1)]) }}
                </p>
            </div>
        </div>

        {{-- Active Filters Summary --}}
        @if($source || $userId)
        <div class="fi-section rounded-xl rt-filter-pill">
            <p class="rt-filter-pill-text">
                {{ __('filament/reports.rt_filters_active') }}
                @if($source) <strong>{{ __('filament/reports.rt_filter_source_label') }}</strong> {{ $source }} @endif
                @if($source && $userId) &middot; @endif
                @if($userId) <strong>{{ __('filament/reports.rt_filter_agent_label') }}</strong> {{ $this->getAgents()[$userId] ?? $userId }} @endif
            </p>
        </div>
        @endif

        {{-- Histogram Chart --}}
        <div class="fi-section rounded-xl bg-white shadow-sm ring-1 ring-gray-950/5 dark:bg-gray-900 dark:ring-white/10 rt-section-pad">
            <h3 class="rt-section-title-mb">{{ __('filament/reports.response_time_distribution') }}</h3>
            <div wire:key="hist-{{ $dateRange }}-{{ $dateFrom }}-{{ $dateTo }}-{{ $userId }}-{{ $source }}">
                <div x-data="{
                    chart: null,
                    init() {
                        if (this.chart) this.chart.destroy();
                        const ctx = document.getElementById('response-hist').getContext('2d');
                        this.chart = new Chart(ctx, {
                            type: 'bar',
                            data: {
                                {{-- Chart labels routed through $report['bucket_labels'] so each bar respects tenant locale. --}}
                                labels: @js(array_values($report['bucket_labels'] ?? array_keys($report['buckets']))),
                                datasets: [{
                                    label: @js(__('filament/reports.rt_chart_dataset_label')),
                                    data: @js(array_values($report['buckets'])),
                                    backgroundColor: [
                                        'rgba(16,185,129,0.8)',
                                        'rgba(99,102,241,0.8)',
                                        'rgba(245,158,11,0.8)',
                                        'rgba(249,115,22,0.8)',
                                        'rgba(239,68,68,0.8)',
                                        'rgba(107,114,128,0.8)',
                                    ],
                                    borderRadius: 6,
                                }]
                            },
                            options: {
                                responsive: true,
                                plugins: { legend: { display: false } },
                                scales: { y: { beginAtZero: true, ticks: { stepSize: 1 } } }
                            }
                        });
                    }
                }">
                    <canvas id="response-hist" height="80"></canvas>
                </div>
            </div>
        </div>

        {{-- Bucket Table --}}
        <div class="fi-section rounded-xl bg-white shadow-sm ring-1 ring-gray-950/5 dark:bg-gray-900 dark:ring-white/10 rt-section-overflow">
            <div class="rt-section-header">
                <h3 class="rt-section-title">{{ __('filament/reports.distribution_breakdown') }}</h3>
            </div>
            <table class="rt-table">
                <thead>
                    <tr>
                        <th>{{ __('filament/reports.rt_time_bucket_col') }}</th>
                        <th>{{ __('filament/reports.rt_leads_col') }}</th>
                        <th>{{ __('filament/reports.rt_percent_of_total_col') }}</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($report['buckets'] as $bucket => $count)
                        <tr>
                            {{-- Bucket label routed through $report['bucket_labels'] for tenant locale. --}}
                            <td class="rt-cell-bucket">{{ $report['bucket_labels'][$bucket] ?? $bucket }}</td>
                            <td class="rt-cell-count">{{ number_format($count) }}</td>
                            <td class="rt-cell-percent">{{ $report['total'] > 0 ? round($count / $report['total'] * 100, 1) : 0 }}%</td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    </div>
</x-filament-panels::page>
