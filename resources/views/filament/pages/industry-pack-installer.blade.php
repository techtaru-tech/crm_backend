<x-filament-panels::page>
    <link rel="stylesheet" href="{{ asset('css/views/filament/pages/industry-pack-installer.css') }}">

    <p class="ipi-intro">
        {{ __('filament/industry_pack_installer.intro') }}
    </p>

    <div class="ipi-grid">
        @foreach ($packs as $pack)
            <div class="ipi-card">
                <div class="ipi-head">
                    <div class="ipi-icon">
                        <svg viewBox="0 0 20 20" fill="currentColor"><path d="M10.707 2.293a1 1 0 00-1.414 0l-7 7a1 1 0 001.414 1.414L4 10.414V17a1 1 0 001 1h2a1 1 0 001-1v-2a1 1 0 011-1h2a1 1 0 011 1v2a1 1 0 001 1h2a1 1 0 001-1v-6.586l.293.293a1 1 0 001.414-1.414l-7-7z"/></svg>
                    </div>
                    <h3 class="ipi-name">{{ $pack['name'] }}</h3>
                </div>
                <p class="ipi-desc">{{ $pack['description'] }}</p>
                <ul class="ipi-stats">
                    <li>{{ __('filament/industry_pack_installer.stat_pipelines') }}        <strong>{{ $pack['pipelines'] }}</strong></li>
                    <li>{{ __('filament/industry_pack_installer.stat_custom_fields') }}    <strong>{{ $pack['custom_fields'] }}</strong></li>
                    <li>{{ __('filament/industry_pack_installer.stat_tags') }}             <strong>{{ $pack['tags'] }}</strong></li>
                    <li>{{ __('filament/industry_pack_installer.stat_email_templates') }}  <strong>{{ $pack['email_templates'] }}</strong></li>
                    <li>{{ __('filament/industry_pack_installer.stat_automations') }}      <strong>{{ $pack['automations'] }}</strong></li>
                    <li>{{ __('filament/industry_pack_installer.stat_forms') }}            <strong>{{ $pack['forms'] }}</strong></li>
                </ul>
                <button
                    type="button"
                    class="ipi-btn"
                    wire:click="install(@js($pack['key']))" wire:confirm="{{ __('filament/industry_pack_installer.confirm_install_pack', ['name' => $pack['name']]) }}"
                    wire:loading.attr="disabled"
                    wire:target="install"
                >
                    <svg viewBox="0 0 20 20" fill="currentColor"><path d="M10 3a1 1 0 011 1v5h5a1 1 0 110 2h-5v5a1 1 0 11-2 0v-5H4a1 1 0 110-2h5V4a1 1 0 011-1z"/></svg>
                    {{ __('filament/industry_pack_installer.install_pack') }}
                </button>
            </div>
        @endforeach
    </div>
</x-filament-panels::page>
