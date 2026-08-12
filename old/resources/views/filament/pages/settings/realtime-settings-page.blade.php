<x-filament-panels::page>
    <x-filament::section>
        <form wire:submit.prevent="save">
            {{ $this->form }}
        </form>
    </x-filament::section>
</x-filament-panels::page>
