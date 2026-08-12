<!DOCTYPE html>
<html lang="{{ app()->getLocale() }}">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width,initial-scale=1">
    <meta name="robots" content="noindex,nofollow">
    <title>{{ __('quotes_public.accepted_title_suffix', ['number' => $quote->quote_number]) }}</title>
    <link rel="stylesheet" href="{{ asset('css/views/public/quote/accepted.css') }}">
</head>
<body>
<div class="card">
    <div class="icon">&check;</div>
    <h1>{{ __('quotes_public.accepted_heading') }}</h1>
    <div class="num">{{ __('quotes_public.accepted_quote_label', ['number' => $quote->quote_number]) }}</div>
    <p>{{ __('quotes_public.accepted_recorded_body') }}</p>
    @if($invoice)
        <div class="next">
            <strong>{{ __('quotes_public.accepted_next_step') }}</strong>
            {!! __('quotes_public.accepted_invoice_body', ['number' => '<span class="qa-num">' . e($invoice->invoice_number) . '</span>']) !!}
            <div class="qa-invoice-row"><a href="{{ route('invoice.show', $invoice->public_token) }}" class="btn qa-btn-flush">{{ __('quotes_public.accepted_view_pay_invoice') }}</a></div>
        </div>
    @else
        <div class="next">
            <strong>{{ __('quotes_public.accepted_what_next') }}</strong>
            {{ __('quotes_public.accepted_invoice_pending') }}
        </div>
    @endif
</div>
</body>
</html>
