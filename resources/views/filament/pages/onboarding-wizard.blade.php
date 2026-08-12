<x-filament-panels::page>
    {{-- No max-width wrapper: the wizard fills the available panel
         width so wide wizard steps (repeater rows, 2-column form
         layouts, etc.) don't force a horizontal scrollbar.  Full
         width is also set at the Page class level via
         getMaxContentWidth(). --}}
    <link rel="stylesheet" href="{{ asset('css/views/filament/pages/onboarding-wizard.css') }}">
    <div class="ow-hero">
        <div class="ow-hero-row">
            <div class="ow-hero-icon-wrap">
                <svg xmlns="http://www.w3.org/2000/svg" width="32" height="32" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.8">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M9.813 15.904 9 18.75l-.813-2.846a4.5 4.5 0 0 0-3.09-3.09L2.25 12l2.847-.813a4.5 4.5 0 0 0 3.09-3.09L9 5.25l.813 2.846a4.5 4.5 0 0 0 3.09 3.09L15.75 12l-2.847.813a4.5 4.5 0 0 0-3.09 3.09ZM18.259 8.715 18 9.75l-.259-1.035a3.375 3.375 0 0 0-2.455-2.456L14.25 6l1.036-.259a3.375 3.375 0 0 0 2.455-2.456L18 2.25l.259 1.035a3.375 3.375 0 0 0 2.456 2.456L21.75 6l-1.035.259a3.375 3.375 0 0 0-2.456 2.456Z"/>
                </svg>
            </div>
            <div>
                <h2 class="ow-hero-title">{{ __('filament/onboarding_wizard.hero_title') }}</h2>
                <p class="ow-hero-sub">{{ __('filament/onboarding_wizard.hero_subtitle') }}</p>
            </div>
        </div>
    </div>

    <form wire:submit="complete">
        {{ $this->form }}
    </form>
</x-filament-panels::page>
