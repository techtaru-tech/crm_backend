<?php

namespace App\Http\Controllers;

use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Http\Response;

class WebhookController extends Controller
{
    /**
     * Inbound webhook receiver for /api/webhook/{tenant}/{source}/{token}.
     *
     * HISTORY / BUG FIX: this method used to be a placeholder that
     * returned an empty 200 and DROPPED every payload — a silent
     * data-loss trap.  Any lead-source provider configured against the
     * /api/ variant of the webhook URL got a success response while
     * every lead vanished.  It now delegates to the canonical
     * InboundWebhookController::handle (the same implementation
     * routes/web.php's /webhook/... route uses), so BOTH URLs behave
     * identically: token lookup, signature verification, WebhookLog
     * row, and ProcessInboundWebhook dispatch.
     *
     * The model's getWebhookUrlAttribute hands out the /webhook/...
     * (web.php) form, so most installs use that — but vendors who
     * pasted the /api/ form, or anyone who guessed it, no longer
     * silently lose leads.
     */
    public function receive(Request $request, string $tenant, string $source, string $token): Response|JsonResponse
    {
        return app(InboundWebhookController::class)->handle($request, $tenant, $source, $token);
    }
}
