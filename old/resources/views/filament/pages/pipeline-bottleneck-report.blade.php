<x-filament-panels::page>
    <link rel="stylesheet" href="{{ asset('css/views/filament/pages/pipeline-bottleneck-report.css') }}">

    @php $rows = $this->getRows(); @endphp

    <div class="fi-section bn-section rounded-xl bg-white shadow-sm ring-1 ring-gray-950/5 dark:bg-gray-900 dark:ring-white/10">
        <div class="bn-section-header">
            <h3 class="bn-section-title">{{ __('filament/pipeline_bottleneck.stages_at_risk') }}</h3>
            <p class="bn-section-subtitle">
                {{ __('filament/pipeline_bottleneck.section_subtitle') }}
            </p>
        </div>

        @if (count($rows) === 0)
            <div class="bn-empty">{{ __('filament/pipeline_bottleneck.empty') }}</div>
        @else
            <table class="bn-table">
                <thead>
                    <tr>
                        <th>{{ __('filament/pipeline_bottleneck.col_pipeline') }}</th>
                        <th>{{ __('filament/pipeline_bottleneck.col_stage') }}</th>
                        <th>{{ __('filament/pipeline_bottleneck.col_leads') }}</th>
                        <th>{{ __('filament/pipeline_bottleneck.col_avg_days') }}</th>
                        <th>{{ __('filament/pipeline_bottleneck.col_expected') }}</th>
                        <th>{{ __('filament/pipeline_bottleneck.col_overdue') }}</th>
                        <th>{{ __('filament/pipeline_bottleneck.col_status') }}</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach ($rows as $r)
                        <tr>
                            <td class="bn-cell-muted">{{ $r['pipeline'] }}</td>
                            <td class="bn-cell-strong">{{ $r['stage'] }}</td>
                            <td class="bn-cell-neutral">{{ number_format($r['lead_count']) }}</td>
                            <td>
                                <span class="bn-badge bn-{{ $r['status'] }}">
                                    {{ __('filament/pipeline_bottleneck.days_short', ['count' => $r['avg_days_in_stage']]) }}
                                </span>
                            </td>
                            <td class="bn-cell-muted">{{ $r['expected_days'] ? __('filament/pipeline_bottleneck.days_short', ['count' => $r['expected_days']]) : '—' }}</td>
                            {{-- Inline style required: dynamic color/weight based on overdue_count value --}}
                            <td style="color:{{ $r['overdue_count'] > 0 ? '#dc2626' : '#6b7280' }};font-weight:{{ $r['overdue_count'] > 0 ? '600' : '400' }}">
                                {{ $r['overdue_count'] }}
                            </td>
                            <td>
                                @php
                                    // Translator-first status label so the badge respects tenant locale.
                                    $bnStatusKey   = 'filament/pipeline_bottleneck.status_' . $r['status'];
                                    $bnStatusTrans = __($bnStatusKey);
                                    $bnStatusLabel = is_string($bnStatusTrans) && $bnStatusTrans !== $bnStatusKey
                                        ? $bnStatusTrans
                                        : ucfirst((string) $r['status']);
                                @endphp
                                <span class="bn-badge bn-{{ $r['status'] }}">{{ $bnStatusLabel }}</span>
                            </td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        @endif
    </div>
</x-filament-panels::page>
