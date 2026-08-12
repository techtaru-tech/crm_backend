<x-filament-panels::page>
    {{--
        Static styles live in public/css/views/filament/pages/source-attribution-report.css.
        Conversion-rate badge color is dynamic (high vs low threshold)
        and applied via the .sa-badge-high / .sa-badge-low class flag.
    --}}
    <link rel="stylesheet" href="{{ asset('css/views/filament/pages/source-attribution-report.css') }}">

    <div class="sa-page">

        <div class="fi-section rounded-xl bg-white shadow-sm ring-1 ring-gray-950/5 dark:bg-gray-900 dark:ring-white/10 sa-section-pad">
            <x-report-date-range
                dateRangeModel="dateRange"
                dateFromModel="dateFrom"
                dateToModel="dateTo"
                :currentRange="$dateRange"
            />
        </div>

        @php $tableData = $this->getTableData(); @endphp
        <div class="fi-section rounded-xl bg-white shadow-sm ring-1 ring-gray-950/5 dark:bg-gray-900 dark:ring-white/10 sa-section-overflow">
            <div class="sa-section-header">
                <h3 class="sa-section-title">{{ __('filament/source_attribution_report.leads_grouped_by_utm') }}</h3>
                <p class="sa-section-sub">{{ __('filament/source_attribution_report.section_sub') }}</p>
            </div>
            <table class="sa-table">
                <thead>
                    <tr>
                        <th>{{ __('filament/source_attribution_report.col_utm_source') }}</th>
                        <th>{{ __('filament/source_attribution_report.col_utm_campaign') }}</th>
                        <th>{{ __('filament/source_attribution_report.col_total_leads') }}</th>
                        <th>{{ __('filament/source_attribution_report.col_won') }}</th>
                        <th>{{ __('filament/source_attribution_report.col_conversion_rate') }}</th>
                        <th>{{ __('filament/source_attribution_report.col_total_won_value') }}</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($tableData as $row)
                        <tr>
                            <td class="sa-cell-source">{{ $row['utm_source'] }}</td>
                            <td class="sa-cell-campaign">{{ $row['utm_campaign'] }}</td>
                            <td class="sa-cell-num">{{ number_format($row['total_leads']) }}</td>
                            <td class="sa-cell-text">{{ number_format($row['won_leads']) }}</td>
                            <td>
                                {{-- badge color flips with conversion-rate threshold (dynamic class flag) --}}
                                <span class="sa-badge {{ $row['conversion_rate'] >= 10 ? 'sa-badge-high' : 'sa-badge-low' }}">
                                    {{ $row['conversion_rate'] }}%
                                </span>
                            </td>
                            <td class="sa-cell-won">{{ \App\Support\Currency::format($row['total_won_value'], \App\Support\Currency::default()) }}</td>
                        </tr>
                    @empty
                        <tr><td colspan="6" class="sa-cell-empty">{{ __('filament/source_attribution_report.empty') }}</td></tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
</x-filament-panels::page>
