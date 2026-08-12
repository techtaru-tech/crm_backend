<!doctype html>
<html lang="{{ app()->getLocale() }}">
<head>
    <meta charset="utf-8">
    <title>{{ __('legal_dpa.document_title') }}</title>
    {{-- Inline CSS retained: dompdf does not fetch external stylesheets
         when generating PDFs.  Keeping the styles next to the markup is
         the standard pattern for PDF-bound Blade templates. --}}
    <style>
        body { font-family: DejaVu Sans, sans-serif; color: #1f2937; font-size: 11px; line-height: 1.55; }
        h1 { font-size: 18px; margin: 0 0 6px; color: #111827; }
        h2 { font-size: 13px; margin: 18px 0 6px; color: #111827; }
        h3 { font-size: 11px; margin: 10px 0 4px; color: #374151; }
        .meta { color: #6b7280; font-size: 10px; }
        p { margin: 0 0 8px; }
        ul { margin: 0 0 8px 16px; padding: 0; }
        ul li { margin: 0 0 4px; }
        .signatures { margin-top: 28px; }
        .signatures table { width: 100%; border-collapse: collapse; }
        .signatures td { width: 50%; padding-right: 14px; vertical-align: top; }
        .sig-line { border-bottom: 1px solid #6b7280; height: 30px; margin-bottom: 4px; }
        .footer { margin-top: 24px; color: #9ca3af; font-size: 9px; text-align: center; }
        .notice { background: #fef3c7; border: 1px solid #fbbf24; padding: 8px 10px; border-radius: 4px; color: #92400e; font-size: 10px; margin: 0 0 14px; }
    </style>
</head>
<body>

{{--
    All clause text is loaded from lang/<locale>/legal_dpa.php so buyers
    can adapt the wording to their jurisdiction or counsel's guidance
    without modifying the view.  English is the fallback; copy the file
    to lang/<other-locale>/legal_dpa.php to translate.
--}}

<h1>{{ __('legal_dpa.heading') }}</h1>
<p class="meta">{{ __('legal_dpa.meta_generated', [
    'date'    => $rendered_at->format('Y-m-d'),
    'company' => $company_name,
]) }}</p>

<div class="notice">
    {{ __('legal_dpa.notice') }}
</div>

<h2>{{ __('legal_dpa.section_1_title') }}</h2>
<p>{{ __('legal_dpa.section_1_intro') }}</p>
<ul>
    <li><strong>{{ __('legal_dpa.section_1_controller_label') }}</strong> {{ __('legal_dpa.section_1_controller_value') }}</li>
    <li><strong>{{ __('legal_dpa.section_1_processor_label') }}</strong> {{ __('legal_dpa.section_1_processor_value', ['company' => $company_name]) }}</li>
</ul>

<h2>{{ __('legal_dpa.section_2_title') }}</h2>
<p>{{ __('legal_dpa.section_2_body') }}</p>

<h2>{{ __('legal_dpa.section_3_title') }}</h2>
<p>{{ __('legal_dpa.section_3_body') }}</p>

<h2>{{ __('legal_dpa.section_4_title') }}</h2>
<ul>
    <li>{{ __('legal_dpa.section_4_item_1') }}</li>
    <li>{{ __('legal_dpa.section_4_item_2') }}</li>
    <li>{{ __('legal_dpa.section_4_item_3') }}</li>
</ul>

<h2>{{ __('legal_dpa.section_5_title') }}</h2>
<ul>
    <li>{{ __('legal_dpa.section_5_item_1') }}</li>
    <li>{{ __('legal_dpa.section_5_item_2') }}</li>
    <li>{{ __('legal_dpa.section_5_item_3') }}</li>
    <li>{{ __('legal_dpa.section_5_item_4') }}</li>
    <li>{{ __('legal_dpa.section_5_item_5') }}</li>
</ul>

<h2>{{ __('legal_dpa.section_6_title') }}</h2>
<p>{{ __('legal_dpa.section_6_body') }}</p>

<h2>{{ __('legal_dpa.section_7_title') }}</h2>
<p>{{ __('legal_dpa.section_7_body') }}</p>

<h2>{{ __('legal_dpa.section_8_title') }}</h2>
<p>{{ __('legal_dpa.section_8_body') }}</p>

<h2>{{ __('legal_dpa.section_9_title') }}</h2>
<p>{{ __('legal_dpa.section_9_body') }}</p>

<h2>{{ __('legal_dpa.section_10_title') }}</h2>
<p>{{ __('legal_dpa.section_10_body') }}</p>

<div class="signatures">
    <table>
        <tr>
            <td>
                <div class="sig-line"></div>
                <strong>{{ __('legal_dpa.sig_controller') }}</strong><br>
                {{ __('legal_dpa.sig_name') }} ____________________<br>
                {{ __('legal_dpa.sig_title') }} ____________________<br>
                {{ __('legal_dpa.sig_date') }} ____________________
            </td>
            <td>
                <div class="sig-line"></div>
                <strong>{{ __('legal_dpa.sig_processor', ['company' => $company_name]) }}</strong><br>
                {{ __('legal_dpa.sig_name') }} ____________________<br>
                {{ __('legal_dpa.sig_title') }} ____________________<br>
                {{ __('legal_dpa.sig_date') }} ____________________
            </td>
        </tr>
    </table>
</div>

<div class="footer">
    {{ __('legal_dpa.footer', [
        'datetime' => $rendered_at->format('Y-m-d H:i'),
        'company'  => $company_name,
    ]) }}
</div>

</body>
</html>
