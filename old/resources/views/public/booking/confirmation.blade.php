@php
    use Illuminate\Support\Carbon;
    $tz     = $booking->timezone ?: 'UTC';
    $starts = Carbon::parse($booking->starts_at)->setTimezone($tz);
    $ends   = Carbon::parse($booking->ends_at)->setTimezone($tz);
    $cancelled = $booking->status === 'cancelled';
@endphp
<!DOCTYPE html>
<html lang="{{ app()->getLocale() }}">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>{{ $cancelled ? __('booking.confirmation_title_cancelled', ['name' => $meetingType->name]) : __('booking.confirmation_title_confirmed', ['name' => $meetingType->name]) }}</title>
    <link rel="stylesheet" href="{{ asset('css/views/public/booking/confirmation.css') }}">
</head>
<body>
<div class="wrap">
    {{-- Cancellation state is conveyed via the .icon--cancelled
         modifier class (not via inline style) so this view contains
         zero inline styles, per CodeCanyon's no-inline-styles rule. --}}
    <div class="icon {{ $cancelled ? 'icon--cancelled' : '' }}" role="img" aria-label="{{ $cancelled ? __('booking.confirmation_aria_cancelled') : __('booking.confirmation_aria_confirmed') }}">
        @if($cancelled)
            <svg aria-hidden="true" focusable="false" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M6 18L18 6M6 6l12 12"/></svg>
        @else
            <svg aria-hidden="true" focusable="false" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M5 13l4 4L19 7"/></svg>
        @endif
    </div>
    <h1>{{ $cancelled ? __('booking.confirmation_heading_cancelled') : __('booking.confirmation_heading_confirmed') }}</h1>
    <p class="sub">{{ $cancelled ? __('booking.confirmation_sub_cancelled') : __('booking.confirmation_sub_confirmed', ['email' => $booking->guest_email]) }}</p>

    <div class="row"><div class="lbl">{{ __('booking.meeting_label_row') }}</div><div class="val">{{ $meetingType->name }}</div></div>
    <div class="row"><div class="lbl">{{ __('booking.host_label_row') }}</div><div class="val">{{ $host->name }}</div></div>
    <div class="row"><div class="lbl">{{ __('booking.when_label_row') }}</div><div class="val">{{ $starts->translatedFormat('l, M j, Y') }}<br>{{ $starts->translatedFormat('g:i A') }} – {{ $ends->translatedFormat('g:i A') }} ({{ $tz }})</div></div>
    <div class="row"><div class="lbl">{{ __('booking.guest_label_row') }}</div><div class="val">{{ $booking->guest_name }}<br><span class="bc-guest-email">{{ $booking->guest_email }}</span></div></div>
    @if($meetingType->location_type !== 'custom' || $meetingType->location_details)
        <div class="row"><div class="lbl">{{ __('booking.location_label_row') }}</div><div class="val">
            {{ match($meetingType->location_type) {
                'google_meet' => __('booking.location_google_meet'),
                'zoom' => __('booking.location_zoom'),
                'phone' => __('booking.location_phone'),
                'in_person' => __('booking.location_in_person'),
                default => __('booking.location_see_details'),
            } }}
            @if($meetingType->location_details)
                <div class="bc-loc-detail">{{ $meetingType->location_details }}</div>
            @endif
        </div></div>
    @endif

    <div class="btn-row">
        @if(! $cancelled)
            <a class="btn btn-primary" href="{{ url('/book/ics/' . $booking->reschedule_token) }}">{{ __('booking.add_to_calendar_ics') }}</a>
            <a class="btn btn-ghost" href="{{ route('book.reschedule', ['token' => $booking->reschedule_token]) }}">{{ __('booking.reschedule_action') }}</a>
            {{-- `data-confirm` is consumed by public/js/views/shared/confirm-form.js
                 (declarative replacement for the legacy onsubmit handler). --}}
            <form method="POST" action="{{ route('book.cancel', ['token' => $booking->reschedule_token]) }}" class="bc-cancel-form" data-confirm="{{ __('booking.confirm_cancel_meeting') }}">
                @csrf
                <button type="submit" class="btn btn-danger">{{ __('booking.cancel') }}</button>
            </form>
        @else
            <a class="btn btn-primary" href="{{ url('/book/' . $host->id . '/' . $meetingType->slug) }}">{{ __('booking.book_new_time') }}</a>
        @endif
    </div>
</div>
<script src="{{ asset('js/views/shared/confirm-form.js') }}" defer></script>
</body>
</html>
