@php
    /** @var array{items:array,done:int,total:int,progress:int} $data */
@endphp
<x-filament-widgets::widget>
    {{--
        Static styles live in public/css/views/filament/widgets/getting-started-checklist.css.
        DYNAMIC values kept inline: progress bar width, item-card
        background alpha (done state), check-circle background color
        (done state).  Hover effect moved from onmouseover/onmouseout
        attribute handlers to CSS :hover pseudo-class.
    --}}
    <div class="gsc-card">
        <div class="gsc-header">
            <div>
                <p class="gsc-eyebrow">{{ __('filament/getting_started.eyebrow') }}</p>
                <h3 class="gsc-title">{{ __('filament/getting_started.title', ['total' => $total]) }}</h3>
                <p class="gsc-subtitle">
                    {{ __('filament/getting_started.subtitle') }}
                </p>
            </div>
            <div class="gsc-counter">
                <div class="gsc-counter-value">{{ __('filament/getting_started.counter_format', ['done' => $done, 'total' => $total]) }}</div>
                <div class="gsc-counter-percent">{{ __('filament/getting_started.counter_percent', ['progress' => $progress]) }}</div>
            </div>
        </div>

        <div class="gsc-progress-track">
            {{-- progress bar width is dynamic --}}
            <div class="gsc-progress-fill" style="width:{{ $progress }}%;"></div>
        </div>

        <div class="gsc-items">
            @foreach($items as $item)
                {{-- background alpha varies with $item['done'] (dynamic) --}}
                <a href="{{ $item['url'] }}" class="gsc-item" style="background:rgba(255,255,255,{{ $item['done'] ? '.12' : '.22' }});">
                    {{-- check-circle background flips between green (done) and translucent white (not-done) --}}
                    <div class="gsc-check" style="background:{{ $item['done'] ? '#10b981' : 'rgba(255,255,255,.25)' }};">
                        @if($item['done'])
                            <svg width="14" height="14" viewBox="0 0 20 20" fill="currentColor"><path fill-rule="evenodd" d="M16.704 4.153a.75.75 0 01.143 1.052l-8 10.5a.75.75 0 01-1.127.075l-4.5-4.5a.75.75 0 011.06-1.06l3.894 3.893 7.48-9.817a.75.75 0 011.05-.143z" clip-rule="evenodd"/></svg>
                        @else
                            <x-dynamic-component :component="$item['icon']" class="gsc-check-icon-not-done"/>
                        @endif
                    </div>
                    <div class="gsc-item-body">
                        {{-- label gets line-through when done (class flag) --}}
                        <div class="gsc-item-label {{ $item['done'] ? 'gsc-item-label-done' : '' }}">{{ $item['label'] }}</div>
                        <div class="gsc-item-description">{{ $item['description'] }}</div>
                    </div>
                </a>
            @endforeach
        </div>
    </div>

    <link rel="stylesheet" href="{{ asset('css/views/filament/widgets/getting-started-checklist.css') }}">
</x-filament-widgets::widget>
