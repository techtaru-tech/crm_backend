@extends('portal._layout')
@section('title', __('portal.page_title_sign_in'))

@section('content')
<link rel="stylesheet" href="{{ asset('css/views/portal/login.css') }}">
<div class="pl-wrap">
    <div class="card">
        <h1>{{ __('portal.sign_in') }}</h1>
        <p class="pl-lead">{{ __('portal.login_lead') }}</p>
        <form method="POST" action="{{ route('portal.login.send') }}">
            @csrf
            <input type="email" name="email" required autofocus placeholder="{{ __('portal.email_placeholder') }}" value="{{ old('email') }}">
            @error('email')<p class="pl-error">{{ $message }}</p>@enderror
            <button type="submit" class="btn btn-primary pl-submit">{{ __('portal.send_login_link') }}</button>
        </form>
        <p class="pl-foot">
            {{ __('portal.login_foot') }}
        </p>
    </div>
</div>
@endsection
