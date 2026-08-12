<?php

declare(strict_types=1);

/*
|------------------------------------------------------------
| Lead-source display labels (config(leadhub.sources) mirror)
|------------------------------------------------------------
| Accessed via __('lead_sources.<source_key>').
|
| Keys mirror config/leadhub.sources so any consumer that today
| reads the raw config value can switch to the translator and
| stay backwards-compatible.  Currently consumed by
| app/Filament/Pages/KanbanBoard.php source-label rendering.
*/

return [
    'facebook'      => 'Facebook Lead Ads',
    'instagram'     => 'Instagram',
    'tiktok'        => 'TikTok',
    'linkedin'      => 'LinkedIn',
    'whatsapp'      => 'WhatsApp',
    'viber'         => 'Viber',
    'telegram'      => 'Telegram',
    'twitter'       => 'Twitter / X',
    'snapchat'      => 'Snapchat',
    'pinterest'     => 'Pinterest',
    'google_ads'    => 'Google Ads',
    'youtube'       => 'YouTube',
    'microsoft_ads' => 'Microsoft Advertising',
    'email'         => 'Email (IMAP)',
    'typeform'      => 'Typeform',
    'jotform'       => 'JotForm',
    'calendly'      => 'Calendly',
    'web_form'      => 'LeadHub Web Form',
    'manual'        => 'Manual Entry',
];
