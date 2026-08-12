<x-filament-panels::page>
    <link rel="stylesheet" href="{{ asset('css/views/filament/pages/reports/email-sequences-report.css') }}">

    @php $rows = $this->getRows(); @endphp

    <div class="fi-section rounded-xl bg-white shadow-sm ring-1 ring-gray-950/5 dark:bg-gray-900 dark:ring-white/10 seq-card">
        <div class="seq-card-header">
            <h3 class="seq-card-title">{{ __('filament/reports.sequence_performance') }}</h3>
        </div>

        @if(empty($rows))
            <div class="seq-empty">{{ __('filament/reports.es_no_sequences') }}</div>
        @else
            <table class="seq-table">
                <thead>
                    <tr>
                        <th>{{ __('filament/reports.es_sequence_col') }}</th>
                        <th>{{ __('filament/reports.es_status_col') }}</th>
                        <th>{{ __('filament/reports.es_enrolled_col') }}</th>
                        <th>{{ __('filament/reports.es_completed_col') }}</th>
                        <th>{{ __('filament/reports.es_replied_col') }}</th>
                        <th>{{ __('filament/reports.es_reply_rate_col') }}</th>
                        <th>{{ __('filament/reports.es_open_rate_col') }}</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($rows as $row)
                        @php
                            $badgeClass = match($row['status']) {
                                'active' => 'seq-badge-active',
                                'paused' => 'seq-badge-paused',
                                default  => 'seq-badge-draft',
                            };
                        @endphp
                        <tr>
                            <td class="seq-cell-name">{{ $row['name'] }}</td>
                            @php
                                // Translator-first status label so the badge respects tenant locale.
                                $seqStatusKey   = 'filament/reports.es_status_' . $row['status'];
                                $seqStatusTrans = __($seqStatusKey);
                                $seqStatusLabel = is_string($seqStatusTrans) && $seqStatusTrans !== $seqStatusKey
                                    ? $seqStatusTrans
                                    : ucfirst((string) $row['status']);
                            @endphp
                            <td class="seq-cell-status"><span class="seq-badge {{ $badgeClass }}">{{ $seqStatusLabel }}</span></td>
                            <td>{{ $row['enrolled'] }}</td>
                            <td>{{ $row['completed'] }}</td>
                            <td>{{ $row['replied'] }}</td>
                            <td>{{ number_format($row['reply_rate'], 1) }}%</td>
                            <td>{{ number_format($row['open_rate'], 1) }}%</td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        @endif
    </div>
</x-filament-panels::page>
