<x-filament-panels::page>
    <link rel="stylesheet" href="{{ asset('css/views/filament/pages/settings/_form-actions.css') }}">

    <form wire:submit="save">
        {{ $this->form }}
        <div class="fs-form-actions">
            <x-filament::button type="submit" color="primary">{{ __('filament/messaging_settings.save_settings') }}</x-filament::button>
        </div>
    </form>
</x-filament-panels::page>
