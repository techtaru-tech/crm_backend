@extends('emails.layout')

@section('content')
<h2 style="margin: 0 0 16px 0; font-size: 20px; font-weight: 600; color: #111827;">
    {{ $headline }}
</h2>

@foreach($lines as $line)
<p style="margin: 0 0 12px 0; font-size: 15px; color: #374151; line-height: 1.6;">
    {!! $line !!}
</p>
@endforeach

<div style="text-align: center; margin: 32px 0;">
    <a href="{{ $actionUrl }}"
       style="display:inline-block;background-color:{{ $primaryColor }};color:#ffffff !important;padding:14px 28px;border-radius:6px;text-decoration:none;font-size:15px;font-weight:600;">
        <span style="color:#ffffff;">{{ $actionLabel }}</span>
    </a>
</div>
@endsection
