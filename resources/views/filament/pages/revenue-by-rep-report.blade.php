<x-filament-panels::page>
    <link rel="stylesheet" href="{{ asset('css/views/filament/pages/revenue-by-rep-report.css') }}">

    @php
        $rows    = $this->getRows();
        $chart   = $this->getChartPayload();
        $maxVal  = empty($chart['values']) ? 1 : max(max($chart['values']), 1);
        // Localised currency formatter — Currency::format() picks the tenant
        // display symbol + locale-aware decimal/grouping characters instead
        // of a hardcoded "$1,234.56" English leak in the table + bar values.
        $reportCurrency = \App\Support\Currency::default();
        $fmt = fn ($v) => \App\Support\Currency::format((float) $v, $reportCurrency);
    @endphp

    <div class="rr-page">

        {{-- Date range selector --}}
        <div class="fi-section rounded-xl bg-white shadow-sm ring-1 ring-gray-950/5 dark:bg-gray-900 dark:ring-white/10 rr-section-pad-sm">
            <div class="rr-date-form">
                <div>
                    <label class="rr-date-label">{{ __('filament/revenue_by_rep.range_label') }}</label>
                    <select wire:model.live="dateRange">
                        <option value="7">{{ __('filament/revenue_by_rep.range_last_7_days') }}</option>
                        <option value="30">{{ __('filament/revenue_by_rep.range_last_30_days') }}</option>
                        <option value="60">{{ __('filament/revenue_by_rep.range_last_60_days') }}</option>
                        <option value="90">{{ __('filament/revenue_by_rep.range_last_90_days') }}</option>
                        <option value="this_month">{{ __('filament/revenue_by_rep.range_this_month') }}</option>
                        <option value="last_month">{{ __('filament/revenue_by_rep.range_last_month') }}</option>
                        <option value="this_year">{{ __('filament/revenue_by_rep.range_this_year') }}</option>
                        <option value="custom">{{ __('filament/revenue_by_rep.range_custom') }}</option>
                    </select>
                </div>
                <div>
                    <label class="rr-date-label">{{ __('filament/revenue_by_rep.range_from') }}</label>
                    <input type="date" wire:model.live="dateFrom">
                </div>
                <div>
                    <label class="rr-date-label">{{ __('filament/revenue_by_rep.range_to') }}</label>
                    <input type="date" wire:model.live="dateTo">
                </div>
            </div>
        </div>

        {{-- Bar chart --}}
        @if (count($rows) > 0)
            <div class="fi-section rounded-xl bg-white shadow-sm ring-1 ring-gray-950/5 dark:bg-gray-900 dark:ring-white/10 rr-section-pad">
                <h3 class="rr-section-title-mb">{{ __('filament/revenue_by_rep.won_revenue_per_rep') }}</h3>
                @foreach ($rows as $r)
                    @php $pct = $maxVal > 0 ? max(2, ($r['won_value'] / $maxVal) * 100) : 0; @endphp
                    <div class="rr-bar-row">
                        <div class="rr-bar-label">{{ $r['name'] }}</div>
                        <div class="rr-bar-wrap">
                            <div class="rr-bar" style="width:{{ $pct }}%"></div>
                        </div>
                        <div class="rr-bar-value">{{ $fmt($r['won_value']) }}</div>
                    </div>
                @endforeach
            </div>
        @endif

        {{-- Table --}}
        <div class="fi-section rounded-xl bg-white shadow-sm ring-1 ring-gray-950/5 dark:bg-gray-900 dark:ring-white/10 rr-section-overflow">
            <div class="rr-section-header">
                <h3 class="rr-section-title">{{ __('filament/revenue_by_rep.rep_performance') }}</h3>
            </div>
            @if (count($rows) === 0)
                <div class="rr-empty">{{ __('filament/revenue_by_rep.empty') }}</div>
            @else
                <table class="rr-table">
                    <thead><tr>
                        <th>{{ __('filament/revenue_by_rep.col_rep') }}</th>
                        <th>{{ __('filament/revenue_by_rep.col_won') }}</th>
                        <th>{{ __('filament/revenue_by_rep.col_won_value') }}</th>
                        <th>{{ __('filament/revenue_by_rep.col_lost') }}</th>
                        <th>{{ __('filament/revenue_by_rep.col_pipeline_value') }}</th>
                        <th>{{ __('filament/revenue_by_rep.col_win_rate') }}</th>
                    </tr></thead>
                    <tbody>
                        @foreach ($rows as $r)
                            <tr>
                                <td class="rr-cell-name">{{ $r['name'] }}</td>
                                <td>{{ number_format($r['won_count']) }}</td>
                                <td class="rr-cell-won">{{ $fmt($r['won_value']) }}</td>
                                <td>{{ number_format($r['lost_count']) }}</td>
                                <td>{{ $fmt($r['pipeline_value']) }}</td>
                                <td>{{ number_format($r['win_rate'], 1) }}%</td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            @endif
        </div>
    </div>
</x-filament-panels::page>
