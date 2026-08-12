@extends('emails.layout')

@section('content')
    <div style="background:#fef2f2;border-left:4px solid #dc2626;padding:16px 20px;border-radius:6px;margin:0 0 20px;">
        <h1 style="font-size:22px;font-weight:700;color:#991b1b;margin:0 0 6px;">{{ __('mail.payment_failed_heading') }}</h1>
        <p style="margin:0;color:#7f1d1d;font-size:14px;">{{ __('mail.payment_failed_attempt', ['attempt' => $retryAttempt]) }}</p>
    </div>

    <p style="margin:0 0 16px;line-height:1.6;">{{ __('mail.payment_failed_greeting') }}</p>

    <p style="margin:0 0 16px;line-height:1.6;">
        {!! __('mail.payment_failed_body', ['workspace' => '<strong>' . e($workspaceName) . '</strong>']) !!}
    </p>

    @if ($amount)
        <table cellpadding="0" cellspacing="0" border="0" style="width:100%;margin:16px 0 20px;background:#f9fafb;border-radius:8px;">
            <tr>
                <td style="padding:14px 20px;border-bottom:1px solid #e5e7eb;font-size:14px;color:#6b7280;">{{ __('mail.payment_failed_amount_label') }}</td>
                <td style="padding:14px 20px;border-bottom:1px solid #e5e7eb;font-size:14px;color:#111827;font-weight:600;text-align:right;">
                    {{ $currency ? $currency . ' ' : '' }}{{ $amount }}
                </td>
            </tr>
            @if ($nextRetryAt)
                <tr>
                    <td style="padding:14px 20px;font-size:14px;color:#6b7280;">{{ __('mail.payment_failed_next_retry_label') }}</td>
                    <td style="padding:14px 20px;font-size:14px;color:#111827;font-weight:600;text-align:right;">{{ $nextRetryAt }}</td>
                </tr>
            @endif
        </table>
    @endif

    <p style="margin:0 0 16px;line-height:1.6;">
        {{ __('mail.payment_failed_cta_body') }}
    </p>

    <div style="text-align:center;margin:32px 0;">
        <a href="{{ $updatePaymentUrl }}" style="display:inline-block;background:#dc2626;color:#ffffff;padding:14px 28px;border-radius:8px;font-weight:600;text-decoration:none;font-size:15px;">
            {{ __('mail.payment_failed_button') }}
        </a>
    </div>

    <p style="margin:24px 0 0;line-height:1.6;color:#6b7280;font-size:13px;">
        {{ __('mail.payment_failed_help') }}
    </p>
@endsection
