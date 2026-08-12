@php
    use Illuminate\Support\Carbon;
    $tz     = $booking->timezone ?: 'UTC';
    $starts = Carbon::parse($booking->starts_at)->setTimezone($tz);
    $ends   = Carbon::parse($booking->ends_at)->setTimezone($tz);
    $type   = $booking->meetingType;
    $host   = $booking->user;
@endphp
<!DOCTYPE html>
<html><head><meta charset="utf-8"><title>{{ __('mail.meeting_booked_title') }}</title></head>
<body style="font-family:system-ui,Arial,sans-serif;background:#f3f4f6;padding:24px;margin:0;color:#111827;">
<div style="max-width:560px;margin:0 auto;background:#fff;border-radius:12px;padding:28px;border:1px solid #e5e7eb;">
    <div style="border-left:4px solid #4f46e5;padding-left:12px;margin-bottom:20px;">
        <h1 style="margin:0;font-size:20px;">{{ $forHost ? __('mail.meeting_booked_heading_host') : __('mail.meeting_booked_heading_guest') }}</h1>
        <p style="margin:4px 0 0;color:#6b7280;font-size:14px;">{{ $type?->name }}</p>
    </div>
    <table style="width:100%;font-size:14px;border-collapse:collapse;">
        <tr><td style="padding:6px 0;color:#6b7280;width:120px;">{{ __('mail.meeting_booked_label_when') }}</td><td style="padding:6px 0;"><strong>{{ $starts->translatedFormat('l, M j, Y') }}</strong><br>{{ $starts->translatedFormat('g:i A') }} – {{ $ends->translatedFormat('g:i A') }} ({{ $tz }})</td></tr>
        @if($forHost)
            <tr><td style="padding:6px 0;color:#6b7280;">{{ __('mail.meeting_booked_label_guest') }}</td><td style="padding:6px 0;">{{ $booking->guest_name }} &lt;{{ $booking->guest_email }}&gt;</td></tr>
            @if($booking->guest_phone)<tr><td style="padding:6px 0;color:#6b7280;">{{ __('mail.meeting_booked_label_phone') }}</td><td style="padding:6px 0;">{{ $booking->guest_phone }}</td></tr>@endif
        @else
            <tr><td style="padding:6px 0;color:#6b7280;">{{ __('mail.meeting_booked_label_host') }}</td><td style="padding:6px 0;">{{ $host?->name }}</td></tr>
        @endif
        @if($type?->location_type)
            <tr><td style="padding:6px 0;color:#6b7280;">{{ __('mail.meeting_booked_label_location') }}</td><td style="padding:6px 0;">
                {{ match($type->location_type) {
                    'google_meet' => __('mail.meeting_booked_location_google_meet'),
                    'zoom' => __('mail.meeting_booked_location_zoom'),
                    'phone' => __('mail.meeting_booked_location_phone'),
                    'in_person' => __('mail.meeting_booked_location_in_person'),
                    default => __('mail.meeting_booked_location_default'),
                } }}
                @if($type->location_details)<br><span style="color:#6b7280;white-space:pre-line;">{{ $type->location_details }}</span>@endif
            </td></tr>
        @endif
        @if($booking->notes)<tr><td style="padding:6px 0;color:#6b7280;">{{ __('mail.meeting_booked_label_notes') }}</td><td style="padding:6px 0;white-space:pre-line;">{{ $booking->notes }}</td></tr>@endif
    </table>
    <div style="margin-top:20px;">
        <a href="{{ $booking->reschedule_url }}" style="display:inline-block;background:#4f46e5;color:#fff;padding:10px 16px;border-radius:8px;text-decoration:none;font-weight:500;">{{ __('mail.meeting_booked_btn_reschedule') }}</a>
        <a href="{{ $booking->cancel_url }}" style="display:inline-block;padding:10px 16px;border-radius:8px;text-decoration:none;color:#4f46e5;border:1px solid #e5e7eb;margin-left:6px;">{{ __('mail.meeting_booked_btn_cancel') }}</a>
    </div>
    <p style="margin-top:20px;font-size:12px;color:#9ca3af;">{{ __('mail.meeting_booked_ics_note') }}</p>
</div>
</body></html>
