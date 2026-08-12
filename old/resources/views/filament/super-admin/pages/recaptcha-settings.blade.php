<x-filament-panels::page>
    <link rel="stylesheet" href="{{ asset('css/views/filament/super-admin/pages/recaptcha-settings.css') }}">
    <form wire:submit="save">
        {{ $this->form }}

        <div class="rs-actions">
            <x-filament::button type="submit">
                {{ __('filament/sa_recaptcha.action_save') }}
            </x-filament::button>
        </div>
    </form>
</x-filament-panels::page>
