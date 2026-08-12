<?php

namespace App\Providers;

use App\Models\Lead;
use App\Models\MeetingBooking;
use App\Observers\LeadObserver;
use App\Observers\MeetingBookingObserver;
use App\Services\Automations\AutomationDispatcher;
use App\Services\ConnectorRegistry;
use App\Services\IntegrationDispatcher;
use App\Services\InvitationService;
use App\Services\LeadActivityService;
use App\Services\LeadDuplicateDetector;
use App\Services\LeadIngestionService;
use App\Services\LeadScoringService;
use App\Services\LicenseService;
use App\Services\PlanService;
use App\Services\SettingsService;
use App\Services\TenantSmtpManager;
use App\Services\TwoFactorAuthService;
use App\Events\FormSubmitted;
use App\Listeners\AuthAttemptListener;
use App\Listeners\DispatchFormSubmittedAutomations;
use App\Listeners\SendSubscriptionNotifications;
use Illuminate\Cache\RateLimiting\Limit;
use Illuminate\Http\Request;
use Illuminate\Queue\Events\JobProcessing;
use Illuminate\Support\Facades\Event;
use Illuminate\Support\Facades\Queue;
use Illuminate\Support\Facades\RateLimiter;
use Illuminate\Support\ServiceProvider;
use PragmaRX\Google2FA\Google2FA;

class AppServiceProvider extends ServiceProvider
{
    public function register(): void
    {
        $this->app->singleton(Google2FA::class, fn () => new Google2FA());

        $this->app->singleton(TwoFactorAuthService::class, function ($app) {
            return new TwoFactorAuthService($app->make(Google2FA::class));
        });

        $this->app->singleton(InvitationService::class);

        $this->app->singleton(LeadDuplicateDetector::class);
        $this->app->singleton(LeadIngestionService::class);
        $this->app->singleton(ConnectorRegistry::class);
        $this->app->singleton(LeadScoringService::class);
        $this->app->singleton(LeadActivityService::class);
        $this->app->singleton(AutomationDispatcher::class);
        $this->app->singleton(IntegrationDispatcher::class);
        $this->app->singleton(\App\Services\ReportService::class);
        $this->app->singleton(LicenseService::class);
        $this->app->singleton(SettingsService::class);
        $this->app->singleton(TenantSmtpManager::class);
        $this->app->singleton(PlanService::class);
        $this->app->singleton(\App\Services\BillingMetricsService::class);
        $this->app->singleton(\App\Services\TenantSecurityService::class);
        $this->app->singleton(\App\Services\SubscriptionEventService::class);
        $this->app->singleton(\App\Billing\PaymentGatewayManager::class);

        // Swap Filament's password-reset notification for a sync
        // variant.  Filament's RequestPasswordReset page resolves
        // the notification via `app(ResetPassword::class, ...)` and
        // calls `$user->notify($instance)`.  Filament's class
        // implements ShouldQueue, so every reset email went to the
        // jobs table on shared-hosting installs where no queue
        // worker runs.  Overriding User::sendPasswordResetNotification
        // alone isn't enough because Filament bypasses it with a
        // callback — only this container binding catches the path.
        $this->app->bind(
            \Filament\Auth\Notifications\ResetPassword::class,
            function ($app, array $parameters) {
                $token = (string) ($parameters['token'] ?? '');
                return new \App\Notifications\SyncPasswordResetNotification($token);
            }
        );
    }

    public function boot(): void
    {
        // Bump Livewire's temporary-file-upload validation cap.  The
        // package default is `max:12288` (12 MB) which short-circuits
        // every Filament FileUpload before Filament's own ->maxSize()
        // check even runs — buyers uploading a 27.9 MB update zip
        // through /super-admin/updates were getting:
        //
        //   The data.package.<uuid> field must not be greater than
        //   12288 kilobytes.
        //
        // …even though Updates.php declares maxSize(204800) (200 MB)
        // on the FileUpload itself.  Bumping to 307200 (300 MB)
        // covers the current update zip (~28 MB), the modules zip
        // (capped at 100 MB), and leaves headroom for future bundles
        // without forcing buyers to publish + maintain their own
        // config/livewire.php.  Per-field Filament ->maxSize() caps
        // still apply on top of this — it's a max-of-the-stack
        // setting, not a per-upload override.
        config()->set('livewire.temporary_file_upload.rules', ['required', 'file', 'max:307200']);

        // M-A4: Lead's lifecycle concerns split across multiple
        // observers — Eloquent fires every observer registered for
        // a model on each lifecycle event.  Keeping the orthogonal
        // concerns (audit log, outbound webhook, the rest) in
        // separate classes lets each evolve without touching the
        // others.
        Lead::observe(LeadObserver::class);
        Lead::observe(\App\Observers\LeadAuditObserver::class);
        Lead::observe(\App\Observers\LeadWebhookObserver::class);
        MeetingBooking::observe(MeetingBookingObserver::class);

        // Tagged-cache invalidation. NO-OP on file/database cache
        // drivers (the helper short-circuits there); on redis /
        // memcached this flushes the tenant's nav badges + dashboard
        // widgets the moment a write happens, so the dashboard never
        // shows up to 60 s of stale data after a Lead/AutomationRun/
        // LeadImport flips state.
        Lead::observe(\App\Observers\TenantCacheFlushObserver::class);
        \App\Models\AutomationRun::observe(\App\Observers\TenantCacheFlushObserver::class);
        \App\Models\LeadImport::observe(\App\Observers\TenantCacheFlushObserver::class);

        // Per-tenant provisioning. Currently seeds the starter email-
        // template set into every newly-created tenant so the workspace
        // owner has working templates on first login. Catches the SA UI
        // path, public registration, future API/CLI - any code path
        // that calls Tenant::create. Bypassed by demo seeders via
        // Tenant::withoutEvents() because the demo has its own template
        // fixtures and doesn't want the starter set on every refresh.
        \App\Models\Tenant::observe(\App\Observers\TenantProvisioningObserver::class);

        // Live-demo banner. Renders only when LEADHUB_DEMO_MODE=true,
        // applied to both Filament panels (admin + super-admin) via a
        // global render hook so we don't have to duplicate the
        // registration in two PanelProviders.  The banner markup +
        // styles + English copy live in the dedicated demo-banner
        // Blade view + CSS file + lang file per CodeCanyon Items 1+2
        // (no inline styles, every visible string routed via __()).
        \Filament\Support\Facades\FilamentView::registerRenderHook(
            \Filament\View\PanelsRenderHook::BODY_START,
            fn (): string => \App\Support\DemoMode::isOn()
                ? view('filament.demo-banner')->render()
                : '',
        );

        // Pending-migrations banner. Shown to Super-Admins on either panel
        // when the DB has unrun migrations (new code deployed before
        // `migrate`).  Replaces the old white-screen 500 with an actionable
        // nudge to System Health.  is_super_admin is checked FIRST so the
        // (cached) migration probe only runs for operators, never tenants.
        \Filament\Support\Facades\FilamentView::registerRenderHook(
            \Filament\View\PanelsRenderHook::BODY_START,
            fn (): string => (auth()->user()?->is_super_admin ?? false)
                && \App\Support\MigrationStatus::hasPending()
                ? view('filament.migrations-banner')->render()
                : '',
        );
        Event::listen(FormSubmitted::class, DispatchFormSubmittedAutomations::class);
        Event::subscribe(SendSubscriptionNotifications::class);

        // AI SDR: any human reply through the messaging channel (lead
        // inbound, or a rep replying from the Conversations panel) HARD-
        // pauses the autonomous agent. The email side of the same rule
        // lives in LeadEmail::booted(). The agent is email-only and never
        // emits NewLeadMessage, so it can never pause itself here.
        Event::listen(
            \App\Events\NewLeadMessage::class,
            \App\Listeners\PauseAiSdrOnHumanReply::class,
        );

        // M-A4: notify-the-assignee logic lives in a listener so the
        // observer can stay focused on Eloquent-lifecycle work.  The
        // event already implements ShouldBroadcast — Laravel's built-in
        // BroadcastListener handles the realtime payload, so we don't
        // need a separate "broadcast" listener on top of this.
        Event::listen(
            \App\Events\LeadAssigned::class,
            \App\Listeners\Lead\NotifyLeadAssignee::class,
        );

        // Brute-force monitoring: every Failed auth event writes a
        // login_attempts row and triggers a 15-minute lockout when the
        // threshold (5 failures / 15 minutes) is hit.
        Event::listen(\Illuminate\Auth\Events\Failed::class, AuthAttemptListener::class);

        // Rate limiter for public self-service registration: 5 POSTs/hour/IP.
        RateLimiter::for('register', function (Request $request) {
            return Limit::perHour(5)->by($request->ip());
        });

        // Invitation accept — 10 POSTs per hour per IP.  Invitation
        // tokens are 64-char random so brute-forcing is impractical,
        // but the limit prevents a slow enumeration + covers retry
        // loops during the password-confirm field.
        RateLimiter::for('invite-accept', function (Request $request) {
            return Limit::perHour(10)->by($request->ip());
        });

        // Password reset request — 5 per 15 minutes per IP + per
        // email address (whichever hits first).  Prevents
        // address-enumeration AND mass-mailing abuse.
        RateLimiter::for('password-reset-request', function (Request $request) {
            return [
                Limit::perMinutes(15, 5)->by($request->ip()),
                Limit::perMinutes(15, 3)->by(strtolower((string) $request->input('email'))),
            ];
        });

        // Email open/click tracking — 60 hits/min keyed by remote IP so
        // one noisy proxy can't burn the limit for every other recipient
        // sharing the same default global bucket.
        RateLimiter::for('email-track', function (Request $request) {
            return Limit::perMinute(60)->by('email-track:'.$request->ip());
        });

        // LeadBot chat — public, unauthenticated, cross-origin POST from
        // the tenant's own website.  20 messages/min/IP is plenty for a
        // human conversation while blunting abuse/scraping of the shared
        // (global) OpenAI key.  Per-conversation + per-bot daily caps in
        // PublicChatbotController back this up.
        RateLimiter::for('chatbot', function (Request $request) {
            return Limit::perMinute(20)->by('chatbot:'.$request->ip());
        });

        // Queue::before / Queue::after — bind `current_tenant` for the
        // duration of any queued job that exposes a `tenantId` property
        // on its command.  In an HTTP request, ResolveTenant middleware
        // sets the binding; in a queue worker there is no middleware,
        // so without this hook every BelongsToTenant query inside a
        // job either fails-closed (returns zero rows) or — worse —
        // inherits the worker's previous job's binding.
        //
        // Implementation: parse the resolved command via reflection so
        // the property can be public/protected/private, and so a job
        // without a `tenantId` property is a transparent no-op (the
        // hook just falls through).  Wrapped in try/catch with a
        // silent-failure fallback because this is queue infrastructure
        // — we never want to break job processing if the payload
        // shape is unexpected.
        Queue::before(function (JobProcessing $event) {
            try {
                $serialized = $event->job->payload()['data']['command'] ?? null;
                if (! is_string($serialized) || $serialized === '') {
                    return;
                }

                $command = @unserialize($serialized);
                if (! is_object($command)) {
                    return;
                }

                $reflection = new \ReflectionObject($command);
                if (! $reflection->hasProperty('tenantId')) {
                    return;
                }

                $property = $reflection->getProperty('tenantId');
                $property->setAccessible(true);
                $tenantId = $property->getValue($command);

                if (! $tenantId) {
                    return;
                }

                $tenant = \App\Models\Tenant::withoutGlobalScope('tenant')
                    ->find((int) $tenantId);
                if ($tenant) {
                    app()->instance('current_tenant', $tenant);
                }
            } catch (\Throwable) {
                // Never let context binding break job execution.
            }

            // Mirror the active app locale onto Carbon so any
            // ->diffForHumans() / ->translatedFormat() invoked from
            // inside a queued job (e.g. SendNotificationDigest's
            // "5 minutes ago" timestamps) renders in the same
            // language as the surrounding email body, which IS
            // routed through __() — Notification::locale()/
            // withLocale() restores the translator locale per job,
            // but Carbon needs an independent setLocale() call.
            try {
                $currentLocale = app()->getLocale();
                if (is_string($currentLocale) && $currentLocale !== '') {
                    \Carbon\Carbon::setLocale($currentLocale);
                    \Carbon\CarbonImmutable::setLocale($currentLocale);
                }
            } catch (\Throwable) {
                // Locale pack missing — fall back to Carbon's previous
                // setting rather than crashing the worker.
            }
        });

        Queue::after(function () {
            if (app()->bound('current_tenant')) {
                app()->forgetInstance('current_tenant');
            }
        });

        // Diagnostic: warn when SA_IP_ALLOWLIST_STRICT is on but
        // TRUSTED_PROXIES is empty.  Strict mode requires REMOTE_ADDR
        // (the raw TCP peer) to match the allowlist — meaningful only
        // when there is no proxy in front of PHP-FPM.  An empty
        // TRUSTED_PROXIES with strict mode is the canonical
        // direct-to-PHP setup, but only safe if the buyer is certain
        // PHP-FPM is unreachable except via the local web server.
        // Surface this in the log so a misconfiguration is visible
        // during the smoke-test phase of a deploy.
        if (
            (bool) config('auth.super_admin_ip_allowlist_strict', false)
            && (string) config('auth.super_admin_ip_allowlist', '') !== ''
            && trim((string) env('TRUSTED_PROXIES', '')) === ''
        ) {
            \Illuminate\Support\Facades\Log::warning(
                'SA_IP_ALLOWLIST_STRICT is on but TRUSTED_PROXIES is empty — '
                . 'make sure direct PHP-FPM access is blocked, otherwise the '
                . 'allowlist offers no real protection against forged '
                . 'X-Forwarded-For headers.'
            );
        }
    }
}
