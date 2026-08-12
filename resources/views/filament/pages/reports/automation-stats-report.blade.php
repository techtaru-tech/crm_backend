<x-filament-panels::page>
    <link rel="stylesheet" href="{{ asset('css/views/filament/pages/reports/automation-stats-report.css') }}">

    <div class="as-page">

        {{-- Filters --}}
        <div class="fi-section rounded-xl bg-white shadow-sm ring-1 ring-gray-950/5 dark:bg-gray-900 dark:ring-white/10 as-section-pad">
            <x-report-date-range
                dateRangeModel="dateRange"
                dateFromModel="dateFrom"
                dateToModel="dateTo"
                :currentRange="$dateRange"
            />
        </div>

        @php $tableData = $this->getTableData(); @endphp
        <div class="fi-section rounded-xl bg-white shadow-sm ring-1 ring-gray-950/5 dark:bg-gray-900 dark:ring-white/10 as-section-overflow">
            <div class="as-section-header">
                <h3 class="as-section-title">{{ __('filament/reports.automation_performance') }}</h3>
            </div>
            <table class="as-table">
                <thead>
                    <tr>
                        <th>{{ __('filament/reports.as_automation_col') }}</th>
                        <th>{{ __('filament/reports.as_trigger_col') }}</th>
                        <th>{{ __('filament/reports.as_status_col') }}</th>
                        <th>{{ __('filament/reports.as_total_runs_col') }}</th>
                        <th>{{ __('filament/reports.as_success_runs_col') }}</th>
                        <th>{{ __('filament/reports.as_success_rate_col') }}</th>
                        <th>{{ __('filament/reports.as_avg_run_time_col') }}</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($tableData as $row)
                        @php
                            $secs = $row['avg_run_seconds'] ?? 0;
                            // duration suffixes routed through __() so non-English locales render their own abbreviation.
                            $runLabel = $secs >= 60
                                ? (string) __('filament/reports.duration_minutes_short', ['n' => round($secs/60, 1)])
                                : (string) __('filament/reports.duration_seconds_short', ['n' => $secs]);
                            // Translator-first trigger label so the badge respects tenant locale.
                            // Falls back to a humanised slug for tenant-custom or legacy triggers
                            // not in Automation::triggerLabels().
                            $triggerSlug   = (string) ($row['trigger_type'] ?? '');
                            $triggerLabels = \App\Models\Automation::triggerLabels();
                            $trigger       = $triggerSlug === ''
                                ? '—'
                                : ($triggerLabels[$triggerSlug] ?? ucfirst(str_replace('_', ' ', $triggerSlug)));
                            $rate = $row['success_rate'] ?? 0;
                            $barColor = $rate >= 80 ? '#22c55e' : ($rate >= 50 ? '#eab308' : '#ef4444');
                        @endphp
                        <tr>
                            <td class="as-cell-name">{{ $row['name'] }}</td>
                            <td class="as-cell-trigger">{{ $trigger }}</td>
                            <td>
                                @if($row['enabled'])
                                    <span class="as-badge as-badge-active">{{ __('filament/reports.as_badge_active') }}</span>
                                @else
                                    <span class="as-badge as-badge-disabled">{{ __('filament/reports.as_badge_disabled') }}</span>
                                @endif
                            </td>
                            <td class="as-cell-num">{{ number_format($row['total_runs']) }}</td>
                            <td class="as-cell-won">{{ number_format($row['success_runs']) }}</td>
                            <td>
                                <div class="as-rate-wrap">
                                    <span class="as-progress-bg">
                                        <span class="as-progress-bar" style="width:{{ min($rate, 100) }}%;background:{{ $barColor }}"></span>
                                    </span>
                                    <span class="as-rate-text">{{ $rate }}%</span>
                                </div>
                            </td>
                            <td class="as-cell-secs">{{ $secs > 0 ? $runLabel : '—' }}</td>
                        </tr>
                    @empty
                        <tr><td colspan="7" class="as-cell-empty">{{ __('filament/reports.as_no_runs_in_period') }}</td></tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
</x-filament-panels::page>
