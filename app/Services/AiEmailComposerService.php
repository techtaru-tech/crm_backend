<?php

namespace App\Services;

use App\Models\Lead;
use App\Services\Ai\TenantOpenAiKeyResolver;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class AiEmailComposerService
{
    public function __construct(private readonly TenantOpenAiKeyResolver $keyResolver) {}

    public function draft(Lead $lead, string $intent = 'follow_up', ?string $context = null): ?array
    {
        $apiKey = $this->keyResolver->resolveForId($lead->tenant_id);
        if (!$apiKey) {
            return null;
        }

        // i18n: intent label + system prompt routed through translator
        // so the OpenAI-composed email subject + body land in the buyer's
        // workspace locale.  :locale tells GPT which language to write in;
        // without this the model defaulted to English regardless of the
        // workspace's app locale.
        $intentLabel = match ($intent) {
            'introduction' => (string) __('lead_next_actions.ai_email_composer.intent.introduction'),
            'follow_up'    => (string) __('lead_next_actions.ai_email_composer.intent.follow_up'),
            'proposal'     => (string) __('lead_next_actions.ai_email_composer.intent.proposal'),
            're_engage'    => (string) __('lead_next_actions.ai_email_composer.intent.re_engage'),
            'closing'      => (string) __('lead_next_actions.ai_email_composer.intent.closing'),
            default        => (string) __('lead_next_actions.ai_email_composer.intent.default'),
        };

        $locale       = (string) app()->getLocale();
        $systemPrompt = (string) __('lead_next_actions.ai_email_composer.system_prompt', ['locale' => $locale]);

        $prompt = $this->buildPrompt($lead, $intentLabel, $context);

        try {
            $response = Http::withHeaders([
                'Authorization' => 'Bearer ' . $apiKey,
                'Content-Type'  => 'application/json',
            ])->timeout(config('ai.openai.timeout', 30))
              ->post(config('ai.openai.base_url', 'https://api.openai.com/v1') . '/chat/completions', [
                'model'       => config('ai.openai.model', 'gpt-4o-mini'),
                'temperature' => 0.7,
                'messages'    => [
                    [
                        'role'    => 'system',
                        'content' => $systemPrompt,
                    ],
                    [
                        'role'    => 'user',
                        'content' => $prompt,
                    ],
                ],
                'functions' => [
                    [
                        'name'        => 'compose_email',
                        'description' => (string) __('lead_next_actions.ai_email_composer.function_description'),
                        'parameters'  => [
                            'type'       => 'object',
                            'properties' => [
                                'subject' => ['type' => 'string', 'description' => (string) __('lead_next_actions.ai_email_composer.subject_description')],
                                'body'    => ['type' => 'string', 'description' => (string) __('lead_next_actions.ai_email_composer.body_description')],
                            ],
                            'required' => ['subject', 'body'],
                        ],
                    ],
                ],
                'function_call' => ['name' => 'compose_email'],
            ]);

            if (!$response->successful()) {
                Log::warning('AiEmailComposerService: OpenAI request failed', [
                    'lead_id' => $lead->id,
                    'status'  => $response->status(),
                ]);
                return null;
            }

            $args = json_decode(
                $response->json('choices.0.message.function_call.arguments', '{}'),
                true
            );

            if (empty($args['subject']) || empty($args['body'])) {
                return null;
            }

            return [
                'subject' => $args['subject'],
                'body'    => $args['body'],
            ];
        } catch (\Throwable $e) {
            Log::warning('AiEmailComposerService: exception', [
                'lead_id' => $lead->id,
                'error'   => $e->getMessage(),
            ]);
            return null;
        }
    }

    private function buildPrompt(Lead $lead, string $intentLabel, ?string $userContext): string
    {
        // i18n: prompt body labels routed through translator so a
        // buyer with full-locale config can override the entire schema sent
        // to OpenAI. Defaults stay English (canonical contract with the model).
        $writeLabel        = (string) __('lead_next_actions.ai_email_composer.prompt_write_label');
        $forLeadLabel      = (string) __('lead_next_actions.ai_email_composer.prompt_for_lead');
        $nameLabel         = (string) __('lead_next_actions.ai_email_composer.prompt_name');
        $companyLabel      = (string) __('lead_next_actions.ai_email_composer.prompt_company');
        $jobTitleLabel     = (string) __('lead_next_actions.ai_email_composer.prompt_job_title');
        $industryLabel     = (string) __('lead_next_actions.ai_email_composer.prompt_industry');
        $sourceLabel       = (string) __('lead_next_actions.ai_email_composer.prompt_source');
        $statusLabel       = (string) __('lead_next_actions.ai_email_composer.prompt_status');
        $scoreLabel        = (string) __('lead_next_actions.ai_email_composer.prompt_score');
        $sourceDefault     = (string) __('lead_next_actions.ai_email_composer.prompt_source_default');
        $statusDefault     = (string) __('lead_next_actions.ai_email_composer.prompt_status_default');
        $additionalContext = (string) __('lead_next_actions.ai_email_composer.prompt_additional_context');
        $signoff           = (string) __('lead_next_actions.ai_email_composer.prompt_closing_signoff');
        $closing           = (string) __('lead_next_actions.ai_email_composer.prompt_closing_instructions', ['signoff' => $signoff]);

        $lines = [
            $writeLabel . ' ' . $intentLabel . ' ' . $forLeadLabel,
            $nameLabel . ': ' . $lead->full_name,
            $lead->company  ? $companyLabel . ': ' . $lead->company  : null,
            $lead->job_title ? $jobTitleLabel . ': ' . $lead->job_title : null,
            $lead->industry  ? $industryLabel . ': ' . $lead->industry  : null,
            $sourceLabel . ': ' . ($lead->source ?? $sourceDefault),
            $statusLabel . ': ' . ($lead->status?->value ?? $statusDefault),
            $scoreLabel . ': '  . ($lead->lead_score ?? 0) . '/100',
        ];

        if ($userContext) {
            $lines[] = $additionalContext . ': ' . $userContext;
        }

        $lines[] = $closing;

        return implode("\n", array_filter($lines));
    }

    // M-A1: resolveApiKey() moved to App\Services\Ai\TenantOpenAiKeyResolver.
}
