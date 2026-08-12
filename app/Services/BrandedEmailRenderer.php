<?php

namespace App\Services;

use App\Models\Tenant;

/**
 * Wraps a tenant's email body HTML with a branded header and footer
 * so every outbound email (sequence sends, automation sends, test
 * sends) carries the tenant's identity instead of looking like a raw
 * unstyled <p>.
 *
 * Usage:
 *   $html = app(BrandedEmailRenderer::class)->wrap(
 *       tenant: $tenant,
 *       bodyHtml: $rawBody,
 *       subject: $subject,
 *   );
 */
class BrandedEmailRenderer
{
    /**
     * Wrap the raw body HTML inside a brand-coloured header/footer.
     * Colours + logo come from $tenant->branding.*; falls back to the
     * app-global defaults so a tenant that hasn't set branding still
     * gets a reasonable-looking email.
     */
    public function wrap(?Tenant $tenant, string $bodyHtml, string $subject = ''): string
    {
        $primary = $this->color($tenant, 'primary_color', '#4f46e5');
        $appName = $tenant?->getAppName()
            ?? config('leadhub.branding.app_name', 'LeadHub');
        $logoUrl = $tenant?->getBranding('logo_url')
            ?: config('leadhub.branding.logo_url');
        $footerText = $tenant?->getBranding('footer_text')
            ?: __('branded_email.footer_sent_by', ['app' => $appName]);
        $year = date('Y');
        $locale = app()->getLocale();
        $copyright = __('branded_email.copyright_all_rights_reserved');

        // XSS fix: $subject (from EmailTemplate->subject), $appName
        // (from Tenant->branding.app_name), $footerText (from Tenant->
        // branding.footer_text), and the implicit $copyright (from the
        // translator — developer-controlled but escaped for belt-and-
        // suspenders) all flow into the heredoc unescaped without these
        // wrappers.  A tenant admin can store `</title><script>alert(1)
        // </script>` in any branding field; many webmail clients sandbox
        // <script> but render <iframe>/<style>/<link>; the recipient is
        // the LEAD (random public visitor) → admin→lead XSS surface.
        // Pre-escape every interpolated value here in PHP so the heredoc
        // can stay readable.
        $safeSubject    = e($subject);
        $safeAppName    = e($appName);
        $safeFooterText = e($footerText);
        $safeCopyright  = e($copyright);
        $safeAppNameTrunc = $this->truncatePlain($safeAppName, 30);

        // Email clients strip <style>; all colours must be inline.
        // Table-based layout for Outlook/Gmail compatibility.
        $logoHtml = $logoUrl
            ? '<img src="' . e($logoUrl) . '" alt="' . $safeAppName . '" height="32" style="display:block;border:0;outline:none;text-decoration:none;max-height:32px;">'
            : '<span style="font-size:18px;font-weight:700;color:#ffffff;">' . $safeAppName . '</span>';

        $preheader = $subject ? $this->truncatePlain(strip_tags($bodyHtml), 90) : '';
        $safePreheader = e($preheader);

        return <<<HTML
<!doctype html>
<html lang="{$locale}">
<head>
  <meta charset="utf-8">
  <meta name="viewport" content="width=device-width,initial-scale=1">
  <title>{$safeSubject}</title>
</head>
<body style="margin:0;padding:0;background:#f3f4f6;font-family:-apple-system,BlinkMacSystemFont,'Segoe UI',Roboto,'Helvetica Neue',Arial,sans-serif;color:#111827;line-height:1.55;">
  <!-- Preheader (invisible in preview text) -->
  <div style="display:none;overflow:hidden;line-height:1px;opacity:0;max-height:0;max-width:0;">{$safePreheader}</div>

  <table role="presentation" width="100%" cellspacing="0" cellpadding="0" border="0" style="background:#f3f4f6;padding:24px 12px;">
    <tr>
      <td align="center">

        <table role="presentation" width="600" cellspacing="0" cellpadding="0" border="0" style="max-width:600px;width:100%;background:#ffffff;border-radius:14px;overflow:hidden;box-shadow:0 2px 8px rgba(17,24,39,.06);">

          <!-- Header -->
          <tr>
            <td style="background:linear-gradient(135deg,{$primary} 0%,{$this->shade($primary, -15)} 100%);padding:22px 28px;">
              <table role="presentation" width="100%" cellspacing="0" cellpadding="0" border="0">
                <tr>
                  <td align="left" style="vertical-align:middle;">{$logoHtml}</td>
                  <td align="right" style="vertical-align:middle;font-size:12px;color:rgba(255,255,255,.85);letter-spacing:.04em;text-transform:uppercase;font-weight:600;">{$safeAppNameTrunc}</td>
                </tr>
              </table>
            </td>
          </tr>

          <!-- Body -->
          <!-- NOTE on \$bodyHtml: this value comes from EmailTemplate->body_html
               (tenant-admin authored TinyMCE content) after token interpolation.
               It is INTENTIONALLY rendered raw because the tenant authored it
               as HTML; escape would break formatting.  The trust gate is that
               only authenticated tenant admins can author it.  follow-
               up: add an HTMLPurifier pass (composer require mews/purifier or
               ezyang/htmlpurifier) at template save time so a future
               cross-tenant injection (e.g. via API import) can't plant
               <script>/<iframe>.  Documented limitation. -->
          <tr>
            <td style="padding:32px 32px 12px;font-size:15px;color:#111827;">
              {$bodyHtml}
            </td>
          </tr>

          <!-- Footer -->
          <tr>
            <td style="padding:24px 32px 28px;border-top:1px solid #f3f4f6;">
              <p style="margin:0 0 8px;font-size:12px;color:#6b7280;line-height:1.5;">{$safeFooterText}</p>
              <p style="margin:0;font-size:11px;color:#9ca3af;">&copy; {$year} {$safeAppName}. {$safeCopyright}</p>
            </td>
          </tr>
        </table>

      </td>
    </tr>
  </table>
</body>
</html>
HTML;
    }

    /**
     * Interpolate {first_name}, {last_name}, {full_name}, {email},
     * {company} tokens in any string.  Used by both subject + body
     * so the replacement is consistent everywhere.
     */
    public function interpolate(string $template, array $ctx): string
    {
        $pairs = [
            '{first_name}' => $ctx['first_name'] ?? '',
            '{last_name}'  => $ctx['last_name']  ?? '',
            '{full_name}'  => $ctx['full_name']  ?? trim(($ctx['first_name'] ?? '') . ' ' . ($ctx['last_name'] ?? '')),
            '{email}'      => $ctx['email']      ?? '',
            '{company}'    => $ctx['company']    ?? '',
            '{phone}'      => $ctx['phone']      ?? '',
        ];
        return strtr($template, $pairs);
    }

    private function color(?Tenant $tenant, string $key, string $default): string
    {
        $val = $tenant?->getBranding($key);
        return is_string($val) && preg_match('/^#[0-9a-f]{3,8}$/i', $val) ? $val : $default;
    }

    /** Darken/lighten a hex colour by a rough percentage for gradient stops. */
    private function shade(string $hex, int $pct): string
    {
        $hex = ltrim($hex, '#');
        if (strlen($hex) === 3) {
            $hex = $hex[0] . $hex[0] . $hex[1] . $hex[1] . $hex[2] . $hex[2];
        }
        if (strlen($hex) !== 6) {
            return '#' . $hex;
        }
        [$r, $g, $b] = array_map(fn ($h) => hexdec($h), str_split($hex, 2));
        $adj = fn ($c) => max(0, min(255, (int) round($c + ($c * $pct / 100))));
        return sprintf('#%02x%02x%02x', $adj($r), $adj($g), $adj($b));
    }

    private function truncatePlain(string $s, int $max): string
    {
        $s = preg_replace('/\s+/', ' ', trim($s));
        return mb_strlen($s) > $max ? rtrim(mb_substr($s, 0, $max - 1)) . '…' : $s;
    }
}
