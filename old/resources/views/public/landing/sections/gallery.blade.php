@php
    $headline = $content['headline'] ?? '';
    // Prefer the dedicated `gallery_items` key (new distinct state path);
    // fall back to the legacy shared `items` key for sections saved
    // before the repeater-collision fix / not yet data-migrated.
    $items    = $content['gallery_items'] ?? $content['items'] ?? [];
@endphp
<section class="lp-section lp-gallery">
<link rel="stylesheet" href="{{ asset('css/views/public/landing/sections/gallery.css') }}">
<div class="lp-wrap lp-center">
    @if($headline)<h2>{{ $headline }}</h2>@endif
    <div class="lp-grid">
        @foreach($items as $item)
            @if(!empty($item['image_url']))
                <figure>
                    <img src="{{ e($item['image_url']) }}" alt="{{ e($item['caption'] ?? '') }}">
                    @if(!empty($item['caption']))
                        <figcaption>{{ $item['caption'] }}</figcaption>
                    @endif
                </figure>
            @endif
        @endforeach
    </div>
</div>
</section>
