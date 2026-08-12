<x-filament-panels::page>
    <link rel="stylesheet" href="{{ asset('css/views/filament/pages/settings/license-settings-page.css') }}">

    {{-- License Status Badge — $licenseStatus is App\Billing\License\LicenseStatus
         (typed DTO).  Properties: status, message, buyer, licenseType,
         itemName, purchasedAt, supportedUntil, expiresAt, domain, plan. --}}
    <div class="fi-section rounded-xl bg-white shadow-sm ring-1 ring-gray-950/5 dark:bg-gray-900 dark:ring-white/10 ls-section">
        <div class="ls-row">
            <div>
                <h3 class="ls-title">{{ __('filament/license_settings.license_status') }}</h3>
                <p class="ls-sub">{{ $licenseStatus?->message ?: __('filament/license_settings.status_unknown') }}</p>
            </div>
            <div class="ls-status-wrap">
                @php $status = $licenseStatus?->status ?? 'invalid'; @endphp
                @if($status === 'valid')
                    <span class="ls-pill ls-pill-active">{{ __('filament/license_settings.pill_active') }}</span>
                @elseif($status === 'expired')
                    <span class="ls-pill ls-pill-expired">{{ __('filament/license_settings.pill_expired') }}</span>
                @elseif($status === 'unlicensed')
                    <span class="ls-pill ls-pill-unlicensed">{{ __('filament/license_settings.pill_not_activated') }}</span>
                @else
                    <span class="ls-pill ls-pill-invalid">{{ __('filament/license_settings.pill_invalid') }}</span>
                @endif
            </div>
        </div>

        @if($licenseStatus?->buyer)
            <div class="ls-details">
                <div class="ls-details-grid">
                    <div class="ls-detail-label">{{ __('filament/license_settings.detail_licensed_to') }}</div>
                    <div class="ls-detail-value">{{ $licenseStatus->buyer }}</div>

                    @if($licenseStatus->licenseType)
                        <div class="ls-detail-label">{{ __('filament/license_settings.detail_license_type') }}</div>
                        <div class="ls-detail-value">{{ $licenseStatus->licenseType }}</div>
                    @endif

                    @if($licenseStatus->supportedUntil)
                        <div class="ls-detail-label">{{ __('filament/license_settings.detail_support_until') }}</div>
                        <div class="ls-detail-value">{{ $licenseStatus->supportedUntil }}</div>
                    @endif

                    @if($licenseStatus->purchasedAt)
                        <div class="ls-detail-label">{{ __('filament/license_settings.detail_purchased') }}</div>
                        <div class="ls-detail-value">{{ \Carbon\Carbon::parse($licenseStatus->purchasedAt)->translatedFormat('M d, Y') }}</div>
                    @endif
                </div>
            </div>
        @endif
    </div>

    <form wire:submit="verify" class="ls-form">
        {{ $this->form }}
        <div class="ls-form-actions">
            <x-filament::button type="submit" color="primary">{{ __('filament/license_settings.verify_license') }}</x-filament::button>
        </div>
    </form>
</x-filament-panels::page>
