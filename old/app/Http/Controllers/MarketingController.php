<?php

namespace App\Http\Controllers;

use App\Services\PlanService;
use App\Settings\LandingContent;
use Illuminate\Http\Request;

/**
 * Public-facing marketing pages (landing + pricing).
 *
 * Only renders on the primary app domain. On tenant subdomains or
 * custom domains the routes redirect to /admin instead so visitors
 * see the tenant's workspace, not the SaaS marketing site.
 */
class MarketingController extends Controller
{
    public function landing(Request $request, PlanService $plans)
    {
        if ($this->isTenantHost($request)) {
            return redirect('/admin');
        }

        // Authenticated visitors used to be bounced straight into
        // their panel (/admin or /super-admin) on a landing-page
        // visit.  We removed that redirect: an operator or tenant
        // staffer who clicks a footer link or types the root URL
        // has a legitimate reason to look at the public marketing
        // page (brand review, sharing a link, checking copy).  The
        // nav pill shows a "Sign in" CTA they can use to jump back
        // into their panel when they're ready.

        // Five landing variants, chosen in SA → Script Settings:
        //   light     — polished white marketing (default)
        //   warm      — cream/serif editorial (Relate-genre), light
        //   modern    — dark, gradient-purple SaaS look
        //   editorial — dark monochrome, bento grid, post-Linear
        //   classic   — original content-heavy template
        $variant = (string) config('leadhub.landing_variant', 'light');
        $view = match ($variant) {
            'warm'      => 'marketing.landing-warm',
            'modern'    => 'marketing.landing-modern',
            'editorial' => 'marketing.landing-editorial',
            'classic'   => 'marketing.landing',
            default     => 'marketing.landing-light',
        };

        // Resilience: a fresh install with no plans seeded or no
        // landing-content settings row would otherwise 500 the public
        // homepage (the very first URL a buyer's customer hits) with
        // a bare Laravel stack trace.  Fall back to empty data and
        // let the blade render an "under construction" state.
        try {
            $publicPlans = $plans->getAllPlans(publicOnly: true);
        } catch (\Throwable $e) {
            \Illuminate\Support\Facades\Log::warning('MarketingController landing: plan resolution failed', ['error' => $e->getMessage()]);
            $publicPlans = collect();
        }

        try {
            $content = app(LandingContent::class);
        } catch (\Throwable $e) {
            \Illuminate\Support\Facades\Log::warning('MarketingController landing: LandingContent unavailable', ['error' => $e->getMessage()]);
            $content = null;
        }

        return view($view, [
            'plans'      => $publicPlans,
            'appName'    => config('leadhub.branding.app_name', 'LeadHub'),
            'canSignUp'  => (bool) config('leadhub.registration.enabled', true),
            'trialDays'  => (int) config('plans.trial_days', 14),
            'content'    => $content,
        ]);
    }

    public function pricing(Request $request, PlanService $plans)
    {
        if ($this->isTenantHost($request)) {
            return redirect('/admin');
        }

        try {
            $publicPlans = $plans->getAllPlans(publicOnly: true);
        } catch (\Throwable $e) {
            \Illuminate\Support\Facades\Log::warning('MarketingController pricing: plan resolution failed', ['error' => $e->getMessage()]);
            $publicPlans = collect();
        }

        return view('marketing.pricing', [
            'plans'     => $publicPlans,
            'appName'   => config('leadhub.branding.app_name', 'LeadHub'),
            'canSignUp' => (bool) config('leadhub.registration.enabled', true),
            'trialDays' => (int) config('plans.trial_days', 14),
        ]);
    }

    /**
     * True when the current host is a tenant subdomain or a mapped
     * tenant custom domain — not the primary marketing domain.
     */
    protected function isTenantHost(Request $request): bool
    {
        $host      = $request->getHost();
        $appDomain = config('leadhub.domain', parse_url(config('app.url'), PHP_URL_HOST));

        if (! $appDomain) {
            return false;
        }

        if ($host === $appDomain) {
            return false;
        }

        return true;
    }
}
