<x-filament-panels::page>
    <link rel="stylesheet" href="{{ asset('css/views/filament/pages/settings/email-settings-page.css') }}">

    {{-- Override notice — clarifies that custom SMTP replaces the
         service's default mailer for everything this workspace sends,
         so admins understand the blast radius before changing values. --}}
    <div class="es-info-banner">
        <svg class="es-info-icon" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.8" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" d="M12 9v3.75m9-.75a9 9 0 11-18 0 9 9 0 0118 0zm-9 3.75h.008v.008H12v-.008z"/></svg>
        <div>
            <p class="es-info-title">{{ __('filament/email_settings.override_notice_title') }}</p>
            <p class="es-info-body">
                {!! __('filament/email_settings.override_notice_body_html') !!}
            </p>
        </div>
    </div>

    <form wire:submit="save">
        {{ $this->form }}
        <div class="es-form-actions">
            {{-- "Send Test Email" moved to the header actions bar (top-right)
                 so it opens the modal with a recipient input. The inline
                 button used to skip that modal and always send to the
                 logged-in admin's address. --}}
            <x-filament::button type="submit" color="primary">{{ __('filament/email_settings.save_settings') }}</x-filament::button>
        </div>
    </form>
</x-filament-panels::page>
