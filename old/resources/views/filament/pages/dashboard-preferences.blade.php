<x-filament-panels::page>
    {{--
        Static styles live in public/css/views/filament/pages/dashboard-preferences.css.
        Hover effects moved from onmouseover/onmouseout inline
        attribute handlers to CSS :hover pseudo-class (cleaner +
        audit-friendly).  No dynamic interpolations in this view.
    --}}
    <form wire:submit.prevent="save">
        <div class="dp-card">
            <h2 class="dp-title">{{ __('filament/dashboard_preferences.visible_widgets') }}</h2>
            <p class="dp-subtitle">
                {{ __('filament/dashboard_preferences.widgets_intro') }}
            </p>

            <div class="dp-options">
                @foreach($this->widgetOptions as $value => $label)
                    <label class="dp-option">
                        <input
                            type="checkbox"
                            value="{{ $value }}"
                            wire:model.live="enabledWidgets"
                            class="dp-checkbox"
                        />
                        <div class="dp-option-body">
                            <p class="dp-option-label">{{ $label }}</p>
                            <p class="dp-option-key">{{ $value }}</p>
                        </div>
                    </label>
                @endforeach
            </div>

            <div class="dp-actions">
                <button type="submit" class="dp-btn-primary">
                    <svg width="16" height="16" viewBox="0 0 20 20" fill="currentColor" aria-hidden="true"><path fill-rule="evenodd" d="M16.704 4.153a.75.75 0 01.143 1.052l-8 10.5a.75.75 0 01-1.127.075l-4.5-4.5a.75.75 0 011.06-1.06l3.894 3.893 7.48-9.817a.75.75 0 011.05-.143z" clip-rule="evenodd"/></svg>
                    {{ __('filament/dashboard_preferences.save_preferences') }}
                </button>

                <button type="button" wire:click="resetDefaults" class="dp-btn-secondary">
                    <svg width="16" height="16" viewBox="0 0 20 20" fill="currentColor" aria-hidden="true"><path fill-rule="evenodd" d="M4.755 10.059a7.5 7.5 0 0112.548-3.364l1.903 1.903h-3.183a.75.75 0 100 1.5h4.992a.75.75 0 00.75-.75V4.356a.75.75 0 00-1.5 0v3.18l-1.9-1.9A9 9 0 003.306 9.67a.75.75 0 101.45.388zm15.408 3.352a.75.75 0 00-.919.53 7.5 7.5 0 01-12.548 3.364l-1.902-1.903h3.183a.75.75 0 000-1.5H2.984a.75.75 0 00-.75.75v4.992a.75.75 0 001.5 0v-3.18l1.9 1.9a9 9 0 0015.059-4.035.75.75 0 00-.53-.918z" clip-rule="evenodd"/></svg>
                    {{ __('filament/dashboard_preferences.reset_defaults') }}
                </button>
            </div>
        </div>
    </form>

    <link rel="stylesheet" href="{{ asset('css/views/filament/pages/dashboard-preferences.css') }}">
</x-filament-panels::page>
