@extends('emails.layout')

@section('content')
    <h1 style="font-size:22px;font-weight:700;color:#111827;margin:0 0 16px;">{{ __('mail.subscription_cancelled_heading') }}</h1>

    <p style="margin:0 0 16px;line-height:1.6;">{{ __('mail.subscription_cancelled_greeting') }}</p>

    <p style="margin:0 0 16px;line-height:1.6;">
        {!! __('mail.subscription_cancelled_intro', ['workspace' => '<strong>' . e($workspaceName) . '</strong>', 'app' => e($appName)]) !!}
        @if ($endsAt)
            {!! __('mail.subscription_cancelled_ends_at', ['date' => '<strong>' . e($endsAt) . '</strong>']) !!}
        @else
            {{ __('mail.subscription_cancelled_immediate') }}
        @endif
    </p>

    <p style="margin:0 0 16px;line-height:1.6;color:#6b7280;">
        {{ __('mail.subscription_cancelled_data_safe') }}
    </p>

    @if ($reason)
        <p style="margin:0 0 16px;line-height:1.6;color:#6b7280;font-size:13px;">
            {!! __('mail.subscription_cancelled_reason', ['reason' => '<em>' . e($reason) . '</em>']) !!}
        </p>
    @endif

    <div style="text-align:center;margin:32px 0;">
        <a href="{{ $reactivateUrl }}" style="display:inline-block;background:{{ $primaryColor }};color:#ffffff;padding:14px 28px;border-radius:8px;font-weight:600;text-decoration:none;font-size:15px;">
            {{ __('mail.subscription_cancelled_button') }}
        </a>
    </div>

    <p style="margin:24px 0 0;line-height:1.6;color:#6b7280;font-size:13px;">
        {{ __('mail.subscription_cancelled_footer') }}
    </p>
@endsection
