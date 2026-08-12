<?php

declare(strict_types=1);

/*
|------------------------------------------------------------
| Landing-page starter templates — translation strings
|------------------------------------------------------------
| One entry per template in config/landing_page_templates.php.
| Accessed via __('landing_page_templates.<key>.<field>').
|
| The Filament "Use template" Select on the LandingPage list
| page falls back to the English copy in config when a key is
| missing, mirroring App\Support\Currency::label() and
| PlanService::translateDisplay().
*/

return [

    'saas' => [
        'name'        => 'SaaS Product',
        'description' => '7-section conversion-focused page: hero + social proof + features + stats + testimonials + pricing + FAQ + final CTA.',
    ],

    'lead_capture' => [
        'name'        => 'Lead Magnet (Ebook)',
        'description' => 'Hero + inline form + 3 benefit highlights + social proof + FAQ. Ideal for ebook, webinar, or whitepaper gating.',
    ],

    'product_launch' => [
        'name'        => 'Product Launch',
        'description' => '6-section announcement page: hero + gallery + feature highlights + testimonials + pricing + CTA. Perfect for a new release or feature drop.',
    ],

];
