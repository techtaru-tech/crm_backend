<?php

namespace App\Mail;

use App\Models\Tenant;
use App\Settings\BrandingSettings;
use Illuminate\Mail\Mailable;

/**
 * All LeadHub outbound emails should extend this class.
 *
 * Precedence for every visual / identity field injected into the
 * email view layer:
 *   1. Tenant-specific override    (tenant.branding JSON column)
 *   2. SA-wide default             (Spatie BrandingSettings)
 *   3. config('leadhub.branding.*') fallback
 *
 * Precedence for the outgoing SMTP transport itself (MAIL_HOST,
 * MAIL_PORT, credentials):
 *   1. .env                        (LeadHub's SA "Script Settings"
 *                                   page writes here)
 *   2. config/mail.php             (default)
 *
 * The `from` address on an outbound message:
 *   1. tenant.branding.email_from  (per-tenant override)
 *   2. config('mail.from.address') (which mirrors MAIL_FROM_ADDRESS)
 *
 * Do NOT add a third "sender_name" source of truth — tenant
 * branding + .env is enough.  BrandingSettings' email_sender_name
 * field is kept only for the SA preview page; the actual From name
 * on sent messages comes from the tenant or MAIL_FROM_NAME.
 *
 * Injected into blade view data:
 *   $appName, $primaryColor, $logoUrl, $footerText,
 *   $headerBackground, $footerBackground, $footerTextColor.
 */
abstract class BrandedMailable extends Mailable
{
    public string $appName;
    public string $primaryColor;
    public ?string $logoUrl;
    public string $footerText;
    public string $headerBackground;      // CSS `background` value (colour OR gradient)
    public string $footerBackground;      // footer row background colour
    public string $footerTextColor;       // footer row text colour
    public ?Tenant $tenant = null;

    public function withTenant(?Tenant $tenant): static
    {
        $this->tenant       = $tenant;
        $this->appName      = $tenant?->getAppName() ?? config('leadhub.branding.app_name', 'LeadHub');
        // CSS-context defense: `$primaryColor` interpolates raw
        // into ~15 email blade templates as
        //   style="background:{{ $primaryColor }};color:#fff;..."
        // Filament's ColorPicker validates ONLY on the client; a
        // tenant-admin who tampers the Livewire payload (or any future
        // code path that mass-assigns the branding JSON) can persist
        //   primary_color = 'red; }; body{display:none}'
        // which then breaks out of the inline CSS declaration in every
        // outbound email.  Modern webmail clients aggressively sanitise
        // CSS in inbound mail, but several (Outlook for Mac, some
        // mobile native clients) still render inline `style` payloads
        // verbatim — so this is a real exfil + spoofing vector, not
        // just a theoretical one.  ColorSafety::safeHex enforces a
        // strict `#rgb` / `#rgba` / `#rrggbb` / `#rrggbbaa` shape and
        // falls back to indigo on any non-hex content.  This central
        // wrap protects every BrandedMailable subclass at once.
        $this->primaryColor = \App\Support\ColorSafety::safeHex(
            $tenant?->getPrimaryColor() ?? config('leadhub.branding.primary_color', '#4f46e5'),
            '#4f46e5'
        );
        $this->logoUrl      = $tenant?->getBranding('logo_url') ?? config('leadhub.branding.logo_url');
        $this->footerText   = $tenant?->getBranding('footer_text') ?? config('leadhub.branding.footer_text', '');

        $this->resolveHeaderFooterColours($tenant);

        $fromName    = $tenant?->getBranding('email_sender_name') ?? $this->appName;
        $fromAddress = $tenant?->getBranding('email_from') ?? config('mail.from.address');

        if ($fromAddress) {
            $this->from($fromAddress, $fromName);
        }

        return $this;
    }

    protected function buildFromTenant(): void
    {
        if (! isset($this->appName)) {
            $tenant             = app()->bound('current_tenant') ? app('current_tenant') : null;
            $this->appName      = $tenant?->getAppName() ?? config('leadhub.branding.app_name', 'LeadHub');
            // CSS-context defense (mirror of withTenant() above).
            // Without this wrap, subclasses that go through buildFromTenant
            // (queued jobs that lose the explicit withTenant call) would
            // emit unsanitised tenant CSS into every email body.
            $this->primaryColor = \App\Support\ColorSafety::safeHex(
                $tenant?->getPrimaryColor() ?? config('leadhub.branding.primary_color', '#4f46e5'),
                '#4f46e5'
            );
            $this->logoUrl      = $tenant?->getBranding('logo_url') ?? null;
            $this->footerText   = $tenant?->getBranding('footer_text') ?? '';
            $this->tenant       = $tenant;

            $this->resolveHeaderFooterColours($tenant);
        }
    }

    /**
     * Header + footer colours: prefer tenant branding overrides
     * when set, otherwise fall back to SA BrandingSettings which
     * supports solid or gradient header.
     */
    protected function resolveHeaderFooterColours(?Tenant $tenant): void
    {
        try {
            $sa = app(BrandingSettings::class);
        } catch (\Throwable) {
            $sa = null;
        }

        // CSS-injection defense: tenant-level overrides are
        // free-text and ColorPicker validation is client-side only.
        // Validate tenant inputs via ColorSafety::safeHex (single colour).
        // SA-level values come from a server-validated UI (BrandingSettings
        // saving rule) so they're trusted — but route them through
        // safeCssBackground for the gradient case (header may be a gradient).
        $tenantHeader = $tenant?->getBranding('email_header_color');
        if (filled($tenantHeader)) {
            $this->headerBackground = \App\Support\ColorSafety::safeHex((string) $tenantHeader, $this->primaryColor);
        } elseif ($sa) {
            $this->headerBackground = \App\Support\ColorSafety::safeCssBackground(
                $sa->resolveEmailHeaderBackground(),
                $this->primaryColor
            );
        } else {
            // Hard fallback = tenant primary colour, which itself
            // falls back to config if the tenant didn't set one.
            $this->headerBackground = $this->primaryColor;
        }

        $tenantFooter = $tenant?->getBranding('email_footer_color');
        if (filled($tenantFooter)) {
            $this->footerBackground = \App\Support\ColorSafety::safeHex((string) $tenantFooter, '#f9fafb');
        } elseif ($sa) {
            $this->footerBackground = \App\Support\ColorSafety::safeHex($sa->email_footer_color, '#f9fafb');
        } else {
            $this->footerBackground = '#f9fafb';
        }

        $tenantFooterText = $tenant?->getBranding('email_footer_text_color');
        if (filled($tenantFooterText)) {
            $this->footerTextColor = \App\Support\ColorSafety::safeHex((string) $tenantFooterText, '#6b7280');
        } elseif ($sa) {
            $this->footerTextColor = \App\Support\ColorSafety::safeHex($sa->email_footer_text_color, '#6b7280');
        } else {
            $this->footerTextColor = '#6b7280';
        }
    }
}
