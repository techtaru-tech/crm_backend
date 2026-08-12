@extends('emails.layout')

@section('content')
    <h1 style="font-size:22px;font-weight:700;color:#111827;margin:0 0 16px;">{{ __('mail.tenant_erasure_requested_heading') }}</h1>

    <p style="margin:0 0 16px;line-height:1.6;">{{ __('mail.tenant_erasure_requested_greeting', ['name' => $requesterName]) }}</p>

    <p style="margin:0 0 16px;line-height:1.6;">
        {!! __('mail.tenant_erasure_requested_intro', ['workspace' => '<strong>' . e($workspaceName) . '</strong>', 'app' => e($appName), 'days' => $coolOffDays]) !!}
    </p>

    <p style="margin:0 0 16px;line-height:1.6;">
        {{ __('mail.tenant_erasure_requested_window', ['days' => $coolOffDays]) }}
    </p>

    <div style="text-align:center;margin:32px 0;">
        <a href="{{ $cancelUrl }}" style="display:inline-block;background:{{ $primaryColor }};color:#ffffff;padding:14px 28px;border-radius:8px;font-weight:600;text-decoration:none;font-size:15px;">
            {{ __('mail.tenant_erasure_requested_button') }}
        </a>
    </div>

    <p style="margin:24px 0 0;line-height:1.6;color:#6b7280;font-size:13px;">
        {{ __('mail.tenant_erasure_requested_footer') }}
    </p>
@endsection
