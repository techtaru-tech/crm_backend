<?php

use App\Http\Controllers\EmailTrackingController;
use App\Http\Controllers\ImpersonationController;
use App\Http\Controllers\InvitationController;
use App\Http\Controllers\OAuthConnectionController;
use App\Http\Controllers\PublicFormController;
use App\Http\Controllers\PublicLandingPageController;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| Application Routes
|--------------------------------------------------------------------------
| Installation is handled by public/install.php (pure PHP, no Laravel).
| The EnsureInstalled middleware is no longer needed — index.php redirects
| to install.php before Laravel boots if storage/installed.lock is missing.
|--------------------------------------------------------------------------
*/
Route::middleware(['web'])->group(function () {
    Route::get('/', [\App\Http\Controllers\MarketingController::class, 'landing'])->name('marketing.landing');
    Route::get('/pricing', [\App\Http\Controllers\MarketingController::class, 'pricing'])->name('marketing.pricing');
    // Documentation is hosted on the external docs site, not bundled
    // with the product. The route remains so any existing links keep
    // working — it 302s to the canonical docs URL.
    Route::redirect('/docs', 'https://docs.themesic.com/docs/leadhub-saas/', 302)
        ->name('marketing.docs');

    // Block-screen shown by EnforceLicense middleware when the
    // install runs out of grace.  Lives at the top level (outside
    // any panel) so a panel-level boot failure can't prevent the
    // operator from seeing the reason and reaching Settings →
    // License to paste a fresh code.
    Route::view('/license-required', 'license-required')
        ->name('license.required');

    // SuperAdmin-managed static pages: About, Terms, Privacy, Contact,
    // plus any extras the operator creates in /super-admin/static-pages.
    Route::get('/pages/{slug}', [\App\Http\Controllers\PublicStaticPageController::class, 'show'])
        ->where('slug', '[a-z0-9-]+')
        ->name('page.show');

    // GDPR Article 28 DPA template — public download so B2B prospects'
    // procurement teams can route it through their legal review.
    Route::get('/legal/dpa.pdf', [\App\Http\Controllers\LegalDocumentsController::class, 'dpa'])
        ->name('legal.dpa');

    // Symlink-free public asset serving for branding logos / favicons and
    // tenant assets.  Filament's FileUpload stores these on the `public`
    // disk (storage/app/public), normally exposed via the public/storage
    // symlink (`php artisan storage:link`).  On shared hosting that
    // symlink is frequently unavailable, so operator-uploaded logos never
    // appear ("logo doesn't show").  All branding asset URLs are rendered
    // through App\Support\PublicMedia::url(), which points at this route,
    // and this route streams the file straight from the public disk — no
    // symlink required, on any host.  A dedicated /media prefix (not
    // /storage) avoids colliding with the framework's storage.local serve
    // route; the branding/ + tenant-assets/ constraint keeps it scoped to
    // intended asset dirs and prevents shadowing /{workspace}/{slug}.
    Route::get('/media/{path}', function (string $path) {
        abort_if(str_contains($path, '..'), 404);
        $disk = \Illuminate\Support\Facades\Storage::disk('public');
        abort_unless($disk->exists($path), 404);

        return $disk->response($path, null, [
            'Cache-Control' => 'public, max-age=31536000, immutable',
        ]);
    })->where('path', '(branding|tenant-assets)/.*')->name('media.serve');

    // UI language switcher — writes to session and bounces back.
    Route::get('/locale/{locale}', [\App\Http\Controllers\LocaleController::class, 'switch'])
        ->whereAlpha('locale')
        ->name('locale.switch');

    // Public self-service registration (guarded by config flag inside controller).
    Route::get('/register', [\App\Http\Controllers\RegistrationController::class, 'show'])->name('register.show');
    Route::post('/register', [\App\Http\Controllers\RegistrationController::class, 'store'])
        ->middleware('throttle:register')
        ->name('register.store');

    /*
    |----------------------------------------------------------------------
    | Impersonation Routes (Super Admin only, POST for CSRF protection)
    |----------------------------------------------------------------------
    */
    Route::post('/super-admin/impersonate/{tenant}', [ImpersonationController::class, 'start'])
        ->middleware('auth')
        ->name('superadmin.impersonate');

    Route::post('/impersonation/stop', [ImpersonationController::class, 'stop'])
        ->middleware('auth')
        ->name('impersonation.stop');

    Route::get('/invitation/{token}', [InvitationController::class, 'show'])
        ->middleware(['signed'])
        ->name('invitation.accept');

    Route::post('/invitation/{token}', [InvitationController::class, 'accept'])
        ->middleware(['signed', 'throttle:invite-accept', \App\Http\Middleware\CheckSeatLimit::class])
        ->name('invitation.accept.post');

    // First-time password setup for tenant admins provisioned from
    // the SA panel.  Distinct from Filament's built-in password-reset
    // so the UX copy reads "Set your password" not "Reset" — a brand
    // new user has nothing to reset.
    //
    // Security model: the token is Laravel's password-broker token
    // (64 chars of random hex, stored hashed server-side, single-use,
    // TTL from auth.passwords.users.expire — default 60 min).  That's
    // the same protection Laravel's "forgot password" flow relies on.
    // We intentionally do NOT wrap this in Laravel's signed-URL
    // middleware because signature verification is fragile behind
    // reverse proxies and CDNs (TrustProxies misconfig, scheme
    // mismatches) and the token alone is sufficient.
    Route::get('/admin/password-setup/{token}', [\App\Http\Controllers\PasswordSetupController::class, 'show'])
        ->name('admin.password-setup');

    Route::post('/admin/password-setup/{token}', [\App\Http\Controllers\PasswordSetupController::class, 'store'])
        ->middleware('throttle:6,1')
        ->name('admin.password-setup.store');

    Route::middleware([\App\Http\Middleware\ResolveTenant::class, \App\Http\Middleware\InjectTenantBranding::class])
        ->group(function () {
            Route::get('/forms/{tenant}/{slug}', [PublicFormController::class, 'show'])->name('forms.show');
            Route::post('/forms/{tenant}/{slug}', [PublicFormController::class, 'submit'])->name('forms.submit');
            Route::get('/forms/_widget_/{formId}', [PublicFormController::class, 'widget'])
                // Embeddable widget — explicitly opt out of the
                // global SecurityHeaders X-Frame-Options=SAMEORIGIN
                // so third-party tenant sites can iframe it.  This
                // is the only public-web surface that legitimately
                // needs to render cross-origin.
                ->withoutMiddleware([\App\Http\Middleware\SecurityHeaders::class])
                ->name('forms.widget');

            // ── Legacy public landing page route (/p/{slug}) ──
            // Kept alive to 301-redirect old deep-links into the new
            // /{workspace}/{slug} pattern once we've resolved which
            // workspace the page belongs to.  Public POST submit is
            // retained so external forms posting back to /p/{slug}/submit
            // still work — no breaking URL changes for existing leads.
            Route::get('/p/{slug}', [PublicLandingPageController::class, 'showLegacy'])
                ->name('landing.show.legacy');
            Route::post('/p/{slug}/submit', [PublicLandingPageController::class, 'submitLegacy'])
                ->middleware('throttle:20,1')
                ->name('landing.submit.legacy');
        });

    Route::get('/export/download', function (\Illuminate\Http\Request $request) {
        if (! $request->hasValidSignature()) {
            abort(401, (string) __('leads_export.abort_invalid_signature'));
        }
        $file = (string) $request->query('file', '');
        if ($file === '' || ! \Illuminate\Support\Facades\Storage::disk('local')->exists($file)) {
            abort(404, (string) __('leads_export.abort_file_not_found'));
        }

        // Tenant-bound signed URL.  ExportLeads writes filenames like
        // `exports/leads_{tenant_id}_{timestamp}.csv`; even with a
        // valid signature, we additionally require the requesting
        // user's tenant_id to match the encoded one so a leaked link
        // can't be replayed by a user from a different tenant within
        // the 24h TTL.  Super admins skip the check (they audit
        // exports across the install).
        $user = $request->user();
        $isSa = $user && (
            method_exists($user, 'isSuperAdmin')
                ? $user->isSuperAdmin()
                : (bool) ($user->is_super_admin ?? false)
        );
        if (! $isSa && $user) {
            $expected = (int) $user->tenant_id;
            if ($expected > 0 && ! str_contains($file, "_{$expected}_")) {
                abort(403, (string) __('leads_export.abort_tenant_mismatch'));
            }
        }

        return \Illuminate\Support\Facades\Storage::disk('local')->download($file, basename($file));
    })->middleware('auth')->name('export.download');

    /*
    |----------------------------------------------------------------------
    | Public landing pages — /{workspace}/{slug}
    |----------------------------------------------------------------------
    | Registered LAST in the web group so every explicit route above wins
    | the first-match race.  The workspace parameter is further
    | constrained to reject reserved path segments (see
    | App\Support\ReservedSlugs), so a workspace with a hostile slug like
    | "admin" cannot intercept /admin/login.
    |
    | This replaces the legacy /p/{slug} pattern.  /p/{slug} still 301
    | redirects here for backwards compatibility.
    */
    Route::middleware([\App\Http\Middleware\ResolveTenant::class, \App\Http\Middleware\InjectTenantBranding::class])
        ->group(function () {
            Route::get('/{workspace}/{slug}', [PublicLandingPageController::class, 'show'])
                ->where('workspace', \App\Support\ReservedSlugs::routeConstraint())
                ->where('slug',      '^[a-z0-9][a-z0-9\-]{0,127}$')
                ->name('landing.show');

            Route::post('/{workspace}/{slug}/submit', [PublicLandingPageController::class, 'submit'])
                ->where('workspace', \App\Support\ReservedSlugs::routeConstraint())
                ->where('slug',      '^[a-z0-9][a-z0-9\-]{0,127}$')
                ->middleware('throttle:20,1')
                ->name('landing.submit');
        });
});

/*
|--------------------------------------------------------------------------
| OAuth Connection Routes
|--------------------------------------------------------------------------
*/
Route::middleware(['web', 'auth'])->prefix('oauth')->name('oauth.')->group(function () {
    Route::get('/{source}/{connectionId}/redirect', [OAuthConnectionController::class, 'redirect'])
        ->name('redirect');
    Route::get('/{source}/callback', [OAuthConnectionController::class, 'callback'])
        ->name('callback');
});

/*
|--------------------------------------------------------------------------
| Integration OAuth Routes (CRM/Marketing OAuth 2.0 flows)
|--------------------------------------------------------------------------
*/
Route::middleware(['web', 'auth'])->prefix('integrations/oauth')->name('integration.oauth.')->group(function () {
    Route::get('/{type}/redirect', [\App\Http\Controllers\IntegrationOAuthController::class, 'redirect'])
        ->name('redirect');
    Route::get('/{type}/callback', [\App\Http\Controllers\IntegrationOAuthController::class, 'callback'])
        ->name('callback');
});

/*
|--------------------------------------------------------------------------
| Email Open / Click Tracking
|--------------------------------------------------------------------------
| Both endpoints are public and unauthenticated (emails are read by end
| users without a session). Tokens are 32-char random strings scoped to a
| single LeadEmail row.
*/
// Per-IP throttle so one noisy network can't burn the global limiter
// for every other recipient.  Named limiter `email-track` is registered
// in App\Providers\AppServiceProvider::boot() and keys by request IP.
Route::middleware('throttle:email-track')->group(function () {
    Route::get('/emails/track/open/{token}', [EmailTrackingController::class, 'open'])
        ->name('email.track.open');
    Route::get('/emails/track/click/{token}', [EmailTrackingController::class, 'click'])
        ->name('email.track.click');
});

Route::post('/webhook/{tenant}/{source}/{token}', [\App\Http\Controllers\InboundWebhookController::class, 'handle'])
    ->middleware('throttle:120,1')
    ->name('webhook.inbound');

// GET twin of the POST webhook receiver — some integrations issue a
// challenge GET before posting.  Same throttle ceiling as POST so a
// leaked token can't be used to enumerate (tenant, source, token)
// combinations at unrestricted speed.
Route::get('/webhook/{tenant}/{source}/{token}', [\App\Http\Controllers\InboundWebhookController::class, 'handle'])
    ->middleware('throttle:120,1')
    ->name('webhook.inbound.get');

/*
|--------------------------------------------------------------------------
| Voice Telephony — Click-to-Call (outbound leg)
|--------------------------------------------------------------------------
*/
Route::middleware(['web', 'auth'])->group(function () {
    Route::post('/voice/call/initiate', [\App\Http\Controllers\VoiceController::class, 'initiate'])
        ->name('voice.initiate')
        ->middleware('throttle:30,1');
});

/*
|--------------------------------------------------------------------------
| Browser Push Subscription Routes
|--------------------------------------------------------------------------
*/
Route::middleware(['web', 'auth'])->prefix('admin/push')->name('push.')->group(function () {
    Route::post('/subscribe',   [\App\Http\Controllers\PushSubscriptionController::class, 'subscribe'])->name('subscribe');
    Route::post('/unsubscribe', [\App\Http\Controllers\PushSubscriptionController::class, 'unsubscribe'])->name('unsubscribe');
});

/*
|--------------------------------------------------------------------------
| Calendar OAuth Routes (Google Calendar — Outlook ships in follow-up)
|--------------------------------------------------------------------------
*/
Route::middleware(['web', 'auth'])->prefix('admin/calendar')->name('calendar.')->group(function () {
    Route::get('/connect/{provider}', [\App\Http\Controllers\CalendarOAuthController::class, 'connect'])
        ->name('connect');
    Route::get('/oauth/callback', [\App\Http\Controllers\CalendarOAuthController::class, 'callback'])
        ->name('callback');
    Route::post('/disconnect/{id}', [\App\Http\Controllers\CalendarOAuthController::class, 'disconnect'])
        ->name('disconnect');
});

/*
|--------------------------------------------------------------------------
| Report Export Routes
|--------------------------------------------------------------------------
*/
Route::middleware(['web', 'auth'])->prefix('admin/reports/export')->name('reports.export.')->group(function () {
    Route::get('/csv', [\App\Http\Controllers\Reports\ExportController::class, 'csv'])->name('csv');
    Route::get('/pdf', [\App\Http\Controllers\Reports\ExportController::class, 'pdf'])->name('pdf');
});

/*
|--------------------------------------------------------------------------
| Update Banner Dismiss
|--------------------------------------------------------------------------
*/
Route::middleware(['web', 'auth'])->post('/update-banner/dismiss', function (\Illuminate\Http\Request $request) {
    $v = $request->input('v', '');
    if ($v) {
        session()->put('update_banner_dismissed_' . $v, true);
    }
    return back();
})->name('update-banner.dismiss');

/*
|--------------------------------------------------------------------------
| Billing Routes (only registered when BILLING_ENABLED=true)
|--------------------------------------------------------------------------
*/
/*
|--------------------------------------------------------------------------
| Public Lead Capture Widget Routes
|--------------------------------------------------------------------------
| No auth, no CSRF — these are called from external websites
*/
Route::get('/widget/{uuid}/loader.js', [\App\Http\Controllers\PublicWidgetController::class, 'loader'])
    ->name('widget.loader');

Route::post('/widget/{uuid}/submit', [\App\Http\Controllers\PublicWidgetController::class, 'submit'])
    ->name('widget.submit')
    ->withoutMiddleware([\Illuminate\Foundation\Http\Middleware\PreventRequestForgery::class]);

// Admin-facing preview — mock host page with the widget embedded, so
// operators can verify colours/copy/position before going live.
Route::get('/widget/{uuid}/preview', [\App\Http\Controllers\PublicWidgetController::class, 'preview'])
    ->name('widget.preview');

/*
|--------------------------------------------------------------------------
| LeadBot — AI website chatbot (public, cross-origin, embedded on the
| tenant's own site).  No auth, no CSRF — same posture as the lead
| capture widget above.  POST is rate-limited by the named 'chatbot'
| limiter (see AppServiceProvider) on top of per-conversation + daily
| caps enforced in the controller.
*/
Route::get('/chatbot/{uuid}/loader.js', [\App\Http\Controllers\PublicChatbotController::class, 'loader'])
    ->name('chatbot.loader');

Route::post('/chatbot/{uuid}/chat', [\App\Http\Controllers\PublicChatbotController::class, 'chat'])
    ->middleware('throttle:chatbot')
    ->name('chatbot.chat')
    ->withoutMiddleware([\Illuminate\Foundation\Http\Middleware\PreventRequestForgery::class]);

// CORS preflight for the cross-origin JSON POST above.
Route::options('/chatbot/{uuid}/chat', [\App\Http\Controllers\PublicChatbotController::class, 'preflight'])
    ->withoutMiddleware([\Illuminate\Foundation\Http\Middleware\PreventRequestForgery::class]);

// Admin-facing preview — mock host page with the bubble embedded.
Route::get('/chatbot/{uuid}/preview', [\App\Http\Controllers\PublicChatbotController::class, 'preview'])
    ->name('chatbot.preview');

/*
| Billing: gateway-agnostic checkout + webhooks. Middleware-gated so
| only logged-in tenant users can reach checkout; webhooks are open
| but verified by each driver (signature + secret).
*/
Route::middleware(['web', 'auth'])->prefix('billing')->name('billing.')->group(function () {
    Route::get('/checkout/{gateway}/{plan}', [\App\Http\Controllers\BillingController::class, 'checkout'])
        ->name('checkout');
    Route::get('/razorpay/launch/{order}', [\App\Http\Controllers\BillingController::class, 'razorpayLaunch'])
        ->name('razorpay.launch');
    Route::get('/portal', [\App\Http\Controllers\BillingController::class, 'customerPortal'])
        ->name('portal');
    Route::get('/receipts/{id}.pdf', [\App\Http\Controllers\BillingController::class, 'receiptPdf'])
        ->whereNumber('id')
        ->middleware('throttle:20,1')
        ->name('receipt.pdf');
});

// Gateway webhooks are stateless POSTs from external services
// (Stripe, PayPal, Razorpay, Paystack).  Hosting them on the `web`
// stack would needlessly start sessions, encrypt cookies, and run
// CSRF for every delivery — none of which the webhook handler reads.
// Worse, an oddly-shaped response from the handler could let a
// session cookie leak back to the gateway.  Register the route
// outside the `web` group with only a generous IP throttle so
// legitimate retry bursts pass while obvious abuse is blocked.
// `BillingController::webhook` and every gateway driver
// (handleWebhook) are session/auth-free — verified end-to-end.
Route::post('/billing/webhook/{gateway}', [\App\Http\Controllers\BillingController::class, 'webhook'])
    ->middleware('throttle:240,1')
    ->name('billing.webhook');

/*
| Super Admin backup download — gated to SA users at the controller level.
*/
Route::middleware(['web', 'auth'])->group(function () {
    Route::get('/admin/super/backups/download', [\App\Http\Controllers\BackupDownloadController::class, 'download'])
        ->name('super.backups.download');
});

/*
|--------------------------------------------------------------------------
| GDPR Article 20 — Tenant Data Export Download
|--------------------------------------------------------------------------
| Token-gated single-use ZIP delivery.  The token is minted by
| ExportTenantDataJob and stored in cache for 24h with the requesting
| user's id; the controller verifies the auth user matches before
| streaming, then deletes the file + the cache entry so the link is
| one-shot.  Authentication-gated so an anonymous attacker who scrapes
| a token URL still has to sign in as the requesting user.
*/
Route::middleware(['web', 'auth'])->group(function () {
    Route::get('/admin/data-export/download/{token}', [\App\Http\Controllers\DataExportController::class, 'download'])
        ->where('token', '[a-f0-9]{64}')
        ->middleware('throttle:30,1')
        ->name('data-export.download');
});

/*
|--------------------------------------------------------------------------
| Web Visitor Tracking (public, cross-origin)
|--------------------------------------------------------------------------
| script(): serves tenant-scoped JS snippet.
| hit()   : records a page view.
| identify(): links visitor_token → Lead.
*/
Route::get('/tracking/{uuid}/track.js', [\App\Http\Controllers\TrackingController::class, 'script'])
    ->middleware('throttle:240,1')
    ->name('tracking.script')
    ->withoutMiddleware([\Illuminate\Foundation\Http\Middleware\PreventRequestForgery::class]);

Route::post('/tracking/{uuid}/hit', [\App\Http\Controllers\TrackingController::class, 'track'])
    ->middleware('throttle:240,1')
    ->name('tracking.track')
    ->withoutMiddleware([\Illuminate\Foundation\Http\Middleware\PreventRequestForgery::class]);

// 10/min instead of 60/min — H11.  Even with the always-{ok:true}
// constant-shape response in place, an attacker testing 60 emails/minute
// per IP could still extract useful timing data over a long window.
// Tracking page-view hits stay at 240/min via /hit since those are
// legitimately bursty.
Route::post('/tracking/{uuid}/identify', [\App\Http\Controllers\TrackingController::class, 'identify'])
    ->middleware('throttle:10,1')
    ->name('tracking.identify')
    ->withoutMiddleware([\Illuminate\Foundation\Http\Middleware\PreventRequestForgery::class]);

/*
|--------------------------------------------------------------------------
| Public Meeting Scheduler (Calendly-style booking pages)
|--------------------------------------------------------------------------
*/
Route::middleware(['web'])->group(function () {
    Route::get('/book/{user}/{slug}', [\App\Http\Controllers\PublicBookingController::class, 'show'])->name('book.show');
    Route::get('/book/{user}/{slug}/availability', [\App\Http\Controllers\PublicBookingController::class, 'availability'])->name('book.availability');
    Route::post('/book/{user}/{slug}', [\App\Http\Controllers\PublicBookingController::class, 'book'])
        ->name('book.create')
        ->middleware('throttle:30,1');
    Route::get('/book/confirmation/{token}', [\App\Http\Controllers\PublicBookingController::class, 'confirmation'])->name('book.confirmation');
    Route::get('/book/reschedule/{token}', [\App\Http\Controllers\PublicBookingController::class, 'reschedule'])->name('book.reschedule');
    Route::post('/book/cancel/{token}', [\App\Http\Controllers\PublicBookingController::class, 'cancel'])
        ->name('book.cancel')
        ->middleware('throttle:10,1');
    Route::get('/book/ics/{token}', [\App\Http\Controllers\PublicBookingController::class, 'ics'])->name('book.ics');
});

/*
|--------------------------------------------------------------------------
| Customer Portal — magic-link auth, lead self-service dashboard
|--------------------------------------------------------------------------
*/
Route::middleware(['web'])->prefix('portal')->group(function () {
    Route::get('/login', [\App\Http\Controllers\Portal\AuthController::class, 'showLogin'])->name('portal.login');
    Route::post('/login', [\App\Http\Controllers\Portal\AuthController::class, 'sendLink'])
        ->middleware('throttle:5,15')
        ->name('portal.login.send');
    Route::get('/sent', [\App\Http\Controllers\Portal\AuthController::class, 'showSent'])->name('portal.login.sent');
    Route::get('/verify/{token}', [\App\Http\Controllers\Portal\AuthController::class, 'verify'])
        ->middleware('throttle:20,1')
        ->name('portal.verify');
    Route::post('/logout', [\App\Http\Controllers\Portal\AuthController::class, 'logout'])->name('portal.logout');

    Route::middleware('portal.auth')->group(function () {
        Route::get('/', [\App\Http\Controllers\Portal\DashboardController::class, 'index'])->name('portal.dashboard');
        Route::post('/upload', [\App\Http\Controllers\Portal\DashboardController::class, 'uploadAttachment'])
            ->middleware('throttle:30,1')
            ->name('portal.upload');
    });
});

/*
|--------------------------------------------------------------------------
| Public Quote + Invoice Routes
|--------------------------------------------------------------------------
| Customer-facing (unauthenticated) endpoints gated by a 64-char opaque
| token on the Quote/Invoice row. Tenant branding is resolved via
| ResolveTenant/InjectTenantBranding middleware so the public view inherits
| the tenant's colours + logo.
*/
Route::middleware(['web', \App\Http\Middleware\ResolveTenant::class, \App\Http\Middleware\InjectTenantBranding::class])->group(function () {
    Route::get('/quote/{token}', [\App\Http\Controllers\PublicQuoteController::class, 'show'])
        ->name('quote.show')
        ->middleware('throttle:60,1');
    Route::post('/quote/{token}/accept', [\App\Http\Controllers\PublicQuoteController::class, 'accept'])
        ->name('quote.accept')
        ->middleware('throttle:20,1');
    Route::post('/quote/{token}/decline', [\App\Http\Controllers\PublicQuoteController::class, 'decline'])
        ->name('quote.decline')
        ->middleware('throttle:20,1');
    Route::get('/quote/{token}/pdf', [\App\Http\Controllers\PublicQuoteController::class, 'pdf'])
        ->name('quote.pdf')
        ->middleware('throttle:30,1');

    Route::get('/invoice/{token}', [\App\Http\Controllers\PublicInvoiceController::class, 'show'])
        ->name('invoice.show')
        ->middleware('throttle:60,1');
    Route::post('/invoice/{token}/pay', [\App\Http\Controllers\PublicInvoiceController::class, 'pay'])
        ->name('invoice.pay')
        ->middleware('throttle:20,1');
    Route::get('/invoice/{token}/success', [\App\Http\Controllers\PublicInvoiceController::class, 'paymentSuccess'])
        ->name('invoice.success');
    Route::get('/invoice/{token}/pdf', [\App\Http\Controllers\PublicInvoiceController::class, 'pdf'])
        ->name('invoice.pdf')
        ->middleware('throttle:30,1');
});

/*
|--------------------------------------------------------------------------
| Mobile PWA — offline-capable capture + business-card OCR
|--------------------------------------------------------------------------
*/
Route::middleware(['web', 'auth'])->prefix('mobile')->group(function () {
    Route::get('/capture', [\App\Http\Controllers\MobileController::class, 'capture'])->name('mobile.capture');
    Route::post('/capture', [\App\Http\Controllers\MobileController::class, 'storeCapture'])
        ->middleware('throttle:120,1')
        ->name('mobile.capture.store');

    Route::get('/scan', [\App\Http\Controllers\MobileController::class, 'scan'])->name('mobile.scan');
    Route::post('/scan/ocr', [\App\Http\Controllers\MobileController::class, 'ocrBusinessCard'])
        ->middleware('throttle:30,1')
        ->name('mobile.scan.ocr');
});

// Offline shell — public so the service worker can fetch it without auth.
Route::middleware('web')->get('/mobile/offline', [\App\Http\Controllers\MobileController::class, 'offline'])
    ->name('mobile.offline');
