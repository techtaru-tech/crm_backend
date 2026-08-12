<?php

declare(strict_types=1);

namespace App\Services\Chatbot;

use App\Models\ChatbotConfig;
use App\Models\ChatConversation;
use App\Models\Lead;
use App\Models\LeadMessage;
use App\Models\Pipeline;
use App\Models\PipelineStage;
use App\Services\Ai\TenantOpenAiKeyResolver;
use App\Services\LeadDuplicateDetector;
use App\Support\DemoMode;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

/**
 * LeadBot brain.
 *
 * Turns one visitor message into a reply, applying two server-side tools
 * the model may *request* but never *execute* itself:
 *
 *   - capture_lead  → dedupes + creates a Lead routed to the bot's
 *                     configured pipeline/stage, links it to the
 *                     conversation, and drops a channel='chatbot'
 *                     LeadMessage so the transcript lands in the inbox.
 *   - book_meeting  → returns the configured MeetingType booking URL.
 *
 * Hard rules baked in:
 *   - AI always runs on the GLOBAL / super-admin OpenAI key
 *     ({@see TenantOpenAiKeyResolver} falls back to it).  No key →
 *     graceful canned reply, and we STILL capture the lead if the
 *     visitor volunteered an email (never lose the lead).
 *   - Prompt-injection hardened: the system prompt resists exfiltration
 *     and other tenants' data is isolated by tenant SCOPE (not by the
 *     prompt). Tool side-effects are validated server-side — email/phone
 *     are re-validated, and routing comes from the config and is re-checked
 *     against the tenant, never from model output.
 *   - Token-budgeted: persona + knowledge are hard-capped and only the
 *     last few turns are sent, so a long chat can't balloon the bill.
 *   - DEMO_MODE safe: on the public demo we never hit the real API
 *     (would burn the owner's key) and never write outside the demo —
 *     we return the canned reply but still record the transcript.
 *   - Fail-closed: every external/parse failure degrades to the canned
 *     reply; nothing throws out of {@see respond()}.
 */
class ChatbotResponder
{
    /** Max characters of persona text injected into the system prompt. */
    private const PERSONA_CHAR_CAP = 1_500;

    /** How many prior turns (user+assistant) to replay for context. */
    private const HISTORY_TURNS = 10;

    /** Max characters accepted from a single visitor message. */
    public const MAX_USER_MESSAGE_CHARS = 2_000;

    public function __construct(
        private readonly TenantOpenAiKeyResolver $keyResolver,
        private readonly LeadDuplicateDetector $duplicateDetector,
        private readonly ChatbotKnowledgeCompiler $knowledgeCompiler,
    ) {
    }

    /**
     * @return array{reply:string, captured:bool, booking_url:?string}
     */
    public function respond(ChatConversation $conversation, ChatbotConfig $config, string $userMessage): array
    {
        $userMessage = trim($userMessage);
        if ($userMessage === '') {
            return $this->result($this->cannedReply($config), false, null);
        }
        $userMessage = mb_substr($userMessage, 0, self::MAX_USER_MESSAGE_CHARS);

        $apiKey = $this->keyResolver->resolveForId($conversation->tenant_id);

        // No key OR demo mode → never call the API.  Degrade gracefully
        // but still try to capture a lead the visitor volunteered, so a
        // key-less / demo install never silently drops a hot lead.
        if (! $apiKey || DemoMode::isOn()) {
            return $this->degradedResponse($conversation, $config, $userMessage);
        }

        try {
            return $this->aiResponse($conversation, $config, $userMessage, $apiKey);
        } catch (\Throwable $e) {
            Log::warning('ChatbotResponder: exception — degrading', [
                'conversation_id' => $conversation->id,
                'error'           => $e->getMessage(),
            ]);

            return $this->degradedResponse($conversation, $config, $userMessage);
        }
    }

    /**
     * Full AI round-trip with server-side tool execution.
     *
     * @return array{reply:string, captured:bool, booking_url:?string}
     */
    private function aiResponse(ChatConversation $conversation, ChatbotConfig $config, string $userMessage, string $apiKey): array
    {
        $messages = $this->buildMessages($conversation, $config, $userMessage);
        $tools = $this->toolDefinitions($config);

        $first = $this->callOpenAi($apiKey, $messages, $tools);
        if ($first === null) {
            return $this->degradedResponse($conversation, $config, $userMessage);
        }

        $choice = $first['choices'][0]['message'] ?? [];
        $toolCalls = $choice['tool_calls'] ?? [];

        $captured = false;
        $bookingUrl = null;

        if (! empty($toolCalls)) {
            // Replay the assistant's tool-call turn, then append one
            // tool result per call (server-validated), then ask the
            // model for the final natural-language reply.
            $messages[] = [
                'role'       => 'assistant',
                'content'    => $choice['content'] ?? null,
                'tool_calls' => $toolCalls,
            ];

            foreach ($toolCalls as $call) {
                [$result, $didCapture, $url] = $this->executeTool($conversation, $config, $call);
                $captured = $captured || $didCapture;
                $bookingUrl = $bookingUrl ?? $url;

                $messages[] = [
                    'role'         => 'tool',
                    'tool_call_id' => $call['id'] ?? 'call_0',
                    'content'      => $result,
                ];
            }

            // Second round-trip with NO tools so the model just writes prose.
            $second = $this->callOpenAi($apiKey, $messages, null);
            $reply = $this->extractContent($second) ?? $this->cannedReply($config);
        } else {
            $reply = $this->extractContent($first) ?? $this->cannedReply($config);
        }

        // Heuristic safety net: if the model didn't capture but the
        // visitor clearly volunteered an email, capture anyway.
        if (! $captured && ($email = $this->sniffEmail($userMessage)) !== null) {
            $captured = $this->captureLead($conversation, $config, ['email' => $email]) || $captured;
        }

        return $this->result($reply, $captured, $bookingUrl);
    }

    /**
     * No-API path (no key configured, or DEMO_MODE on).  Returns a
     * polite canned reply but still captures a volunteered email so the
     * lead is never lost.
     *
     * @return array{reply:string, captured:bool, booking_url:?string}
     */
    private function degradedResponse(ChatConversation $conversation, ChatbotConfig $config, string $userMessage): array
    {
        $captured = false;
        if (($email = $this->sniffEmail($userMessage)) !== null) {
            $captured = $this->captureLead($conversation, $config, ['email' => $email]);
        }

        return $this->result($this->cannedReply($config), $captured, null);
    }

    /**
     * Build the OpenAI message array: a hardened system prompt followed
     * by the last few turns of history.
     *
     * The caller (PublicChatbotController) persists the visitor turn
     * BEFORE invoking respond(), so $history already ends with it — we
     * must NOT append $userMessage again or the model sees it twice.
     * As a defensive fallback (e.g. the responder called directly in a
     * context that did not pre-store the turn), append it only when the
     * latest history row isn't already that exact user message.
     *
     * @return array<int, array<string, mixed>>
     */
    private function buildMessages(ChatConversation $conversation, ChatbotConfig $config, string $userMessage): array
    {
        $messages = [[
            'role'    => 'system',
            'content' => $this->systemPrompt($config),
        ]];

        $history = $conversation->messages()
            ->whereIn('role', [\App\Models\ChatMessage::ROLE_USER, \App\Models\ChatMessage::ROLE_ASSISTANT])
            ->latest('id')
            ->limit(self::HISTORY_TURNS)
            ->get()
            ->reverse()
            ->values();

        foreach ($history as $msg) {
            $messages[] = [
                'role'    => $msg->role,
                'content' => mb_substr((string) $msg->content, 0, self::MAX_USER_MESSAGE_CHARS),
            ];
        }

        $last = $history->last();
        $alreadyPresent = $last
            && $last->role === \App\Models\ChatMessage::ROLE_USER
            && trim((string) $last->content) === trim($userMessage);

        if (! $alreadyPresent) {
            $messages[] = ['role' => 'user', 'content' => $userMessage];
        }

        return $messages;
    }

    /**
     * The injection-hardened system prompt.  Persona + knowledge are
     * the ONLY tenant-authored content; everything else is fixed guard
     * rails the visitor cannot override.
     */
    private function systemPrompt(ChatbotConfig $config): string
    {
        $persona = trim((string) ($config->persona ?? ''));
        $persona = $persona !== ''
            ? mb_substr($persona, 0, self::PERSONA_CHAR_CAP)
            : (string) __('chatbot.default_persona');

        // Compile the bot's knowledge from the tenant's own content (curated
        // notes + published landing pages + active products), token-budgeted.
        $knowledge = $this->knowledgeCompiler->compile($config);
        if ($knowledge === '') {
            $knowledge = (string) __('chatbot.no_knowledge');
        }

        $locale = (string) app()->getLocale();
        $business = $config->tenant?->name ?? (string) __('chatbot.this_business');

        $guard = (string) __('chatbot.system_guardrails', [
            'business' => $business,
            'locale'   => $locale,
        ]);

        return $guard
            . "\n\n=== PERSONA ===\n" . $persona
            . "\n\n=== KNOWLEDGE (the ONLY facts you may state about the business) ===\n" . $knowledge
            . "\n=== END KNOWLEDGE ===";
    }

    /**
     * Tool schema. `book_meeting` is only offered when the bot has a
     * meeting type configured.
     *
     * @return array<int, array<string, mixed>>
     */
    private function toolDefinitions(ChatbotConfig $config): array
    {
        $tools = [[
            'type'     => 'function',
            'function' => [
                'name'        => 'capture_lead',
                'description' => 'Save the visitor as a sales lead once they share at least an email (or a phone number). Call this as soon as you have a contact detail.',
                'parameters'  => [
                    'type'       => 'object',
                    'properties' => [
                        'email'      => ['type' => 'string', 'description' => 'Visitor email address'],
                        'phone'      => ['type' => 'string', 'description' => 'Visitor phone number'],
                        'first_name' => ['type' => 'string', 'description' => 'Visitor first name if given'],
                        'last_name'  => ['type' => 'string', 'description' => 'Visitor last name if given'],
                    ],
                    // No `required` — the server re-validates that at
                    // least one of email/phone is present and rejects
                    // empty captures, so the model can be lenient.
                ],
            ],
        ]];

        if ($config->meeting_type_id) {
            $tools[] = [
                'type'     => 'function',
                'function' => [
                    'name'        => 'book_meeting',
                    'description' => 'Offer the visitor a link to book a meeting. Call this when the visitor wants to talk to a human, schedule a demo, or book a call.',
                    'parameters'  => ['type' => 'object', 'properties' => (object) []],
                ],
            ];
        }

        return $tools;
    }

    /**
     * Execute one model-requested tool SERVER-SIDE.  The model's
     * arguments are treated as untrusted: routing always comes from the
     * trusted config, and contact details are re-validated.
     *
     * @param  array<string, mixed>  $call
     * @return array{0:string, 1:bool, 2:?string}  [tool_result_json, didCapture, bookingUrl]
     */
    private function executeTool(ChatConversation $conversation, ChatbotConfig $config, array $call): array
    {
        $name = $call['function']['name'] ?? '';
        $args = [];
        $rawArgs = $call['function']['arguments'] ?? '{}';
        if (is_string($rawArgs)) {
            $decoded = json_decode($rawArgs, true);
            $args = is_array($decoded) ? $decoded : [];
        }

        if ($name === 'capture_lead') {
            $ok = $this->captureLead($conversation, $config, $args);

            return [
                json_encode(['captured' => $ok], JSON_THROW_ON_ERROR),
                $ok,
                null,
            ];
        }

        if ($name === 'book_meeting') {
            $url = $this->bookingUrl($config);

            return [
                json_encode(['booking_url' => $url], JSON_THROW_ON_ERROR),
                false,
                $url,
            ];
        }

        return [json_encode(['error' => 'unknown_tool'], JSON_THROW_ON_ERROR), false, null];
    }

    /**
     * Dedupe + persist a lead routed to the bot's configured
     * pipeline/stage, link it to the conversation, and bridge the
     * transcript into the inbox via a channel='chatbot' LeadMessage.
     *
     * Returns false (no-op) when no usable contact detail is present —
     * we never create empty/garbage leads from a hallucinated tool call.
     *
     * @param  array<string, mixed>  $args
     */
    public function captureLead(ChatConversation $conversation, ChatbotConfig $config, array $args): bool
    {
        $email = $this->cleanEmail($args['email'] ?? null);
        $phone = $this->cleanPhone($args['phone'] ?? null);

        // Server-side guard: refuse captures with no contact handle.
        if ($email === null && $phone === null) {
            return false;
        }

        $tenantId = (int) $conversation->tenant_id;

        try {
            $existing = $this->duplicateDetector->findExisting($tenantId, $email, $phone);

            if ($existing) {
                $lead = $existing;
            } else {
                // Route to the bot's configured pipeline/stage, but only if
                // they STILL exist for this tenant — a since-deleted pipeline
                // must land the lead as "unassigned", never on a dangling ref.
                [$pipelineId, $stageId] = $this->resolveRouting($config);

                $lead = Lead::create(array_filter([
                    'tenant_id'         => $tenantId,
                    'source'            => 'chatbot',
                    'source_id'         => (string) $config->uuid,
                    'first_name'        => $this->cleanName($args['first_name'] ?? null),
                    'last_name'         => $this->cleanName($args['last_name'] ?? null),
                    'email'             => $email,
                    'phone'             => $phone,
                    'status'            => 'new',
                    'pipeline_id'       => $pipelineId,
                    'pipeline_stage_id' => $stageId,
                    'raw_data'          => [
                        'chatbot_uuid'    => $config->uuid,
                        'conversation_id' => $conversation->id,
                    ],
                ], static fn ($v) => $v !== null));
            }

            // Link the conversation to the lead and flip status once.
            if ($conversation->lead_id !== $lead->id) {
                $conversation->forceFill([
                    'lead_id' => $lead->id,
                    'status'  => ChatConversation::STATUS_CAPTURED,
                ])->save();

                $this->bridgeToInbox($conversation, $lead);
            }

            return true;
        } catch (\Throwable $e) {
            Log::warning('ChatbotResponder::captureLead failed', [
                'conversation_id' => $conversation->id,
                'error'           => $e->getMessage(),
            ]);

            return false;
        }
    }

    /**
     * Drop a single channel='chatbot' inbound LeadMessage so the chat
     * shows up in the existing Conversations inbox.  Best-effort: a
     * failure here must not abort the capture.
     */
    private function bridgeToInbox(ChatConversation $conversation, Lead $lead): void
    {
        try {
            $transcript = $conversation->messages()
                ->whereIn('role', [\App\Models\ChatMessage::ROLE_USER, \App\Models\ChatMessage::ROLE_ASSISTANT])
                ->orderBy('id')
                ->limit(self::HISTORY_TURNS * 2)
                ->get()
                ->map(fn ($m) => ($m->role === \App\Models\ChatMessage::ROLE_USER ? '👤 ' : '🤖 ') . $m->content)
                ->implode("\n");

            LeadMessage::create([
                'tenant_id'       => $conversation->tenant_id,
                'lead_id'         => $lead->id,
                'channel'         => 'chatbot',
                'direction'       => 'inbound',
                'from_identifier' => 'web-chat',
                'to_identifier'   => 'leadbot',
                'body'            => $transcript !== '' ? $transcript : (string) __('chatbot.inbox_started'),
                'status'          => 'delivered',
            ]);
        } catch (\Throwable $e) {
            Log::warning('ChatbotResponder::bridgeToInbox failed', [
                'conversation_id' => $conversation->id,
                'error'           => $e->getMessage(),
            ]);
        }
    }

    /**
     * Resolve the bot's configured pipeline/stage to ids that still exist for
     * this tenant. A since-deleted (or otherwise unresolvable) pipeline falls
     * back to null so the lead lands "unassigned" instead of carrying a
     * dangling reference; the stage is kept only when it belongs to the
     * resolved pipeline. Scoped explicitly by the config's tenant_id (not the
     * ambient request tenant), so it is correct from any call site.
     *
     * @return array{0: int|null, 1: int|null}  [pipeline_id, pipeline_stage_id]
     */
    private function resolveRouting(ChatbotConfig $config): array
    {
        $tenantId = (int) $config->tenant_id;

        $pipelineExists = $config->pipeline_id && Pipeline::withoutGlobalScope('tenant')
            ->where('tenant_id', $tenantId)
            ->whereKey($config->pipeline_id)
            ->exists();

        if (! $pipelineExists) {
            return [null, null];
        }

        $stageOk = $config->pipeline_stage_id && PipelineStage::withoutGlobalScope('tenant')
            ->where('tenant_id', $tenantId)
            ->where('pipeline_id', $config->pipeline_id)
            ->whereKey($config->pipeline_stage_id)
            ->exists();

        return [(int) $config->pipeline_id, $stageOk ? (int) $config->pipeline_stage_id : null];
    }

    private function bookingUrl(ChatbotConfig $config): ?string
    {
        $meetingType = $config->meetingType;
        if (! $meetingType || ! $meetingType->is_active) {
            return null;
        }

        return $meetingType->booking_url;
    }

    /**
     * Single OpenAI chat/completions call.  Returns the decoded body or
     * null on any non-success / transport error (caller degrades).
     *
     * @param  array<int, array<string, mixed>>       $messages
     * @param  array<int, array<string, mixed>>|null  $tools
     * @return array<string, mixed>|null
     */
    private function callOpenAi(string $apiKey, array $messages, ?array $tools): ?array
    {
        $payload = [
            'model'       => config('ai.openai.model', 'gpt-4o-mini'),
            'temperature' => 0.4,
            'max_tokens'  => 400,
            'messages'    => $messages,
        ];
        if (! empty($tools)) {
            $payload['tools'] = $tools;
            $payload['tool_choice'] = 'auto';
        }

        $response = Http::withHeaders([
            'Authorization' => 'Bearer ' . $apiKey,
            'Content-Type'  => 'application/json',
        ])
            ->timeout((int) config('ai.openai.timeout', 30))
            ->post(config('ai.openai.base_url', 'https://api.openai.com/v1') . '/chat/completions', $payload);

        if (! $response->successful()) {
            Log::warning('ChatbotResponder: OpenAI request failed', ['status' => $response->status()]);

            return null;
        }

        $json = $response->json();

        return is_array($json) ? $json : null;
    }

    /** @param array<string, mixed>|null $body */
    private function extractContent(?array $body): ?string
    {
        $content = $body['choices'][0]['message']['content'] ?? null;
        if (! is_string($content)) {
            return null;
        }
        $content = trim($content);

        return $content !== '' ? $content : null;
    }

    private function cannedReply(ChatbotConfig $config): string
    {
        return (string) __('chatbot.canned_reply');
    }

    private function sniffEmail(string $text): ?string
    {
        if (preg_match('/[\w.+\-]+@[\w\-]+\.[\w.\-]+/', $text, $m)) {
            return $this->cleanEmail($m[0]);
        }

        return null;
    }

    private function cleanEmail(mixed $value): ?string
    {
        if (! is_string($value)) {
            return null;
        }
        $value = strtolower(trim($value));
        if ($value === '' || mb_strlen($value) > 200) {
            return null;
        }

        return filter_var($value, FILTER_VALIDATE_EMAIL) ? $value : null;
    }

    private function cleanPhone(mixed $value): ?string
    {
        if (! is_string($value)) {
            return null;
        }
        $value = trim($value);
        // Keep digits, spaces, and common phone punctuation only.
        if ($value === '' || ! preg_match('/^[+0-9()\s.\-]{6,30}$/', $value)) {
            return null;
        }

        return $value;
    }

    private function cleanName(mixed $value): ?string
    {
        if (! is_string($value)) {
            return null;
        }
        $value = trim($value);

        return $value !== '' ? mb_substr($value, 0, 100) : null;
    }

    /**
     * @return array{reply:string, captured:bool, booking_url:?string}
     */
    private function result(string $reply, bool $captured, ?string $bookingUrl): array
    {
        return [
            'reply'       => $reply,
            'captured'    => $captured,
            'booking_url' => $bookingUrl,
        ];
    }
}
