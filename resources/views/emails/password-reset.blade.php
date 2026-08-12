@extends('emails.layout')

@section('content')
<p style="margin:0 0 16px 0;font-size:16px;color:#374151;">{{ __('mail.password_reset_greeting', ['name' => $userName]) }}</p>

<p style="margin:0 0 16px 0;font-size:16px;color:#374151;">
    {!! __('mail.password_reset_intro', ['app' => '<strong>' . e($appName) . '</strong>']) !!}
</p>

<div style="text-align:center;margin:32px 0;">
    <a href="{{ $resetUrl }}"
       style="display:inline-block;background-color:{{ $primaryColor }};color:#ffffff !important;padding:14px 28px;border-radius:6px;text-decoration:none;font-size:16px;font-weight:600;">
        <span style="color:#ffffff;">{{ __('mail.password_reset_button') }}</span>
    </a>
</div>

<p style="margin:0 0 8px 0;font-size:14px;color:#6b7280;">
    {{ __('mail.password_reset_expires', ['minutes' => $expiresMinutes]) }}
</p>
<p style="margin:12px 0 0 0;font-size:13px;color:#9ca3af;word-break:break-all;">
    {{ __('mail.password_reset_fallback') }}<br>
    {{ $resetUrl }}
</p>
@endsection
