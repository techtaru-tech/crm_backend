<!DOCTYPE html>
<html lang="{{ app()->getLocale() }}">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width,initial-scale=1">
    <meta name="robots" content="noindex,nofollow">
    <title>{{ __('invoices_public.page_title', ['number' => $invoice->invoice_number]) }}</title>
    <link rel="stylesheet" href="{{ asset('css/views/public/invoice/show.css') }}">
</head>
<body>
<div class="wrap">
    @if(session('info'))
        <div class="notice">{{ session('info') }}</div>
    @endif
    @if($errors->any())
        <div class="err">{{ $errors->first() }}</div>
    @endif

    <div class="card">
        <div class="hdr">
            <div class="meta">
                <div>
                    <h1>{{ __('invoices_public.invoice_label_prefix', ['number' => $invoice->invoice_number]) }}</h1>
                    @if($invoice->quote)
                        <div class="num">{{ __('invoices_public.from_quote_prefix', ['number' => $invoice->quote->quote_number]) }}</div>
                    @endif
                </div>
                <div class="total">
                    {{ number_format((float) $invoice->amountDue(), 2) }} {{ $invoice->currency }}
                    <small>{{ __('invoices_public.amount_due') }}</small>
                </div>
            </div>
        </div>
        <div class="body">
            @php
                $statusClass = match($invoice->status) {
                    'paid' => 'pill-success',
                    'partial', 'overdue' => 'pill-warning',
                    default => 'pill-info',
                };
            @endphp
            @php
                // Translator-first invoice status label so the pill respects recipient locale.
                $invStatusKey   = 'invoices_public.status_' . $invoice->status;
                $invStatusTrans = __($invStatusKey);
                $invStatusLabel = is_string($invStatusTrans) && $invStatusTrans !== $invStatusKey
                    ? $invStatusTrans
                    : ucfirst((string) $invoice->status);
            @endphp
            <span class="pill {{ $statusClass }}">{{ $invStatusLabel }}</span>

            <div class="meta-row is-meta-row">
                <div class="meta-box">
                    <strong>{{ __('invoices_public.issued') }}</strong>
                    {{ $invoice->issued_date?->translatedFormat('M j, Y') ?? '—' }}
                </div>
                <div class="meta-box">
                    <strong>{{ __('invoices_public.due') }}</strong>
                    {{ $invoice->due_date?->translatedFormat('M j, Y') ?? '—' }}
                </div>
                <div class="meta-box">
                    <strong>{{ __('invoices_public.bill_to') }}</strong>
                    @if($invoice->lead)
                        {{ trim($invoice->lead->first_name . ' ' . $invoice->lead->last_name) }}
                    @elseif($invoice->company)
                        {{ $invoice->company->name ?? '—' }}
                    @else
                        —
                    @endif
                </div>
            </div>

            <h2>{{ __('invoices_public.items_heading') }}</h2>
            <table>
                <thead>
                <tr>
                    <th>{{ __('invoices_public.col_item') }}</th>
                    <th class="r">{{ __('invoices_public.col_qty') }}</th>
                    <th class="r">{{ __('invoices_public.col_unit') }}</th>
                    <th class="r">{{ __('invoices_public.col_total') }}</th>
                </tr>
                </thead>
                <tbody>
                @foreach($invoice->items as $item)
                    <tr>
                        <td>
                            <strong>{{ $item->name }}</strong>
                            @if($item->description)<div class="desc">{{ $item->description }}</div>@endif
                        </td>
                        <td class="r">{{ $item->quantity }}</td>
                        <td class="r">{{ number_format((float) $item->unit_price, 2) }}</td>
                        <td class="r"><strong>{{ number_format((float) $item->total, 2) }}</strong></td>
                    </tr>
                @endforeach
                </tbody>
            </table>

            <div class="tot">
                <div class="tot-row"><span>{{ __('invoices_public.subtotal') }}</span><span>{{ number_format((float) $invoice->subtotal, 2) }} {{ $invoice->currency }}</span></div>
                @if((float) $invoice->discount_amount > 0)
                    <div class="tot-row"><span>{{ __('invoices_public.discount') }}</span><span>− {{ number_format((float) $invoice->discount_amount, 2) }} {{ $invoice->currency }}</span></div>
                @endif
                @if((float) $invoice->tax_rate > 0)
                    <div class="tot-row"><span>{{ __('invoices_public.tax_label', ['rate' => number_format((float) $invoice->tax_rate, 2)]) }}</span><span>{{ number_format((float) $invoice->tax_amount, 2) }} {{ $invoice->currency }}</span></div>
                @endif
                <div class="tot-row grand"><span>{{ __('invoices_public.total') }}</span><span>{{ number_format((float) $invoice->total, 2) }} {{ $invoice->currency }}</span></div>
                @if((float) $invoice->amount_paid > 0)
                    <div class="tot-row"><span>{{ __('invoices_public.paid') }}</span><span>− {{ number_format((float) $invoice->amount_paid, 2) }} {{ $invoice->currency }}</span></div>
                    <div class="tot-row grand"><span>{{ __('invoices_public.amount_due') }}</span><span>{{ number_format((float) $invoice->amountDue(), 2) }} {{ $invoice->currency }}</span></div>
                @endif
            </div>
        </div>
    </div>

    {{-- Payment gateways removed from the public tenant invoice page.
         In this build the tenant admin marks the invoice paid manually
         from inside their workspace (the status updates automatically
         on the logged-out view). Online checkout is reserved for the
         SaaS operator's subscription billing only, not tenant-issued
         invoices. --}}
    {{-- Only a "Paid in full" confirmation chip when the tenant has
         marked the invoice paid.  Everything else — the "How to pay"
         card with contact-the-sender copy — was removed in line with
         product direction: tenant-issued invoices in this build are
         reconciled out-of-band, no public checkout and no payment
         instructions section on the lead-facing view. --}}
    @if($invoice->status === 'paid')
        <div class="card">
            <div class="body is-paid-row">
                <div class="is-paid-badge">
                    <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round"><polyline points="20 6 9 17 4 12"/></svg>
                    {{ __('invoices_public.paid_in_full') }}
                </div>
                @if($invoice->paid_at)
                    <p class="is-paid-when">{{ __('invoices_public.marked_paid_on', ['date' => $invoice->paid_at->translatedFormat('M j, Y')]) }}</p>
                @endif
            </div>
        </div>
    @endif

    <div class="is-pdf-row">
        <a href="{{ route('invoice.pdf', $invoice->public_token) }}" class="btn btn-gray">{{ __('invoices_public.download_pdf') }}</a>
    </div>

    <div class="foot">{{ __('invoices_public.secured_link_suffix', ['app' => config('app.name')]) }}</div>
</div>
</body>
</html>
