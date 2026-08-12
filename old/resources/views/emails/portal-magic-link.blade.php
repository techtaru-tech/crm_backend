<!DOCTYPE html>
<html>
<head><meta charset="UTF-8"></head>
<body style="font-family:-apple-system,Segoe UI,Roboto,sans-serif;background:#f9fafb;margin:0;padding:40px 20px;">
    <table role="presentation" style="max-width:560px;margin:0 auto;background:#fff;border-radius:12px;overflow:hidden;box-shadow:0 1px 3px rgba(0,0,0,.05);" cellpadding="0" cellspacing="0">
        <tr><td style="background:linear-gradient(135deg,#4f46e5,#8b5cf6);padding:24px 32px;color:#fff;">
            <h1 style="font-size:18px;margin:0;font-weight:700;">{{ $tenantName }}</h1>
        </td></tr>
        <tr><td style="padding:32px;">
            <p style="font-size:16px;color:#111827;margin:0 0 14px;">{{ __('mail.portal_magic_link_greeting', ['name' => $lead->first_name ?: __('mail.portal_magic_link_default_name')]) }}</p>
            <p style="font-size:15px;color:#4b5563;margin:0 0 26px;line-height:1.6;">{{ __('mail.portal_magic_link_body') }}</p>
            <p style="margin:0 0 26px;">
                <a href="{{ $url }}" style="background:#4f46e5;color:#fff;padding:12px 28px;border-radius:8px;text-decoration:none;font-weight:600;font-size:15px;display:inline-block;">{{ __('mail.portal_magic_link_button') }}</a>
            </p>
            <p style="font-size:13px;color:#9ca3af;margin:0;line-height:1.6;">{{ __('mail.portal_magic_link_ignore') }}</p>
        </td></tr>
        <tr><td style="padding:16px 32px;border-top:1px solid #f3f4f6;color:#9ca3af;font-size:12px;">
            {{ __('mail.portal_magic_link_fallback') }}<br>
            <span style="word-break:break-all;color:#6b7280;">{{ $url }}</span>
        </td></tr>
    </table>
</body>
</html>
