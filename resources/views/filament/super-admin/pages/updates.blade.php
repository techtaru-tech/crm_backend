<x-filament-panels::page>
    @php $info = $this->getVersionInfo(); @endphp

    <div class="up-hero">
        <div class="up-hero-row">
            <div>
                <p class="up-hero-eyebrow">{{ __('filament/sa_updates.installed_version') }}</p>
                <h2 class="up-hero-version">v{{ $info['current'] }}</h2>
                @if(! empty($info['update']['last_checked_at']))
                    <p class="up-hero-checked">{{ __('filament/sa_updates.last_checked', ['time' => \Carbon\Carbon::parse($info['update']['last_checked_at'])->diffForHumans()]) }}</p>
                @endif
            </div>

            @if(! empty($info['update']['update_available']))
                <div class="up-update-card">
                    <p class="up-update-label">{{ __('filament/sa_updates.update_available') }}</p>
                    <p class="up-update-version">v{{ $info['update']['latest_version'] }}</p>
                    @if(! empty($info['update']['changelog_url']))
                        <a href="{{ $info['update']['changelog_url'] }}" target="_blank" rel="noopener noreferrer" class="up-update-link">{{ __('filament/sa_updates.view_changelog') }}</a>
                    @endif
                </div>
            @else
                <div class="up-latest-pill">
                    <p class="up-latest-text">{{ __('filament/sa_updates.on_latest_version') }}</p>
                </div>
            @endif
        </div>
    </div>

    {{ $this->form }}

    @php $history = $this->getHistory(); @endphp

    <div class="up-history">
        <h3 class="up-history-title">{{ __('filament/sa_updates.update_history') }}</h3>

        @if(empty($history))
            <p class="up-history-empty">{{ __('filament/sa_updates.history_empty') }}</p>
        @else
            <table class="up-table">
                <thead>
                    <tr class="up-table-head-row">
                        <th class="up-table-th">{{ __('filament/sa_updates.col_when') }}</th>
                        <th class="up-table-th">{{ __('filament/sa_updates.col_package') }}</th>
                        <th class="up-table-th">{{ __('filament/sa_updates.col_from_to') }}</th>
                        <th class="up-table-th">{{ __('filament/sa_updates.col_backup') }}</th>
                        <th class="up-table-th-right">{{ __('filament/sa_updates.col_result') }}</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($history as $h)
                        <tr class="up-table-row">
                            <td class="up-td">{{ \Carbon\Carbon::parse($h['started_at'])->diffForHumans() }}</td>
                            <td class="up-td-mono">{{ $h['package'] }}</td>
                            <td class="up-td">v{{ $h['from_version'] }} → v{{ $h['to_version'] ?? '?' }}</td>
                            <td class="up-td-backup">{{ $h['backup'] ?? '—' }}</td>
                            <td class="up-td-right">
                                @if(! empty($h['error']))
                                    <span class="up-badge-failed">{{ __('filament/sa_updates.badge_failed') }}</span>
                                @else
                                    <span class="up-badge-ok">{{ __('filament/sa_updates.badge_files_written', ['count' => $h['files_written'] ?? 0]) }}</span>
                                @endif
                            </td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        @endif
    </div>

    <link rel="stylesheet" href="{{ asset('css/views/filament/super-admin/pages/updates.css') }}">
</x-filament-panels::page>
