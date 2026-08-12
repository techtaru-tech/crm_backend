<?php

declare(strict_types=1);

/*
|------------------------------------------------------------
| AI SDR — prompts, fallbacks, handoff + UI strings
|------------------------------------------------------------
| Accessed via __('ai_sdr.<key>'). The :locale placeholder in the system
| prompt tells the model which language to write the email in, so the
| persisted touch matches the workspace locale.
*/

return [
    'default_persona' => 'a friendly, concise B2B sales development representative',

    'system_prompt' => "You are :persona acting as an autonomous SDR. Your goal is to ':goal' the lead through short, helpful follow-up messages.\n\n"
        . "Write all message content in the language identified by this locale code: :locale.\n\n"
        . "Rules: be concise and human; never be pushy; one clear call to action per message; never invent facts about the lead or the offer; if the lead seems uninterested or asks to stop, choose 'stop'; if the lead shows clear buying intent or asks to talk to a person, choose 'qualify'.\n"
        . "You decide ONE next action by calling the decide_next_action function. Do not output anything else.",

    'prompt' => [
        'offer'         => 'Our offer',
        'lead_name'     => 'Lead name',
        'company'       => 'Company',
        'job_title'     => 'Job title',
        'status'        => 'Lead status',
        'score'         => 'Lead score',
        'touches_sent'  => 'Follow-ups already sent',
        'history_header'=> 'Conversation so far (oldest first)',
        'history_none'  => '(no prior emails — this would be the first touch)',
        'lead_label'    => 'LEAD',
        'us_label'      => 'US',
        'instructions'  => 'Decide the single best next action now. If sending, write a subject and body tailored to the conversation so far.',
        'booking_goal'  => 'Your objective is to get the lead to book a meeting. When they show interest, warmly invite them to pick a time using this booking link (include it verbatim in your message): :url',
    ],

    'function' => [
        'description'              => 'Decide the single next action for this lead in the autonomous follow-up sequence.',
        'action_description'       => "send = send a follow-up email now; wait = do nothing this cycle; qualify = the lead is qualified, hand to a human; handoff = hand to a human now; stop = end outreach (lead uninterested or asked to stop).",
        'subject_description'      => 'Email subject line. Required when action is send.',
        'body_description'         => 'Email body (plain text or simple HTML). Required when action is send.',
        'reason_description'       => 'A short machine-readable reason for the chosen action.',
        'intent_score_description' => 'Estimated buying intent from 0 (none) to 100 (very high).',
    ],

    // Deterministic templated touches used when NO OpenAI key is set
    // anywhere (tenant or global). The feature still works fully degraded.
    'fallback' => [
        'default_first_name' => 'there',
        'first_subject'      => 'Quick question from :company',
        'first_body'         => "Hi :first,\n\nI wanted to reach out personally. :offer\n\nWould you be open to a quick chat to see if this is a fit?\n\nBest regards",
        'followup_subject'   => 'Following up, :first',
        'followup_body'      => "Hi :first,\n\nJust circling back on my previous note — happy to answer any questions whenever the timing is right.\n\nBest regards",
    ],

    'handoff' => [
        'note_prefix' => 'AI SDR handed this lead off for human follow-up. Summary:',
        'activity'    => 'AI SDR qualified the lead and handed it off',
    ],
];
