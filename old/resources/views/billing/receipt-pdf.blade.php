<!doctype html>
{{--
   |--------------------------------------------------------------
   | Receipt PDF — INLINE STYLES + INLINE <style> ARE REQUIRED HERE
   |--------------------------------------------------------------
   |
   | DomPDF (the renderer behind every PDF in this app) cannot fetch
   | external CSS via <link rel="stylesheet">.  Stylesheets MUST be
   | inline in <style> or applied via the style="" attribute on each
   | element, otherwise PDFs render without typography or layout.
   |
   | This template therefore intentionally inlines its CSS.  Human-
   | readable copy comes through __('pdf.receipt.*') so buyers can
   | translate without touching the markup.
--}}
<html lang="{{ app()->getLocale() }}">
<head>
    <meta charset="utf-8">
    <title>{{ __('pdf.receipt.doc_title', ['number' => $receipt->receipt_number]) }}</title>
    <style>
        body { font-family: DejaVu Sans, sans-serif; color: #1f2937; font-size: 12px; line-height: 1.55; }
        h1 { font-size: 20px; margin: 0 0 4px; color: #111827; }
        .meta { color: #6b7280; font-size: 11px; margin: 0 0 18px; }
        .block { margin: 0 0 14px; }
        .label { color: #6b7280; font-size: 10px; text-transform: uppercase; letter-spacing: 0.04em; }
        .value { color: #111827; font-size: 12px; font-weight: 600; }
        table { width: 100%; border-collapse: collapse; margin: 14px 0; }
        th, td { padding: 8px 10px; border-bottom: 1px solid #e5e7eb; text-align: left; }
        th { color: #6b7280; font-size: 10px; text-transform: uppercase; letter-spacing: 0.04em; font-weight: 600; }
        td.amount { text-align: right; }
        .total-row td { border-top: 2px solid #111827; border-bottom: 0; font-size: 13px; font-weight: 700; padding-top: 12px; }
        .footer { margin-top: 28px; color: #9ca3af; font-size: 10px; text-align: center; }
        .grid { width: 100%; }
        .grid td { width: 50%; vertical-align: top; padding: 0 10px 10px 0; border: 0; }
    </style>
</head>
<body>

<h1>{{ __('pdf.receipt.title') }}</h1>
<div class="meta">{{ $receipt->receipt_number }} · {{ __('pdf.receipt.issued', ['date' => $receipt->issued_at->format('Y-m-d')]) }}</div>

@php
    /*
     | Post-erasure rendering.  When the receipt's tenant has been
     | hard-deleted (GDPR Article 17), $receipt->tenant_id is NULL
     | (relaxed FK with nullOnDelete) and $tenant arrives here as
     | null.  Fall back to the receipt's stashed metadata for any
     | billing fields that still happen to be there -- but the
     | TenantErasureService has already scrubbed billing_email,
     | business_name, billing_address, and vat_number from that
     | metadata, so in practice the post-erasure receipt renders
     | <anonymized> for the billed-to block.  The amount, currency,
     | gateway, plan, and receipt_number remain intact for the
     | tax-record reader.
     */
    $meta          = is_array($receipt->metadata ?? null) ? $receipt->metadata : [];
    $billedName    = $tenant?->business_name ?: $tenant?->name ?: ($meta['business_name'] ?? null);
    $billedAddress = $tenant?->billing_address ?? ($meta['billing_address'] ?? null);
    $billedCountry = $tenant?->billing_country ?? ($meta['billing_country'] ?? null);
    $billedVat     = $tenant?->vat_number ?? ($meta['vat_number'] ?? null);
    $billedEmail   = $tenant?->billing_email
        ?? $tenant?->owner?->email
        ?? ($meta['billing_email'] ?? null);
    $isAnonymized  = $tenant === null && empty($billedName);
@endphp

<table class="grid">
    <tr>
        <td>
            <div class="label">{{ __('pdf.receipt.from') }}</div>
            <div class="value">{{ $company_name }}</div>
        </td>
        <td>
            <div class="label">{{ __('pdf.receipt.billed_to') }}</div>
            @if ($isAnonymized)
                <div class="value">{{ __('pdf.receipt.anonymized') }}</div>
                <div style="font-size:11px;color:#9ca3af;">{{ __('pdf.receipt.gdpr_anonymized') }}</div>
            @else
                <div class="value">{{ $billedName ?: __('pdf.receipt.anonymized') }}</div>
                @if (! empty($billedAddress))
                    <div style="font-size:11px;color:#374151;white-space:pre-line;">{{ $billedAddress }}</div>
                @endif
                @if (! empty($billedCountry))
                    <div style="font-size:11px;color:#374151;">{{ $billedCountry }}</div>
                @endif
                @if (! empty($billedVat))
                    <div style="font-size:11px;color:#374151;">{{ __('pdf.receipt.vat_label', ['number' => $billedVat]) }}</div>
                @endif
                @if (! empty($billedEmail))
                    <div style="font-size:11px;color:#6b7280;">{{ $billedEmail }}</div>
                @endif
            @endif
        </td>
    </tr>
</table>

<table>
    <thead>
        <tr>
            <th>{{ __('pdf.receipt.th_description') }}</th>
            <th>{{ __('pdf.receipt.th_plan') }}</th>
            <th class="amount">{{ __('pdf.receipt.th_amount') }}</th>
        </tr>
    </thead>
    <tbody>
        <tr>
            @php
                // Translator-first gateway label so the receipt respects tenant locale.
                // Stripe/PayPal/Razorpay/Paystack are brand-name keys; 'manual' resolves
                // to the localised "Manual" so non-English buyers don't see English mid-PDF.
                $gwKey   = 'pdf.receipt.gateway_' . $receipt->gateway;
                $gwTrans = __($gwKey);
                $gwLabel = is_string($gwTrans) && $gwTrans !== $gwKey
                    ? $gwTrans
                    : ucfirst((string) $receipt->gateway);
            @endphp
            <td>
                {{ __('pdf.receipt.subscription_via', ['gateway' => $gwLabel]) }}
                @if ($receipt->external_id)
                    <div style="font-size:10px;color:#9ca3af;">{{ __('pdf.receipt.ref', ['ref' => $receipt->external_id]) }}</div>
                @endif
            </td>
            @php
                // Translator-first plan label for the PDF receipt body so
                // the recipient sees their plan name in their locale.  The
                // DB plan `name` column is English; lang/<locale>/plans.<key>.name
                // overrides it (PlanService::translateDisplay pattern).
                $rcptPlanSlug  = (string) ($plan?->key ?? $receipt->plan_key ?? '');
                $rcptPlanKey   = 'plans.' . $rcptPlanSlug . '.name';
                $rcptPlanTry   = $rcptPlanSlug !== '' ? __($rcptPlanKey) : '';
                $rcptPlanLabel = is_string($rcptPlanTry) && $rcptPlanTry !== $rcptPlanKey
                    ? $rcptPlanTry
                    : ($plan?->name ?? ($rcptPlanSlug !== '' ? ucfirst(str_replace(['_', '-'], ' ', $rcptPlanSlug)) : '—'));
            @endphp
            <td>{{ $rcptPlanLabel }}</td>
            <td class="amount">{{ number_format((float) $receipt->amount, 2) }} {{ $receipt->currency }}</td>
        </tr>
        <tr class="total-row">
            <td colspan="2">{{ __('pdf.receipt.total_paid') }}</td>
            <td class="amount">{{ number_format((float) $receipt->amount, 2) }} {{ $receipt->currency }}</td>
        </tr>
    </tbody>
</table>

<div class="block">
    <div class="label">{{ __('pdf.receipt.payment_method') }}</div>
    {{-- Reuse the $gwLabel computed above for the payment-method block. --}}
    <div class="value">{{ $gwLabel }}</div>
</div>

<div class="footer">
    {{ __('pdf.receipt.auto_footer', ['company' => $company_name]) }}
</div>

</body>
</html>
