<!DOCTYPE html>
{{--
   |--------------------------------------------------------------
   | Invoice PDF — INLINE STYLES + INLINE <style> ARE REQUIRED HERE
   |--------------------------------------------------------------
   |
   | DomPDF cannot fetch external CSS via <link rel="stylesheet">.
   | Inline <style> + style="" on elements is the only reliable way
   | to apply typography and layout to a rendered PDF.  Human-
   | readable copy comes through __('pdf.invoice.*') for translation.
--}}
<html lang="{{ app()->getLocale() }}">
<head>
    <meta charset="UTF-8">
    <title>{{ __('pdf.invoice.doc_title', ['number' => $invoice->invoice_number]) }}</title>
    <style>
        @page { margin: 32px 36px; }
        /* Force every node to inherit DejaVu Sans so admin-entered
           HTML in notes/descriptions (which may contain inline
           style="font-family:Arial" etc) can't leak a mixed
           Arial/Times look into the PDF. .num + code go mono. */
        *{font-family:DejaVu Sans,sans-serif !important;}
        .num, .num *, code, pre{font-family:DejaVu Sans Mono,monospace !important;}
        body{font-family:DejaVu Sans,sans-serif;color:#0f172a;font-size:12px;line-height:1.5;margin:0}
        .hdr{border-bottom:3px solid #4f46e5;padding-bottom:14px;margin-bottom:22px}
        .hdr table{width:100%;border:0}
        /* DejaVu Sans (bundled with DomPDF) only ships regular + bold
           weights. Weight 800 makes DomPDF fall back to a completely
           different typeface, which is the source of the "poor font"
           mismatch on the top-left title. Capped at 700. */
        .brand{font-family:'DejaVu Sans',sans-serif !important;font-size:22px;font-weight:700;color:#4f46e5;letter-spacing:-0.01em}
        .num{font-family:DejaVu Sans Mono,monospace;font-size:11px;color:#64748b}
        h1{font-size:18px;margin:6px 0 0;color:#0f172a}
        .right{text-align:right}
        .grid{width:100%;border-collapse:collapse;margin:22px 0}
        .grid th{background:#f1f5f9;color:#334155;font-size:10px;text-transform:uppercase;letter-spacing:.05em;text-align:left;padding:8px 10px;font-weight:700;border-bottom:2px solid #cbd5e1}
        .grid td{padding:9px 10px;border-bottom:1px solid #e2e8f0;color:#334155;vertical-align:top}
        .grid .r{text-align:right}
        .desc{color:#64748b;font-size:11px;margin-top:2px}
        .tot{width:55%;margin-left:45%;border-collapse:collapse;margin-top:8px}
        .tot td{padding:5px 0;font-size:12px}
        .tot .r{text-align:right}
        .tot .grand td{font-size:14px;font-weight:700;color:#0f172a;padding-top:8px;border-top:1.5px solid #334155}
        .tot .due td{font-size:14px;font-weight:700;color:#4f46e5;padding-top:6px}
        .meta{font-size:11px;color:#64748b}
        .pill{display:inline-block;padding:2px 10px;border-radius:10px;font-size:10px;font-weight:700;background:#e0e7ff;color:#3730a3;text-transform:uppercase;letter-spacing:.05em}
        .paid-stamp{border:3px solid #16a34a;color:#16a34a;padding:8px 18px;border-radius:8px;display:inline-block;font-size:16px;font-weight:700;text-transform:uppercase;letter-spacing:.12em;transform:rotate(-6deg);margin-top:14px}
        .foot{margin-top:30px;border-top:1px solid #e2e8f0;padding-top:10px;text-align:center;color:#94a3b8;font-size:10px}
        .notes{margin-top:18px;padding:12px 14px;background:#f8fafc;border:1px solid #cbd5e1;border-radius:6px;font-size:11px;color:#334155;white-space:pre-wrap}
    </style>
</head>
<body>
<div class="hdr">
    <table>
        <tr>
            <td>
                @if(! empty($logo))
                    <img src="{{ $logo }}" alt="" style="max-height:54px;max-width:220px;margin-bottom:6px">
                @else
                    <div class="brand">{{ $invoice->tenant?->name ?? config('app.name') }}</div>
                @endif
                <div class="num">{{ __('pdf.invoice.label') }}</div>
                <h1>{{ $invoice->invoice_number }}</h1>
            </td>
            <td class="right">
                <div class="meta">{{ __('pdf.invoice.issued', ['date' => $invoice->issued_date?->translatedFormat('M j, Y') ?? '—']) }}</div>
                <div class="meta">{{ __('pdf.invoice.due', ['date' => $invoice->due_date?->translatedFormat('M j, Y') ?? '—']) }}</div>
                @php
                    // Translator-first status label so the public PDF respects the recipient
                    // locale. Mirrors the pattern in resources/views/public/invoice/show.blade.php.
                    $pdfInvStatusKey   = 'invoices_public.status_' . $invoice->status;
                    $pdfInvStatusTrans = __($pdfInvStatusKey);
                    $pdfInvStatusLabel = $pdfInvStatusTrans !== $pdfInvStatusKey
                        ? (string) $pdfInvStatusTrans
                        : ucfirst((string) $invoice->status);
                @endphp
                <div style="margin-top:8px"><span class="pill">{{ mb_strtoupper($pdfInvStatusLabel) }}</span></div>
                @if($invoice->status === 'paid')
                    <div class="paid-stamp">{{ __('pdf.invoice.paid_stamp') }}</div>
                @endif
            </td>
        </tr>
    </table>
</div>

<table style="width:100%;margin-bottom:14px">
    <tr>
        <td style="vertical-align:top;width:50%">
            <div class="meta" style="font-weight:700;color:#334155">{{ __('pdf.invoice.from') }}</div>
            <div>{{ $invoice->tenant?->name ?? config('app.name') }}</div>
        </td>
        <td style="vertical-align:top;width:50%">
            <div class="meta" style="font-weight:700;color:#334155">{{ __('pdf.invoice.bill_to') }}</div>
            <div>
                @if($invoice->lead)
                    {{ trim($invoice->lead->first_name . ' ' . $invoice->lead->last_name) }}<br>
                    @if($invoice->lead->email) {{ $invoice->lead->email }}<br> @endif
                    @if($invoice->lead->company) {{ $invoice->lead->company }} @endif
                @elseif($invoice->company)
                    {{ $invoice->company->name ?? '' }}
                @else
                    —
                @endif
            </div>
        </td>
    </tr>
</table>

<table class="grid">
    <thead>
    <tr>
        <th>{{ __('pdf.invoice.th_item') }}</th>
        <th class="r">{{ __('pdf.invoice.th_qty') }}</th>
        <th class="r">{{ __('pdf.invoice.th_unit', ['currency' => $invoice->currency]) }}</th>
        <th class="r">{{ __('pdf.invoice.th_total', ['currency' => $invoice->currency]) }}</th>
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

<table class="tot">
    <tr><td>{{ __('pdf.invoice.subtotal') }}</td><td class="r">{{ number_format((float) $invoice->subtotal, 2) }} {{ $invoice->currency }}</td></tr>
    @if((float) $invoice->discount_amount > 0)
        <tr><td>{{ __('pdf.invoice.discount') }}</td><td class="r">− {{ number_format((float) $invoice->discount_amount, 2) }} {{ $invoice->currency }}</td></tr>
    @endif
    @if((float) $invoice->tax_rate > 0)
        <tr><td>{{ __('pdf.invoice.tax', ['rate' => number_format((float) $invoice->tax_rate, 2)]) }}</td><td class="r">{{ number_format((float) $invoice->tax_amount, 2) }} {{ $invoice->currency }}</td></tr>
    @endif
    <tr class="grand"><td>{{ __('pdf.invoice.grand_total') }}</td><td class="r">{{ number_format((float) $invoice->total, 2) }} {{ $invoice->currency }}</td></tr>
    @if((float) $invoice->amount_paid > 0)
        <tr><td>{{ __('pdf.invoice.paid') }}</td><td class="r">− {{ number_format((float) $invoice->amount_paid, 2) }} {{ $invoice->currency }}</td></tr>
        <tr class="due"><td>{{ __('pdf.invoice.amount_due') }}</td><td class="r">{{ number_format((float) $invoice->amountDue(), 2) }} {{ $invoice->currency }}</td></tr>
    @endif
</table>

@if($invoice->notes)
    <div class="notes">{{ $invoice->notes }}</div>
@endif

<div class="foot">{{ __('pdf.invoice.generated', ['date' => now()->translatedFormat('M j, Y'), 'app' => config('app.name')]) }}</div>
</body>
</html>
