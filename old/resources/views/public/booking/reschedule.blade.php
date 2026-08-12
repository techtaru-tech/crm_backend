@php
    use Illuminate\Support\Carbon;
    $tz     = $booking->timezone ?: 'UTC';
    $starts = Carbon::parse($booking->starts_at)->setTimezone($tz);
@endphp
<!DOCTYPE html>
<html lang="{{ app()->getLocale() }}">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>{{ __('booking.reschedule_title_prefix', ['name' => $meetingType->name]) }}</title>
    <link rel="stylesheet" href="{{ asset('css/views/public/booking/reschedule.css') }}">
</head>
<body>
<div class="wrap">
    <h1>{{ __('booking.reschedule_heading', ['name' => $meetingType->name]) }}</h1>
    <p class="sub">{{ __('booking.reschedule_currently_for') }} <strong>{{ $starts->translatedFormat('l, M j, Y g:i A') }} ({{ $tz }})</strong></p>
    <div class="note">{{ __('booking.reschedule_note') }}</div>
    <p class="br-actions">
        <a class="btn btn-primary" href="{{ url('/book/' . $host->id . '/' . $meetingType->slug) }}">{{ __('booking.reschedule_pick_new_time') }}</a>
        <a class="btn btn-ghost" href="{{ route('book.confirmation', ['token' => $booking->reschedule_token]) }}">{{ __('booking.reschedule_back_to_booking') }}</a>
    </p>
    {{-- `data-confirm` is consumed by public/js/views/shared/confirm-form.js
         (declarative replacement for the legacy onsubmit handler). --}}
    <form method="POST" action="{{ route('book.cancel', ['token' => $booking->reschedule_token]) }}" class="br-cancel-form" data-confirm="{{ __('booking.confirm_cancel_existing') }}">
        @csrf
        <input type="hidden" name="reason" value="{{ __('booking.reschedule_reason_value') }}">
        <button type="submit" class="btn btn-ghost br-cancel-btn">{{ __('booking.cancel_existing_booking') }}</button>
    </form>
</div>
<script src="{{ asset('js/views/shared/confirm-form.js') }}" defer></script>
</body>
</html>
