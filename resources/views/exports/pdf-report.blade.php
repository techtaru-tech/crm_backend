<!DOCTYPE html>
{{--
   |--------------------------------------------------------------
   | Analytics PDF report — INLINE STYLES REQUIRED HERE
   |--------------------------------------------------------------
   |
   | DomPDF cannot fetch external CSS via <link rel="stylesheet">.
   | Inline <style> + style="" on elements is the only reliable way
   | to apply typography and layout to a rendered PDF.  Brand color
   | is interpolated via {{ $brandColor }} per-tenant (dynamic).
   | Human-readable copy comes through __('pdf.report.*').
--}}
<html lang="{{ app()->getLocale() }}">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>{{ $reportTitle }}</title>
    <style>
        * { margin: 0; padding: 0; box-sizing: border-box; }
        body { font-family: DejaVu Sans, Arial, sans-serif; font-size: 12px; color: #1f2937; background: #fff; }

        .header {
            display: flex;
            align-items: center;
            justify-content: space-between;
            padding: 20px 30px;
            background: {{ $brandColor ?? '#4f46e5' }};
            color: white;
        }
        .header .brand { font-size: 20px; font-weight: 700; }
        .header .meta { text-align: right; font-size: 11px; opacity: 0.85; }

        .report-title {
            padding: 16px 30px;
            border-bottom: 2px solid #e5e7eb;
            background: #f9fafb;
        }
        .report-title h1 { font-size: 16px; font-weight: 700; color: #111827; }
        .report-title .period { font-size: 11px; color: #6b7280; margin-top: 3px; }

        table {
            width: 100%;
            border-collapse: collapse;
            margin: 0;
        }
        thead tr { background: #f3f4f6; }
        thead th {
            padding: 10px 16px;
            text-align: left;
            font-size: 11px;
            font-weight: 600;
            color: #6b7280;
            text-transform: uppercase;
            letter-spacing: 0.05em;
            border-bottom: 1px solid #e5e7eb;
        }
        thead th:not(:first-child) { text-align: right; }
        tbody td {
            padding: 9px 16px;
            font-size: 12px;
            color: #374151;
            border-bottom: 1px solid #f3f4f6;
        }
        tbody td:not(:first-child) { text-align: right; }
        tbody tr:nth-child(even) { background: #f9fafb; }
        tbody tr:last-child td { border-bottom: none; }

        .footer {
            margin-top: 24px;
            padding: 12px 30px;
            font-size: 10px;
            color: #9ca3af;
            text-align: center;
            border-top: 1px solid #e5e7eb;
        }
    </style>
</head>
<body>

    <div class="header">
        <div class="brand">
            @if($logoUrl)
                <img src="{{ $logoUrl }}" alt="{{ $brandName }}" style="height:28px; vertical-align:middle;">
            @else
                {{ $brandName }}
            @endif
        </div>
        <div class="meta">
            {{ __('pdf.report.exported', ['time' => $exportedAt]) }}<br>
            {{ __('pdf.report.analytics', ['app' => $appName ?? config('leadhub.branding.app_name', 'LeadHub')]) }}
        </div>
    </div>

    <div class="report-title">
        <h1>{{ $reportTitle }}</h1>
        <div class="period">{{ __('pdf.report.period', ['from' => $dateFrom, 'to' => $dateTo]) }}</div>
        @if(!empty($activeFilters))
            <div class="period" style="margin-top:4px;">
                {{ __('pdf.report.filters') }}
                @foreach($activeFilters as $label => $value)
                    <strong>{{ $label }}:</strong> {{ $value }}{{ !$loop->last ? ' &middot; ' : '' }}
                @endforeach
            </div>
        @endif
    </div>

    {{-- SVG charts intentionally omitted — DomPDF cannot position SVG <text>
         elements, causing axis/label text to render as garbage above the table. --}}

    @if(count($rows) > 0)
        <table>
            <thead>
                <tr>
                    @foreach($headers as $header)
                        <th>{{ $header }}</th>
                    @endforeach
                </tr>
            </thead>
            <tbody>
                @foreach($rows as $row)
                    <tr>
                        @foreach($row as $cell)
                            <td>{{ $cell }}</td>
                        @endforeach
                    </tr>
                @endforeach
            </tbody>
        </table>
    @else
        <div style="padding: 40px; text-align: center; color: #9ca3af;">
            {{ __('pdf.report.no_data') }}
        </div>
    @endif

    <div class="footer">
        {{ __('pdf.report.footer', ['brand' => $brandName, 'time' => $exportedAt, 'count' => count($rows)]) }}
    </div>

</body>
</html>
