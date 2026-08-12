<x-filament-panels::page>
    <div class="bk-hero">
        <p class="bk-hero-eyebrow">{{ __('filament/sa_backups.hero_eyebrow') }}</p>
        <h2 class="bk-hero-title">{{ __('filament/sa_backups.hero_title') }}</h2>
        <p class="bk-hero-sub">{!! __('filament/sa_backups.hero_sub_html') !!}</p>
    </div>

    @php $backups = $this->getBackups(); @endphp

    <div class="bk-card">
        @if(empty($backups))
            <div class="bk-empty">
                <svg width="48" height="48" viewBox="0 0 24 24" fill="none" stroke="#9ca3af" stroke-width="1.5" class="bk-empty-icon"><path stroke-linecap="round" stroke-linejoin="round" d="M20.25 7.5l-.625 10.632a2.25 2.25 0 01-2.247 2.118H6.622a2.25 2.25 0 01-2.247-2.118L3.75 7.5M10 11.25h4M3.375 7.5h17.25c.621 0 1.125-.504 1.125-1.125v-1.5c0-.621-.504-1.125-1.125-1.125H3.375c-.621 0-1.125.504-1.125 1.125v1.5c0 .621.504 1.125 1.125 1.125z"/></svg>
                <p class="bk-empty-text">{{ __('filament/sa_backups.empty_no_backups') }}</p>
            </div>
        @else
            <table class="bk-table">
                <thead>
                    <tr class="bk-table-head-row">
                        <th class="bk-table-th">{{ __('filament/sa_backups.col_archive') }}</th>
                        <th class="bk-table-th">{{ __('filament/sa_backups.col_created') }}</th>
                        <th class="bk-table-th-right">{{ __('filament/sa_backups.col_size') }}</th>
                        <th class="bk-table-th-right">{{ __('filament/sa_backups.col_actions') }}</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($backups as $b)
                        <tr class="bk-table-row">
                            <td class="bk-td-archive">{{ $b['name'] }}</td>
                            <td class="bk-td-created">{{ $b['created_at']->translatedFormat('M j, Y g:i a') }}<br><span class="bk-td-relative">{{ $b['created_at']->diffForHumans() }}</span></td>
                            <td class="bk-td-size">{{ \App\Filament\SuperAdmin\Pages\Backups::humanSize($b['size']) }}</td>
                            <td class="bk-td-actions">
                                <a href="{{ url('/admin/super/backups/download?name=' . urlencode($b['name'])) }}" class="bk-btn-download">{{ __('filament/sa_backups.btn_download') }}</a>
                                <button type="button" wire:click="verifyBackup('{{ $b['name'] }}')" class="bk-btn bk-btn-verify">{{ __('filament/sa_backups.btn_verify') }}</button>
                                {{-- Restore + Delete go through Filament's mountAction so the confirmation is a styled modal (heading, description, danger icon) rather than the native browser confirm() dialog produced by wire:confirm.  The action methods live on Backups.php — restoreBackupAction() / deleteBackupAction(). --}}
                                <button type="button" x-data x-on:click="$wire.mountAction('restoreBackup', { name: @js($b['name']) })" class="bk-btn bk-btn-restore">{{ __('filament/sa_backups.btn_restore') }}</button>
                                <button type="button" x-data x-on:click="$wire.mountAction('deleteBackup', { name: @js($b['name']) })" class="bk-btn bk-btn-delete">{{ __('filament/sa_backups.btn_delete') }}</button>
                            </td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        @endif
    </div>

    @php
        // Real toggle lives on Spatie-backed GeneralSettings, NOT in
        // config/leadhub.php. Reading config() always returned false and made
        // this indicator misleading vs. routes/console.php which already
        // gates the cron job on this exact setting. Wrap in try/catch so a
        // malformed/missing settings row never 500s the page.
        try {
            $nightlyBackupsEnabled = (bool) app(\App\Settings\GeneralSettings::class)->auto_nightly_backup;
        } catch (\Throwable $e) {
            $nightlyBackupsEnabled = false;
        }
    @endphp
    <div class="bk-nightly">
        <strong>{{ __('filament/sa_backups.nightly_status_strong', ['state' => $nightlyBackupsEnabled ? __('filament/sa_backups.nightly_state_enabled') : __('filament/sa_backups.nightly_state_disabled')]) }}</strong>
        @if($nightlyBackupsEnabled)
            {{ __('filament/sa_backups.nightly_enabled_description') }}
        @else
            {{ __('filament/sa_backups.nightly_disabled_prefix') }}<a href="{{ route('filament.super-admin.pages.script-settings') }}" class="bk-nightly-link">{{ __('filament/sa_backups.nightly_disabled_link_text') }}</a>{{ __('filament/sa_backups.nightly_disabled_suffix') }}
        @endif
        {{ __('filament/sa_backups.nightly_footer_note') }}
    </div>

    <link rel="stylesheet" href="{{ asset('css/views/filament/super-admin/pages/backups.css') }}">
</x-filament-panels::page>
