<x-filament-panels::page>
    {{--
        All UI copy is loaded from lang/<locale>/data_privacy.php so
        buyers can adapt or translate without touching this view.
        Static styles live in public/css/views/filament/pages/privacy/data-privacy.css.
        Only DYNAMIC values stay inline — the deletion-card border
        color flips when $deletion_pending is true.
    --}}
    @if (! $tenant)
        <div class="dp-error-card">
            {{ __('data_privacy.error_no_tenant') }}
        </div>
    @else
        {{-- ═════ Right of Access — Data Export ═════ --}}
        <div class="dp-card">
            <div class="dp-card-header">
                <div class="dp-card-icon dp-card-icon-export">
                    📦
                </div>
                <div class="dp-card-body">
                    <h3 class="dp-card-title">{{ __('data_privacy.export_title') }}</h3>
                    <p class="dp-card-description">
                        {{ __('data_privacy.export_description') }}
                    </p>
                </div>
            </div>

            @if ($export_status === 'pending' || $export_status === 'processing')
                <div class="dp-banner-processing">
                    <span class="dp-spinner"></span>
                    <div>
                        <strong class="dp-banner-processing-title">{{ __('data_privacy.export_building_title') }}</strong>
                        <div class="dp-banner-processing-body">{{ __('data_privacy.export_building_body') }}</div>
                    </div>
                </div>
            @elseif ($export_status === 'failed')
                <div class="dp-banner-failed">
                    <strong class="dp-banner-failed-title">{{ __('data_privacy.export_failed_title') }}</strong>
                    <div class="dp-banner-failed-body">{{ __('data_privacy.export_failed_body') }}</div>
                </div>
                <button wire:click="requestExport" class="dp-btn-primary">
                    {{ __('data_privacy.export_failed_btn') }}
                </button>
            @elseif ($export_ready)
                <div class="dp-banner-ready">
                    <div>
                        <strong class="dp-banner-ready-title">{{ __('data_privacy.export_ready_title') }}</strong>
                        <div class="dp-banner-ready-body">
                            @if ($export_size_human)
                                {{ __('data_privacy.export_ready_size', ['size' => $export_size_human]) }} ·
                            @endif
                            @if ($export_expires_at)
                                {{ __('data_privacy.export_ready_expires', ['when' => $export_expires_at->diffForHumans()]) }}
                            @endif
                        </div>
                    </div>
                    <button wire:click="downloadExport" class="dp-btn-success">
                        {{ __('data_privacy.export_download_btn') }}
                    </button>
                </div>
                <button wire:click="requestExport" class="dp-btn-secondary">
                    {{ __('data_privacy.export_rebuild_btn') }}
                </button>
            @else
                <button wire:click="requestExport" class="dp-btn-primary">
                    {{ __('data_privacy.export_request_btn') }}
                </button>
            @endif
        </div>

        {{-- ═════ Right to Erasure — Workspace Deletion ═════ --}}
        {{-- border color is dynamic ($deletion_pending) — kept inline --}}
        <div class="dp-card-no-margin" style="border:1px solid {{ $deletion_pending ? '#fecaca' : '#e5e7eb' }};">
            <div class="dp-card-header">
                <div class="dp-card-icon dp-card-icon-delete">
                    🗑️
                </div>
                <div class="dp-card-body">
                    <h3 class="dp-card-title">{{ __('data_privacy.deletion_title') }}</h3>
                    <p class="dp-card-description">
                        {{ __('data_privacy.deletion_description') }}
                    </p>
                </div>
            </div>

            @if ($deletion_pending && $deletion_scheduled)
                <div class="dp-banner-scheduled">
                    <div>
                        <strong class="dp-banner-scheduled-title">{{ __('data_privacy.deletion_scheduled_title') }}</strong>
                        <div class="dp-banner-scheduled-body">
                            {{ __('data_privacy.deletion_scheduled_on', ['date' => $deletion_scheduled->translatedFormat('F j, Y')]) }}
                            @if ($deletion_days_left !== null)
                                {{ trans_choice('data_privacy.deletion_days_left', $deletion_days_left, ['count' => $deletion_days_left]) }}
                            @endif
                        </div>
                    </div>
                    @if (! empty($is_owner))
                        <button wire:click="cancelDeletion" class="dp-btn-cancel-deletion">
                            {{ __('data_privacy.deletion_cancel_btn') }}
                        </button>
                    @endif
                </div>
            @elseif (! empty($is_owner))
                <button wire:click="requestDeletion"
                        wire:confirm="{{ __('data_privacy.deletion_request_confirm') }}"
                        class="dp-btn-request-deletion">
                    {{ __('data_privacy.deletion_request_btn') }}
                </button>
            @else
                {{-- Non-owner admins reach this page to access the data-export controls but cannot schedule workspace deletion.  Surface a clear info notice rather than a button that would just bounce to a "Owner only." toast. --}}
                <div class="dp-non-owner-info">
                    {{ __('data_privacy.deletion_not_owner') }}
                </div>
            @endif
        </div>

        <p class="dp-gdpr-footer">
            {{ __('data_privacy.gdpr_footer') }}
        </p>
    @endif

    <link rel="stylesheet" href="{{ asset('css/views/filament/pages/privacy/data-privacy.css') }}">
</x-filament-panels::page>
