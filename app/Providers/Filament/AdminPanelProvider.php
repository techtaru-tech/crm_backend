<?php

namespace App\Providers\Filament;

use App\Filament\Pages\ProfilePage;
use App\Filament\Pages\SessionsPage;
use App\Filament\Pages\TwoFactorSetup;
use App\Filament\Widgets\LiveLeadFeed;
use Filament\Auth\MultiFactor\App\AppAuthentication;
use Filament\Http\Middleware\Authenticate;
use Filament\Http\Middleware\AuthenticateSession;
use Filament\Http\Middleware\DisableBladeIconComponents;
use Filament\Http\Middleware\DispatchServingFilamentEvent;
use Filament\Navigation\NavigationGroup;
use App\Filament\Pages\Dashboard;
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

class AdminPanelProvider extends PanelProvider
{
    public function panel(Panel $panel): Panel
    {
        return $panel
            ->default()
            ->id('admin')
            ->path('admin')
            ->login(\App\Filament\Pages\WhiteLabelLogin::class)
            ->passwordReset()
            ->emailVerification()
            ->multiFactorAuthentication([
                AppAuthentication::make()->recoverable(),
            ])
            // Favicon follows the operator's uploaded branding (this tenant's
            // favicon → SA-global BrandingSettings.favicon_url → bundled mark) so
            // the workspace login page AND the admin pages show the same favicon
            // as the public site — previously hardcoded, so only the marketing
            // landing reflected an uploaded favicon. Closure-deferred + try/catch
            // like brandLogo so it's read per-request and survives a not-yet-
            // migrated settings table.
            ->favicon(function () {
                $tenant = app()->bound('current_tenant') ? app('current_tenant') : null;
                $tenantFav = $tenant?->getBranding('favicon_url');
                if (filled($tenantFav)) {
                    return \App\Support\PublicMedia::url($tenantFav);
                }
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
                'primary' => Color::Indigo,
            ])
            ->brandName(fn () => (app()->bound('current_tenant') ? app('current_tenant') : null)?->getAppName() ?? config('leadhub.branding.app_name', 'LeadHub'))
            // Native brandLogo() replaces the text-only fallback Filament
            // otherwise shows.  Resolution chain (most specific wins):
            //   1. this tenant's own uploaded logo_url
            //   2. the SA-global BrandingSettings.logo_url (install-wide
            //      default set at /super-admin/branding) — previously
            //      MISSING, which is why "logo dont change on the whole
            //      site": a SA who set a global logo never saw it on any
            //      tenant panel.
            //   3. the bundled LeadHub mark.
            // BrandingSettings read is try/catch-wrapped so a not-yet-
            // migrated settings table falls through instead of 500-ing.
            ->brandLogo(function () {
                $tenant = app()->bound('current_tenant') ? app('current_tenant') : null;
                $tenantLogo = $tenant?->getBranding('logo_url');
                if (filled($tenantLogo)) {
                    return \App\Support\PublicMedia::url($tenantLogo);
                }
                try {
                    $saLogo = app(\App\Settings\BrandingSettings::class)->logo_url;
                    if (filled($saLogo)) {
                        return \App\Support\PublicMedia::url($saLogo);
                    }
                } catch (\Throwable) {
                    // settings table not migrated yet — fall through
                }
                return asset('leadhub-brand.svg');
            })
            ->brandLogoHeight('2.25rem')
            // Sidebar logo jumps back to the public marketing landing
            // page instead of the tenant admin dashboard.  There's a
            // Dashboard link in the nav for tenant admins who want to
            // hop to their own overview; the logo becomes the
            // "escape to the marketing site" affordance.
            ->homeUrl('/')
            ->navigationGroups([
                // The string passed to make() is the IDENTIFIER that
                // Page/Resource classes match against via their static
                // $navigationGroup property — keep it English.  The
                // displayed label comes from ->label(__('navigation.groups.X'))
                // so buyers can translate without touching this file.
                NavigationGroup::make('Leads')->label(__('navigation.groups.leads'))->collapsible(false),
                NavigationGroup::make('Pipeline')->label(__('navigation.groups.pipeline'))->collapsible(false),
                NavigationGroup::make('Inbox')->label(__('navigation.groups.inbox'))->collapsible(false),
                NavigationGroup::make('Forms')->label(__('navigation.groups.forms'))->collapsible(false),
                NavigationGroup::make('Automations')->label(__('navigation.groups.automations'))->collapsible(false),
                NavigationGroup::make('Integrations')->label(__('navigation.groups.integrations')),
                NavigationGroup::make('Reports')->label(__('navigation.groups.reports')),
                // Settings groups: expanded by default so new tenants
                // can see what's available at a glance. Still
                // collapsible by user preference via the caret icon.
                NavigationGroup::make('Brand & Domain')->label(__('navigation.groups.brand_and_domain')),
                NavigationGroup::make('Communications')->label(__('navigation.groups.communications')),
                NavigationGroup::make('Users & Access')->label(__('navigation.groups.team_and_access')),
                NavigationGroup::make('Advanced')->label(__('navigation.groups.advanced')),
                NavigationGroup::make('Account')->label(__('navigation.groups.account')),
                // Generic catch-all groups used by misc Pages/Resources
                // ('Settings' for tenant-level config like Industry Packs,
                // 'Tools' for utility resources like Tracking Snippets and
                // Lead-Capture Widgets).  Without an explicit make()/label()
                // entry the sidebar would render the raw English
                // identifier instead of resolving via the lang file.
                NavigationGroup::make('Settings')->label(__('navigation.groups.settings')),
                NavigationGroup::make('Sales')->label(__('navigation.groups.sales')),
                NavigationGroup::make('Templates')->label(__('navigation.groups.templates')),
                NavigationGroup::make('Tools')->label(__('navigation.groups.tools')),
            ])
            ->discoverResources(in: app_path('Filament/Resources'), for: 'App\Filament\Resources')
            ->discoverPages(in: app_path('Filament/Pages'), for: 'App\Filament\Pages')
            ->pages([
                Dashboard::class,
            ])
            ->discoverWidgets(in: app_path('Filament/Widgets'), for: 'App\Filament\Widgets')
            ->widgets([
                \App\Filament\Widgets\GettingStartedChecklist::class,
                \App\Filament\Widgets\LeadsStatsOverview::class,
                // Phase 1 funnel tiles: unassigned, today's / overdue
                // follow-ups, leads by rep.
                \App\Filament\Widgets\FunnelFollowUpOverview::class,
                \App\Filament\Widgets\RevenueForecastWidget::class,
                \App\Filament\Widgets\LeadsOverTimeChart::class,
                \App\Filament\Widgets\LeadsBySourceChart::class,
                \App\Filament\Widgets\PipelineDistributionChart::class,
                \App\Filament\Widgets\LeadStatusChart::class,
                LiveLeadFeed::class,
            ])
            ->userMenuItems([
                // "Profile" entry surfaces the existing ProfilePage
                // in the user dropdown so staff can actually reach
                // their profile from the top-right avatar — it was
                // only visible in the sidebar "Settings" group
                // before, which an operator wouldn't scan for
                // "who am I / edit my name" tasks.  Default
                // Filament renders the signed-in user's name +
                // avatar in the user menu trigger already.
                'profile' => \Filament\Navigation\MenuItem::make()
                    ->label(__('navigation.user_menu.my_profile'))
                    ->icon('heroicon-o-user-circle')
                    ->url(fn () => \App\Filament\Pages\ProfilePage::getUrl()),
                'dashboard_preferences' => \Filament\Navigation\MenuItem::make()
                    ->label(__('navigation.user_menu.customize_dashboard'))
                    ->icon('heroicon-o-adjustments-horizontal')
                    ->url('/admin/dashboard/preferences'),
            ])
            ->renderHook(
                PanelsRenderHook::HEAD_END,
                fn () => '<link rel="apple-touch-icon" sizes="180x180" href="/apple-touch-icon.png">'
                    . '<link rel="manifest" href="/site.webmanifest">'
                    . '<meta name="theme-color" content="#4f46e5">'
                    // Static admin-panel overrides (chart-filter widget,
                    // .fi-logo float animation) — extracted to
                    // public/css/views/filament/admin-panel-overrides.css
                    // per CodeCanyon's "no inline styles unless dynamic" rule.
                    // ?v=<filemtime> cache-busts the stylesheet (it is linked
                    // without Vite hashing) so CSS fixes load instead of a
                    // stale cached copy after an update.
                    . '<link rel="stylesheet" href="' . asset('css/views/filament/admin-panel-overrides.css') . '?v=' . (@filemtime(public_path('css/views/filament/admin-panel-overrides.css')) ?: '1') . '">'
                    . '<script src="' . asset('vendor/chartjs/chart.umd.min.js') . '" defer></script>'
                    // PWA service-worker registration + declarative-action
                    // helpers (clipboard, confirm-form) — extracted to
                    // external files so this render hook contains zero
                    // inline JavaScript.
                    . '<script src="' . asset('js/views/shared/sw-register.js') . '" defer></script>'
                    . '<script src="' . asset('js/views/shared/clipboard.js') . '" defer></script>'
                    . '<script src="' . asset('js/views/shared/confirm-form.js') . '" defer></script>',
            )
            ->renderHook(
                PanelsRenderHook::BODY_START,
                fn () => view('filament.impersonation-bar')
            )
            // reCAPTCHA v3 — inject hidden input (wire:model-bound
            // to WhiteLabelLogin::$recaptchaToken) + executor
            // script right below the login form.  The script re-
            // executes on submit so the token is fresh when the
            // Livewire authenticate() method reads it.  Empty
            // string when reCAPTCHA is off / keys missing, so this
            // costs nothing on an unconfigured install.
            ->renderHook(
                PanelsRenderHook::AUTH_LOGIN_FORM_AFTER,
                fn () => app(\App\Services\RecaptchaService::class)
                    ->filamentLoginHtml('admin_login')
            )
            // .fi-logo float + drop-shadow animation moved into the
            // panel-wide stylesheet (see admin-panel-overrides.css) —
            // this render hook is no longer needed.  Kept removed to
            // avoid duplicate rules; keep the brandLogo config above.
            ->renderHook(
                PanelsRenderHook::TOPBAR_END,
                fn () => auth()->check() ? view('filament.notification-bell') : ''
            )
            ->renderHook(
                PanelsRenderHook::BODY_END,
                fn () => auth()->check() ? view('filament.push-sw') : ''
            )
            // CMD+K command palette — global keyboard shortcut for
            // jumping between pages and triggering common actions.
            // Renders only for authed users; no-op otherwise.
            ->renderHook(
                PanelsRenderHook::BODY_END,
                fn () => auth()->check() ? view('filament.cmd-k') : ''
            )
            ->renderHook(
                PanelsRenderHook::PAGE_START,
                function () {
                    $user = auth()->user();
                    if (! $user) return '';
                    if (! $user->isSuperAdmin()) return '';
                    $info = cache('leadhub.update_info');
                    if (! $info) return '';
                    $current = config('leadhub.version', '1.0.0');
                    $latest  = $info['latest_version'] ?? $current;
                    if (version_compare($latest, $current, '<=')) return '';
                    $changelogUrl = $info['changelog_url'] ?? null;
                    return view('filament.update-banner', compact('latest', 'current', 'changelogUrl'));
                }
            )
            // (Previously: a "Outbound email is not configured" banner
            // shown on every tenant /admin/* page when mail.default ===
            // 'log'. Removed entirely - SMTP is a platform-level setting
            // that ONLY the Super Admin can configure (via Script
            // Settings or .env). Surfacing the banner to tenant admins
            // and team members was misleading because they have no way
            // to act on it from the tenant panel. The Super Admin's
            // setup guide already covers email configuration.)
            // GDPR Article 17 — pending-deletion banner.  When a workspace
            // owner has scheduled erasure, every page of the tenant panel
            // surfaces a red banner with the date + a link to the Privacy
            // & Data page where the Cancel button lives.  Without this,
            // co-workers signing in mid-cool-off would have no idea their
            // data was about to be wiped.
            // GDPR Article 17 — pending-deletion banner.  Extracted from
            // an inline HtmlString to a Blade view + CSS file + lang
            // keys per CodeCanyon Items 1 + 2.  The view renders the
            // banner with the scheduled deletion date (passed in) and
            // a Cancel-deletion link to the Privacy & Data page.
            ->renderHook(
                PanelsRenderHook::PAGE_START,
                function () {
                    $user = auth()->user();
                    if (! $user) return '';
                    $tenant = $user->tenant ?? null;
                    if (! $tenant || ! $tenant->deletion_requested_at) return '';

                    $scheduled = $tenant->deletion_scheduled_at;
                    $when = $scheduled?->translatedFormat('F j, Y');

                    return view('filament.deletion-banner', ['when' => $when]);
                }
            )
            ->middleware([
                // Filament panels do NOT run Laravel's `web` group, so the
                // TrustProxies entry prepended in bootstrap/app.php never
                // reached /admin/*.  Behind any TLS-terminating proxy
                // (Cloudflare, nginx, a dev tunnel) the panel therefore read
                // the request as plain http on the proxy's internal host and
                // emitted http:// asset + redirect URLs.  Keep it first so
                // everything below sees the corrected scheme/host/IP.
                \App\Http\Middleware\TrustProxies::class,
                EncryptCookies::class,
                AddQueuedCookiesToResponse::class,
                StartSession::class,
                AuthenticateSession::class,
                ShareErrorsFromSession::class,
                PreventRequestForgery::class,
                SubstituteBindings::class,
                DisableBladeIconComponents::class,
                DispatchServingFilamentEvent::class,
                // EnforceLicense runs early so a revoked / missing
                // licence blocks the panel even before tenant resolution
                // or subscription gates fire.  Demo mode short-circuits
                // it; the Licence Settings + logout routes are on the
                // middleware's internal allowlist so the operator can
                // always paste in a fresh code.
                \App\Http\Middleware\EnforceLicense::class,
                \App\Http\Middleware\CheckImpersonation::class,
                \App\Http\Middleware\ResolveTenant::class,
                \App\Http\Middleware\EnforceTenantScope::class,
                \App\Http\Middleware\EnforceSubscription::class,
                \App\Http\Middleware\RedirectToOnboarding::class,
                \App\Http\Middleware\InjectTenantBranding::class,
                \App\Http\Middleware\EnforceSecuritySettings::class,
            ])
            ->authMiddleware([
                Authenticate::class,
                \App\Http\Middleware\EnforceNotSuspended::class,
                \App\Http\Middleware\RedirectSuperAdminFromTenantPanel::class,
                \App\Http\Middleware\Enforce2Fa::class,
            ]);
    }
}
