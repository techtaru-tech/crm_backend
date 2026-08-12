@php
    $appName = $invitation->tenant->getAppName();
    $workspaceName = $invitation->tenant->name;
    $roleKey = 'invitation_accept.role_' . strtolower($invitation->role);
    $roleLabel = \Illuminate\Support\Facades\Lang::has($roleKey)
        ? __($roleKey)
        : ucfirst($invitation->role);
    $expires = $invitation->expires_at->diffForHumans();
@endphp
<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>{{ __('auth_pages.invitation_accept_title', ['app' => $appName]) }}</title>
    <link rel="icon" href="/favicon.svg" type="image/svg+xml">
    {{-- Inter font self-hosted under public/vendor/ — no external CDN load. --}}
    <link rel="stylesheet" href="{{ asset('vendor/fonts/inter/inter.css') }}">
    <link rel="stylesheet" href="{{ asset('css/auth/invitation-accept.css') }}">
</head>
<body>
    <div class="wrap">
        <div class="brand">
            <img src="/favicon.svg" alt="{{ $appName }}">
            {{ $appName }}
        </div>

        <div class="intro">
            <div class="pill">
                <svg width="12" height="12" viewBox="0 0 20 20" fill="currentColor" aria-hidden="true"><path d="M2.003 5.884L10 9.882l7.997-3.998A2 2 0 0016 4H4a2 2 0 00-1.997 1.884z"/><path d="M18 8.118l-8 4-8-4V14a2 2 0 002 2h12a2 2 0 002-2V8.118z"/></svg>
                {{ __('auth_pages.invitation_pill') }}
            </div>
            <h1>{!! __('auth_pages.invitation_youre_invited', ['workspace' => '<strong>'.e($workspaceName).'</strong>']) !!}</h1>
            <p>{!! __('auth_pages.invitation_role_on_app', ['role' => '<strong>'.e($roleLabel).'</strong>', 'app' => '<strong>'.e($appName).'</strong>']) !!}</p>
        </div>

        <div class="card">
            <h2>{{ __('auth_pages.create_your_account') }}</h2>

            @if ($errors->any())
                <div class="errs">
                    <strong>{{ __('auth_pages.fix_the_following') }}</strong>
                    <ul>
                        @foreach ($errors->all() as $error)
                            <li>{{ $error }}</li>
                        @endforeach
                    </ul>
                </div>
            @endif

            {!! $recaptchaScript ?? '' !!}
            <form method="POST" action="{{ $postAction }}" autocomplete="off">
                @csrf
                @if($recaptchaActive ?? false)
                    <input type="hidden" name="g-recaptcha-response" value="">
                @endif

                <div class="row">
                    <label for="email">{{ __('auth_pages.email_label') }}</label>
                    <input id="email" type="email" value="{{ $invitation->email }}" readonly>
                </div>

                <div class="row">
                    <label for="name">{{ __('auth_pages.your_name_label') }}</label>
                    <input id="name" type="text" name="name" value="{{ old('name') }}"
                           placeholder="{{ __('auth_pages.name_placeholder') }}" required autofocus autocomplete="name">
                </div>

                <div class="row">
                    <label for="password">{{ __('auth_pages.password_label') }}</label>
                    <input id="password" type="password" name="password"
                           placeholder="{{ __('auth_pages.password_min_placeholder') }}" required
                           minlength="8" autocomplete="new-password">
                    <div class="hint">{{ __('auth_pages.password_strength_hint') }}</div>
                </div>

                <div class="row">
                    <label for="password_confirmation">{{ __('auth_pages.confirm_password_label') }}</label>
                    <input id="password_confirmation" type="password" name="password_confirmation"
                           required autocomplete="new-password">
                </div>

                <button type="submit">{{ __('auth_pages.accept_invitation_submit') }}</button>
            </form>
        </div>

        <div class="foot">
            {{ __('auth_pages.invitation_expires_at', ['when' => $expires]) }}
        </div>
    </div>
</body>
</html>
