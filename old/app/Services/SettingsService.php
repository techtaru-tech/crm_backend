<?php

namespace App\Services;

use App\Models\AuditLog;
use App\Models\Tenant;

/**
 * Thin wrapper that reads/writes settings and branding from the
 * Tenant's JSON columns, with optional audit logging.
 */
class SettingsService
{
    private ?Tenant $tenant = null;

    public function forTenant(Tenant $tenant): static
    {
        $this->tenant = $tenant;
        return $this;
    }

    private function getTenant(): Tenant
    {
        if ($this->tenant) {
            return $this->tenant;
        }

        $tenant = app()->bound('current_tenant') ? app('current_tenant') : null;

        if (! $tenant && ($user = auth()->user())) {
            $tenant = $user->tenant;
        }

        if (! $tenant) {
            // Translated because SafeAction (Filament Concern) forwards
            // exception messages straight into the user-visible toast
            // body via Notification::make()->body($e->getMessage()).
            // A non-English buyer hitting an impersonation/branding
            // edge case would otherwise see the raw English literal.
            throw new \RuntimeException((string) __('exceptions.no_current_tenant'));
        }

        return $this->tenant = $tenant;
    }

    public function get(string $key, mixed $default = null): mixed
    {
        return $this->getTenant()->getSetting($key, $default);
    }

    public function getBranding(string $key, mixed $default = null): mixed
    {
        return $this->getTenant()->getBranding($key, $default);
    }

    public function set(string $key, mixed $value): void
    {
        $tenant = $this->getTenant();
        $old    = data_get($tenant->settings ?? [], $key);

        $settings       = $tenant->settings ?? [];
        data_set($settings, $key, $value);
        $tenant->settings = $settings;
        $tenant->save();

        $this->auditSettingChange('settings', $key, $old, $value);
    }

    public function setBranding(string $key, mixed $value): void
    {
        $tenant   = $this->getTenant();
        $old      = data_get($tenant->branding ?? [], $key);

        $branding = $tenant->branding ?? [];
        data_set($branding, $key, $value);
        $tenant->branding = $branding;
        $tenant->save();

        $this->auditSettingChange('branding', $key, $old, $value);
    }

    public function mergeBranding(array $values): void
    {
        $tenant   = $this->getTenant();
        $branding = array_merge($tenant->branding ?? [], $values);
        $tenant->update(['branding' => $branding]);

        $this->auditSettingChange('branding', 'bulk_update', null, array_keys($values));
    }

    public function mergeSettings(array $values): void
    {
        $tenant   = $this->getTenant();
        $settings = array_merge($tenant->settings ?? [], $values);
        $tenant->update(['settings' => $settings]);

        $this->auditSettingChange('settings', 'bulk_update', null, array_keys($values));
    }

    /**
     * Generate and save the tenant's branding CSS file.
     * Stored at public/tenant/{id}/branding.css
     * Applies: primary color, accent color, login background color + image, favicon.
     */
    public function generateBrandingCss(Tenant $tenant): void
    {
        $primary     = $tenant->getBranding('primary_color', '#4f46e5');
        $accent      = $tenant->getBranding('accent_color', '#06b6d4');
        $loginBg     = $tenant->getBranding('login_bg_color', '#f3f4f6');
        $loginBgImg  = $tenant->getBranding('login_bg_image', '');
        $logoUrl     = $tenant->getBranding('logo_url', '');
        $faviconUrl  = $tenant->getBranding('favicon_url', '');

        $loginBgCss = $loginBgImg
            ? "url('{$loginBgImg}') center/cover no-repeat"
            : $loginBg;

        $css = ":root {\n"
             . "  --fi-primary: {$primary};\n"
             . "  --fi-primary-50: {$primary}1a;\n"
             . "  --fi-accent: {$accent};\n"
             . "  --color-primary-50: {$primary}0d;\n"
             . "  --color-primary-100: {$primary}1a;\n"
             . "  --color-primary-200: {$primary}33;\n"
             . "  --color-primary-300: {$primary}4d;\n"
             . "  --color-primary-400: {$primary}80;\n"
             . "  --color-primary-500: {$primary};\n"
             . "  --color-primary-600: {$primary};\n"
             . "  --color-primary-700: {$primary};\n"
             . "  --color-primary-800: {$primary};\n"
             . "  --color-primary-900: {$primary};\n"
             . "  --color-primary-950: {$primary};\n"
             . "  --tenant-login-bg: {$loginBgCss};\n"
             . "  --tenant-logo-url: url('{$logoUrl}');\n"
             . "}\n";

        if ($loginBgImg) {
            $css .= ".fi-simple-layout { background: {$loginBgCss}; }\n";
        } else {
            $css .= ".fi-simple-layout { background: {$loginBg}; }\n";
        }

        if ($logoUrl) {
            $css .= ".fi-logo img { content: url('{$logoUrl}'); }\n";
        }

        $dir = public_path("tenant/{$tenant->id}");
        if (! is_dir($dir)) {
            mkdir($dir, 0755, true);
        }
        file_put_contents("{$dir}/branding.css", $css);

        if ($faviconUrl) {
            file_put_contents("{$dir}/favicon-override.css",
                "link[rel='icon'] { href: url('{$faviconUrl}'); }\n"
            );
        }
    }

    private function auditSettingChange(string $group, string $key, mixed $old, mixed $new): void
    {
        try {
            if (class_exists(AuditLog::class) && method_exists(AuditLog::class, 'record')) {
                AuditLog::record(
                    action: "settings.{$group}.changed",
                    auditable: $this->tenant,
                    oldValues: ['key' => $key, 'value' => $old],
                    newValues: ['key' => $key, 'value' => $new],
                    tags: "settings,{$group}",
                );
            }
        } catch (\Throwable $e) {
            logger()->warning('SettingsService: failed to record audit log', [
                'group' => $group,
                'key'   => $key,
                'error' => $e->getMessage(),
            ]);
        }
    }
}
