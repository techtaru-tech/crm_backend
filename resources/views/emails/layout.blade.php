<!DOCTYPE html>
{{--
   |--------------------------------------------------------------
   | Email layout — INLINE STYLES ARE REQUIRED HERE
   |--------------------------------------------------------------
   |
   | Gmail, Outlook, Apple Mail, Yahoo and every other major email
   | client either strip <style> blocks entirely or refuse to fetch
   | external stylesheets via <link>.  The only reliable way to
   | render branded email on every client is inline style="" on
   | every element.
   |
   | This file (and every template extending it under emails/*)
   | therefore intentionally uses inline styles — the same pattern
   | used by every Laravel-Mailable / Markdown-Mail stack and every
   | major MTA template (Mailchimp, SendGrid, Postmark, etc.).
   |
   | Static CSS extraction does not apply to email templates.
--}}
<html lang="{{ app()->getLocale() }}">
<head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />
    <title>{{ $subject ?? $appName ?? __('mail.layout_default_title') }}</title>
</head>
<body style="margin:0;padding:0;background:#f3f4f6;font-family:-apple-system,BlinkMacSystemFont,'Segoe UI',Roboto,sans-serif;font-size:15px;color:#374151;">
{{-- Preheader text — hidden in the rendered email but shown
     as the inbox preview snippet by Gmail/Outlook/Apple Mail.
     Falls back to the subject line so every mailable contributes
     a readable preview instead of the recipient's eye landing on
     raw sentence fragments from the body copy. --}}
<div style="display:none;overflow:hidden;line-height:1px;opacity:0;max-height:0;max-width:0;color:transparent;">
    {{ $preheader ?? ($subject ?? __('mail.layout_preheader_fallback', ['app' => $appName ?? __('mail.layout_default_title')])) }}
</div>
<div style="width:100%;background:#f3f4f6;padding:40px 0;">
    <div style="max-width:600px;margin:0 auto;background:#ffffff;border-radius:12px;overflow:hidden;box-shadow:0 2px 8px rgba(0,0,0,.06);">
        {{-- Header: brand mark + tagline row.  Rendered as a
             <table> rather than flex/grid so it survives Outlook
             rendering faithfully.  `$headerBackground` is resolved
             from SA branding settings (supports solid colour OR
             linear gradient) with a tenant-level override via
             `email_header_color`. --}}
        <div style="padding:28px 32px 24px;background:{{ $headerBackground ?? ($primaryColor ?? '#4f46e5') }};text-align:center;">
            @if (!empty($logoUrl))
                <img src="{{ $logoUrl }}" alt="{{ $appName ?? __('mail.layout_default_title') }}" style="height:40px;max-width:220px;object-fit:contain;display:block;margin:0 auto;" />
            @else
                <table cellpadding="0" cellspacing="0" border="0" align="center" style="margin:0 auto;">
                    <tr>
                        <td valign="middle" style="padding-right:10px;">
                            <img src="{{ url('/favicon.svg') }}" alt="" width="36" height="36" style="display:block;width:36px;height:36px;" />
                        </td>
                        <td valign="middle">
                            <span style="display:block;color:#ffffff;font-size:24px;font-weight:700;letter-spacing:-0.015em;line-height:1;">{{ $appName ?? __('mail.layout_default_title') }}</span>
                        </td>
                    </tr>
                </table>
            @endif
            @if(! empty($headerTagline))
                <p style="margin:10px 0 0 0;color:rgba(255,255,255,.85);font-size:13px;font-weight:500;">{{ $headerTagline }}</p>
            @endif
        </div>
        {{-- Body --}}
        <div style="padding:36px 32px;">
            @yield('content')
        </div>
        {{-- Footer: `$footerBackground` + `$footerTextColor` pulled
             from SA BrandingSettings (with tenant overrides via
             email_footer_color / email_footer_text_color).  Falls
             back to the old neutral grey. --}}
        <div style="padding:24px 32px;background:{{ $footerBackground ?? '#f9fafb' }};border-top:1px solid #e5e7eb;text-align:center;font-size:12px;color:{{ $footerTextColor ?? '#9ca3af' }};line-height:1.6;">
            <p style="margin:0 0 6px;color:{{ $footerTextColor ?? '#6b7280' }};font-weight:500;">{{ $appName ?? __('mail.layout_default_title') }}</p>
            {!! nl2br(e($footerText ?: __('mail.layout_footer_default', ['app' => $appName ?? __('mail.layout_default_title')]))) !!}
        </div>
    </div>
</div>
</body>
</html>
