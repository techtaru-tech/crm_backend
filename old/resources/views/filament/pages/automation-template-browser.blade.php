<x-filament-panels::page>
    <link rel="stylesheet" href="{{ asset('css/views/filament/pages/automation-template-browser.css') }}">

    <p class="atb-intro">
        {{ __('filament/automation_template_browser.intro') }}
    </p>

    <div class="atb-grid">
        @foreach ($templates as $tpl)
            <div class="atb-card">
                <h3 class="atb-card-name">{{ $tpl['name'] }}</h3>
                <p class="atb-card-desc">{{ $tpl['description'] }}</p>
                <div class="atb-meta">
                    <span class="atb-badge">{{ $tpl['trigger_type'] }}</span>
                    <span class="atb-step-count">{{ trans_choice('filament/automation_template_browser.step_count', $tpl['step_count'], ['count' => $tpl['step_count']]) }}</span>
                </div>
                <button
                    type="button"
                    class="atb-btn"
                    wire:click="useTemplate(@js($tpl['key']))"
                    wire:loading.attr="disabled"
                    wire:target="useTemplate"
                >
                    <svg viewBox="0 0 20 20" fill="currentColor"><path d="M10 3a1 1 0 011 1v5h5a1 1 0 110 2h-5v5a1 1 0 11-2 0v-5H4a1 1 0 110-2h5V4a1 1 0 011-1z"/></svg>
                    {{ __('filament/automation_template_browser.use_template') }}
                </button>
            </div>
        @endforeach
    </div>
</x-filament-panels::page>
