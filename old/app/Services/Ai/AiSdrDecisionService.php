<?php

declare(strict_types=1);

namespace App\Services\Ai;

use App\Models\AiSdrAgent;
use App\Models\AiSdrEnrollment;
use App\Models\Lead;
use App\Models\LeadEmail;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

/**
 * Makes ONE follow-up decision for an AI SDR enrollment.
 *
 * Returns a pure AiSdrDecision — it never sends mail, mutates the
 * enrollment, or writes audit rows (that is ProcessAiSdrEnrollment's job).
 *
 * Failure policy (non-negotiable, enforced here not just in the prompt):
 *   - No OpenAI key (resolver falls back to the GLOBAL super-admin key,
 *     so this only triggers when neither tenant nor global key is set):
 *     degrade to a DETERMINISTIC templated touch — never hard-fail.
 *   - Any AI error (network, non-200, malformed function args, exception):
 *     FAIL-CLOSED to `wait`. We would rather under-send than send garbage
 *     or double-send on a transient blip.
 *
 * The model runs on the global key by design (TenantOpenAiKeyResolver
 * already falls back to config('services.openai.api_key')).
 */
class AiSdrDecisionService
{
    /** Max prior emails fed into the prompt as conversation history. */
    private const HISTORY_LIMIT = 10;

    /** Rough gpt-4o-mini pricing (USD per 1K tokens) for cost estimates. */
    private const COST_PER_1K_PROMPT = 0.00015;
    private const COST_PER_1K_COMPLETION = 0.0006;

    public function __construct(private readonly TenantOpenAiKeyResolver $keyResolver) {}

    public function decide(AiSdrAgent $agent, AiSdrEnrollment $enrollment, Lead $lead): AiSdrDecision
    {
        $apiKey = $this->keyResolver->resolveForId($lead->tenant_id);

        if (! $apiKey) {
            // No key anywhere → deterministic templated touch so the
            // feature still works on installs that never configure OpenAI.
            return $this->fallbackDecision($agent, $enrollment, $lead);
        }

        try {
            return $this->aiDecision($apiKey, $agent, $enrollment, $lead);
        } catch (\Throwable $e) {
            Log::warning('AiSdrDecisionService: exception, failing closed to wait', [
                'enrollment_id' => $enrollment->id,
                'lead_id'       => $lead->id,
                'error'         => $e->getMessage(),
            ]);
            // FAIL-CLOSED: never send on an error.
            return AiSdrDecision::wait('ai_error');
        }
    }

    private function aiDecision(string $apiKey, AiSdrAgent $agent, AiSdrEnrollment $enrollment, Lead $lead): AiSdrDecision
    {
        $response = Http::withHeaders([
            'Authorization' => 'Bearer ' . $apiKey,
            'Content-Type'  => 'application/json',
        ])->timeout((int) config('ai.openai.timeout', 30))
          ->post(config('ai.openai.base_url', 'https://api.openai.com/v1') . '/chat/completions', [
            'model'         => config('ai.openai.model', 'gpt-4o-mini'),
            'temperature'   => 0.4,
            'messages'      => [
                ['role' => 'system', 'content' => $this->systemPrompt($agent)],
                ['role' => 'user',   'content' => $this->userPrompt($agent, $enrollment, $lead)],
            ],
            'functions'     => [$this->functionSchema()],
            'function_call' => ['name' => 'decide_next_action'],
        ]);

        if (! $response->successful()) {
            Log::warning('AiSdrDecisionService: OpenAI non-200, failing closed', [
                'enrollment_id' => $enrollment->id,
                'status'        => $response->status(),
            ]);
            return AiSdrDecision::wait('ai_http_' . $response->status());
        }

        $args = json_decode(
            (string) $response->json('choices.0.message.function_call.arguments', '{}'),
            true
        );

        if (! is_array($args) || empty($args['action'])) {
            return AiSdrDecision::wait('ai_no_action');
        }

        $action = (string) $args['action'];
        if (! in_array($action, ['send', 'wait', 'qualify', 'handoff', 'stop'], true)) {
            return AiSdrDecision::wait('ai_bad_action');
        }

        $usage   = (array) $response->json('usage', []);
        $promptT = isset($usage['prompt_tokens']) ? (int) $usage['prompt_tokens'] : null;
        $compT   = isset($usage['completion_tokens']) ? (int) $usage['completion_tokens'] : null;

        // A "send" with no body is meaningless — downgrade to wait rather than
        // dispatch an empty touch. A subject is required for EMAIL only; SMS /
        // WhatsApp touches are body-only.
        $subject      = isset($args['subject']) ? trim((string) $args['subject']) : '';
        $body         = isset($args['body']) ? trim((string) $args['body']) : '';
        $needsSubject = ! $agent->usesMessagingChannel();
        if ($action === 'send' && ($body === '' || ($needsSubject && $subject === ''))) {
            return AiSdrDecision::wait('ai_empty_send');
        }

        return new AiSdrDecision(
            action: $action,
            subject: $subject !== '' ? $subject : null,
            body: $body !== '' ? $body : null,
            reason: isset($args['reason']) ? (string) $args['reason'] : null,
            intentScore: isset($args['intent_score']) ? $this->clampScore($args['intent_score']) : null,
            wasFallback: false,
            model: (string) config('ai.openai.model', 'gpt-4o-mini'),
            promptTokens: $promptT,
            completionTokens: $compT,
            costUsd: $this->estimateCost($promptT, $compT),
        );
    }

    /**
     * Deterministic decision when no OpenAI key is configured anywhere.
     * Sends a templated touch until max_touches, then qualifies for human
     * review (the runner enforces the ceiling too — belt and braces).
     */
    private function fallbackDecision(AiSdrAgent $agent, AiSdrEnrollment $enrollment, Lead $lead): AiSdrDecision
    {
        $first = $lead->first_name ?: __('ai_sdr.fallback.default_first_name');

        if ($enrollment->touches_sent === 0) {
            $subject = __('ai_sdr.fallback.first_subject', ['company' => $agent->tenant?->name ?? config('app.name')]);
            $body    = __('ai_sdr.fallback.first_body', [
                'first' => $first,
                'offer' => (string) ($agent->offer_context ?? ''),
            ]);
        } else {
            $subject = __('ai_sdr.fallback.followup_subject', ['first' => $first]);
            $body    = __('ai_sdr.fallback.followup_body', ['first' => $first]);
        }

        return AiSdrDecision::send(
            subject: (string) $subject,
            body: (string) $body,
            reason: 'no_api_key_templated_fallback',
            intentScore: null,
            wasFallback: true,
        );
    }

    private function systemPrompt(AiSdrAgent $agent): string
    {
        $locale = (string) app()->getLocale();

        return (string) __('ai_sdr.system_prompt', [
            'locale'  => $locale,
            'persona' => (string) ($agent->persona ?? __('ai_sdr.default_persona')),
            'goal'    => (string) $agent->goal,
        ]);
    }

    private function userPrompt(AiSdrAgent $agent, AiSdrEnrollment $enrollment, Lead $lead): string
    {
        $history = $this->conversationHistory($lead);

        $lines = [
            __('ai_sdr.prompt.offer') . ': ' . (string) ($agent->offer_context ?? ''),
            __('ai_sdr.prompt.lead_name') . ': ' . $lead->full_name,
            $lead->company   ? __('ai_sdr.prompt.company') . ': ' . $lead->company     : null,
            $lead->job_title ? __('ai_sdr.prompt.job_title') . ': ' . $lead->job_title : null,
            __('ai_sdr.prompt.status') . ': ' . ($lead->status?->value ?? 'new'),
            __('ai_sdr.prompt.score') . ': ' . (int) ($lead->lead_score ?? 0) . '/100',
            __('ai_sdr.prompt.touches_sent') . ': ' . (int) $enrollment->touches_sent . '/' . (int) $agent->max_touches,
            '',
            __('ai_sdr.prompt.history_header') . ':',
            $history !== '' ? $history : __('ai_sdr.prompt.history_none'),
            '',
            $agent->wantsBooking() && $agent->meetingType?->booking_url
                ? __('ai_sdr.prompt.booking_goal', ['url' => (string) $agent->meetingType->booking_url])
                : null,
            __('ai_sdr.prompt.instructions'),
        ];

        return implode("\n", array_filter($lines, static fn ($l) => $l !== null));
    }

    private function conversationHistory(Lead $lead): string
    {
        $emails = LeadEmail::withoutGlobalScope('tenant')
            ->where('tenant_id', $lead->tenant_id)
            ->where('lead_id', $lead->id)
            ->orderByDesc('created_at')
            ->limit(self::HISTORY_LIMIT)
            ->get(['direction', 'subject', 'body_text', 'created_at'])
            ->reverse();

        $out = [];
        foreach ($emails as $email) {
            $who = $email->direction === 'inbound'
                ? __('ai_sdr.prompt.lead_label')
                : __('ai_sdr.prompt.us_label');
            $text = trim((string) ($email->body_text ?? ''));
            if ($text === '') {
                $text = (string) ($email->subject ?? '');
            }
            $out[] = $who . ': ' . \Illuminate\Support\Str::limit($text, 400);
        }

        return implode("\n", $out);
    }

    /**
     * @return array<string,mixed>
     */
    private function functionSchema(): array
    {
        return [
            'name'        => 'decide_next_action',
            'description' => (string) __('ai_sdr.function.description'),
            'parameters'  => [
                'type'       => 'object',
                'properties' => [
                    'action' => [
                        'type'        => 'string',
                        'enum'        => ['send', 'wait', 'qualify', 'handoff', 'stop'],
                        'description' => (string) __('ai_sdr.function.action_description'),
                    ],
                    'subject' => [
                        'type'        => 'string',
                        'description' => (string) __('ai_sdr.function.subject_description'),
                    ],
                    'body' => [
                        'type'        => 'string',
                        'description' => (string) __('ai_sdr.function.body_description'),
                    ],
                    'reason' => [
                        'type'        => 'string',
                        'description' => (string) __('ai_sdr.function.reason_description'),
                    ],
                    'intent_score' => [
                        'type'        => 'integer',
                        'description' => (string) __('ai_sdr.function.intent_score_description'),
                    ],
                ],
                'required' => ['action', 'reason'],
            ],
        ];
    }

    private function clampScore(mixed $score): int
    {
        $n = (int) $score;
        return max(0, min(100, $n));
    }

    private function estimateCost(?int $promptTokens, ?int $completionTokens): ?float
    {
        if ($promptTokens === null && $completionTokens === null) {
            return null;
        }

        return round(
            (($promptTokens ?? 0) / 1000) * self::COST_PER_1K_PROMPT
            + (($completionTokens ?? 0) / 1000) * self::COST_PER_1K_COMPLETION,
            6
        );
    }
}
