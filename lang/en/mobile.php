<?php

declare(strict_types=1);

/*
|------------------------------------------------------------
| Mobile PWA views (resources/views/mobile/*.blade.php)
|------------------------------------------------------------
| Accessed via __('mobile.<key>').
*/

return [

    // ─── Page titles ──────────────────────────────────────────────
    'page_title_scan'           => 'Scan a card',
    'page_title_capture'        => 'Capture lead',
    'page_title_offline'        => 'Offline',
    'page_title_mobile'         => 'Mobile',

    // ─── Layout ───────────────────────────────────────────────────
    'admin_link'                => 'Admin',
    'install_button'            => 'Install',
    'offline_banner'            => "You're offline. Captures will queue & sync when you're back online.",
    'install_prompt_prefix'     => 'Install',
    'install_prompt_suffix'     => 'to your home screen for offline capture.',

    // ─── Scan page ────────────────────────────────────────────────
    'scan_heading'              => 'Scan a business card',
    'read_the_card'             => 'Read the card',
    'review_and_save'           => 'Review & save',
    'save_lead'                 => 'Save lead',
    'scan_hint'                 => "Snap the front of the card. We'll read it and pre-fill the capture form. Requires an internet connection.",
    'open_camera_gallery'       => 'Open camera / gallery',
    'file_size_hint'            => 'JPG or PNG, up to 8 MB',

    // ─── Lead form field labels (shared by capture & scan) ────────
    'first_name'                => 'First name',
    'last_name'                 => 'Last name',
    'email'                     => 'Email',
    'phone'                     => 'Phone',
    'company'                   => 'Company',
    'notes'                     => 'Notes',
    'assign_to'                 => 'Assign to',
    'title'                     => 'Title',
    'website'                   => 'Website',
    'notes_placeholder'         => 'What did you talk about?',
    'assign_to_me'              => 'Me (:name)',

    // ─── Capture page ─────────────────────────────────────────────
    'new_lead_heading'          => 'New lead',
    'scan_a_card_instead'       => 'Scan a card instead',
    'capture_hint'              => "Captured here lands in your workspace instantly — or queues and syncs if you're offline.",

    // ─── Offline page ─────────────────────────────────────────────
    'youre_offline'             => "You're offline",
    'capture_a_lead_anyway'     => 'Capture a lead anyway',
    'offline_hint'              => "But that's fine — LeadHub saves captures locally and syncs them as soon as you're back online.",

    // ─── Inline-JS runtime strings (scan.blade.php) ───────────────
    'js_reading'                => 'Reading…',
    'js_ocr_failed'             => 'OCR failed',
    'js_ocr_unavailable'        => 'OCR unavailable. Type it manually.',
    'js_save_failed'            => 'Save failed',

    // ─── Inline-JS runtime strings (capture.blade.php) ────────────
    'js_add_email_or_phone'     => 'Add an email or phone first.',
    'js_saving'                 => 'Saving…',
    'js_saved'                  => 'Saved.',
    'js_offline_queued'         => "Offline — saved to queue. Will sync when you're back online.",

    // ─── MobileController JSON response messages ──────────────────
    // These flow into the mobile blade view fallback patterns
    // `json.message || @js(__('mobile.<key>'))` so the JSON side
    // sends translated strings and the JS side still has a fallback.
    'capture_error_email_or_phone_required'   => 'An email or phone is required.',
    'capture_dedup_existing_lead'             => 'A matching lead was found — showing the existing record.',
    'capture_success_lead_captured'           => 'Lead captured.',
    'ocr_error_openai_key_missing'            => 'OpenAI API key is not configured on the server.',
    'ocr_error_request_failed'                => 'OCR request failed.',
    'ocr_error_provider_status'               => 'OCR provider returned :status',
    'ocr_error_parse_failed'                  => 'Could not parse OCR response.',

    // Pass-33 i18n fix — OCR prompt routed through translator so the AI
    // returns extracted fields tagged in the buyer's locale (job-title
    // free text, for example, defaults to English absent this instruction).
    // The structured JSON keys (first_name, last_name, etc.) stay English
    // because they are the schema contract with downstream consumers.
    // :locale tells OpenAI which language to interpret/respond in.
    'ocr_business_card_prompt' => 'Extract business-card fields from this photo. Respond in the language with locale code ":locale" only. Respond with ONLY a JSON object with keys: first_name, last_name, email, phone, company, title, website. Use null for any field you cannot read confidently. Do not invent data.',
];
