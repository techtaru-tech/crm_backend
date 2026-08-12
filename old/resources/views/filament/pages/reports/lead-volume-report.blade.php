<x-filament-panels::page>
    <link rel="stylesheet" href="{{ asset('css/views/filament/pages/reports/lead-volume-report.css') }}">

    <div class="lv-page">

        {{-- Filters --}}
        <div class="fi-section rounded-xl bg-white shadow-sm ring-1 ring-gray-950/5 dark:bg-gray-900 dark:ring-white/10 lv-section-pad">
            <x-report-date-range
                dateRangeModel="dateRange"
                dateFromModel="dateFrom"
                dateToModel="dateTo"
                :currentRange="$dateRange"
            >
                <div>
                    <label class="lv-label">{{ __('filament/reports.lv_source_label') }}</label>
                    <input
                        type="text"
                        wire:model.live.debounce.400ms="source"
                        placeholder="{{ __('filament/reports.lv_source_placeholder') }}"
                        class="lv-input"
                    >
                </div>
                <div>
                    <label class="lv-label">{{ __('filament/reports.lv_group_by_label') }}</label>
                    <select wire:model.live="groupBy" class="lv-select">
                        <option value="day">{{ __('filament/reports.lv_group_by_day') }}</option>
                        <option value="week">{{ __('filament/reports.lv_group_by_week') }}</option>
                        <option value="month">{{ __('filament/reports.lv_group_by_month') }}</option>
                    </select>
                </div>
                <div class="lv-total-pill">
                    {{ __('filament/reports.lv_total_pill', ['count' => number_format($this->getTotalLeads())]) }}
                </div>
            </x-report-date-range>
        </div>

        {{-- Chart --}}
        @php
            $chartData   = $this->getChartData();
            $chartIsland = [
                'labels'     => $chartData['labels'] ?? [],
                'data'       => $chartData['data']   ?? [],
                'chartLabel' => __('filament/reports.lv_chart_dataset_label'),
            ];
        @endphp
        <div class="fi-section rounded-xl bg-white shadow-sm ring-1 ring-gray-950/5 dark:bg-gray-900 dark:ring-white/10 lv-section-pad">
            <h3 class="lv-section-title-mb">
                {{ __('filament/reports.lv_leads_over_time') }}
                @if($source) <span class="lv-section-sub">{{ __('filament/reports.lv_source_separator', ['source' => $source]) }}</span> @endif
            </h3>
            {{-- wire:key on the outer wrapper so a filter change forces
                 Livewire to replace the canvas + JSON island rather than
                 morph-reuse them; the static JS listens for
                 livewire:morphed and re-inits the chart. --}}
            <div wire:key="chart-{{ $dateRange }}-{{ $groupBy }}-{{ $dateFrom }}-{{ $dateTo }}-{{ $source }}">
                <canvas id="lead-volume-chart" height="80"></canvas>
                {{-- JSON island consumed by
                     public/js/views/filament/pages/reports/lead-volume-report.js.
                     Replaces an earlier inline Alpine x-data init() that
                     raced the deferred Chart.js load and left the canvas
                     blank. --}}
                <script type="application/json" id="lead-volume-chart-data">@json($chartIsland)</script>
            </div>
        </div>

        {{-- Data Table --}}
        @php $tableData = $this->getTableData(); @endphp
        <div class="fi-section rounded-xl bg-white shadow-sm ring-1 ring-gray-950/5 dark:bg-gray-900 dark:ring-white/10 lv-section-overflow">
            <div class="lv-section-header">
                <h3 class="lv-section-title">{{ __('filament/reports.breakdown') }}</h3>
            </div>
            <table class="lv-table">
                <thead>
                    <tr>
                        <th>{{ __('filament/reports.lv_period_col') }}</th>
                        <th>{{ __('filament/reports.lv_leads_col') }}</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($tableData as $row)
                        <tr>
                            <td class="lv-cell-period">{{ $row['label'] }}</td>
                            <td class="lv-cell-count">{{ number_format($row['count']) }}</td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="2" class="lv-cell-empty">{{ __('filament/reports.lv_no_data_in_period') }}</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>

    @push('scripts')
        {{-- Chart.js — vendored locally under public/vendor/ (no CDN).
             The panel render hook also injects this script in the head
             with `defer`, but loading it here in the scripts stack
             guarantees window.Chart is defined before our init JS runs
             even on Livewire SPA navigation, and a duplicate <script>
             with the same src is a no-op for the browser cache. --}}
        <script src="{{ asset('vendor/chartjs/chart.umd.min.js') }}"></script>
        <script src="{{ asset('js/views/filament/pages/reports/lead-volume-report.js') }}"></script>
    @endpush
</x-filament-panels::page>
