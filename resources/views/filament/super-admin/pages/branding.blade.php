<x-filament-panels::page>
    {{--
        Renders BrandingPage's form schema and a plain submit
        button.  The surrounding <form wire:submit="save"> binds
        clicks to BrandingPage::save() via Livewire — same proven
        pattern used by EmailBrandingPage + RecaptchaSettingsPage.

        IMPORTANT: do NOT render `$this->getFormActions()[0]` here.
        In Filament 4, echoing a Filament\Actions\Action instance
        inside a wire:submit form dispatches mountAction('save')
        against Livewire, which can't reconcile against the page's
        public save() method (it's wired as a wire:submit handler,
        not as a registered Action) and throws a 500.  Keep the
        button as a plain <x-filament::button type="submit">.
    --}}
    <form wire:submit="save">
        {{ $this->form }}

        {{-- Inline styles (NOT Tailwind utilities like mt-6) because
             Filament 4's production CSS purge strips utility classes
             that don't appear in any vendor blade.  The previous
             `mt-6` div evaporated to `margin-top: 0` and the Save
             button rendered flush against the last section with no
             breathing room (visually "stuck at the top" of where it
             should be).  Inline styles cannot be purged.

             Pattern mirrors RecaptchaSettingsPage's custom `.rs-actions`
             treatment + the standard Filament form-footer look: top
             border separator, generous top padding so the button reads
             as a deliberate "form actions" zone rather than a stray
             element. --}}
        <div style="margin-top: 1.5rem; padding-top: 1.5rem; padding-bottom: 0.5rem; border-top: 1px solid rgb(228 228 231); display: flex; gap: 0.75rem;">
            <x-filament::button type="submit">
                {{ __('filament/sa_branding.action_save') }}
            </x-filament::button>
        </div>
    </form>
</x-filament-panels::page>
