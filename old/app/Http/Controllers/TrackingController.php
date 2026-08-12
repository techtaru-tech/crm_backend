<?php

namespace App\Http\Controllers;

use App\Models\Lead;
use App\Models\PageView;
use App\Models\TrackingSnippet;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Support\Str;

class TrackingController extends Controller
{
    /**
     * Serve tenant-scoped tracker JS.
     */
    public function script(string $uuid): Response
    {
        $snippet = TrackingSnippet::withoutGlobalScope('tenant')
            ->where('uuid', $uuid)
            ->where('is_active', true)
            ->first();

        if (! $snippet) {
            return response("/* tracking snippet not found */", 404, [
                'Content-Type'                => 'application/javascript',
                'Access-Control-Allow-Origin' => '*',
            ]);
        }

        $base     = rtrim(url('/'), '/');
        $trackUrl = $base . '/tracking/' . $uuid . '/hit';
        $idUrl    = $base . '/tracking/' . $uuid . '/identify';

        $js = <<<JS
(function(){
  var TRACKING_URL = "{$trackUrl}";
  var IDENTIFY_URL = "{$idUrl}";
  var token = null;
  try { token = localStorage.getItem('lh_vid'); } catch(e){}
  if(!token){
    token = Math.random().toString(36).slice(2) + Date.now().toString(36);
    try{ localStorage.setItem('lh_vid', token); }catch(e){}
  }
  var params = new URLSearchParams(window.location.search);
  var data = {
    url: window.location.href,
    path: window.location.pathname,
    title: document.title,
    referrer: document.referrer || null,
    visitor_token: token,
    utm_source: params.get('utm_source'),
    utm_medium: params.get('utm_medium'),
    utm_campaign: params.get('utm_campaign')
  };
  try {
    fetch(TRACKING_URL, {
      method: 'POST',
      headers: {'Content-Type':'application/json'},
      body: JSON.stringify(data),
      keepalive: true,
      credentials: 'omit'
    }).catch(function(){});
  } catch(e){}

  window.LeadHubTracker = {
    identify: function(email, phone){
      try {
        fetch(IDENTIFY_URL, {
          method:'POST',
          headers:{'Content-Type':'application/json'},
          body: JSON.stringify({visitor_token: token, email: email || null, phone: phone || null}),
          credentials: 'omit'
        }).catch(function(){});
      } catch(e){}
    },
    visitorToken: token
  };
})();
JS;

        return response($js, 200, [
            'Content-Type'                 => 'application/javascript',
            'Access-Control-Allow-Origin'  => '*',
            'Cache-Control'                => 'public, max-age=300',
        ]);
    }

    /**
     * Record a page view hit.
     */
    public function track(Request $request, string $uuid): JsonResponse
    {
        $snippet = TrackingSnippet::withoutGlobalScope('tenant')
            ->where('uuid', $uuid)
            ->where('is_active', true)
            ->first();

        if (! $snippet) {
            return $this->corsJson(['error' => 'not_found'], 404);
        }

        $data = $request->validate([
            'url'           => 'required|string|max:2048',
            'path'          => 'nullable|string|max:500',
            'title'         => 'nullable|string|max:255',
            'referrer'      => 'nullable|string|max:255',
            'visitor_token' => 'nullable|string|max:40',
            'utm_source'    => 'nullable|string|max:255',
            'utm_medium'    => 'nullable|string|max:255',
            'utm_campaign'  => 'nullable|string|max:255',
            'duration'      => 'nullable|integer|min:0',
        ]);

        // Domain allow-list check
        if (! empty($snippet->allowed_domains)) {
            $host = parse_url($data['url'], PHP_URL_HOST) ?? '';
            $ok = false;
            foreach ($snippet->allowed_domains as $allowed) {
                $allowed = strtolower(trim((string) $allowed));
                if ($allowed === '') continue;
                if ($host === $allowed || str_ends_with($host, '.' . $allowed)) {
                    $ok = true;
                    break;
                }
            }
            if (! $ok) {
                return $this->corsJson(['error' => 'domain_not_allowed'], 403);
            }
        }

        $token = $data['visitor_token'] ?? null;
        if (! $token) {
            $token = (string) Str::uuid();
        }

        // Link to existing lead via prior identify
        $leadId = PageView::withoutGlobalScope('tenant')
            ->where('tenant_id', $snippet->tenant_id)
            ->where('visitor_token', $token)
            ->whereNotNull('lead_id')
            ->latest('id')
            ->value('lead_id');

        PageView::withoutGlobalScope('tenant')->create([
            'tenant_id'        => $snippet->tenant_id,
            'visitor_token'    => $token,
            'lead_id'          => $leadId,
            'url'              => $data['url'],
            'path'             => $data['path'] ?? parse_url($data['url'], PHP_URL_PATH),
            'title'            => $data['title'] ?? null,
            'referrer'         => $data['referrer'] ?? null,
            'utm_source'       => $data['utm_source'] ?? null,
            'utm_medium'       => $data['utm_medium'] ?? null,
            'utm_campaign'     => $data['utm_campaign'] ?? null,
            'ip_address'       => $request->ip(),
            'user_agent'       => substr((string) $request->userAgent(), 0, 500),
            'duration_seconds' => $data['duration'] ?? null,
            'viewed_at'        => now(),
        ]);

        $snippet->forceFill([
            'views_count'    => ($snippet->views_count ?? 0) + 1,
            'last_viewed_at' => now(),
        ])->saveQuietly();

        return $this->corsJson(['visitor_token' => $token]);
    }

    /**
     * Identify a visitor — attach to a known lead.
     *
     * H11: this endpoint must NOT leak the existence of a lead.  It's
     * called from a public tracker.js snippet, so any third-party site
     * embedding the snippet could otherwise enumerate "is email X a
     * lead in tenant Y?" by watching the response code.  Two hardenings:
     *
     *  1. Constant response shape regardless of match — `{ok: true}`
     *     for every code path that's "well-formed but no link possible".
     *     Validation/snippet-not-found still 4xx so legitimate clients
     *     can debug their integration.
     *
     *  2. Visitor-token ownership check — the supplied visitor_token
     *     must already exist as a PageView in this tenant before we
     *     allow linking it to a lead.  Without this an attacker could
     *     POST their own random visitor_token + a target's email, and
     *     after a single identify call the attacker's future page-view
     *     hits would be tied to the target's lead profile.
     */
    public function identify(Request $request, string $uuid): JsonResponse
    {
        $snippet = TrackingSnippet::withoutGlobalScope('tenant')
            ->where('uuid', $uuid)
            ->where('is_active', true)
            ->first();

        if (! $snippet) {
            return $this->corsJson(['error' => 'not_found'], 404);
        }

        $data = $request->validate([
            'visitor_token' => 'required|string|max:40',
            'email'         => 'nullable|email|max:255',
            'phone'         => 'nullable|string|max:30',
        ]);

        if (empty($data['email']) && empty($data['phone'])) {
            return $this->corsJson(['error' => 'email_or_phone_required'], 422);
        }

        // Visitor-token ownership: the token must already have at least
        // one PageView row in this tenant.  Otherwise an attacker can
        // mint random visitor_tokens for free and tie them to other
        // people's leads.
        $owns = PageView::withoutGlobalScope('tenant')
            ->where('tenant_id', $snippet->tenant_id)
            ->where('visitor_token', $data['visitor_token'])
            ->exists();
        if (! $owns) {
            return $this->corsJson(['ok' => true]);
        }

        $query = Lead::withoutGlobalScope('tenant')->where('tenant_id', $snippet->tenant_id);
        if (! empty($data['email'])) {
            $query->where('email', $data['email']);
        } elseif (! empty($data['phone'])) {
            $query->where('phone_normalized', preg_replace('/\D/', '', $data['phone']));
        }
        $lead = $query->first();

        if ($lead) {
            PageView::withoutGlobalScope('tenant')
                ->where('tenant_id', $snippet->tenant_id)
                ->where('visitor_token', $data['visitor_token'])
                ->whereNull('lead_id')
                ->update(['lead_id' => $lead->id]);
        }

        // Always-{ok:true}: don't reveal whether the lead exists.
        return $this->corsJson(['ok' => true]);
    }

    protected function corsJson(array $data, int $status = 200): JsonResponse
    {
        return response()->json($data, $status, [
            'Access-Control-Allow-Origin'  => '*',
            'Access-Control-Allow-Headers' => 'Content-Type',
        ]);
    }
}
