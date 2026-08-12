<?php

declare(strict_types=1);

return [
    'first_contact' => [
        'title'  => 'Make first contact',
        'reason' => 'This lead has never been contacted. Reach out within the hour for best conversion rates.',
    ],
    'follow_up' => [
        'title'  => 'Follow up — :days days since last contact',
        'reason' => 'Leads contacted within 24 hours are 7x more likely to qualify. Send a follow-up now.',
    ],
    'qualify' => [
        'title'  => 'Qualify the lead',
        'reason' => 'Lead score is low (:score/100). Ask qualifying questions to determine fit.',
    ],
    'create_task' => [
        'title'  => 'Create a follow-up task',
        'reason' => 'No open tasks for this lead. Schedule a reminder to keep the deal moving.',
    ],
    'log_call' => [
        'title'  => 'Log a call note',
        'reason' => 'Keep your CRM up to date with notes after every interaction.',
    ],
    'priority' => [
        'high'   => 'High',
        'medium' => 'Medium',
        'low'    => 'Low',
    ],

    // ─── Fallback summary (LeadNextActionService::fallbackSummary) ──────
    // Rendered into the lead-detail header card when no OpenAI key is
    // configured for the tenant. Translate at render-time against the
    // user's current locale.
    'fallback_summary' => [
        'this_lead'         => 'This lead',
        'at_company'        => ' at :company',
        'unassigned_stage'  => 'unassigned stage',
        'never_contacted'   => 'Never been contacted — first-touch outreach is the highest-leverage action.',
        'last_contacted'    => 'Last contacted :days day(s) ago.',
        'high_score'        => 'High score — prioritise.',
        'low_score'         => 'Low score — qualify before investing more time.',
        'mid_score'         => 'Mid-tier score — keep nurturing.',
        'sentence_template' => ':name:company sits in :stage (:days day(s) old). :second :third',
    ],

    // ─── Fallback draft email (LeadNextActionService::fallbackDraftEmail)
    // Rendered into the "Draft email" composer when no OpenAI key is
    // configured. Translate at render-time against the user's locale.
    'fallback_draft_email' => [
        'default_first_name'  => 'there',
        'opener_formal'       => 'Dear :first,',
        'opener_direct'       => 'Hi :first,',
        'opener_empathetic'   => 'Hi :first, hope your week is going well —',
        'opener_friendly'     => 'Hey :first,',
        'body_followup'       => "Wanted to circle back on our last chat to see if you've had a chance to think things over.",
        'body_first_touch'    => 'Just reaching out about your inquiry — I noticed you came in via :source and I had a couple of ideas that might be useful.',
        'cta'                 => 'Would 15 minutes this week work for a quick call? I can come prepared with specifics on next steps.',
        'signoff'             => 'Thanks',
    ],

    // ─── AiEmailComposerService prompts ─────────────────────────────────
    // Pass-33 i18n fix: intent labels + system prompt routed through the
    // translator so the OpenAI-composed email subject + body land in the
    // buyer's workspace locale.  :locale tells GPT which language to write
    // in.  Buyer post-install: translate the English text but keep the
    // :locale placeholder.
    'ai_email_composer' => [
        'intent' => [
            'introduction' => 'an initial introduction and qualification email',
            'follow_up'    => 'a warm follow-up email',
            'proposal'     => 'a proposal or next-steps email',
            're_engage'    => 'a re-engagement email for a cold lead',
            'closing'      => 'a closing or deal confirmation email',
            'default'      => 'a sales follow-up email',
        ],
        'system_prompt' => 'You are an expert B2B sales email writer. Respond in the language with locale code ":locale" only. Write concise, personalized, professional emails. Never use generic filler phrases like "I hope this email finds you well." Return JSON only.',
        // Prompt body field labels — kept English by default (schema contract).
        // Buyer override fully localises the user-message structure too.
        'prompt_write_label'   => 'Write',
        'prompt_for_lead'      => 'for a lead with these details:',
        'prompt_name'          => 'Name',
        'prompt_company'       => 'Company',
        'prompt_job_title'     => 'Job Title',
        'prompt_industry'      => 'Industry',
        'prompt_source'        => 'Lead Source',
        'prompt_status'        => 'Lead Status',
        'prompt_score'         => 'Lead Score',
        'prompt_source_default' => 'website',
        'prompt_status_default' => 'new',
        'prompt_additional_context' => 'Additional context',
        // The closing instructions sent at the end of the user message —
        // routed through translator so a buyer can override the sign-off
        // and word-count constraints to match their brand voice.
        'prompt_closing_instructions' => "\nKeep it under 150 words. Personalize based on the source and company. Sign off as ':signoff'.",
        'prompt_closing_signoff' => 'The Team',
        // OpenAI function-call schema descriptions — sent in the API
        // call as part of the `functions` payload.  Kept English by
        // default (canonical OpenAI contract); buyers can localise.
        'function_description' => 'Return a sales email draft',
        'subject_description'  => 'Email subject line',
        'body_description'     => 'Email body in plain text with paragraph breaks. Do not use HTML tags.',
    ],

    // ─── LeadEnrichmentService prompts ──────────────────────────────────
    // Pass-33 i18n fix: prompts routed through the translator so the
    // OpenAI-extracted enrichment fields (industry, job_title — free-text
    // values stored on the lead) land in the buyer's workspace locale.
    // :locale tells GPT which language to answer in.  Buyer post-install:
    // translate the English text but keep the :locale placeholder.
    'lead_enrichment' => [
        'system_prompt' => 'You are a B2B lead enrichment assistant. Respond in the language with locale code ":locale" only. Given a name, email domain, and email address, return structured data. If you cannot determine something with reasonable confidence, return null for that field. Never fabricate data.',
        // The user-message labels — kept English by default as the schema
        // contract with the model; buyer can override for full locale.
        'prompt_name'   => 'Name',
        'prompt_email'  => 'Email',
        'prompt_domain' => 'Domain',
        'prompt_instruction' => 'Based on the email domain, infer company name, industry, and available public information. Do not hallucinate specific person details.',
        // OpenAI function-call schema descriptions — sent in the API
        // call as part of the `functions` payload.
        'function_description'      => 'Return structured enrichment data for a lead',
        'company_description'       => 'Company name from domain',
        'job_title_description'     => 'Likely job title based on available info',
        'industry_description'      => 'Industry sector',
        'company_size_description'  => 'Estimated company size (e.g. 1-10, 11-50, 51-200, 201-1000, 1000+)',
        'country_description'       => 'Country code (ISO 3166-1 alpha-2)',
        'linkedin_url_description'  => 'LinkedIn company page URL if inferable from domain',
    ],

    // ─── OpenAI prompts (LeadNextActionService — recommend/summarizeLead/draftFollowUpEmail) ─
    // Pass-33 i18n fix: prompts routed through the translator so the AI
    // responds in the buyer's locale.  Buyer's OpenAI output is rendered
    // verbatim in the lead-detail UI; without an explicit locale instruction
    // GPT-4o-mini defaulted to English regardless of the workspace locale.
    // Each prompt receives :locale (the active app locale code) so the model
    // knows which language to answer in.  Buyer post-install: translate the
    // English text BUT leave the :locale placeholder intact.
    'ai_prompts' => [
        'recommendations_system' => 'You are a sales coaching AI. Respond in the language with locale code ":locale" only. Given a lead\'s profile and status, return exactly 3 prioritized next best actions to move the deal forward. Be specific and actionable. Return valid JSON only.',
        'recommendations_user_suffix' => "\n\nReturn exactly 3 actions as a JSON array: [{\"priority\": \"high|medium|low\", \"icon\": \"phone|email|task|note|warning|star\", \"title\": \"Short action title in language :locale\", \"reason\": \"1-2 sentence explanation in language :locale\"}]",
        'summary_system' => 'You are a sales coaching AI. Respond in the language with locale code ":locale" only. Given a lead profile, write a 2-3 sentence executive summary covering: who they are, where they stand in the funnel, and what to do next. No fluff, no greetings.',
        'draft_email_system' => 'You are a sales rep drafting a follow-up email. Respond in the language with locale code ":locale" only. Tone: :tone. Write a complete email body (no subject line). Reference one specific detail from the lead profile. End with a single clear call to action. Plain text, 80-150 words, no preamble.',
        // Context labels fed to the model alongside the structured data
        // (still in English so the canonical field name semantics survive
        // across locales — model is told to RESPOND in :locale but the
        // schema labels are the contract with the prompt).  Buyer
        // override only if you need to fully localise the schema too.
        'context_lead'          => 'Lead',
        'context_at'            => 'at',
        'context_source'        => 'Source',
        'context_status'        => 'Status',
        'context_score'         => 'Score',
        'context_pipeline_stage' => 'Pipeline stage',
        'context_days_created'  => 'Days since created',
        'context_days_contact'  => 'Days since last contact',
        'context_days_activity' => 'Days since last activity',
        'context_open_tasks'    => 'Open tasks',
        'context_job_title'     => 'Job title',
        'context_industry'      => 'Industry',
        'context_unknown'       => 'unknown',
        'context_new'           => 'new',
        'context_none'          => 'none',
    ],
];
