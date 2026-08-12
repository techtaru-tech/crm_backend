<?php

declare(strict_types=1);

/*
|------------------------------------------------------------
| LeadSource enum — translatable case labels
|------------------------------------------------------------
| Accessed via __('enums/lead_source.<case_value>').
|
| Note on brand/product names:
| Many strings below contain proprietary product names
| (Facebook, Instagram, TikTok, LinkedIn, etc.) which are
| trademarks of their respective owners.  These are kept
| in their original (English) form in this default file,
| but translators can localise the surrounding words
| ("Leads", "Lead Forms", "Lead Ads", "Web Form",
| "Manual Entry") as needed.
*/

return [
    'meta'          => 'Facebook Lead Ads',
    'instagram'     => 'Instagram Leads',
    'tiktok'        => 'TikTok Lead Generation',
    'linkedin'      => 'LinkedIn Lead Gen Forms',
    'whatsapp'      => 'WhatsApp Business',
    'viber'         => 'Viber',
    'telegram'      => 'Telegram',
    'twitter'       => 'Twitter / X',
    'snapchat'      => 'Snapchat Lead Ads',
    'pinterest'     => 'Pinterest Lead Ads',
    'google_ads'    => 'Google Ads Lead Forms',
    'youtube'       => 'YouTube Lead Forms',
    'microsoft_ads' => 'Microsoft Advertising',
    'email'         => 'Email (IMAP)',
    'typeform'      => 'Typeform',
    'jotform'       => 'JotForm',
    'calendly'      => 'Calendly',
    'web_form'      => 'LeadHub Web Form',
    'manual'        => 'Manual Entry',
];
