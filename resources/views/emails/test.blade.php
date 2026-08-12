@extends('emails.layout')

@section('content')
    <h1 style="font-size:22px;font-weight:700;color:#111827;margin:0 0 16px;">{{ __('mail.test_heading') }}</h1>
    <p style="margin:0 0 16px;line-height:1.65;">{{ __('mail.test_greeting', ['name' => $user->name]) }}</p>
    <p style="margin:0 0 16px;line-height:1.65;">{!! __('mail.test_body', ['app' => '<strong>' . e($appName) . '</strong>']) !!}</p>
    <p style="margin:0 0 16px;line-height:1.65;">{{ __('mail.test_continued') }}</p>
    <a href="{{ url('/admin') }}" style="display:inline-block;padding:12px 28px;background:{{ $primaryColor ?? '#4f46e5' }};color:#ffffff !important;font-weight:600;font-size:15px;text-decoration:none;border-radius:8px;margin:20px 0;mso-padding-alt:0;text-align:center;">
        <!--[if mso]><i style="letter-spacing:28px;mso-font-width:-100%;mso-text-raise:18pt">&nbsp;</i><![endif]-->
        <span style="color:#ffffff;">{{ __('mail.test_button') }}</span>
        <!--[if mso]><i style="letter-spacing:28px;mso-font-width:-100%">&nbsp;</i><![endif]-->
    </a>
@endsection
