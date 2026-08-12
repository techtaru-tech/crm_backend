<x-filament-panels::page>
    <link rel="stylesheet" href="{{ asset('css/views/filament/pages/settings/_form-actions.css') }}">

    <form wire:submit="save">
        {{ $this->form }}
        <div class="fs-form-actions-multi">
            <x-filament::button wire:click="testStorage" color="gray" type="button">{{ __('filament/storage_settings.test_connection') }}</x-filament::button>
            <x-filament::button type="submit" color="primary">{{ __('filament/storage_settings.save_settings') }}</x-filament::button>
        </div>
    </form>
</x-filament-panels::page>
