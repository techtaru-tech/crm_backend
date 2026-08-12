@php
    $appName = config('leadhub.branding.app_name', 'LeadHub');
@endphp
<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>{{ __('auth_pages.password_setup_title', ['app' => $appName]) }}</title>
    <link rel="icon" href="/favicon.svg" type="image/svg+xml">
    {{-- Inter font self-hosted under public/vendor/ — no external CDN load. --}}
    <link rel="stylesheet" href="{{ asset('vendor/fonts/inter/inter.css') }}">
    <link rel="stylesheet" href="{{ asset('css/views/auth/password-setup.css') }}">
</head>
<body>
    <div class="wrap">
        <div class="brand">
            <img src="/favicon.svg" alt="{{ $appName }}">
            {{ $appName }}
        </div>

        <div class="card">
            <h1>{{ __('auth_pages.set_your_password') }}</h1>
            <p class="lead">
                {!! __('auth_pages.password_setup_lead', ['app' => '<strong>'.e($appName).'</strong>', 'email' => '<strong>'.e($email).'</strong>']) !!}
            </p>

            @if($errors->any())
                <div class="errs">
                    <strong>{{ __('auth_pages.fix_the_following') }}</strong>
                    <ul>
                        @foreach($errors->all() as $error)
                            <li>{{ $error }}</li>
                        @endforeach
                    </ul>
                </div>
            @endif

            <form method="POST" action="{{ url()->current() }}" autocomplete="off">
                @csrf

                <div class="row">
                    <label for="email">{{ __('auth_pages.email_label') }}</label>
                    <input id="email" type="email" name="email" value="{{ $email }}" readonly>
                </div>

                <div class="row">
                    <label for="password">{{ __('auth_pages.new_password_label') }}</label>
                    <input id="password" type="password" name="password"
                           placeholder="{{ __('auth_pages.password_min_placeholder') }}" required autofocus
                           autocomplete="new-password">
                    <div class="hint">{{ __('auth_pages.password_strength_hint') }}</div>
                </div>

                <div class="row">
                    <label for="password_confirmation">{{ __('auth_pages.confirm_password_label') }}</label>
                    <input id="password_confirmation" type="password" name="password_confirmation"
                           required autocomplete="new-password">
                </div>

                <button type="submit">{{ __('auth_pages.set_password_submit') }}</button>
            </form>
        </div>

        <div class="foot">
            {!! __('auth_pages.password_setup_link_expired', ['signin' => '<a href="/admin/login">'.e(__('auth_pages.signin_link')).'</a>']) !!}
        </div>
    </div>
</body>
</html>
