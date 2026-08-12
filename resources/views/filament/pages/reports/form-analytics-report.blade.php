<x-filament-panels::page>
    {{--
        Static styles live in public/css/views/filament/pages/reports/form-analytics-report.css.
        No dynamic interpolations remain inline.
    --}}
    <link rel="stylesheet" href="{{ asset('css/views/filament/pages/reports/form-analytics-report.css') }}">

    <div class="fa-page">

        {{-- Filters --}}
        <div class="fi-section rounded-xl bg-white shadow-sm ring-1 ring-gray-950/5 dark:bg-gray-900 dark:ring-white/10 fa-section-pad">
            <x-report-date-range
                dateRangeModel="dateRange"
                dateFromModel="dateFrom"
                dateToModel="dateTo"
                :currentRange="$dateRange"
            >
                <div class="fa-summary-pill">
                    {{ __('filament/reports.total_submissions_label', ['count' => number_format($this->getTotalSubmissions())]) }}
                </div>
            </x-report-date-range>
        </div>

        {{-- Submission Trend Chart --}}
        @php $trend = $this->getTrendData(); @endphp
        <div class="fi-section rounded-xl bg-white shadow-sm ring-1 ring-gray-950/5 dark:bg-gray-900 dark:ring-white/10 fa-section-pad">
            <h3 class="fa-section-title">{{ __('filament/reports.submission_trend') }}</h3>
            <div wire:key="trend-{{ $dateRange }}-{{ $dateFrom }}-{{ $dateTo }}">
                <div x-data="{
                    chart: null,
                    init() {
                        if (this.chart) this.chart.destroy();
                        const ctx = document.getElementById('form-trend-chart').getContext('2d');
                        this.chart = new Chart(ctx, {
                            type: 'bar',
                            data: {
                                labels: @js($trend['labels']),
                                datasets: [{
                                    label: @js(__('filament/reports.fa_chart_dataset_label')),
                                    data: @js($trend['data']),
                                    backgroundColor: 'rgba(16,185,129,0.7)',
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
                    <canvas id="form-trend-chart" height="70"></canvas>
                </div>
            </div>
        </div>

        {{-- Form Table --}}
        @php $tableData = $this->getTableData(); @endphp
        <div class="fi-section rounded-xl bg-white shadow-sm ring-1 ring-gray-950/5 dark:bg-gray-900 dark:ring-white/10 fa-section-overflow">
            <div class="fa-section-header">
                <h3 class="fa-section-title-tight">{{ __('filament/reports.top_forms') }}</h3>
            </div>
            <table class="fa-table">
                <thead>
                    <tr>
                        <th>{{ __('filament/reports.fa_form_name_col') }}</th>
                        <th>{{ __('filament/reports.fa_status_col') }}</th>
                        <th>{{ __('filament/reports.fa_submissions_col') }}</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($tableData as $row)
                        <tr>
                            <td class="fa-cell-name">{{ $row['name'] }}</td>
                            <td>
                                @if($row['active'])
                                    <span class="fa-badge fa-badge-active">{{ __('filament/reports.fa_status_active') }}</span>
                                @else
                                    <span class="fa-badge fa-badge-inactive">{{ __('filament/reports.fa_status_inactive') }}</span>
                                @endif
                            </td>
                            <td class="fa-cell-num">{{ number_format($row['submissions']) }}</td>
                        </tr>
                    @empty
                        <tr><td colspan="3" class="fa-cell-empty">{{ __('filament/reports.fa_no_submissions_in_period') }}</td></tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
</x-filament-panels::page>
