<?php

declare(strict_types=1);

namespace App\Http\Controllers;

use App\Models\ChatbotConfig;
use App\Models\ChatConversation;
use App\Models\ChatMessage;
use App\Services\Chatbot\ChatbotResponder;
use App\Support\ColorSafety;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\Log;

/**
 * LeadBot public endpoints (embedded on the tenant's OWN website).
 *
 * Mirrors {@see PublicWidgetController}: every lookup uses
 * `withoutGlobalScope('tenant')` because these are unauthenticated,
 * cross-origin requests with no resolved tenant (the fail-closed
 * BelongsToTenant scope would otherwise 404 everything).  The random
 * `uuid` is the unguessable access key; the config's `tenant_id` is the
 * trusted tenant binding for everything created downstream.
 *
 * Responses are BUFFERED JSON (not SSE/streaming) — shared hosting can't
 * hold a streaming connection open reliably.
 */
class PublicChatbotController extends Controller
{
    /** Hard ceiling on visitor messages within ONE conversation. */
    private const PER_CONVERSATION_MESSAGE_CAP = 40;

    /**
     * GET /chatbot/{uuid}/loader.js
     *
     * Emits a tiny per-bot bootstrap that publishes the public config on
     * `window.__LeadBot` then loads the cacheable, static widget script
     * (public/js/chatbot/widget.js — ships prebuilt, no JIT/build).
     */
    public function loader(Request $request, string $uuid): Response
    {
        $isPreview = $request->boolean('preview');

        $query = ChatbotConfig::withoutGlobalScope('tenant')->where('uuid', $uuid);
        if (! $isPreview) {
            $query->where('enabled', true);
        }
        $config = $query->first();

        if (! $config) {
            return response('/* leadbot not found */', 404, ['Content-Type' => 'application/javascript']);
        }

        // All values below are interpolated into a JS object literal via
        // json_encode(), which safely escapes quotes, </script>, and
        // unicode.  Colors land in a CSS context, so they go through
        // ColorSafety::safeHex (rejects `red; }; body{...}` payloads).
        $cfg = [
            'uuid'        => $config->uuid,
            'endpoint'    => url('/chatbot/' . $config->uuid . '/chat'),
            'name'        => (string) ($config->name ?? __('chatbot.default_name')),
            'greeting'    => (string) ($config->greeting ?? __('chatbot.default_greeting')),
            'color'       => ColorSafety::safeHex($config->primary_color ?? null, '#4f46e5'),
            'placeholder' => (string) __('chatbot.input_placeholder'),
            'sendLabel'   => (string) __('chatbot.send_label'),
            'launchLabel' => (string) __('chatbot.launch_label'),
        ];

        $json = json_encode($cfg, JSON_UNESCAPED_SLASHES | JSON_HEX_TAG | JSON_HEX_APOS | JSON_HEX_QUOT);
        $widgetSrc = asset('js/chatbot/widget.js');
        $jsKey = str_replace('-', '', $uuid);

        $js = <<<JS
(function(){
  if (window.__LeadBotLoaded_{$jsKey}) return;
  window.__LeadBotLoaded_{$jsKey} = true;
  window.__LeadBot = window.__LeadBot || {};
  window.__LeadBot["{$jsKey}"] = {$json};
  var s = document.createElement('script');
  s.src = "{$widgetSrc}";
  s.async = true;
  s.setAttribute('data-leadbot', "{$jsKey}");
  document.head.appendChild(s);
})();
JS;

        return response($js, 200, [
            'Content-Type'                => 'application/javascript',
            'Cache-Control'               => 'public, max-age=300',
            'Access-Control-Allow-Origin' => '*',
        ]);
    }

    /**
     * POST /chatbot/{uuid}/chat
     *
     * Buffered start+continue endpoint.  Body:
     *   { message: string, conversation_token?: int }
     *
     * Returns:
     *   { conversation_token, reply, captured, booking_url }
     */
    public function chat(Request $request, string $uuid): JsonResponse
    {
        $config = ChatbotConfig::withoutGlobalScope('tenant')
            ->where('uuid', $uuid)
            ->where('enabled', true)
            ->first();

        if (! $config) {
            return $this->cors(response()->json(['error' => __('chatbot.not_found')], 404));
        }

        // Bind tenant context for every downstream BelongsToTenant write
        // (conversation, lead, lead_message).  Mirrors PublicFormController.
        $tenant = $config->tenant()->withoutGlobalScope('tenant')->first();
        if (! $tenant) {
            return $this->cors(response()->json(['error' => __('chatbot.not_found')], 404));
        }
        app()->instance('current_tenant', $tenant);

        $validated = $request->validate([
            'message'            => 'required|string|max:' . ChatbotResponder::MAX_USER_MESSAGE_CHARS,
            'conversation_token' => 'nullable|integer',
        ]);

        $ipHash = $this->visitorIpHash($request);

        $conversation = $this->resolveConversation(
            $config,
            $tenant->id,
            $ipHash,
            $validated['conversation_token'] ?? null,
        );

        if ($conversation === null) {
            return $this->cors(response()->json(['error' => __('chatbot.session_invalid')], 422));
        }

        // Per-conversation cap — stop runaway single sessions.
        $userTurns = $conversation->messages()->where('role', ChatMessage::ROLE_USER)->count();
        if ($userTurns >= self::PER_CONVERSATION_MESSAGE_CAP) {
            return $this->cors(response()->json([
                'conversation_token' => $conversation->id,
                'reply'              => (string) __('chatbot.conversation_limit'),
                'captured'           => false,
                'booking_url'        => null,
            ], 429));
        }

        // Daily cap across ALL visitors of this bot — protects the
        // shared (global) OpenAI key from runaway spend.
        if ($this->dailyCapReached($config, $tenant->id)) {
            // Still record the visitor message so we don't lose it, but
            // return the canned reply without burning the API key.
            $this->store($conversation, ChatMessage::ROLE_USER, $validated['message']);
            $reply = (string) __('chatbot.busy_reply');
            $this->store($conversation, ChatMessage::ROLE_ASSISTANT, $reply);

            return $this->cors(response()->json([
                'conversation_token' => $conversation->id,
                'reply'              => $reply,
                'captured'           => false,
                'booking_url'        => null,
            ]));
        }

        // Persist the visitor turn BEFORE generating, so the responder's
        // history + the inbox bridge see it.
        $this->store($conversation, ChatMessage::ROLE_USER, $validated['message']);

        try {
            $result = app(ChatbotResponder::class)->respond($conversation, $config, $validated['message']);
        } catch (\Throwable $e) {
            Log::warning('PublicChatbotController: responder threw', [
                'uuid'  => $uuid,
                'error' => $e->getMessage(),
            ]);
            $result = [
                'reply'       => (string) __('chatbot.canned_reply'),
                'captured'    => false,
                'booking_url' => null,
            ];
        }

        $this->store($conversation, ChatMessage::ROLE_ASSISTANT, $result['reply']);

        return $this->cors(response()->json([
            'conversation_token' => $conversation->id,
            'reply'              => $result['reply'],
            'captured'           => (bool) $result['captured'],
            'booking_url'        => $result['booking_url'] ?? null,
        ]));
    }

    /**
     * OPTIONS /chatbot/{uuid}/chat — CORS preflight for cross-origin
     * JSON POSTs from the tenant's site.
     */
    public function preflight(): Response
    {
        return $this->cors(response('', 204));
    }

    /**
     * GET /chatbot/{uuid}/preview — admin mock host page so operators
     * can try the bot before embedding.  Inactive bots preview too.
     */
    public function preview(string $uuid): Response
    {
        $config = ChatbotConfig::withoutGlobalScope('tenant')->where('uuid', $uuid)->first();
        if (! $config) {
            abort(404);
        }

        $loaderUrl = url('/chatbot/' . $uuid . '/loader.js?preview=1');
        $locale    = e(app()->getLocale());
        $title     = e($config->name ?: __('chatbot.preview_title'));
        $banner    = e(__('chatbot.preview_banner'));
        $mockH1    = e(__('chatbot.preview_mock_h1'));
        $mockIntro = e(__('chatbot.preview_mock_intro'));

        return response(<<<HTML
<!doctype html>
<html lang="{$locale}">
<head>
<meta charset="utf-8">
<meta name="viewport" content="width=device-width, initial-scale=1">
<title>{$title}</title>
<style>
  *{box-sizing:border-box}
  body{margin:0;font-family:system-ui,-apple-system,Segoe UI,Roboto,sans-serif;color:#111827;background:#f9fafb;line-height:1.55}
  .banner{background:#eef2ff;border-bottom:1px solid #c7d2fe;color:#3730a3;padding:12px 24px;text-align:center;font-size:.875rem;font-weight:600}
  .hero{background:linear-gradient(135deg,#4f46e5,#7c3aed);color:#fff;padding:80px 24px;text-align:center}
  .hero h1{margin:0 0 12px;font-size:2.25rem;font-weight:800}
  .hero p{margin:0;opacity:.9;font-size:1.125rem}
</style>
</head>
<body>
  <div class="banner">{$banner}</div>
  <div class="hero">
    <h1>{$mockH1}</h1>
    <p>{$mockIntro}</p>
  </div>
  <script src="{$loaderUrl}" defer></script>
</body>
</html>
HTML, 200, ['Content-Type' => 'text/html; charset=utf-8']);
    }

    /**
     * Resolve an existing conversation (by opaque integer token) or
     * start a fresh one.
     *
     * Hijack guard: a continuation must match BOTH the bot (uuid) AND
     * the visitor IP hash that started it.  A token that points at
     * another bot / another visitor is treated as a brand-new session
     * rather than reused, so a guessed sequential id can't read or
     * extend a stranger's thread.
     */
    private function resolveConversation(ChatbotConfig $config, int $tenantId, ?string $ipHash, ?int $token): ?ChatConversation
    {
        if ($token !== null) {
            $existing = ChatConversation::withoutGlobalScope('tenant')
                ->where('id', $token)
                ->where('chatbot_config_id', $config->id)
                ->where('status', '!=', ChatConversation::STATUS_CLOSED)
                ->first();

            if ($existing && hash_equals((string) $existing->visitor_ip_hash, (string) $ipHash)) {
                return $existing;
            }
            // Token didn't validate → fall through and start fresh.
        }

        return ChatConversation::create([
            'tenant_id'         => $tenantId,
            'chatbot_config_id' => $config->id,
            'visitor_ip_hash'   => $ipHash,
            'status'            => ChatConversation::STATUS_OPEN,
            'started_at'        => Carbon::now(),
        ]);
    }

    private function dailyCapReached(ChatbotConfig $config, int $tenantId): bool
    {
        // A tenant-set cap of 0 / blank means "use the platform default" — NOT
        // "unlimited". The shared (super-admin) OpenAI key must always keep a
        // ceiling, and a tenant can never raise its own cap above the platform
        // maximum. Read via config() so the bounds survive config:cache.
        $configured = (int) ($config->daily_message_cap ?? 0);
        $cap = min(
            $configured > 0 ? $configured : (int) config('leadhub.chatbot.daily_cap_default', 500),
            (int) config('leadhub.chatbot.daily_cap_max', 5000),
        );
        if ($cap <= 0) {
            return false;
        }

        $todayAssistantCount = ChatMessage::query()
            ->where('role', ChatMessage::ROLE_ASSISTANT)
            ->whereHas('conversation', function ($q) use ($config, $tenantId) {
                $q->withoutGlobalScope('tenant')
                    ->where('tenant_id', $tenantId)
                    ->where('chatbot_config_id', $config->id);
            })
            ->where('created_at', '>=', Carbon::now()->startOfDay())
            ->count();

        return $todayAssistantCount >= $cap;
    }

    private function store(ChatConversation $conversation, string $role, string $content): ChatMessage
    {
        return $conversation->messages()->create([
            'role'    => $role,
            'content' => mb_substr($content, 0, ChatbotResponder::MAX_USER_MESSAGE_CHARS),
        ]);
    }

    /**
     * SHA-256 of (app key + raw IP).  We never store the raw address —
     * the salt makes the hash non-reversible across installs.
     */
    private function visitorIpHash(Request $request): ?string
    {
        $ip = $request->ip();
        if (! $ip) {
            return null;
        }

        return hash('sha256', (string) config('app.key') . '|' . $ip);
    }

    private function cors(JsonResponse|Response $response): JsonResponse|Response
    {
        $response->headers->set('Access-Control-Allow-Origin', '*');
        $response->headers->set('Access-Control-Allow-Methods', 'POST, OPTIONS');
        $response->headers->set('Access-Control-Allow-Headers', 'Content-Type, Accept');

        return $response;
    }
}
