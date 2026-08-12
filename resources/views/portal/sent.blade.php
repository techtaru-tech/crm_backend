@extends('portal._layout')
@section('title', __('portal.page_title_check_email'))

@section('content')
<link rel="stylesheet" href="{{ asset('css/views/portal/sent.css') }}">
<div class="ps-wrap">
    <div class="card ps-card">
        <div class="ps-icon">
            <svg width="28" height="28" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M4 4h16c1.1 0 2 .9 2 2v12c0 1.1-.9 2-2 2H4c-1.1 0-2-.9-2-2V6c0-1.1.9-2 2-2z"/><polyline points="22,6 12,13 2,6"/></svg>
        </div>
        <h1>{{ __('portal.check_your_email') }}</h1>
        <p class="ps-body">
            {{ __('portal.sent_body') }}
        </p>
        <a href="{{ route('portal.login') }}" class="btn btn-ghost ps-alt-btn">{{ __('portal.use_different_email') }}</a>
    </div>
</div>
@endsection
