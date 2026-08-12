<?php

namespace App\Services;

use App\Models\Lead;
use App\Services\Ai\TenantOpenAiKeyResolver;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class LeadEnrichmentService
{
    public function __construct(private readonly TenantOpenAiKeyResolver $keyResolver) {}

    public function enrich(Lead $lead): bool
    {
        $apiKey = $this->keyResolver->resolveForId($lead->tenant_id);
        if (!$apiKey || !$lead->email) {
            return false;
        }

        $domain = substr(strrchr($lead->email, '@'), 1);
        $prompt = $this->buildPrompt($lead->first_name . ' ' . $lead->last_name, $domain, $lead->email);

        // i18n: system prompt routed through translator so the
        // enrichment results (industry, job_title — free-text fields that
        // land in the lead's record and render in the UI) come back in the
        // buyer's workspace locale.  :locale tells GPT which language to
        // answer in.  Without this, industry/job_title defaulted to English
        // labels regardless of the workspace locale.
        $locale       = (string) app()->getLocale();
        $systemPrompt = (string) __('lead_next_actions.lead_enrichment.system_prompt', ['locale' => $locale]);

        try {
            $response = Http::withHeaders([
                'Authorization' => 'Bearer ' . $apiKey,
                'Content-Type'  => 'application/json',
            ])->timeout(config('ai.openai.timeout', 30))
              ->post(config('ai.openai.base_url', 'https://api.openai.com/v1') . '/chat/completions', [
                'model'       => config('ai.openai.model', 'gpt-4o-mini'),
                'temperature' => 0,
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
                        'name'        => 'enrich_lead',
                        'description' => (string) __('lead_next_actions.lead_enrichment.function_description'),
                        'parameters'  => [
                            'type'       => 'object',
                            'properties' => [
                                'company'      => ['type' => ['string', 'null'], 'description' => (string) __('lead_next_actions.lead_enrichment.company_description')],
                                'job_title'    => ['type' => ['string', 'null'], 'description' => (string) __('lead_next_actions.lead_enrichment.job_title_description')],
                                'industry'     => ['type' => ['string', 'null'], 'description' => (string) __('lead_next_actions.lead_enrichment.industry_description')],
                                'company_size' => ['type' => ['string', 'null'], 'description' => (string) __('lead_next_actions.lead_enrichment.company_size_description')],
                                'country'      => ['type' => ['string', 'null'], 'description' => (string) __('lead_next_actions.lead_enrichment.country_description')],
                                'linkedin_url' => ['type' => ['string', 'null'], 'description' => (string) __('lead_next_actions.lead_enrichment.linkedin_url_description')],
                            ],
                            'required' => ['company', 'industry', 'country'],
                        ],
                    ],
                ],
                'function_call' => ['name' => 'enrich_lead'],
            ]);

            if (!$response->successful()) {
                Log::warning('LeadEnrichmentService: OpenAI request failed', [
                    'lead_id' => $lead->id,
                    'status'  => $response->status(),
                ]);
                return false;
            }

            $data = $response->json();
            $args = json_decode(
                $data['choices'][0]['message']['function_call']['arguments'] ?? '{}',
                true
            );

            if (empty($args)) {
                return false;
            }

            $lead->update(array_filter([
                'company'        => $args['company'] ?? null,
                'job_title'      => $args['job_title'] ?? null,
                'industry'       => $args['industry'] ?? null,
                'company_size'   => $args['company_size'] ?? null,
                'country'        => $args['country'] ?? null,
                'linkedin_url'   => $args['linkedin_url'] ?? null,
                'enriched_at'    => now(),
                'enrichment_data' => $args,
            ], fn($v) => $v !== null));

            return true;
        } catch (\Throwable $e) {
            Log::warning('LeadEnrichmentService: exception', [
                'lead_id' => $lead->id,
                'error'   => $e->getMessage(),
            ]);
            return false;
        }
    }

    // M-A1: resolveApiKey() moved to App\Services\Ai\TenantOpenAiKeyResolver
    // (constructor-injected) so its lookup priority is the single
    // source of truth across enrichment + email composer + next-action.

    private function buildPrompt(string $name, string $domain, string $email): string
    {
        // i18n: prompt labels routed through translator so a buyer
        // who fully localises their workspace can also localise the schema
        // sent to OpenAI.  Defaults stay English (canonical contract with
        // the model).  The CONTENT-language locale instruction lives in the
        // system prompt, not here.
        $nameLabel   = (string) __('lead_next_actions.lead_enrichment.prompt_name');
        $emailLabel  = (string) __('lead_next_actions.lead_enrichment.prompt_email');
        $domainLabel = (string) __('lead_next_actions.lead_enrichment.prompt_domain');
        $instruction = (string) __('lead_next_actions.lead_enrichment.prompt_instruction');

        return "{$nameLabel}: {$name}\n{$emailLabel}: {$email}\n{$domainLabel}: {$domain}\n\n{$instruction}";
    }
}
