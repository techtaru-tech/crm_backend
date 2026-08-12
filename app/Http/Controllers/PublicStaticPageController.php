<?php

namespace App\Http\Controllers;

use App\Models\StaticPage;
use Illuminate\Http\Request;
use Illuminate\View\View;

/**
 * Renders a SuperAdmin-managed marketing page at /pages/{slug}.
 * Only returns published pages; drafts 404 for the public.
 *
 * Intentionally does NOT perform tenant resolution — these are
 * SaaS-level pages, served on the primary app host only.  Tenant
 * subdomains redirect to /admin at the controller's top.
 */
class PublicStaticPageController extends Controller
{
    public function show(Request $request, string $slug): View
    {
        $page = StaticPage::published()->where('slug', $slug)->firstOrFail();

        return view('public.static-page.show', [
            'page'    => $page,
            'appName' => config('leadhub.branding.app_name', 'LeadHub'),
            // The marketing layout uses $canSignUp in the nav; keep it
            // populated so the "Start trial" button renders correctly.
            'canSignUp' => (bool) config('leadhub.registration.enabled', true),
        ]);
    }
}
