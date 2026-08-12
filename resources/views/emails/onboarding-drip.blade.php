@extends('emails.layout')

@section('content')
    <h1 style="font-size:22px;font-weight:700;color:#111827;margin:0 0 16px;">{{ $copy['heading'] }}</h1>

    <p style="margin:0 0 16px;line-height:1.6;">{{ __('emails/onboarding_drip.greeting', ['name' => $ownerName]) }}</p>

    <p style="margin:0 0 24px;line-height:1.6;white-space:pre-line;">{{ $copy['body'] }}</p>

    <div style="text-align:center;margin:32px 0;">
        <a href="{{ $copy['cta_url'] }}" style="display:inline-block;background:{{ $primaryColor }};color:#ffffff;padding:14px 28px;border-radius:8px;font-weight:600;text-decoration:none;font-size:15px;">
            {{ $copy['cta'] }}
        </a>
    </div>

    <p style="margin:24px 0 0;line-height:1.6;color:#6b7280;font-size:13px;">
        {{ __('emails/onboarding_drip.intro_line', ['day' => $day]) }}
    </p>
@endsection
