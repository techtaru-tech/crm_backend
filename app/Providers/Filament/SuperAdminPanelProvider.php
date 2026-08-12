<?php

namespace App\Providers\Filament;

use Filament\Http\Middleware\Authenticate;
use Filament\Http\Middleware\AuthenticateSession;
use Filament\Http\Middleware\DisableBladeIconComponents;
use Filament\Http\Middleware\DispatchServingFilamentEvent;
use Filament\Navigation\NavigationGroup;
use Filament\Panel;
use Filament\PanelProvider;
use Filament\Support\Colors\Color;
use Filament\View\PanelsRenderHook;
use Illuminate\Cookie\Middleware\AddQueuedCookiesToResponse;
use Illuminate\Cookie\Middleware\EncryptCookies;
use Illuminate\Foundation\Http\Middleware\PreventRequestForgery;
use Illuminate\Routing\Middleware\SubstituteBindings;
use Illuminate\Session\Middleware\StartSession;
use Illuminate\View\Middleware\ShareErrorsFromSession;

class SuperAdminPanelProvider extends PanelProvider
{
    public function panel(Panel $panel): Panel
    {
        return $panel
            ->id('super-admin')
            ->path('super-admin')
            // Custom login page so we can splice the reCAPTCHA v3
            // check in before the credentials check runs.
            ->login(\App\Filament\SuperAdmin\Pages\SuperAdminLogin::class)
            // Enable the "Forgot password" link on the SA login
            // page.  Uses Laravel's password broker — same plumbing
            // the tenant /admin panel relies on.  No separate
            // reset-URL config needed: Filament wires up the reset
            // form automatically.
            ->passwordReset()
            // Favicon follows the operator's uploaded SA-global branding favicon
            // so the SA login page + pages match the public site (was hardcoded
            // to the bundled mark). Closure-deferred + try/catch like brandLogo.
            ->favicon(function () {
                try {
                    $saFav = app(\App\Settings\BrandingSettings::class)->favicon_url;
                    if (filled($saFav)) {
                        return \App\Support\PublicMedia::url($saFav);
                    }
                } catch (\Throwable) {
                    // settings table not migrated yet — fall through
                }
                return asset('favicon.svg');
            })
            ->colors([
                'primary' => Color::Rose,
            ])
            // Brand name in the SA sidebar header — translated so
            // non-English locales replace "LeadHub Super Admin" with
            // the locale-appropriate phrase.  Closure-deferred so the
            // translator is resolved per-request (active locale is
            // bound after the panel boots).
            ->brandName(fn () => (string) __('filament/sa_dashboard.title'))
            // Sidebar logo reads the SA-global BrandingSettings.logo_url
            // (set at /super-admin/branding) so an operator's uploaded
            // logo actually appears in the panel.  Closure-deferred so
            // settings are read per-request (after boot) — wrapped in
            // try/catch so a not-yet-migrated settings table during a
            // partial install falls back to the bundled mark instead
            // of 500-ing every page.  Customer report: "logo dont
            // change on the whole site".
            ->brandLogo(function () {
                try {
                    $logo = app(\App\Settings\BrandingSettings::class)->logo_url;
                    if (filled($logo)) {
                        return \App\Support\PublicMedia::url($logo);
                    }
                } catch (\Throwable) {
                    // settings table not migrated yet — fall through
                }
                return asset('leadhub-brand.svg');
            })
            ->brandLogoHeight('2.25rem')
            // Make the sidebar logo jump back to the public marketing
            // landing page instead of the SA dashboard.  Matches the
            // "logo === home" convention on most marketing-oriented
            // products, and lets an SA reach the public page without
            // typing the URL.
            ->homeUrl('/')
            ->navigationGroups([
                // make() string is the IDENTIFIER (matches static
                // $navigationGroup on SuperAdmin Page/Resource classes);
                // ->label() supplies the translated displayed label.
                NavigationGroup::make('Tenants')->label(__('navigation.groups.tenants')),
                NavigationGroup::make('Users')->label(__('navigation.groups.users')),
                NavigationGroup::make('Billing')->label(__('navigation.groups.billing')),
                NavigationGroup::make('Marketing')->label(__('navigation.groups.marketing')),
                NavigationGroup::make('System')->label(__('navigation.groups.system')),
            ])
            ->renderHook(
                PanelsRenderHook::BODY_START,
                fn () => view('filament.impersonation-bar')
            )
            // reCAPTCHA v3 on SA login — same hidden-input +
            // executor pattern as the tenant /admin panel but
            // gated by the sa_login action + guard_sa_login
            // toggle.
            ->renderHook(
                PanelsRenderHook::AUTH_LOGIN_FORM_AFTER,
                fn () => app(\App\Services\RecaptchaService::class)
                    ->filamentLoginHtml('sa_login')
            )
            // Disambiguation banner under the Super Admin login form.
            // Without this, trial buyers who
            // hit /super-admin/login first try to use a tenant admin
            // account here and get "Invalid credentials" with no clue
            // why. The banner spells out which panel they're on and
            // links to /admin/login for tenant admins. Filament 4
            // concatenates content from multiple renderHook
            // registrations on the same hook, so this renders below
            // the reCAPTCHA hidden inputs without colliding.
            // Disambiguation banner under the SA login form — extracted
            // from an inline HtmlString to a dedicated Blade view + CSS
            // file so the panel provider has zero inline styles and
            // every visible string flows through __() per CodeCanyon
            // Items 1 + 2.
            ->renderHook(
                PanelsRenderHook::AUTH_LOGIN_FORM_AFTER,
                fn () => view('filament.super-admin.login-disambiguation')
            )
            // Soft bounce on the native Filament brandLogo img + CSS
            // tweaks specific to the Super Admin panel.  Force the
            // entire SA panel content column to fill the full panel
            // width.  Filament 4 emits `fi-panel-super-admin` on the
            // <body>, which lets us scope the override to just this
            // panel without touching the tenant-facing /admin panel.
            //
            // Previous attempts targeted `.fi-resource-create-record-
            // page`-style classes that don't actually exist in
            // Filament 4 — the real wrapper classes are .fi-page,
            // .fi-main, .fi-main-ctn.  getMaxContentWidth():
            // Width::Full on individual pages is honoured upstream,
            // but operators consistently reported narrow render on
            // wide screens.  This CSS makes the constraint irrelevant.
            // SA panel global tweaks — extracted from an inline <style>
            // block to public/css/views/filament/sa-panel-overrides.css
            // per CodeCanyon Item 2 ("no inline styles unless dynamic").
            ->renderHook(
                PanelsRenderHook::HEAD_END,
                // ?v=<filemtime> cache-busts the stylesheet (linked without
                // Vite hashing) so CSS fixes load instead of a stale copy.
                fn () => new \Illuminate\Support\HtmlString(
                    '<link rel="stylesheet" href="' . asset('css/views/filament/sa-panel-overrides.css') . '?v=' . (@filemtime(public_path('css/views/filament/sa-panel-overrides.css')) ?: '1') . '">'
                )
            )
            // (Previously: a persistent yellow "Outbound email is not
            // configured" banner shown on every SA page when
            // mail.default === 'log'. Removed because the Super Admin
            // already has a step-by-step setup guide for SMTP, so the
            // standing banner was redundant and visually noisy.)
            ->discoverResources(in: app_path('Filament/SuperAdmin/Resources'), for: 'App\Filament\SuperAdmin\Resources')
            ->discoverPages(in: app_path('Filament/SuperAdmin/Pages'), for: 'App\Filament\SuperAdmin\Pages')
            ->discoverWidgets(in: app_path('Filament/SuperAdmin/Widgets'), for: 'App\Filament\SuperAdmin\Widgets')
            ->middleware([
                EncryptCookies::class,
                AddQueuedCookiesToResponse::class,
                StartSession::class,
                AuthenticateSession::class,
                ShareErrorsFromSession::class,
                PreventRequestForgery::class,
                SubstituteBindings::class,
                DisableBladeIconComponents::class,
                DispatchServingFilamentEvent::class,
                // SA panel hardening: optional IP allowlist (off by
                // default).  Set SA_IP_ALLOWLIST in .env to enable.
                \App\Http\Middleware\EnforceSuperAdminIpAllowlist::class,
                // EnforceLicense kicks in when the install's licence
                // is missing / invalid / past the grace window.  Demo
                // installs bypass; the Licence Settings page is on
                // the middleware's allowlist so the SA can always
                // paste in a fresh purchase code to recover.
                \App\Http\Middleware\EnforceLicense::class,
            ])
            ->authMiddleware([
                Authenticate::class,
                \App\Http\Middleware\RequireSuperAdmin::class,
                \App\Http\Middleware\EnforceNotSuspended::class,
                // 2FA enforcement: when the system-wide
                // SecuritySettings.enforce_2fa flag is on, redirect SAs
                // who have not enrolled to the universal TwoFactorSetup
                // page on the tenant panel (same `web` guard, so SAs
                // can reach it).  Tenant-side enforcement still flows
                // through the same middleware on AdminPanelProvider —
                // the middleware picks the right enforce_2fa source
                // based on whether `current_tenant` is bound.
                \App\Http\Middleware\Enforce2Fa::class,
            ])
            ->authGuard('web');
    }

    public function register(): void
    {
        parent::register();
    }
}
