<!DOCTYPE html>
{{--
   |--------------------------------------------------------------
   | Quote PDF — INLINE STYLES + INLINE <style> ARE REQUIRED HERE
   |--------------------------------------------------------------
   |
   | DomPDF cannot fetch external CSS via <link rel="stylesheet">.
   | Inline <style> + style="" on elements is the only reliable way
   | to apply typography and layout to a rendered PDF.  Human-
   | readable copy comes through __('pdf.quote.*') for translation.
--}}
<html lang="{{ app()->getLocale() }}">
<head>
    <meta charset="UTF-8">
    <title>{{ __('pdf.quote.doc_title', ['number' => $quote->quote_number]) }}</title>
    <style>
        @page { margin: 32px 36px; }
        /* Force every node to inherit DejaVu Sans so admin-entered HTML
           in notes/introduction/terms can't leak a mixed font look. */
        *{font-family:DejaVu Sans,sans-serif !important;}
        .num, .num *, code, pre{font-family:DejaVu Sans Mono,monospace !important;}
        body{font-family:DejaVu Sans,sans-serif;color:#0f172a;font-size:12px;line-height:1.5;margin:0}
        .hdr{border-bottom:3px solid #4f46e5;padding-bottom:14px;margin-bottom:22px}
        .hdr table{width:100%;border:0}
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
        .section{margin-top:22px}
        .section h2{font-size:11px;text-transform:uppercase;letter-spacing:.08em;color:#64748b;margin:0 0 6px;font-weight:700}
        .section p{margin:0;color:#334155;font-size:12px;white-space:pre-wrap}
        .meta{font-size:11px;color:#64748b}
        .pill{display:inline-block;padding:2px 10px;border-radius:10px;font-size:10px;font-weight:700;background:#e0e7ff;color:#3730a3;text-transform:uppercase;letter-spacing:.05em}
        .sig{margin-top:26px;padding:12px 14px;background:#f8fafc;border:1px solid #cbd5e1;border-radius:6px;font-size:11px;color:#334155}
        .foot{margin-top:30px;border-top:1px solid #e2e8f0;padding-top:10px;text-align:center;color:#94a3b8;font-size:10px}
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
                    <div class="brand">{{ $quote->tenant?->name ?? config('app.name') }}</div>
                @endif
                <div class="num">{{ __('pdf.quote.label') }}</div>
                <h1>{{ $quote->title }}</h1>
            </td>
            <td class="right">
                <div class="num">{{ $quote->quote_number }}</div>
                <div class="meta">{{ __('pdf.quote.issued', ['date' => $quote->created_at->translatedFormat('M j, Y')]) }}</div>
                @if($quote->valid_until)
                    <div class="meta">{{ __('pdf.quote.valid_until', ['date' => $quote->valid_until->translatedFormat('M j, Y')]) }}</div>
                @endif
                @php
                    // Translator-first status label so the public PDF respects the recipient
                    // locale. Mirrors the pattern in resources/views/public/quote/show.blade.php.
                    $pdfQuoteStatusKey   = 'quotes_public.status_' . $quote->status;
                    $pdfQuoteStatusTrans = __($pdfQuoteStatusKey);
                    $pdfQuoteStatusLabel = $pdfQuoteStatusTrans !== $pdfQuoteStatusKey
                        ? (string) $pdfQuoteStatusTrans
                        : ucfirst((string) $quote->status);
                @endphp
                <div style="margin-top:8px"><span class="pill">{{ mb_strtoupper($pdfQuoteStatusLabel) }}</span></div>
            </td>
        </tr>
    </table>
</div>

<table style="width:100%;margin-bottom:14px">
    <tr>
        <td style="vertical-align:top;width:50%">
            <div class="meta" style="font-weight:700;color:#334155">{{ __('pdf.quote.from') }}</div>
            <div>{{ $quote->tenant?->name ?? config('app.name') }}</div>
        </td>
        <td style="vertical-align:top;width:50%">
            <div class="meta" style="font-weight:700;color:#334155">{{ __('pdf.quote.to') }}</div>
            <div>
                @if($quote->lead)
                    {{ trim($quote->lead->first_name . ' ' . $quote->lead->last_name) }}<br>
                    @if($quote->lead->email) {{ $quote->lead->email }}<br> @endif
                    @if($quote->lead->company) {{ $quote->lead->company }} @endif
                @elseif($quote->company)
                    {{ $quote->company->name ?? '' }}
                @else
                    —
                @endif
            </div>
        </td>
    </tr>
</table>

@if($quote->introduction)
    <div class="section">
        <h2>{{ __('pdf.quote.introduction') }}</h2>
        <p>{{ $quote->introduction }}</p>
    </div>
@endif

<table class="grid">
    <thead>
    <tr>
        <th>{{ __('pdf.quote.th_item') }}</th>
        <th class="r">{{ __('pdf.quote.th_qty') }}</th>
        <th class="r">{{ __('pdf.quote.th_unit', ['currency' => $quote->currency]) }}</th>
        <th class="r">{{ __('pdf.quote.th_total', ['currency' => $quote->currency]) }}</th>
    </tr>
    </thead>
    <tbody>
    @foreach($quote->items as $item)
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
    <tr><td>{{ __('pdf.quote.subtotal') }}</td><td class="r">{{ number_format((float) $quote->subtotal, 2) }} {{ $quote->currency }}</td></tr>
    @if((float) $quote->discount_amount > 0)
        <tr><td>{{ __('pdf.quote.discount') }}</td><td class="r">− {{ number_format((float) $quote->discount_amount, 2) }} {{ $quote->currency }}</td></tr>
    @endif
    @if((float) $quote->tax_rate > 0)
        <tr><td>{{ __('pdf.quote.tax', ['rate' => number_format((float) $quote->tax_rate, 2)]) }}</td><td class="r">{{ number_format((float) $quote->tax_amount, 2) }} {{ $quote->currency }}</td></tr>
    @endif
    <tr class="grand"><td>{{ __('pdf.quote.grand_total') }}</td><td class="r">{{ number_format((float) $quote->total, 2) }} {{ $quote->currency }}</td></tr>
</table>

@if($quote->terms)
    <div class="section">
        <h2>{{ __('pdf.quote.terms_conditions') }}</h2>
        <p>{{ $quote->terms }}</p>
    </div>
@endif

@if($quote->signed_at)
    <div class="sig">
        <strong>{{ __('pdf.quote.signed_label') }}</strong> {{ __('pdf.quote.signed_by', ['name' => $quote->signed_name, 'date' => $quote->signed_at->translatedFormat('M j, Y H:i'), 'ip' => $quote->signed_ip]) }}
    </div>
@endif

<div class="foot">{{ __('pdf.quote.generated', ['date' => now()->translatedFormat('M j, Y'), 'app' => config('app.name')]) }}</div>
</body>
</html>
