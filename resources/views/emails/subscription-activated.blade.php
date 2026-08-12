@extends('emails.layout')

@section('content')
    <h1 style="font-size:22px;font-weight:700;color:#111827;margin:0 0 16px;">{{ __('mail.subscription_activated_heading', ['plan' => $planName]) }}</h1>

    <p style="margin:0 0 16px;line-height:1.6;">{{ __('mail.subscription_activated_greeting') }}</p>

    <p style="margin:0 0 16px;line-height:1.6;">
        {!! __('mail.subscription_activated_body', ['workspace' => '<strong>' . e($workspaceName) . '</strong>', 'app' => e($appName)]) !!}
    </p>

    @if ($billingCycle)
        @php
            // Translator-first billing cycle label so the email respects recipient locale.
            // Unknown future cycles fall back to ucfirst() of the raw slug.
            $cycleKey   = 'mail.billing_cycle_' . $billingCycle;
            $cycleTrans = __($cycleKey);
            $cycleLabel = is_string($cycleTrans) && $cycleTrans !== $cycleKey
                ? $cycleTrans
                : ucfirst((string) $billingCycle);
        @endphp
        <p style="margin:0 0 16px;line-height:1.6;">
            {!! __('mail.subscription_activated_billing_cycle', ['cycle' => '<strong>' . e($cycleLabel) . '</strong>']) !!}
        </p>
    @endif

    <div style="text-align:center;margin:32px 0;">
        <a href="{{ $manageUrl }}" style="display:inline-block;background:{{ $primaryColor }};color:#ffffff;padding:14px 28px;border-radius:8px;font-weight:600;text-decoration:none;font-size:15px;">
            {{ __('mail.subscription_activated_button') }}
        </a>
    </div>

    <p style="margin:24px 0 0;line-height:1.6;color:#6b7280;font-size:13px;">
        {{ __('mail.subscription_activated_footer') }}
    </p>
@endsection
