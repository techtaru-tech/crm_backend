@extends('emails.layout')

@section('content')
<p style="margin: 0 0 16px 0; font-size: 16px; color: #374151;">{{ __('mail.invitation_hello') }}</p>

<p style="margin: 0 0 16px 0; font-size: 16px; color: #374151;">
    {!! __('mail.invitation_body', [
        'inviter'   => '<strong>' . e($inviterName) . '</strong>',
        'workspace' => '<strong>' . e($workspaceName) . '</strong>',
        'app'       => '<strong>' . e($appName) . '</strong>',
        'role'      => '<strong>' . e($roleName) . '</strong>',
    ]) !!}
</p>

<div style="text-align: center; margin: 32px 0;">
    <a href="{{ $acceptUrl }}"
       style="display:inline-block;background-color:{{ $primaryColor }};color:#ffffff !important;padding:14px 28px;border-radius:6px;text-decoration:none;font-size:16px;font-weight:600;">
        <span style="color:#ffffff;">{{ __('mail.invitation_button') }}</span>
    </a>
</div>

<p style="margin: 0 0 8px 0; font-size: 14px; color: #6b7280;">
    {{ __('mail.invitation_expiry') }}
</p>
<p style="margin: 0; font-size: 14px; color: #6b7280;">
    {{ __('mail.invitation_ignore') }}
</p>
@endsection
