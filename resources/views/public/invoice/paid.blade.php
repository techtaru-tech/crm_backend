<!DOCTYPE html>
<html lang="{{ app()->getLocale() }}">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width,initial-scale=1">
    <meta name="robots" content="noindex,nofollow">
    <title>{{ __('invoices_public.paid_title_suffix', ['number' => $invoice->invoice_number]) }}</title>
    <link rel="stylesheet" href="{{ asset('css/views/public/invoice/paid.css') }}">
</head>
<body>
<div class="card">
    <div class="icon">&check;</div>
    <h1>@if($invoice->status === 'paid'){{ __('invoices_public.paid_heading_paid') }}@else{{ __('invoices_public.paid_heading_thanks') }}@endif</h1>
    <div class="num">{{ __('invoices_public.paid_invoice_label', ['number' => $invoice->invoice_number]) }}</div>
    <div class="amount">{{ number_format((float) $invoice->total, 2) }} {{ $invoice->currency }}</div>
    <div class="label">{{ __('invoices_public.paid_total_label') }}</div>
    @if($invoice->status === 'paid')
        <p>{{ __('invoices_public.paid_received_body') }}</p>
    @else
        <div class="pending">{{ __('invoices_public.paid_pending_body') }}</div>
    @endif
    <a href="{{ route('invoice.pdf', $invoice->public_token) }}" class="btn">{{ __('invoices_public.paid_download_pdf') }}</a>
</div>
</body>
</html>
