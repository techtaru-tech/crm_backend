@extends('emails.layout')

@section('content')
<p style="margin:0 0 16px 0;font-size:16px;color:#374151;">{{ __('mail.tenant_welcome_hello') }}</p>

<p style="margin:0 0 16px 0;font-size:16px;color:#374151;">
    {!! __('mail.tenant_welcome_intro', ['workspace' => '<strong>' . e($workspaceName) . '</strong>', 'app' => '<strong>' . e($appName) . '</strong>']) !!}
    @if($userSetPassword)
        {{ __('mail.tenant_welcome_user_set_password') }}
    @else
        {{ __('mail.tenant_welcome_admin_created') }}
    @endif
</p>

<div style="background:#f9fafb;border:1px solid #e5e7eb;border-radius:8px;padding:20px;margin:24px 0;">
    <table cellpadding="0" cellspacing="0" border="0" width="100%">
        <tr>
            <td style="padding:6px 0;font-size:14px;color:#6b7280;width:100px;">{{ __('mail.tenant_welcome_workspace_label') }}</td>
            <td style="padding:6px 0;font-size:14px;font-weight:600;color:#111827;">{{ $workspaceName }}</td>
        </tr>
        <tr>
            <td style="padding:6px 0;font-size:14px;color:#6b7280;">{{ __('mail.tenant_welcome_email_label') }}</td>
            <td style="padding:6px 0;font-size:14px;font-weight:600;color:#111827;">{{ $userEmail }}</td>
        </tr>
    </table>
</div>

<div style="text-align:center;margin:32px 0;">
    @if($setupUrl && ! $userSetPassword)
        <a href="{{ $setupUrl }}"
           style="display:inline-block;background-color:{{ $primaryColor }};color:#ffffff !important;padding:14px 28px;border-radius:6px;text-decoration:none;font-size:16px;font-weight:600;">
            <span style="color:#ffffff;">{{ __('mail.tenant_welcome_button_set_password') }}</span>
        </a>
    @else
        <a href="{{ $loginUrl }}"
           style="display:inline-block;background-color:{{ $primaryColor }};color:#ffffff !important;padding:14px 28px;border-radius:6px;text-decoration:none;font-size:16px;font-weight:600;">
            <span style="color:#ffffff;">{{ __('mail.tenant_welcome_button_login') }}</span>
        </a>
    @endif
</div>

@if($setupUrl && ! $userSetPassword)
    <p style="margin:0 0 8px 0;font-size:13px;color:#6b7280;text-align:center;">
        {{ __('mail.tenant_welcome_setup_expires') }}
        <a href="{{ $loginUrl }}" style="color:{{ $primaryColor }};">{{ __('mail.tenant_welcome_forgot_password') }}</a>
        {{ __('mail.tenant_welcome_setup_expires_suffix') }}
    </p>
@endif

<p style="margin:24px 0 0 0;font-size:14px;color:#6b7280;">
    {{ __('mail.tenant_welcome_ignore') }}
</p>
@endsection
