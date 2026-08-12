<x-filament-panels::page>
    @php
        $available = $this->isAvailable();
        $modules   = $this->getModules();
    @endphp

    <div class="mod-hero">
        <p class="mod-hero-eyebrow">{{ __('filament/sa_modules.hero_eyebrow') }}</p>
        <h2 class="mod-hero-title">{{ __('filament/sa_modules.hero_title') }}</h2>
        <p class="mod-hero-sub">
            {!! __('filament/sa_modules.hero_subtitle_html') !!}
        </p>
    </div>

    @if(! $available)
        <div class="mod-warn">
            <strong>{{ __('filament/sa_modules.unavailable_warning_strong') }}</strong>
            {!! __('filament/sa_modules.unavailable_warning_body_html') !!}
        </div>
    @endif

    {{ $this->form }}

    <div class="mod-section">
        <h3 class="mod-section-title">{{ __('filament/sa_modules.installed_section_title') }}</h3>

        @if(empty($modules))
            <p class="mod-empty">{{ __('filament/sa_modules.empty_no_modules') }}</p>
        @else
            <table class="mod-table">
                <thead>
                    <tr class="mod-table-head-row">
                        <th class="mod-table-th">{{ __('filament/sa_modules.col_module') }}</th>
                        <th class="mod-table-th">{{ __('filament/sa_modules.col_version') }}</th>
                        <th class="mod-table-th">{{ __('filament/sa_modules.col_status') }}</th>
                        <th class="mod-table-th-right">{{ __('filament/sa_modules.col_actions') }}</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($modules as $m)
                        <tr class="mod-table-row">
                            <td class="mod-td-name">
                                <div class="mod-name">{{ $m['name'] }}</div>
                                @if($m['description'])
                                    <div class="mod-desc">{{ $m['description'] }}</div>
                                @endif
                                <div class="mod-alias">{{ $m['alias'] }}</div>
                            </td>
                            <td class="mod-td-version">
                                {{ $m['version'] ?? '—' }}
                            </td>
                            <td class="mod-td-status">
                                @if($m['enabled'])
                                    <span class="mod-pill-enabled">{{ __('filament/sa_modules.pill_enabled') }}</span>
                                @else
                                    <span class="mod-pill-disabled">{{ __('filament/sa_modules.pill_disabled') }}</span>
                                @endif
                            </td>
                            <td class="mod-td-actions">
                                @if($m['enabled'])
                                    <button type="button" wire:click="disable('{{ $m['name'] }}')" class="mod-btn mod-btn-disable">{{ __('filament/sa_modules.btn_disable') }}</button>
                                @else
                                    <button type="button" wire:click="enable('{{ $m['name'] }}')" class="mod-btn mod-btn-enable">{{ __('filament/sa_modules.btn_enable') }}</button>
                                @endif
                                <button type="button"
                                    wire:click="remove('{{ $m['name'] }}')"
                                    wire:confirm="{{ __('filament/sa_modules.confirm_permanently_delete', ['name' => $m['name']]) }}"
                                    class="mod-btn mod-btn-delete">{{ __('filament/sa_modules.btn_delete') }}</button>
                            </td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        @endif
    </div>

    <link rel="stylesheet" href="{{ asset('css/views/filament/super-admin/pages/modules.css') }}">
</x-filament-panels::page>
