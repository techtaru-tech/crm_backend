<?php

declare(strict_types=1);

/*
|------------------------------------------------------------
| Filament — AI SDR resource, relation manager + decision log
|------------------------------------------------------------
| Accessed via __('filament/ai_sdr.<key>').
*/

return [
    'nav_label'          => 'AI SDR Agents',
    'model_label'        => 'AI SDR Agent',
    'plural_model_label' => 'AI SDR Agents',

    // ── Form sections ──
    'section_agent'      => 'Agent',
    'section_persona'    => 'Persona & Offer',
    'section_guardrails' => 'Guardrails',
    'section_handoff'    => 'Handoff',

    // ── Fields ──
    'field_name'                 => 'Name',
    'field_goal'                 => 'Goal',
    'field_goal_help'            => 'What the agent is trying to achieve — qualify the lead for a human, or get them to book a meeting.',
    'goal_qualify'               => 'Qualify the lead',
    'goal_book'                  => 'Book a meeting',
    'field_active'               => 'Active',
    'field_channel'              => 'Channel',
    'field_channel_help'         => 'How the agent reaches leads. SMS and WhatsApp need the workspace messaging provider configured and the lead to have a phone number.',
    'channel_email'              => 'Email',
    'channel_sms'                => 'SMS',
    'channel_whatsapp'           => 'WhatsApp',
    'field_persona'              => 'Persona',
    'field_persona_help'         => 'How the agent should sound (e.g. "a warm, concise B2B SDR named Alex").',
    'field_offer_context'        => 'Offer context',
    'field_offer_context_help'   => 'What you sell and the value proposition — the agent uses this in every message.',
    'field_max_touches'          => 'Max follow-ups',
    'field_max_touches_help'     => 'Hard ceiling on messages the agent will send before handing off.',
    'field_min_hours'            => 'Min hours between follow-ups',
    'field_min_hours_help'       => 'The agent will never message the same lead more often than this.',
    'field_business_hours'       => 'Respect business hours',
    'field_business_hours_help'  => 'Only send during the workspace business-hours window.',
    'field_assign_to'            => 'Hand off to',
    'field_assign_to_help'       => 'When the agent qualifies a lead, assign it to this rep.',
    'field_meeting_type'         => 'Meeting type (for booking goal)',
    'field_meeting_type_help'    => 'When the goal is "Book a meeting", the agent shares this meeting type\'s booking link.',

    // ── Table columns ──
    'col_name'                => 'Name',
    'col_active'              => 'Active',
    'col_max_touches'         => 'Max touches',
    'col_active_enrollments'  => 'Active',
    'col_handed_off'          => 'Handed off',
    'col_handoff_rep'         => 'Handoff rep',
    'col_created'             => 'Created',

    // ── Enrollments relation manager ──
    'enrollments_relation_title' => 'Enrolled leads',
    'col_lead'        => 'Lead',
    'col_email'       => 'Email',
    'col_state'       => 'State',
    'col_touches'     => 'Touches',
    'col_intent'      => 'Intent',
    'col_next_action' => 'Next action',
    'col_enrolled_at' => 'Enrolled',
    'filter_state'    => 'State',

    // ── States ──
    'state_active'         => 'Active',
    'state_awaiting_reply' => 'Awaiting reply',
    'state_paused_human'   => 'Paused (human)',
    'state_qualified'      => 'Qualified',
    'state_handed_off'     => 'Handed off',
    'state_opted_out'      => 'Opted out',
    'state_completed'      => 'Completed',

    // ── Decision log ──
    'decision_log'         => 'Decision log',
    'decision_log_heading' => 'AI decisions — :lead',
    'close'                => 'Close',
    'log_fallback'         => 'fallback',
    'log_intent'           => 'intent',
    'log_empty'            => 'No decisions recorded yet.',

    // ── Enroll action (lead detail page) ──
    'enroll_action'        => 'Enroll in AI SDR',
    'enroll_agent'         => 'AI SDR agent',
    'enrolled_success'     => 'Lead enrolled in the AI SDR agent.',
    'enroll_already'       => 'This lead is already enrolled in that agent.',
    'enroll_no_email'      => 'This lead has no email address for an email agent.',
    'enroll_unreachable'   => "This lead can't be reached on the selected agent's channel — email agents need an email address; SMS / WhatsApp agents need a phone number.",
    'enroll_opted_out'     => 'This lead has opted out of AI SDR outreach and cannot be enrolled.',
    'enroll_no_agents'     => 'No active AI SDR agents exist yet. Create one first.',
];
