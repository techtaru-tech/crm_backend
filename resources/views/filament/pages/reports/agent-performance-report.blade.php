<x-filament-panels::page>
    <link rel="stylesheet" href="{{ asset('css/views/filament/pages/reports/agent-performance-report.css') }}">

    <div class="ap-page">

        {{-- Filters --}}
        <div class="fi-section rounded-xl bg-white shadow-sm ring-1 ring-gray-950/5 dark:bg-gray-900 dark:ring-white/10 ap-section-pad">
            <x-report-date-range
                dateRangeModel="dateRange"
                dateFromModel="dateFrom"
                dateToModel="dateTo"
                :currentRange="$dateRange"
            />
        </div>

        {{-- Agent Table --}}
        @php $tableData = $this->getTableData(); @endphp
        <div class="fi-section rounded-xl bg-white shadow-sm ring-1 ring-gray-950/5 dark:bg-gray-900 dark:ring-white/10 ap-section-overflow">
            <div class="ap-section-header">
                <h3 class="ap-section-title">{{ __('filament/reports.agent_performance_section') }}</h3>
                <p class="ap-section-sub">{{ __('filament/reports.ap_section_sub') }}</p>
            </div>
            <table class="ap-table">
                <thead>
                    <tr>
                        <th>{{ __('filament/reports.ap_agent_col') }}</th>
                        <th>{{ __('filament/reports.ap_assigned_col') }}</th>
                        <th>{{ __('filament/reports.ap_won_col') }}</th>
                        <th>{{ __('filament/reports.ap_win_rate_col') }}</th>
                        <th>{{ __('filament/reports.ap_avg_response_col') }}</th>
                        <th>{{ __('filament/reports.ap_avg_close_col') }}</th>
                        <th>{{ __('filament/reports.ap_activities_col') }}</th>
                        <th class="ap-th-center">{{ __('filament/reports.ap_trend_col') }}</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($tableData as $row)
                        @php
                            $respMin = $row['avg_response_min'] ?? 0;
                            // duration suffixes routed through __() so non-English locales render their own abbreviation.
                            $respLabel = $respMin > 0
                                ? ($respMin < 60
                                    ? (string) __('filament/reports.duration_minutes_short', ['n' => $respMin])
                                    : (string) __('filament/reports.duration_hours_short', ['n' => round($respMin/60, 1)]))
                                : '—';
                            $closeDays = $row['avg_close_days'] ?? 0;
                            $sparkline = $row['sparkline'] ?? [0,0,0,0];
                            $sparkMax = max(max($sparkline), 1);
                        @endphp
                        <tr>
                            <td class="ap-cell-agent">
                                <div class="ap-agent-wrap">
                                    <span class="ap-avatar">{{ substr($row['agent'], 0, 1) }}</span>
                                    {{ $row['agent'] }}
                                </div>
                            </td>
                            <td class="ap-cell-num">{{ number_format($row['assigned']) }}</td>
                            <td class="ap-cell-won">{{ number_format($row['won']) }}</td>
                            <td>
                                <div class="ap-rate-wrap">
                                    <span class="ap-progress-bg">
                                        {{-- NOTE: width is DYNAMIC — driven by per-agent win_rate value --}}
                                        <span class="ap-progress-bar" style="width:{{ min($row['win_rate'], 100) }}%"></span>
                                    </span>
                                    <span class="ap-rate-text">{{ $row['win_rate'] }}%</span>
                                </div>
                            </td>
                            <td class="ap-cell-num-fmt">{{ $respLabel }}</td>
                            {{-- duration_days_short routes through __() for tenant locale. --}}
                            <td class="ap-cell-num-fmt">{{ $closeDays > 0 ? __('filament/reports.duration_days_short', ['n' => $closeDays]) : '—' }}</td>
                            <td class="ap-cell-text">{{ number_format($row['activities']) }}</td>
                            <td>
                                <span class="ap-sparkline">
                                    {{-- NOTE: bar height is DYNAMIC — normalized to per-row $sparkMax --}}
                                    @foreach($sparkline as $val)
                                        <span class="ap-spark-bar" style="height:{{ $sparkMax > 0 ? round($val / $sparkMax * 100) : 0 }}%"></span>
                                    @endforeach
                                </span>
                            </td>
                        </tr>
                    @empty
                        <tr><td colspan="8" class="ap-cell-empty">{{ __('filament/reports.ap_no_agents_in_period') }}</td></tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
</x-filament-panels::page>
