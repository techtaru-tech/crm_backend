@php
    $headline = $content['headline'] ?? '';
    // Prefer the dedicated `testimonials` key (new distinct state path);
    // fall back to the legacy shared `items` key for sections saved
    // before the repeater-collision fix / not yet data-migrated.
    $items    = $content['testimonials'] ?? $content['items'] ?? [];
@endphp
<section class="lp-section lp-testimonials">
<link rel="stylesheet" href="{{ asset('css/views/public/landing/sections/testimonials.css') }}">
<div class="lp-wrap">
    @if($headline)<h2>{{ $headline }}</h2>@endif
    <div class="lp-testimonials-grid">
        @foreach($items as $item)
            <figure class="lp-testimonial">
                <div class="lp-testimonials-stars" aria-label="{{ __('marketing.public_testimonials_stars_aria') }}">
                    @for($i = 0; $i < 5; $i++)
                        <svg width="14" height="14" viewBox="0 0 24 24" fill="currentColor"><polygon points="12 2 15 9 22 9.5 17 14.5 18 21 12 17.5 6 21 7 14.5 2 9.5 9 9"/></svg>
                    @endfor
                </div>
                <blockquote class="lp-testimonial-quote">“{{ $item['quote'] ?? '' }}”</blockquote>
                <figcaption class="lp-testimonial-author">
                    @if(! empty($item['avatar_url']))
                        {{-- data-hide-on-error consumed by public/js/views/shared/hide-on-error.js
                             (declarative replacement for inline onerror= handler). --}}
                        <img class="lp-testimonial-avatar" src="{{ e($item['avatar_url']) }}" alt="" data-hide-on-error="1">
                    @else
                        <div class="lp-testimonial-avatar lp-testimonial-avatar--initials">
                            {{ strtoupper(mb_substr($item['author'] ?? '?', 0, 1)) }}
                        </div>
                    @endif
                    <div>
                        <div class="lp-testimonial-name">{{ $item['author'] ?? '' }}</div>
                        @if(! empty($item['role']))<div class="lp-testimonial-role">{{ $item['role'] }}</div>@endif
                    </div>
                </figcaption>
            </figure>
        @endforeach
    </div>
</div>
</section>
