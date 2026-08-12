<?php

declare(strict_types=1);

namespace App\Filament\SuperAdmin\Widgets;

use App\Models\Tenant;
use Filament\Widgets\Widget;

/**
 * First-login setup checklist for the SaaS owner.
 *
 * After fresh install / fresh CodeCanyon-buyer setup, the SA dashboard
 * should make it obvious what still needs to happen before the install
 * is ready to onboard real workspaces.  Without this widget the buyer
 * has to know to navigate to ~5 separate settings pages — most don't,
 * and they end up shipping with MAIL=log and "LeadHub" branding.
 *
 * Auto-hides once 4 of 5 items are done so the banner doesn't follow
 * the operator forever.
 */
class SuperAdminSetupChecklist extends Widget
{
    protected string $view = 'filament.super-admin.widgets.setup-checklist';
    protected static ?int $sort = 0;
    protected int|string|array $columnSpan = 'full';

    /** @return array{items: array<int, array{label:string, done:bool, href:string}>} */
    public function getViewData(): array
    {
        // Translator-first with English fallback per item — keeps the
        // checklist locale-aware while still surfacing copy when the
        // lang file is missing.
        return [
            'items' => [
                [
                    'label' => $this->t('setup_checklist.item_smtp', 'Configure SMTP so emails actually send'),
                    'done'  => $this->smtpConfigured(),
                    'href'  => '/super-admin/script-settings',
                ],
                [
                    'label' => $this->t('setup_checklist.item_gateway', 'Enable at least one payment gateway'),
                    'done'  => $this->anyGatewayEnabled(),
                    // Filament derives the slug from the full page
                    // class name including the "Page" suffix, so the
                    // registered route is /super-admin/billing-settings-page
                    // not /super-admin/billing-settings (which 404'd).
                    'href'  => '/super-admin/billing-settings-page',
                ],
                [
                    'label' => $this->t('setup_checklist.item_brand', 'Set your brand name + colours'),
                    'done'  => $this->brandConfigured(),
                    // /super-admin/branding is served by BrandingPage
                    // (added alongside this fix).  Before that page
                    // existed the link 404'd because there was no
                    // SA-side branding UI at all.
                    'href'  => '/super-admin/branding',
                ],
                [
                    'label' => $this->t('setup_checklist.item_landing', 'Edit landing-page hero copy'),
                    'done'  => $this->landingHeroSet(),
                    // The SA landing-page editor's slug derives from
                    // the LandingPageEditor class name, so the route
                    // is /super-admin/landing-page-editor not
                    // /super-admin/landing-page.
                    'href'  => '/super-admin/landing-page-editor',
                ],
                [
                    'label' => $this->t('setup_checklist.item_workspace', 'Onboard your first workspace'),
                    'done'  => Tenant::withoutGlobalScope('tenant')->exists(),
                    'href'  => '/super-admin/tenants',
                ],
            ],
        ];
    }

    /**
     * Translator-first lookup with English fallback. Mirrors the helper
     * pattern used by App\Support\Currency::label() — returns the
     * literal English string when the translation key is missing so
     * the dashboard never renders raw "filament/foo.bar" placeholders.
     */
    private function t(string $suffix, string $fallback): string
    {
        $key   = 'filament/super_admin_widgets.' . $suffix;
        $trans = __($key);
        return is_string($trans) && $trans !== $key ? $trans : $fallback;
    }

    public static function canView(): bool
    {
        // Hide once at least 4 of 5 items are done so the dashboard
        // doesn't show this banner forever.  Wrapped so a startup
        // exception (e.g. settings table not yet migrated) defaults
        // to "show" — better to surface the checklist than 500 the
        // SA dashboard.
        try {
            $widget = new self();
            $items  = $widget->getViewData()['items'];
            $done   = 0;
            foreach ($items as $item) {
                if (! empty($item['done'])) {
                    $done++;
                }
            }
            return $done < 4;
        } catch (\Throwable) {
            return true;
        }
    }

    private function smtpConfigured(): bool
    {
        return (string) config('mail.default') !== 'log';
    }

    private function anyGatewayEnabled(): bool
    {
        try {
            $s = app(\App\Settings\BillingSettings::class);
            // enabled_gateways is the canonical list of enabled gateway keys.
            // The per-gateway *_enabled booleans this used to read don't exist
            // on BillingSettings, so the checklist item never turned green.
            return ! empty($s->enabled_gateways);
        } catch (\Throwable) {
            return false;
        }
    }

    private function brandConfigured(): bool
    {
        // Prefer the BrandingSettings DB row (what the SA BrandingPage
        // actually writes to) so the checklist flips green as soon as
        // the operator changes the brand name there.  Fall back to
        // the env-backed config value when the settings table has
        // not been migrated yet (partial install).
        try {
            $appName = app(\App\Settings\BrandingSettings::class)->app_name;
            if (is_string($appName) && $appName !== '' && $appName !== 'LeadHub') {
                return true;
            }
        } catch (\Throwable) {
            // Spatie throws when the settings row is missing; fall
            // through to the config check below.
        }

        $appName = config('leadhub.branding.app_name');
        return is_string($appName) && $appName !== '' && $appName !== 'LeadHub';
    }

    private function landingHeroSet(): bool
    {
        try {
            $headline = app(\App\Settings\LandingContent::class)->hero_headline ?? null;
            return is_string($headline) && trim($headline) !== '';
        } catch (\Throwable) {
            return false;
        }
    }
}
