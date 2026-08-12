<x-filament-panels::page>
    <link rel="stylesheet" href="{{ asset('css/views/filament/pages/reports/pipeline-funnel-report.css') }}">

    <div class="pf-page">

        {{-- Pipeline Selector --}}
        <div class="fi-section rounded-xl bg-white shadow-sm ring-1 ring-gray-950/5 dark:bg-gray-900 dark:ring-white/10 pf-section-pad">
            <div class="pf-filter-row">
                <div>
                    <label class="pf-label">{{ __('filament/reports.pf_pipeline_label') }}</label>
                    <select wire:model.live="pipelineId" class="pf-select">
                        @foreach($this->getPipelines() as $id => $name)
                            <option value="{{ $id }}">{{ $name }}</option>
                        @endforeach
                    </select>
                </div>
            </div>
        </div>

        {{-- Funnel Visualization --}}
        @php
            $funnel = $this->getFunnelData();
            $colors = ['#4f46e5','#7c3aed','#9333ea','#2563eb','#0891b2','#0d9488'];
        @endphp
        @if(count($funnel) > 0)
            @php $maxCount = max(array_column($funnel, 'count')) ?: 1; @endphp
            <div class="fi-section rounded-xl bg-white shadow-sm ring-1 ring-gray-950/5 dark:bg-gray-900 dark:ring-white/10 pf-section-pad">
                <h3 class="pf-section-title-mb">{{ __('filament/reports.pipeline_funnel_section') }}</h3>
                @foreach($funnel as $i => $stage)
                    @php $width = $maxCount > 0 ? max(20, ($stage['count'] / $maxCount) * 100) : 20; @endphp
                    <div class="pf-funnel-row">
                        <div class="pf-stage-label">{{ $stage['stage'] }}</div>
                        <div class="pf-bar-wrap">
                            <div class="pf-bar" style="width: {{ $width }}%;background:{{ $colors[$i % count($colors)] }}">
                                {{ __('filament/reports.pf_leads_suffix', ['count' => number_format($stage['count'])]) }}
                            </div>
                        </div>
                        <div class="pf-dropoff">
                            @if($stage['drop_off'] !== null)
                                <span class="pf-drop-down">{{ __('filament/reports.pf_dropoff_suffix', ['pct' => $stage['drop_off']]) }}</span>
                            @else
                                <span class="pf-drop-top">{{ __('filament/reports.pf_top_of_funnel') }}</span>
                            @endif
                        </div>
                    </div>
                @endforeach
            </div>

            {{-- Supporting Table --}}
            <div class="fi-section rounded-xl bg-white shadow-sm ring-1 ring-gray-950/5 dark:bg-gray-900 dark:ring-white/10 pf-section-overflow">
                <div class="pf-section-header">
                    <h3 class="pf-section-title">{{ __('filament/reports.stage_details') }}</h3>
                </div>
                <table class="pf-table">
                    <thead>
                        <tr>
                            <th>{{ __('filament/reports.pf_stage_col') }}</th>
                            <th>{{ __('filament/reports.pf_lead_count_col') }}</th>
                            <th>{{ __('filament/reports.pf_drop_off_col') }}</th>
                            <th>{{ __('filament/reports.pf_avg_days_in_stage_col') }}</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($funnel as $stage)
                            <tr>
                                <td class="pf-cell-stage">{{ $stage['stage'] }}</td>
                                <td class="pf-cell-count">{{ number_format($stage['count']) }}</td>
                                <td class="pf-cell-dropoff">{{ $stage['drop_off'] !== null ? $stage['drop_off'] . '%' : '—' }}</td>
                                <td class="pf-cell-days">{{ $stage['avg_days'] > 0 ? __('filament/reports.pf_days_suffix', ['count' => number_format($stage['avg_days'], 1)]) : '—' }}</td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        @else
            <div class="fi-section rounded-xl bg-white shadow-sm ring-1 ring-gray-950/5 dark:bg-gray-900 dark:ring-white/10 pf-empty">
                {{ __('filament/reports.pf_no_pipeline_data') }}
            </div>
        @endif
    </div>
</x-filament-panels::page>
