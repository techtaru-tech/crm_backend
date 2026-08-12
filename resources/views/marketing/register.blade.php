@extends('marketing.layout')

@section('title', __('marketing.register_page_title', ['app' => $appName]))
@section('description', __('marketing.register_page_description', ['days' => $trialDays]))

@push('head')
<link rel="stylesheet" href="{{ asset('css/marketing/register.css') }}">
@endpush

@section('content')
<div class="reg-wrap">
    <div class="card">
        <div class="trial-badge">
            <svg width="12" height="12" viewBox="0 0 20 20" fill="currentColor"><path d="M10 2a8 8 0 100 16 8 8 0 000-16zm1 11.5a1 1 0 11-2 0v-3a1 1 0 112 0v3zm-1-6.5a1 1 0 110-2 1 1 0 010 2z"/></svg>
            {{ __('marketing.register_trial_badge', ['days' => $trialDays]) }}
        </div>

        <h1>{{ __('marketing.register_h1') }}</h1>
        <p class="lead">{{ __('marketing.register_lead', ['app' => $appName]) }}</p>

        @if($errors->any())
            <div class="errs">
                <strong>{{ __('marketing.register_errs_intro') }}</strong>
                <ul>
                    @foreach($errors->all() as $error)
                        <li>{{ $error }}</li>
                    @endforeach
                </ul>
            </div>
        @endif

        {{-- reCAPTCHA v3: loader script + IIFE that executes for
             the 'register' action and writes the token into the
             hidden input below.  Empty when SA has reCAPTCHA off,
             so the form degrades to plain POST. --}}
        {!! $recaptchaScript ?? '' !!}

        <form method="POST" action="/register" autocomplete="off">
            @csrf
            @if($recaptchaActive ?? false)
                <input type="hidden" name="g-recaptcha-response" value="">
            @endif

            <div class="row" id="ws-row">
                <label for="workspace_name">{{ __('marketing.register_workspace_label') }}</label>
                <input id="workspace_name" type="text" name="workspace_name"
                       value="{{ old('workspace_name') }}"
                       placeholder="{{ __('marketing.register_workspace_placeholder') }}" required>
                <div class="hint">
                    {{ __('marketing.register_workspace_hint_pre') }}
                    <span id="ws-url-preview" class="ws-url-preview">{{ url('/') }}/{{ __('marketing.register_placeholder_slug') }}</span>{!! __('marketing.register_workspace_hint_post') !!}
                </div>
                <div class="hint ws-url-warn" id="ws-url-warn"></div>
            </div>
            {{-- Dynamic config for register.js — base URL + reserved slug list +
                 translated warning template.  Mirrors Str::slug() on the server
                 to give live feedback before the user submits.

                 The payload is pre-built in a PHP block and emitted via a
                 single variable. Inlining the array literal directly inside
                 the json directive can trigger a paren-balancer bug in the
                 Blade compiler when the literal contains nested function
                 calls — the compiled output gets truncated mid-array. --}}
            @php
                $lhRegisterCfg = [
                    'base'            => rtrim(url('/'), '/'),
                    'reserved'        => \App\Support\ReservedSlugs::ALL,
                    'warnTemplate'    => __('marketing.register_slug_reserved_warn'),
                    'placeholderSlug' => __('marketing.register_placeholder_slug'),
                ];
            @endphp
            <script type="application/json" id="lh-register-cfg">@json($lhRegisterCfg)</script>
            <script src="{{ asset('js/views/marketing/register.js') }}" defer></script>

            <div class="row">
                <label for="name">{{ __('marketing.register_name_label') }}</label>
                <input id="name" type="text" name="name" value="{{ old('name') }}" placeholder="{{ __('marketing.register_name_placeholder') }}" required>
            </div>

            <div class="row">
                <label for="email">{{ __('marketing.register_email_label') }}</label>
                <input id="email" type="email" name="email" value="{{ old('email') }}" placeholder="{{ __('marketing.register_email_placeholder') }}" required>
            </div>

            <div class="row">
                <label for="password">{{ __('marketing.register_password_label') }}</label>
                <input id="password" type="password" name="password" placeholder="{{ __('marketing.register_password_placeholder') }}" required>
                <div class="hint">{{ __('marketing.register_password_hint') }}</div>
            </div>

            <div class="row">
                <label for="password_confirmation">{{ __('marketing.register_password_confirm_label') }}</label>
                <input id="password_confirmation" type="password" name="password_confirmation" required>
            </div>

            @if(!empty($selectedPlan))
                <input type="hidden" name="plan" value="{{ $selectedPlan }}">
            @endif

            <div class="row">
                <label for="coupon">{{ __('marketing.register_coupon_label') }} <span class="label-optional">{{ __('marketing.register_coupon_optional') }}</span></label>
                <input id="coupon" type="text" name="coupon"
                       value="{{ old('coupon', request('coupon', '')) }}"
                       placeholder="{{ __('marketing.register_coupon_placeholder') }}"
                       autocomplete="off"
                       maxlength="64">
                <div class="hint">{{ __('marketing.register_coupon_hint') }}</div>
            </div>

            @if (session('coupon_warning'))
                <div class="coupon-warning">
                    {{ session('coupon_warning') }}
                </div>
            @endif

            <div class="terms">
                <input type="checkbox" id="terms" name="terms" value="1" {{ old('terms') ? 'checked' : '' }}>
                <label for="terms">
                    {{ __('marketing.register_terms_pre') }} <a href="{{ $termsUrl ?? '/pages/terms' }}" target="_blank" rel="noopener">{{ __('marketing.register_terms_tos') }}</a> {{ __('marketing.register_terms_and') }} <a href="{{ $privacyUrl ?? '/pages/privacy' }}" target="_blank" rel="noopener">{{ __('marketing.register_terms_privacy') }}</a>.
                </label>
            </div>

            <button type="submit">{{ __('marketing.register_submit') }}</button>
        </form>

        <div class="alt">
            {{ __('marketing.register_alt_have_account') }} <a href="/admin/login">{{ __('marketing.register_alt_signin') }}</a>
        </div>
    </div>
</div>
@endsection
