<x-filament-widgets::widget>
    <link rel="stylesheet" href="{{ asset('css/views/filament/super-admin/widgets/setup-checklist.css') }}">
    <x-filament::section>
        <x-slot name="heading">{{ __('filament/sa_setup_checklist.heading') }}</x-slot>
        <x-slot name="description">{{ __('filament/sa_setup_checklist.description') }}</x-slot>

        <ul class="scl-list">
            @foreach ($items as $item)
                <li class="scl-item">
                    @if ($item['done'])
                        <span aria-label="{{ __('filament/sa_setup_checklist.aria_done') }}" class="scl-tick">&check;</span>
                        <span class="scl-done">{{ $item['label'] }}</span>
                    @else
                        <span aria-label="{{ __('filament/sa_setup_checklist.aria_todo') }}" class="scl-pending">&#9675;</span>
                        <a href="{{ $item['href'] }}" class="scl-link">{{ $item['label'] }}</a>
                    @endif
                </li>
            @endforeach
        </ul>
    </x-filament::section>
</x-filament-widgets::widget>
