@extends('emails.layout')

@section('content')
    @php
        $headline = match ($direction) {
            'upgrade'   => __('mail.plan_changed_heading_upgrade',   ['plan' => $newPlan]),
            'downgrade' => __('mail.plan_changed_heading_downgrade', ['plan' => $newPlan]),
            default     => __('mail.plan_changed_heading_default',   ['plan' => $newPlan]),
        };
    @endphp

    <h1 style="font-size:22px;font-weight:700;color:#111827;margin:0 0 16px;">{{ $headline }}</h1>

    <p style="margin:0 0 16px;line-height:1.6;">{{ __('mail.plan_changed_greeting') }}</p>

    <p style="margin:0 0 16px;line-height:1.6;">
        {!! __('mail.plan_changed_body', ['workspace' => '<strong>' . e($workspaceName) . '</strong>', 'app' => e($appName)]) !!}
    </p>

    @php
        // Translator-first plan labels so the email respects recipient locale.
        // Unknown future plans fall back to ucfirst() of the raw slug.
        $oldPlanKey   = 'mail.plan_value_' . $oldPlan;
        $oldPlanTrans = __($oldPlanKey);
        $oldPlanLabel = is_string($oldPlanTrans) && $oldPlanTrans !== $oldPlanKey
            ? $oldPlanTrans
            : ucfirst((string) $oldPlan);

        $newPlanKey   = 'mail.plan_value_' . $newPlan;
        $newPlanTrans = __($newPlanKey);
        $newPlanLabel = is_string($newPlanTrans) && $newPlanTrans !== $newPlanKey
            ? $newPlanTrans
            : ucfirst((string) $newPlan);
    @endphp
    <table cellpadding="0" cellspacing="0" border="0" style="width:100%;margin:16px 0 24px;background:#f9fafb;border-radius:8px;">
        <tr>
            <td style="padding:14px 20px;border-bottom:1px solid #e5e7eb;font-size:14px;color:#6b7280;">{{ __('mail.plan_changed_previous_label') }}</td>
            <td style="padding:14px 20px;border-bottom:1px solid #e5e7eb;font-size:14px;color:#111827;text-align:right;">{{ $oldPlanLabel }}</td>
        </tr>
        <tr>
            <td style="padding:14px 20px;font-size:14px;color:#6b7280;">{{ __('mail.plan_changed_new_label') }}</td>
            <td style="padding:14px 20px;font-size:14px;color:#111827;font-weight:700;text-align:right;">{{ $newPlanLabel }}</td>
        </tr>
    </table>

    @if ($direction === 'upgrade')
        <p style="margin:0 0 16px;line-height:1.6;">
            {{ __('mail.plan_changed_upgrade_note') }}
        </p>
    @elseif ($direction === 'downgrade')
        <p style="margin:0 0 16px;line-height:1.6;">
            {{ __('mail.plan_changed_downgrade_note') }}
        </p>
    @endif

    <div style="text-align:center;margin:32px 0;">
        <a href="{{ $manageUrl }}" style="display:inline-block;background:{{ $primaryColor }};color:#ffffff;padding:14px 28px;border-radius:8px;font-weight:600;text-decoration:none;font-size:15px;">
            {{ __('mail.plan_changed_button') }}
        </a>
    </div>
@endsection
