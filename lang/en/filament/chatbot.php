<?php

declare(strict_types=1);

/*
|------------------------------------------------------------
| LeadBot admin (app/Filament/Resources/ChatbotConfigResource.php)
|------------------------------------------------------------
| Labels, section headings, help text, and modal copy for the
| AI chatbot admin resource.  Accessed via __('filament/chatbot.<key>').
*/

return [

    // ─── Navigation / model labels ───
    'nav_label'          => 'AI Chatbot',
    'model_label'        => 'AI Chatbot',
    'plural_model_label' => 'AI Chatbots',

    // ─── Sections ───
    'section_identity'   => 'Identity',
    'section_brain'      => 'Knowledge & persona',
    'section_brain_help' => 'Teach the bot who it is and what it knows. It will ONLY answer from the knowledge you provide here.',
    'section_appearance' => 'Appearance',
    'section_routing'    => 'Lead routing & booking',
    'section_limits'     => 'Status & limits',
    'section_embed'      => 'Embed on your website',

    // ─── Fields ───
    'name'           => 'Bot name',
    'greeting'       => 'Greeting message',
    'greeting_help'  => 'The first message visitors see when they open the chat.',

    'persona'             => 'Persona',
    'persona_placeholder' => 'e.g. A friendly, concise support assistant for Acme Inc. that speaks in a warm, professional tone.',
    'persona_help'        => 'How the bot should sound and behave.',

    'knowledge'             => 'Knowledge base',
    'knowledge_placeholder' => "Paste your FAQs, product details, pricing, policies, opening hours…\n\nQ: Do you offer a free trial?\nA: Yes — 14 days, no card required.",
    'knowledge_help'        => 'The ONLY facts the bot may state about your business. Plain text works best.',

    'primary_color' => 'Bubble colour',

    'route_to_pipeline'        => 'Route captured leads to pipeline',
    'initial_stage'            => 'Initial stage',
    'booking_meeting_type'     => 'Offer this meeting type for booking',
    'booking_meeting_type_help'=> 'When set, the bot can share this booking link with interested visitors.',

    'enabled'                => 'Chatbot is active',
    'daily_message_cap'      => 'Daily reply cap',
    'daily_message_cap_help' => 'Max AI replies per day across all visitors (0 = unlimited). Protects against runaway usage.',

    // ─── Embed snippet ───
    'embed_snippet_label'        => 'Embed code',
    'embed_snippet_help'         => 'Paste this one line just before the closing </body> tag on every page of your website.',
    'save_first_for_embed'       => 'Save the chatbot first to get your embed code.',
    'snippet_modal_heading'      => 'Your embed code',
    'snippet_modal_description'  => 'Copy and paste this snippet into your website.',
    'snippet_modal_instructions' => 'Add this one line just before the closing </body> tag on every page where the chat bubble should appear.',
    'modal_close'                => 'Close',

    // ─── Table ───
    'pipeline'   => 'Pipeline',
    'created_at' => 'Created',

    // ─── Actions ───
    'action_preview'     => 'Preview',
    'action_get_snippet' => 'Get embed code',
];
