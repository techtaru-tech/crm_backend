<x-filament-panels::page>
    <link rel="stylesheet" href="{{ asset('css/views/filament/pages/meeting-availability.css') }}">
    <form wire:submit="save">
        {{ $this->form }}
        <div class="ma-actions">
            <x-filament::button type="submit" color="primary">
                {{ __('filament/meeting_availability.save_availability') }}
            </x-filament::button>
        </div>
    </form>
</x-filament-panels::page>
