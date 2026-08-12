@extends('emails.layout')

@section('content')
    <h1 style="font-size:22px;font-weight:700;color:#111827;margin:0 0 16px;">{{ __('mail.trial_expired_heading') }}</h1>

    <p style="margin:0 0 16px;line-height:1.6;">{{ __('mail.trial_expired_greeting') }}</p>

    <p style="margin:0 0 16px;line-height:1.6;">
        {!! __('mail.trial_expired_body', ['workspace' => '<strong>' . e($workspaceName) . '</strong>', 'app' => e($appName)]) !!}
    </p>

    <p style="margin:0 0 24px;line-height:1.6;">
        {{ __('mail.trial_expired_pick_plan') }}
    </p>

    <div style="text-align:center;margin:32px 0;">
        <a href="{{ $upgradeUrl }}" style="display:inline-block;background:{{ $primaryColor }};color:#ffffff;padding:14px 28px;border-radius:8px;font-weight:600;text-decoration:none;font-size:15px;">
            {{ __('mail.trial_expired_button') }}
        </a>
    </div>

    <p style="margin:24px 0 0;line-height:1.6;color:#6b7280;font-size:13px;">
        {{ __('mail.trial_expired_footer') }}
    </p>
@endsection
