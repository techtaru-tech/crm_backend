@php
    use Illuminate\Support\Carbon;
    $tz     = $booking->timezone ?: 'UTC';
    $starts = Carbon::parse($booking->starts_at)->setTimezone($tz);
@endphp
<!DOCTYPE html>
<html><head><meta charset="utf-8"><title>{{ __('mail.meeting_cancelled_title') }}</title></head>
<body style="font-family:system-ui,Arial,sans-serif;background:#f3f4f6;padding:24px;margin:0;color:#111827;">
<div style="max-width:560px;margin:0 auto;background:#fff;border-radius:12px;padding:28px;border:1px solid #e5e7eb;">
    <div style="border-left:4px solid #ef4444;padding-left:12px;margin-bottom:20px;">
        <h1 style="margin:0;font-size:20px;">{{ __('mail.meeting_cancelled_title') }}</h1>
        <p style="margin:4px 0 0;color:#6b7280;font-size:14px;">{{ $booking->meetingType?->name }}</p>
    </div>
    <p style="font-size:14px;color:#374151;">{!! __('mail.meeting_cancelled_body', ['when' => '<strong>' . e($starts->translatedFormat('l, M j, Y g:i A')) . '</strong>', 'tz' => '<strong>' . e($tz) . '</strong>']) !!}</p>
    @if($booking->cancellation_reason)
        <p style="font-size:14px;color:#374151;"><strong>{{ __('mail.meeting_cancelled_reason') }}</strong> {{ $booking->cancellation_reason }}</p>
    @endif
    @php
        $host = $booking->user;
        $type = $booking->meetingType;
    @endphp
    @if(! $forHost && $host && $type)
        <p style="font-size:14px;color:#374151;">{{ __('mail.meeting_cancelled_book_again_intro') }} <a href="{{ url('/book/' . $host->id . '/' . $type->slug) }}" style="color:#4f46e5;">{{ __('mail.meeting_cancelled_book_again_link') }}</a>.</p>
    @endif
</div>
</body></html>
