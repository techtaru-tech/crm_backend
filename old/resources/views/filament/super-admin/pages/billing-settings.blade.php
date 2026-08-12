<x-filament-panels::page>
    <link rel="stylesheet" href="{{ asset('css/views/filament/super-admin/pages/billing-settings.css') }}">
    <div class="bse-hero">
        <p class="bse-eyebrow">{{ __('filament/sa_billing_settings.hero_eyebrow') }}</p>
        <h2 class="bse-title">{{ __('filament/sa_billing_settings.hero_title') }}</h2>
        <p class="bse-body">{{ __('filament/sa_billing_settings.body_intro') }}</p>
    </div>

    {{ $this->form }}
</x-filament-panels::page>
