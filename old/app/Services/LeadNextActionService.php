<?php

namespace App\Services;

use App\Models\Lead;
use App\Services\Ai\TenantOpenAiKeyResolver;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class LeadNextActionService
{
    public function __construct(private readonly TenantOpenAiKeyResolver $keyResolver) {}

    public function recommend(Lead $lead): array
    {
        $apiKey = $this->keyResolver->resolveForId($lead->tenant_id);
        if (!$apiKey) {
            return $this->fallbackRecommendations($lead);
        }

        $context = $this->buildContext($lead);
        // i18n: prompt routed through translator so the AI's
        // returned title/reason fields render in the buyer's locale
        // when they appear in the lead-detail next-action panel.
        $locale       = (string) app()->getLocale();
        $systemPrompt = (string) __('lead_next_actions.ai_prompts.recommendations_system', ['locale' => $locale]);
        $userSuffix   = (string) __('lead_next_actions.ai_prompts.recommendations_user_suffix', ['locale' => $locale]);

        try {
            $response = Http::withHeaders([
                'Authorization' => 'Bearer ' . $apiKey,
                'Content-Type'  => 'application/json',
            ])->timeout(config('ai.openai.timeout', 30))
              ->post(config('ai.openai.base_url', 'https://api.openai.com/v1') . '/chat/completions', [
                'model'       => config('ai.openai.model', 'gpt-4o-mini'),
                'temperature' => 0.3,
                'messages'    => [
                    [
                        'role'    => 'system',
                        'content' => $systemPrompt,
                    ],
                    [
                        'role'    => 'user',
                        'content' => $context . $userSuffix,
                    ],
                ],
            ]);

            if (!$response->successful()) {
                return $this->fallbackRecommendations($lead);
            }

            $content = $response->json('choices.0.message.content', '');
            $content = preg_replace('/```(?:json)?\s*|\s*```/', '', $content);
            $actions = json_decode(trim($content), true);

            if (is_array($actions) && count($actions) > 0) {
                return array_slice($actions, 0, 3);
            }

            return $this->fallbackRecommendations($lead);
        } catch (\Throwable $e) {
            Log::warning('LeadNextActionService: exception', ['lead_id' => $lead->id, 'error' => $e->getMessage()]);
            return $this->fallbackRecommendations($lead);
        }
    }

    /**
     * 2-3 sentence AI-generated summary of the lead's current state +
     * what's known about them.  Designed for the lead-detail header
     * card so reps don't have to read the full activity timeline to
     * remember what's going on.
     *
     * Falls back to a deterministic template when no API key is set,
     * so the feature works even on installs that never configure
     * OpenAI.
     */
    public function summarizeLead(Lead $lead): string
    {
        $apiKey = $this->keyResolver->resolveForId($lead->tenant_id);
        if (! $apiKey) {
            return $this->fallbackSummary($lead);
        }

        $context = $this->buildContext($lead);
        // i18n: prompt routed through translator so the
        // AI-generated lead summary renders in the buyer's locale.
        $locale       = (string) app()->getLocale();
        $systemPrompt = (string) __('lead_next_actions.ai_prompts.summary_system', ['locale' => $locale]);

        try {
            $response = Http::withHeaders([
                'Authorization' => 'Bearer ' . $apiKey,
                'Content-Type'  => 'application/json',
            ])->timeout(config('ai.openai.timeout', 30))
              ->post(config('ai.openai.base_url', 'https://api.openai.com/v1') . '/chat/completions', [
                'model'       => config('ai.openai.model', 'gpt-4o-mini'),
                'temperature' => 0.3,
                'messages'    => [
                    [
                        'role'    => 'system',
                        'content' => $systemPrompt,
                    ],
                    [
                        'role'    => 'user',
                        'content' => $context,
                    ],
                ],
            ]);

            if (! $response->successful()) {
                return $this->fallbackSummary($lead);
            }

            $content = trim((string) $response->json('choices.0.message.content', ''));
            return $content !== '' ? $content : $this->fallbackSummary($lead);
        } catch (\Throwable $e) {
            Log::warning('LeadNextActionService::summarizeLead exception', [
                'lead_id' => $lead->id,
                'error'   => $e->getMessage(),
            ]);
            return $this->fallbackSummary($lead);
        }
    }

    /**
     * Drafts a follow-up email body for the lead, tuned to the picked
     * tone.  Designed for a "Draft email" button on the lead-detail
     * page — the rep can edit the result before sending.
     *
     * @param  string  $tone  one of: friendly | formal | direct | empathetic
     */
    public function draftFollowUpEmail(Lead $lead, string $tone = 'friendly'): string
    {
        $apiKey = $this->keyResolver->resolveForId($lead->tenant_id);
        if (! $apiKey) {
            return $this->fallbackDraftEmail($lead, $tone);
        }

        $context = $this->buildContext($lead);
        // i18n: prompt routed through translator so the
        // AI-drafted follow-up email body is written in the buyer's
        // locale and the rep panel renders it in the workspace UI
        // without an English-language mismatch.
        $locale       = (string) app()->getLocale();
        $systemPrompt = (string) __('lead_next_actions.ai_prompts.draft_email_system', [
            'locale' => $locale,
            'tone'   => $tone,
        ]);

        try {
            $response = Http::withHeaders([
                'Authorization' => 'Bearer ' . $apiKey,
                'Content-Type'  => 'application/json',
            ])->timeout(config('ai.openai.timeout', 30))
              ->post(config('ai.openai.base_url', 'https://api.openai.com/v1') . '/chat/completions', [
                'model'       => config('ai.openai.model', 'gpt-4o-mini'),
                'temperature' => 0.5,
                'messages'    => [
                    [
                        'role'    => 'system',
                        'content' => $systemPrompt,
                    ],
                    [
                        'role'    => 'user',
                        'content' => $context,
                    ],
                ],
            ]);

            if (! $response->successful()) {
                return $this->fallbackDraftEmail($lead, $tone);
            }

            $content = trim((string) $response->json('choices.0.message.content', ''));
            return $content !== '' ? $content : $this->fallbackDraftEmail($lead, $tone);
        } catch (\Throwable $e) {
            Log::warning('LeadNextActionService::draftFollowUpEmail exception', [
                'lead_id' => $lead->id,
                'error'   => $e->getMessage(),
            ]);
            return $this->fallbackDraftEmail($lead, $tone);
        }
    }

    private function fallbackSummary(Lead $lead): string
    {
        // M-I18N: deterministic fallback summary — translator-first so
        // the lead-detail header card renders in the viewer's locale
        // even on installs without a tenant-scoped OpenAI key.
        $name    = $lead->full_name ?: (string) __('lead_next_actions.fallback_summary.this_lead');
        $company = $lead->company
            ? (string) __('lead_next_actions.fallback_summary.at_company', ['company' => $lead->company])
            : '';
        $stage = $lead->pipelineStage?->name
            ?? (string) __('lead_next_actions.fallback_summary.unassigned_stage');
        $score = (int) ($lead->lead_score ?? 0);
        $daysSinceCreated = (int) now()->diffInDays($lead->created_at);
        $daysSinceContact = $lead->contacted_at ? (int) now()->diffInDays($lead->contacted_at) : null;

        $second = $daysSinceContact === null
            ? (string) __('lead_next_actions.fallback_summary.never_contacted')
            : (string) __('lead_next_actions.fallback_summary.last_contacted', ['days' => $daysSinceContact]);

        $third = $score >= 70
            ? (string) __('lead_next_actions.fallback_summary.high_score')
            : ($score < 30
                ? (string) __('lead_next_actions.fallback_summary.low_score')
                : (string) __('lead_next_actions.fallback_summary.mid_score'));

        return (string) __('lead_next_actions.fallback_summary.sentence_template', [
            'name'    => $name,
            'company' => $company,
            'stage'   => $stage,
            'days'    => $daysSinceCreated,
            'second'  => $second,
            'third'   => $third,
        ]);
    }

    private function fallbackDraftEmail(Lead $lead, string $tone): string
    {
        // M-I18N: deterministic fallback email — translator-first so
        // the "Draft email" composer renders in the viewer's locale
        // even on installs without a tenant-scoped OpenAI key.
        $first = $lead->first_name
            ?: (string) __('lead_next_actions.fallback_draft_email.default_first_name');

        $opener = match ($tone) {
            'formal'     => (string) __('lead_next_actions.fallback_draft_email.opener_formal',     ['first' => $first]),
            'direct'     => (string) __('lead_next_actions.fallback_draft_email.opener_direct',     ['first' => $first]),
            'empathetic' => (string) __('lead_next_actions.fallback_draft_email.opener_empathetic', ['first' => $first]),
            default      => (string) __('lead_next_actions.fallback_draft_email.opener_friendly',   ['first' => $first]),
        };

        $body = $lead->contacted_at
            ? (string) __('lead_next_actions.fallback_draft_email.body_followup')
            : (string) __('lead_next_actions.fallback_draft_email.body_first_touch', [
                'source' => (string) ($lead->source ?? ''),
            ]);

        $cta     = (string) __('lead_next_actions.fallback_draft_email.cta');
        $signoff = (string) __('lead_next_actions.fallback_draft_email.signoff');

        return "{$opener}\n\n{$body}\n\n{$cta}\n\n{$signoff}";
    }

    private function buildContext(Lead $lead): string
    {
        $daysSinceCreated   = (int) now()->diffInDays($lead->created_at);
        $daysSinceContacted = $lead->contacted_at ? (int) now()->diffInDays($lead->contacted_at) : null;
        $latestActivity     = $lead->activities()->latest()->first();
        $daysSinceActivity  = $latestActivity ? (int) now()->diffInDays($latestActivity->created_at) : null;
        $openTasks          = $lead->tasks()->where('completed', false)->count();

        // i18n: context schema labels routed through translator so
        // a buyer who fully localises their workspace can also localise the
        // OpenAI input schema.  Defaults stay English (canonical contract
        // with the model).  The CONTENT-language locale instruction lives
        // in the system prompt above, not here.
        $leadLabel       = (string) __('lead_next_actions.ai_prompts.context_lead');
        $atLabel         = (string) __('lead_next_actions.ai_prompts.context_at');
        $sourceLabel     = (string) __('lead_next_actions.ai_prompts.context_source');
        $statusLabel     = (string) __('lead_next_actions.ai_prompts.context_status');
        $scoreLabel      = (string) __('lead_next_actions.ai_prompts.context_score');
        $stageLabel      = (string) __('lead_next_actions.ai_prompts.context_pipeline_stage');
        $createdLabel    = (string) __('lead_next_actions.ai_prompts.context_days_created');
        $contactLabel    = (string) __('lead_next_actions.ai_prompts.context_days_contact');
        $activityLabel   = (string) __('lead_next_actions.ai_prompts.context_days_activity');
        $openTasksLabel  = (string) __('lead_next_actions.ai_prompts.context_open_tasks');
        $jobTitleLabel   = (string) __('lead_next_actions.ai_prompts.context_job_title');
        $industryLabel   = (string) __('lead_next_actions.ai_prompts.context_industry');
        $unknownToken    = (string) __('lead_next_actions.ai_prompts.context_unknown');
        $newToken        = (string) __('lead_next_actions.ai_prompts.context_new');
        $noneToken       = (string) __('lead_next_actions.ai_prompts.context_none');

        return implode("\n", array_filter([
            $leadLabel . ': ' . $lead->full_name . ($lead->company ? ' ' . $atLabel . ' ' . $lead->company : ''),
            $sourceLabel . ': ' . ($lead->source ?? $unknownToken),
            $statusLabel . ': ' . ($lead->status?->value ?? $newToken),
            $scoreLabel . ': ' . ($lead->lead_score ?? 0) . '/100',
            $stageLabel . ': ' . ($lead->pipelineStage?->name ?? $noneToken),
            $createdLabel . ': ' . $daysSinceCreated,
            $daysSinceContacted !== null ? $contactLabel . ': ' . $daysSinceContacted : null,
            $daysSinceActivity !== null ? $activityLabel . ': ' . $daysSinceActivity : null,
            $openTasksLabel . ': ' . $openTasks,
            $lead->job_title ? $jobTitleLabel . ': ' . $lead->job_title : null,
            $lead->industry  ? $industryLabel . ': '  . $lead->industry  : null,
        ]));
    }

    private function fallbackRecommendations(Lead $lead): array
    {
        // M-I18N: fallback recommendations are user-facing strings — translator-first with
        // English fallback so the rep panel respects tenant locale. Placeholders (:days, :score)
        // keep interpolation safe across translations.
        $daysSinceContact = $lead->contacted_at
            ? (int) now()->diffInDays($lead->contacted_at)
            : null;

        $recommendations = [];

        if ($daysSinceContact === null) {
            $recommendations[] = [
                'priority' => 'high',
                'icon'     => 'phone',
                'title'    => (string) __('lead_next_actions.first_contact.title'),
                'reason'   => (string) __('lead_next_actions.first_contact.reason'),
            ];
        } elseif ($daysSinceContact > 3) {
            $recommendations[] = [
                'priority' => 'high',
                'icon'     => 'email',
                'title'    => (string) __('lead_next_actions.follow_up.title', ['days' => $daysSinceContact]),
                'reason'   => (string) __('lead_next_actions.follow_up.reason'),
            ];
        }

        if ($lead->lead_score < 30) {
            $recommendations[] = [
                'priority' => 'medium',
                'icon'     => 'task',
                'title'    => (string) __('lead_next_actions.qualify.title'),
                'reason'   => (string) __('lead_next_actions.qualify.reason', ['score' => (int) $lead->lead_score]),
            ];
        }

        if ($lead->tasks()->where('completed', false)->count() === 0) {
            $recommendations[] = [
                'priority' => 'low',
                'icon'     => 'task',
                'title'    => (string) __('lead_next_actions.create_task.title'),
                'reason'   => (string) __('lead_next_actions.create_task.reason'),
            ];
        }

        if (count($recommendations) < 3) {
            $recommendations[] = [
                'priority' => 'low',
                'icon'     => 'note',
                'title'    => (string) __('lead_next_actions.log_call.title'),
                'reason'   => (string) __('lead_next_actions.log_call.reason'),
            ];
        }

        return array_slice($recommendations, 0, 3);
    }

    // M-A1: resolveApiKey() moved to App\Services\Ai\TenantOpenAiKeyResolver.
}
