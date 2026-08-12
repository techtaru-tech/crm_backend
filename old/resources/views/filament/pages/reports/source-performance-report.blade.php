<x-filament-panels::page>
    <link rel="stylesheet" href="{{ asset('css/views/filament/pages/reports/source-performance-report.css') }}">

    <div class="sp-page">

        {{-- Filters --}}
        <div class="fi-section rounded-xl bg-white shadow-sm ring-1 ring-gray-950/5 dark:bg-gray-900 dark:ring-white/10 sp-section-pad">
            <x-report-date-range
                dateRangeModel="dateRange"
                dateFromModel="dateFrom"
                dateToModel="dateTo"
                :currentRange="$dateRange"
            />
        </div>

        {{-- Bar Chart --}}
        @php $chartData = $this->getChartData(); @endphp
        <div class="fi-section rounded-xl bg-white shadow-sm ring-1 ring-gray-950/5 dark:bg-gray-900 dark:ring-white/10 sp-section-pad">
            <h3 class="sp-section-title-mb">{{ __('filament/reports.lead_volume_by_source') }}</h3>
            <div wire:key="chart-{{ $dateRange }}-{{ $dateFrom }}-{{ $dateTo }}">
                <div x-data="{
                    chart: null,
                    init() {
                        if (this.chart) this.chart.destroy();
                        const ctx = document.getElementById('source-chart').getContext('2d');
                        this.chart = new Chart(ctx, {
                            type: 'bar',
                            data: {
                                labels: @js($chartData['labels']),
                                datasets: [{
                                    label: @js(__('filament/reports.sp_chart_dataset_label')),
                                    data: @js($chartData['data']),
                                    backgroundColor: 'rgba(99,102,241,0.7)',
                                    borderRadius: 4,
                                }]
                            },
                            options: {
                                responsive: true,
                                plugins: { legend: { display: false } },
                                scales: { y: { beginAtZero: true } }
                            }
                        });
                    }
                }">
                    <canvas id="source-chart" height="80"></canvas>
                </div>
            </div>
        </div>

        {{-- Table --}}
        @php $tableData = $this->getTableData(); @endphp
        <div class="fi-section rounded-xl bg-white shadow-sm ring-1 ring-gray-950/5 dark:bg-gray-900 dark:ring-white/10 sp-section-overflow">
            <div class="sp-section-header">
                <h3 class="sp-section-title">{{ __('filament/reports.source_breakdown') }}</h3>
                <p class="sp-section-sub">{{ __('filament/reports.sp_section_sub') }}</p>
            </div>
            <table class="sp-table">
                <thead>
                    <tr>
                        <th>{{ __('filament/reports.sp_source_col') }}</th>
                        <th>{{ __('filament/reports.sp_total_leads_col') }}</th>
                        <th>{{ __('filament/reports.sp_vs_prev_period_col') }}</th>
                        <th>{{ __('filament/reports.sp_converted_col') }}</th>
                        <th>{{ __('filament/reports.sp_conversion_rate_col') }}</th>
                        <th>{{ __('filament/reports.sp_avg_score_col') }}</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($tableData as $row)
                        @php $r = (array) $row; $trend = $r['trend_pct'] ?? null; @endphp
                        <tr>
                            <td class="sp-cell-source">{{ $r['source'] ?? '—' }}</td>
                            <td class="sp-cell-num">{{ number_format($r['total_leads'] ?? 0) }}</td>
                            <td>
                                @if($trend === null)
                                    <span class="sp-cell-dash">—</span>
                                @elseif($trend >= 0)
                                    <span class="sp-trend sp-trend-up">
                                        <svg class="sp-trend-icon" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 10l7-7 7 7"/></svg>
                                        +{{ $trend }}%
                                    </span>
                                @else
                                    <span class="sp-trend sp-trend-down">
                                        <svg class="sp-trend-icon" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 14l-7 7-7-7"/></svg>
                                        {{ $trend }}%
                                    </span>
                                @endif
                            </td>
                            <td class="sp-cell-text">{{ number_format($r['converted'] ?? 0) }}</td>
                            <td>
                                <span class="sp-badge {{ ($r['conversion_rate'] ?? 0) >= 10 ? 'sp-badge-high' : 'sp-badge-low' }}">
                                    {{ $r['conversion_rate'] ?? 0 }}%
                                </span>
                            </td>
                            <td class="sp-cell-text">{{ $r['avg_score'] ?? 0 }}</td>
                        </tr>
                    @empty
                        <tr><td colspan="6" class="sp-cell-empty">{{ __('filament/reports.sp_no_data_in_period') }}</td></tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
</x-filament-panels::page>
