<?php

use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| LeadHub REST API v1 Routes
|--------------------------------------------------------------------------
| Authentication: Bearer API key — Authorization: Bearer lh_...
| Scope enforcement: api.key:<scope> per-route middleware (no group wrapper)
|--------------------------------------------------------------------------
*/

// Canonical docs at /api/docs (no version prefix) + /api/v1/docs alias
Route::get('/docs', [\App\Http\Controllers\Api\ApiDocsController::class, 'index'])->name('api.docs');

// Friendly aliases — buyers reaching for the API docs typically try
// /api/documentation (the L5-Swagger / Scribe convention) or
// /api/swagger. Both point at the same Swagger UI page so neither
// path 404s and the buyer never has to guess the canonical URL.
Route::get('/documentation', [\App\Http\Controllers\Api\ApiDocsController::class, 'index'])->name('api.docs.documentation');
Route::get('/swagger',       [\App\Http\Controllers\Api\ApiDocsController::class, 'index'])->name('api.docs.swagger');

/*
|--------------------------------------------------------------------------
| Unversioned /api/leads aliases — buyer-friendly Pabbly / Zapier path
|--------------------------------------------------------------------------
| The canonical leads CRUD lives under /api/v1/leads (registered inside
| the v1 prefix block below).  The fallback handler at the bottom of
| this file 308-redirects /api/leads → /api/v1/leads, BUT several no-code
| platforms (Pabbly Connect, older Zapier connectors, n8n's HTTP node in
| some modes) don't follow 308 redirects on POST and treat them as
| METHOD_NOT_ALLOWED failures.  Customer integrating Facebook Leads ->
| Pabbly -> LeadHub hit exactly that.
|
| So leads are also exposed DIRECTLY at /api/leads (no version prefix).
| Same controller, same middleware, same scopes — only the URL differs.
| The route names are deliberately unprefixed (api.leads.*) so they don't
| collide with the api.v1.leads.* names inside the v1 group.
|
| If we ever cut a v2 of the leads API with breaking changes, this block
| can be re-pointed at the v2 controller and integrations carry forward
| without buyer-side URL changes.
*/
Route::get('leads', [\App\Http\Controllers\Api\V1\LeadController::class, 'index'])
    ->middleware('api.key:read:leads')
    ->name('api.leads.index');

Route::post('leads', [\App\Http\Controllers\Api\V1\LeadController::class, 'store'])
    ->middleware('api.key:write:leads')
    ->name('api.leads.store');

Route::get('leads/{lead}', [\App\Http\Controllers\Api\V1\LeadController::class, 'show'])
    ->middleware('api.key:read:leads')
    ->name('api.leads.show');

Route::put('leads/{lead}', [\App\Http\Controllers\Api\V1\LeadController::class, 'update'])
    ->middleware('api.key:write:leads')
    ->name('api.leads.update');

Route::patch('leads/{lead}', [\App\Http\Controllers\Api\V1\LeadController::class, 'update'])
    ->middleware('api.key:write:leads')
    ->name('api.leads.update.patch');

Route::delete('leads/{lead}', [\App\Http\Controllers\Api\V1\LeadController::class, 'destroy'])
    ->middleware('api.key:delete:leads')
    ->name('api.leads.destroy');

Route::post('leads/{lead}/tags', [\App\Http\Controllers\Api\V1\LeadController::class, 'attachTag'])
    ->middleware('api.key:write:leads')
    ->name('api.leads.tags.attach');

Route::delete('leads/{lead}/tags/{tag}', [\App\Http\Controllers\Api\V1\LeadController::class, 'detachTag'])
    ->middleware('api.key:write:leads')
    ->name('api.leads.tags.detach');

Route::prefix('v1')->name('api.v1.')->group(function () {

    // Interactive API docs alias
    Route::get('/docs', [\App\Http\Controllers\Api\ApiDocsController::class, 'index'])->name('docs');

    // Health check — no auth required. Pings DB / cache / storage and
    // returns 503 with a per-subsystem breakdown when any one is down.
    Route::get('/health', \App\Http\Controllers\Api\HealthController::class)->name('health');

    // ── Leads ──────────────────────────────────────────────────────────────
    Route::get('leads', [\App\Http\Controllers\Api\V1\LeadController::class, 'index'])
        ->middleware('api.key:read:leads')
        ->name('leads.index');

    Route::post('leads', [\App\Http\Controllers\Api\V1\LeadController::class, 'store'])
        ->middleware('api.key:write:leads')
        ->name('leads.store');

    Route::get('leads/{lead}', [\App\Http\Controllers\Api\V1\LeadController::class, 'show'])
        ->middleware('api.key:read:leads')
        ->name('leads.show');

    Route::put('leads/{lead}', [\App\Http\Controllers\Api\V1\LeadController::class, 'update'])
        ->middleware('api.key:write:leads')
        ->name('leads.update');

    Route::patch('leads/{lead}', [\App\Http\Controllers\Api\V1\LeadController::class, 'update'])
        ->middleware('api.key:write:leads')
        ->name('leads.update.patch');

    Route::delete('leads/{lead}', [\App\Http\Controllers\Api\V1\LeadController::class, 'destroy'])
        ->middleware('api.key:delete:leads')
        ->name('leads.destroy');

    Route::post('leads/{lead}/tags', [\App\Http\Controllers\Api\V1\LeadController::class, 'attachTag'])
        ->middleware('api.key:write:leads')
        ->name('leads.tags.attach');

    Route::delete('leads/{lead}/tags/{tag}', [\App\Http\Controllers\Api\V1\LeadController::class, 'detachTag'])
        ->middleware('api.key:write:leads')
        ->name('leads.tags.detach');

    // ── Pipelines & Stages ────────────────────────────────────────────────
    Route::get('pipelines', [\App\Http\Controllers\Api\V1\PipelineController::class, 'index'])
        ->middleware('api.key:read:pipelines')
        ->name('pipelines.index');

    Route::post('pipelines', [\App\Http\Controllers\Api\V1\PipelineController::class, 'store'])
        ->middleware('api.key:write:pipelines')
        ->name('pipelines.store');

    Route::get('pipelines/{pipeline}', [\App\Http\Controllers\Api\V1\PipelineController::class, 'show'])
        ->middleware('api.key:read:pipelines')
        ->name('pipelines.show');

    Route::put('pipelines/{pipeline}', [\App\Http\Controllers\Api\V1\PipelineController::class, 'update'])
        ->middleware('api.key:write:pipelines')
        ->name('pipelines.update');

    Route::patch('pipelines/{pipeline}', [\App\Http\Controllers\Api\V1\PipelineController::class, 'update'])
        ->middleware('api.key:write:pipelines')
        ->name('pipelines.update.patch');

    Route::delete('pipelines/{pipeline}', [\App\Http\Controllers\Api\V1\PipelineController::class, 'destroy'])
        ->middleware('api.key:write:pipelines')
        ->name('pipelines.destroy');

    Route::get('pipelines/{pipeline}/stages', [\App\Http\Controllers\Api\V1\StageController::class, 'index'])
        ->middleware('api.key:read:pipelines')
        ->name('pipelines.stages.index');

    Route::post('pipelines/{pipeline}/stages', [\App\Http\Controllers\Api\V1\StageController::class, 'store'])
        ->middleware('api.key:write:pipelines')
        ->name('pipelines.stages.store');

    Route::get('pipelines/{pipeline}/stages/{stage}', [\App\Http\Controllers\Api\V1\StageController::class, 'show'])
        ->middleware('api.key:read:pipelines')
        ->name('pipelines.stages.show');

    Route::put('pipelines/{pipeline}/stages/{stage}', [\App\Http\Controllers\Api\V1\StageController::class, 'update'])
        ->middleware('api.key:write:pipelines')
        ->name('pipelines.stages.update');

    Route::patch('pipelines/{pipeline}/stages/{stage}', [\App\Http\Controllers\Api\V1\StageController::class, 'update'])
        ->middleware('api.key:write:pipelines')
        ->name('pipelines.stages.update.patch');

    Route::delete('pipelines/{pipeline}/stages/{stage}', [\App\Http\Controllers\Api\V1\StageController::class, 'destroy'])
        ->middleware('api.key:write:pipelines')
        ->name('pipelines.stages.destroy');

    // ── Tags ──────────────────────────────────────────────────────────────
    Route::get('tags', [\App\Http\Controllers\Api\V1\TagController::class, 'index'])
        ->middleware('api.key:read:tags')
        ->name('tags.index');

    Route::post('tags', [\App\Http\Controllers\Api\V1\TagController::class, 'store'])
        ->middleware('api.key:write:tags')
        ->name('tags.store');

    Route::get('tags/{tag}', [\App\Http\Controllers\Api\V1\TagController::class, 'show'])
        ->middleware('api.key:read:tags')
        ->name('tags.show');

    Route::put('tags/{tag}', [\App\Http\Controllers\Api\V1\TagController::class, 'update'])
        ->middleware('api.key:write:tags')
        ->name('tags.update');

    Route::patch('tags/{tag}', [\App\Http\Controllers\Api\V1\TagController::class, 'update'])
        ->middleware('api.key:write:tags')
        ->name('tags.update.patch');

    Route::delete('tags/{tag}', [\App\Http\Controllers\Api\V1\TagController::class, 'destroy'])
        ->middleware('api.key:write:tags')
        ->name('tags.destroy');

    // ── Users ────────────────────────────────────────────────────────────
    Route::get('users', [\App\Http\Controllers\Api\V1\UserController::class, 'index'])
        ->middleware('api.key:read:users')
        ->name('users.index');

    Route::post('users', [\App\Http\Controllers\Api\V1\UserController::class, 'store'])
        ->middleware('api.key:write:users')
        ->name('users.store');

    Route::get('users/{user}', [\App\Http\Controllers\Api\V1\UserController::class, 'show'])
        ->middleware('api.key:read:users')
        ->name('users.show');

    Route::put('users/{user}', [\App\Http\Controllers\Api\V1\UserController::class, 'update'])
        ->middleware('api.key:write:users')
        ->name('users.update');

    Route::patch('users/{user}', [\App\Http\Controllers\Api\V1\UserController::class, 'update'])
        ->middleware('api.key:write:users')
        ->name('users.patch');

    Route::delete('users/{user}', [\App\Http\Controllers\Api\V1\UserController::class, 'destroy'])
        ->middleware('api.key:delete:users')
        ->name('users.destroy');

    // ── Forms ─────────────────────────────────────────────────────────────
    Route::get('forms', [\App\Http\Controllers\Api\V1\FormController::class, 'index'])
        ->middleware('api.key:read:forms')
        ->name('forms.index');

    Route::post('forms', [\App\Http\Controllers\Api\V1\FormController::class, 'store'])
        ->middleware('api.key:write:forms')
        ->name('forms.store');

    Route::get('forms/{form}', [\App\Http\Controllers\Api\V1\FormController::class, 'show'])
        ->middleware('api.key:read:forms')
        ->name('forms.show');

    Route::put('forms/{form}', [\App\Http\Controllers\Api\V1\FormController::class, 'update'])
        ->middleware('api.key:write:forms')
        ->name('forms.update');

    Route::patch('forms/{form}', [\App\Http\Controllers\Api\V1\FormController::class, 'update'])
        ->middleware('api.key:write:forms')
        ->name('forms.patch');

    Route::delete('forms/{form}', [\App\Http\Controllers\Api\V1\FormController::class, 'destroy'])
        ->middleware('api.key:delete:forms')
        ->name('forms.destroy');

    Route::post('forms/{form}/submissions', [\App\Http\Controllers\Api\V1\FormController::class, 'submit'])
        ->middleware('api.key:write:forms')
        ->name('forms.submit');

    // ── Automations ───────────────────────────────────────────────────────
    Route::get('automations', [\App\Http\Controllers\Api\V1\AutomationController::class, 'index'])
        ->middleware('api.key:read:automations')
        ->name('automations.index');

    Route::post('automations', [\App\Http\Controllers\Api\V1\AutomationController::class, 'store'])
        ->middleware('api.key:write:automations')
        ->name('automations.store');

    Route::get('automations/{automation}', [\App\Http\Controllers\Api\V1\AutomationController::class, 'show'])
        ->middleware('api.key:read:automations')
        ->name('automations.show');

    Route::put('automations/{automation}', [\App\Http\Controllers\Api\V1\AutomationController::class, 'update'])
        ->middleware('api.key:write:automations')
        ->name('automations.update');

    Route::patch('automations/{automation}', [\App\Http\Controllers\Api\V1\AutomationController::class, 'update'])
        ->middleware('api.key:write:automations')
        ->name('automations.patch');

    Route::delete('automations/{automation}', [\App\Http\Controllers\Api\V1\AutomationController::class, 'destroy'])
        ->middleware('api.key:delete:automations')
        ->name('automations.destroy');

    // ── Integrations (full CRUD) ──────────────────────────────────────────
    Route::get('integrations/types', [\App\Http\Controllers\Api\V1\IntegrationController::class, 'types'])
        ->middleware('api.key:read:integrations')
        ->name('integrations.types');

    Route::get('integrations', [\App\Http\Controllers\Api\V1\IntegrationController::class, 'index'])
        ->middleware('api.key:read:integrations')
        ->name('integrations.index');

    Route::post('integrations', [\App\Http\Controllers\Api\V1\IntegrationController::class, 'store'])
        ->middleware('api.key:write:integrations')
        ->name('integrations.store');

    Route::get('integrations/{integration}', [\App\Http\Controllers\Api\V1\IntegrationController::class, 'show'])
        ->middleware('api.key:read:integrations')
        ->name('integrations.show');

    Route::put('integrations/{integration}', [\App\Http\Controllers\Api\V1\IntegrationController::class, 'update'])
        ->middleware('api.key:write:integrations')
        ->name('integrations.update');

    Route::patch('integrations/{integration}', [\App\Http\Controllers\Api\V1\IntegrationController::class, 'update'])
        ->middleware('api.key:write:integrations')
        ->name('integrations.update.patch');

    Route::delete('integrations/{integration}', [\App\Http\Controllers\Api\V1\IntegrationController::class, 'destroy'])
        ->middleware('api.key:write:integrations')
        ->name('integrations.destroy');

});

// Inbound lead intake — supports /api/inbound/leads and /api/v1/inbound.
// Anonymous on purpose (vendors POST without API keys) but rate-limited
// per-IP at the same ceiling as the routes/web.php inbound webhook to
// prevent flooding any tenant's pipeline.
Route::post('/inbound/leads', [\App\Http\Controllers\Api\InboundLeadController::class, 'store'])
    ->middleware('throttle:120,1')
    ->name('inbound.leads');

Route::prefix('v1')->group(function () {
    Route::post('/inbound', [\App\Http\Controllers\Api\InboundLeadController::class, 'store'])
        ->middleware('throttle:120,1')
        ->name('v1.inbound.leads');
});

// Inbound webhook receiver (token-based, no auth) — same rate-limit as
// the inbound lead intake above so a leaked token cannot be used to
// flood the pipeline.
Route::post('/webhook/{tenant}/{source}/{token}', [\App\Http\Controllers\WebhookController::class, 'receive'])
    ->middleware('throttle:120,1')
    ->name('webhook.receive');

// Public form submission endpoint (no API key required).
// Bound to PublicFormController::submit — the same controller handles the
// /forms/{tenant}/{slug} POST on the web stack.  Validation, lead creation,
// reCAPTCHA verification, automation triggers, and FormSubmitted event
// dispatch all live there.  Throttled to 60 req/min/IP to keep the
// open endpoint from being abused as a free lead-spamming vector.
Route::post('/forms/{tenant}/{slug}/submit', [\App\Http\Controllers\PublicFormController::class, 'submit'])
    ->middleware('throttle:60,1')
    ->name('form.submit.public');

// Inbound messaging webhooks (WhatsApp/SMS/Telegram/Viber) — token-based
// auth.  Throttle matches the other inbound webhook receivers so a leaked
// token can't be used to flood the conversation/inbox pipeline.
Route::post('/messaging/{channel}/webhook/{tenant}/{token}', [\App\Http\Controllers\Api\MessagingWebhookController::class, 'handle'])
    ->where('channel', 'whatsapp|sms|telegram|viber')
    ->middleware('throttle:120,1')
    ->name('messaging.webhook');

// Twilio Voice webhooks — tenant + token scoped, no CSRF (Twilio is the caller).
Route::post('/voice/{tenant}/twiml/{token}', [\App\Http\Controllers\Api\VoiceWebhookController::class, 'twiml'])
    ->name('voice.twiml');
Route::post('/voice/{tenant}/status/{token}', [\App\Http\Controllers\Api\VoiceWebhookController::class, 'statusCallback'])
    ->name('voice.status');
Route::post('/voice/{tenant}/recording/{token}', [\App\Http\Controllers\Api\VoiceWebhookController::class, 'recordingCallback'])
    ->name('voice.recording');

/*
|--------------------------------------------------------------------------
| Backward-compat redirect: /api/<resource> -> /api/v1/<resource>
|--------------------------------------------------------------------------
| Pre-compiled API documentation references unversioned paths like
| /api/leads. Recompiling the docs is expensive, so any path under /api/
| that doesn't match an explicit route gets 308-redirected into the v1
| namespace. 308 (vs 301) preserves HTTP method + body, so POST /api/leads
| stays a POST after the redirect rather than being downgraded to GET.
|
| SECURITY NOTE
| -------------
| This is a pure URL rewrite. After the client follows the redirect, the
| request is handled by the same `api.key` middleware as a direct hit on
| /api/v1/<resource>. That middleware (ApiKeyAuthentication line 71)
| resolves tenant_id FROM THE API KEY RECORD, not from any URL slug,
| header, or query parameter:
|
|     $request->attributes->set('tenant_id', $apiKey->tenant_id);
|
| Therefore a client holding tenant B's API key cannot read tenant A's
| leads even when they know A's slug - BelongsToTenant's global scope on
| every Lead query filters by the key's owning tenant_id. The redirect
| introduces no new attack surface; it just makes legacy URLs work.
|
| Excluded from the redirect (these are legitimately at the root of /api/
| and have their own explicit routes registered above):
|
|     /api/v1/*       - already versioned, the canonical API surface
|     /api/docs       - interactive API documentation
|     /api/inbound/*  - Zapier/n8n-style inbound lead intake
|     /api/webhook/*  - generic external webhook receiver
|     /api/forms/*    - public form submit endpoint
|     /api/messaging/*- WhatsApp/SMS/Telegram/Viber webhooks
|     /api/voice/*    - Twilio voice webhooks
|
| Anything else under /api/ that falls through to this handler gets
| bounced to its v1 equivalent. If that v1 equivalent doesn't exist,
| the v1 router 404s with the same shape JSON error the client would
| have got otherwise.
*/
Route::fallback(function (\Illuminate\Http\Request $request) {
    $path = $request->path(); // e.g. "api/leads" or "api/leads/123"

    // Defensive - fallback in routes/api.php only fires under /api/*,
    // but guard explicitly in case Laravel's route prefix handling
    // ever changes underneath us.
    if (! str_starts_with($path, 'api/')) {
        return response()->json([
            'status'  => 'error',
            'message' => 'Endpoint not found.',
            'code'    => 'NOT_FOUND',
        ], 404);
    }

    $afterApi     = substr($path, 4);                  // "leads", "leads/123"
    $firstSegment = explode('/', $afterApi)[0] ?? '';  // "leads"

    // Don't redirect paths whose first segment is itself a root-level
    // /api/ surface. Falling through to here means the path is
    // genuinely invalid (e.g. /api/docs/foo, /api/voice/bar), not a
    // missing version prefix - 404 with our standard error shape.
    $rootSegments = ['v1', 'docs', 'documentation', 'swagger', 'leads', 'inbound', 'webhook', 'forms', 'messaging', 'voice'];
    if ($afterApi === '' || in_array($firstSegment, $rootSegments, true)) {
        return response()->json([
            'status'  => 'error',
            'message' => 'Endpoint not found.',
            'code'    => 'NOT_FOUND',
        ], 404);
    }

    // Preserve query string when bouncing to v1.
    $qs     = $request->getQueryString();
    $target = "/api/v1/{$afterApi}" . ($qs !== null ? "?{$qs}" : '');

    return redirect()->to($target, 308);
});
