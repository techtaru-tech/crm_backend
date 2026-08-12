@php
    $headline = $content['headline'] ?? '';
    // Prefer the dedicated `faqs` key (new distinct state path); fall
    // back to the legacy shared `items` key for sections saved before
    // the repeater-collision fix / not yet data-migrated.
    $items    = $content['faqs'] ?? $content['items'] ?? [];
@endphp
<section class="lp-section lp-faq">
<link rel="stylesheet" href="{{ asset('css/views/public/landing/sections/faq.css') }}">
<div class="lp-wrap">
    @if($headline)<h2>{{ $headline }}</h2>@endif
    <div class="lp-faq-list">
        @foreach($items as $item)
            <details>
                <summary>{{ $item['question'] ?? '' }}</summary>
                <div class="lp-faq-answer">{{ $item['answer'] ?? '' }}</div>
            </details>
        @endforeach
    </div>
</div>
</section>
