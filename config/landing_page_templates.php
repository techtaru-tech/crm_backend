<?php

/*
 * Starter templates for the Landing Page Builder.
 *
 * Each template is a production-quality landing page out of the box:
 * realistic copy, structured sections, benefit-led headlines, social
 * proof, clear CTAs. The tenant edits what they want and ships.
 */

return [

    'saas' => [
        'name'        => 'SaaS Product',
        'description' => '7-section conversion-focused page: hero + social proof + features + stats + testimonials + pricing + FAQ + final CTA.',
        'icon'        => 'heroicon-o-sparkles',
        'sections'    => [
            [
                'type'    => 'hero',
                'content' => [
                    'eyebrow'      => '✨ Now with AI-powered workflows',
                    'headline'     => 'Close more deals. Chase fewer.',
                    'subheadline'  => 'The all-in-one workspace that turns scattered leads into paying customers. 10× faster onboarding than HubSpot. Half the price.',
                    'cta_label'    => 'Start 14-day free trial',
                    'cta_url'      => '#pricing',
                    'image_url'    => '',
                    'alignment'    => 'center',
                    'background'   => 'gradient',
                ],
            ],
            [
                'type'    => 'features',
                'content' => [
                    'headline'    => 'Built for teams that move fast',
                    'subheadline' => 'Every feature you\'d need, none of the bloat. Ship revenue in your first week, not your first quarter.',
                    'items'       => [
                        ['title' => 'Unified inbox',      'body' => 'Email, WhatsApp, SMS and social DMs in one timeline per lead. Never lose a reply again.', 'icon' => 'inbox'],
                        ['title' => 'Smart automations', 'body' => 'Drag-and-drop flows that replace Zapier. If/then rules, drip sequences, lead routing — all native.', 'icon' => 'bolt'],
                        ['title' => 'Revenue reports',    'body' => 'Weighted forecasts, pipeline bottlenecks, rep leaderboards. Metrics your CFO actually trusts.', 'icon' => 'chart'],
                        ['title' => 'AI lead scoring',    'body' => 'Every lead gets a score you can explain. Rule-based, transparent, and tunable to your playbook.', 'icon' => 'sparkles'],
                        ['title' => '19 lead sources',    'body' => 'Meta, Google Ads, TikTok, LinkedIn, Calendly, web forms, IMAP — connected in 60 seconds.', 'icon' => 'globe'],
                        ['title' => 'White-label ready',  'body' => 'Your brand, your domain, your pricing. Resell to clients from day one with zero fees.', 'icon' => 'tag'],
                    ],
                ],
            ],
            [
                'type'    => 'testimonials',
                'content' => [
                    'headline' => 'Trusted by 2,400+ teams',
                    'items'    => [
                        [
                            'quote'      => 'Replaced HubSpot, Calendly and Mailchimp with one tool. Cut our sales-ops cost by 80% the first quarter.',
                            'author'     => 'Sarah Chen',
                            'role'       => 'VP Sales, Northwind Growth',
                            // Avatar URLs in seed data are omitted on purpose — the
                            // testimonials view falls back to a locally-rendered initials
                            // avatar so the app never reaches out to an external CDN.
                            'avatar_url' => null,
                        ],
                        [
                            'quote'      => 'The automation engine alone pays for itself every month. We haven\'t manually routed a lead in 6 months.',
                            'author'     => 'Marcus Tanaka',
                            'role'       => 'Founder, Loop Agency',
                            'avatar_url' => null,
                        ],
                        [
                            'quote'      => 'Set it up Friday night, had my first closed deal by Tuesday. The onboarding is genuinely the best I\'ve ever used.',
                            'author'     => 'Priya Ramanathan',
                            'role'       => 'Co-founder, Clearpath Capital',
                            'avatar_url' => null,
                        ],
                    ],
                ],
            ],
            [
                'type'    => 'pricing',
                'content' => [
                    'headline'    => 'Honest pricing. No seat games.',
                    'subheadline' => 'Pay for the workspace, not per seat. Unlimited users on every plan.',
                    'plans'       => [
                        [
                            'name'     => 'Starter',
                            'price'    => '$29',
                            'interval' => 'mo',
                            'features' => [
                                'Unlimited users',
                                '500 leads / month',
                                '3 pipelines',
                                'Email + web forms',
                                'Basic automations',
                            ],
                            'cta_label' => 'Start free',
                            'cta_url'   => '/register',
                            'highlight' => false,
                        ],
                        [
                            'name'     => 'Growth',
                            'price'    => '$79',
                            'interval' => 'mo',
                            'features' => [
                                'Everything in Starter',
                                '5,000 leads / month',
                                'Unlimited pipelines',
                                'All 19 lead sources',
                                'AI enrichment + scoring',
                                'Scheduled reports',
                                'API access',
                            ],
                            'cta_label' => 'Start free',
                            'cta_url'   => '/register',
                            'highlight' => true,
                        ],
                        [
                            'name'     => 'Scale',
                            'price'    => '$199',
                            'interval' => 'mo',
                            'features' => [
                                'Everything in Growth',
                                'Unlimited leads',
                                'White-label + custom domains',
                                'Dedicated onboarding',
                                'SLA + priority support',
                                'SOC 2 documentation',
                            ],
                            'cta_label' => 'Talk to sales',
                            'cta_url'   => 'mailto:sales@example.com',
                            'highlight' => false,
                        ],
                    ],
                ],
            ],
            [
                'type'    => 'faq',
                'content' => [
                    'headline' => 'Common questions',
                    'items'    => [
                        ['question' => 'How is this different from HubSpot?',               'answer' => 'HubSpot charges per contact and gates core features behind enterprise plans. We charge flat, unlimited users, every feature on every plan. 80% of the feature set at 20% of the lifetime cost.'],
                        ['question' => 'Do I need a developer to set it up?',              'answer' => 'No. Install is a 60-second zip upload. OAuth connects to Meta / Google / LinkedIn / WhatsApp in one click. The automation builder is drag-and-drop — no code.'],
                        ['question' => 'Can I migrate from HubSpot / Pipedrive / Zoho?',   'answer' => 'Yes. CSV import with column mapping is built in, and we have a free migration service for Growth/Scale plans. Most teams are fully migrated within 48 hours.'],
                        ['question' => 'What about my data?',                               'answer' => 'You own it. One-click export, GDPR-compliant, SOC 2 documentation available on request. Self-hosted version available for regulated industries.'],
                        ['question' => 'Is there a free trial?',                           'answer' => '14 days, full access, no credit card. If you\'re not converting more leads by day 14, walk away — no questions asked.'],
                    ],
                ],
            ],
            [
                'type'    => 'cta',
                'content' => [
                    'headline'     => 'Ship revenue this week',
                    'body'         => 'Free trial. No credit card. Cancel any time.',
                    'button_label' => 'Create your workspace →',
                    'button_url'   => '/register',
                    'background'   => 'indigo',
                ],
            ],
        ],
    ],

    'lead_capture' => [
        'name'        => 'Lead Magnet (Ebook)',
        'description' => 'Hero + inline form + 3 benefit highlights + social proof + FAQ. Ideal for ebook, webinar, or whitepaper gating.',
        'icon'        => 'heroicon-o-document-arrow-down',
        'sections'    => [
            [
                'type'    => 'hero',
                'content' => [
                    'eyebrow'      => 'Free guide',
                    'headline'     => 'The 2026 Playbook: 47 tactics top performers use to close 2× more deals',
                    'subheadline'  => '42 pages. Real data from 500+ sales teams. No fluff, no email drip you didn\'t ask for — just the playbook.',
                    'cta_label'    => 'Get the free guide',
                    'cta_url'      => '#form',
                    'image_url'    => '',
                    'alignment'    => 'left',
                    'background'   => 'solid',
                ],
            ],
            [
                'type'    => 'features',
                'content' => [
                    'headline'    => 'What you\'ll learn',
                    'subheadline' => 'Every chapter ends with a one-page tactic you can implement today.',
                    'items'       => [
                        ['title' => 'The 5-stage discovery call',  'body' => 'The framework top AEs use to qualify leads in 12 minutes flat. Includes the exact questions + objection map.', 'icon' => 'chat'],
                        ['title' => 'Pricing psychology that converts', 'body' => 'Why anchoring, decoys and price ladders win. 9 A/B tests with real SaaS conversion data.', 'icon' => 'tag'],
                        ['title' => 'Automations that 3× response rate', 'body' => 'The 4-step sequence that won our team the 2025 Outreach Trophy — template included.', 'icon' => 'bolt'],
                    ],
                ],
            ],
            [
                'type'    => 'testimonials',
                'content' => [
                    'headline' => 'What readers say',
                    'items'    => [
                        [
                            'quote'      => 'More actionable tactics in chapter 3 than 5 years of sales conferences.',
                            'author'     => 'Elena Martinez',
                            'role'       => 'Head of Sales, Bright Loop',
                            'avatar_url' => null,
                        ],
                        [
                            'quote'      => 'Implemented one framework from chapter 7. Win rate jumped 38% in a month.',
                            'author'     => 'James Okonkwo',
                            'role'       => 'AE, Quanta Cloud',
                            'avatar_url' => null,
                        ],
                    ],
                ],
            ],
            [
                'type'    => 'form',
                'content' => [
                    'headline'        => 'Send me the guide',
                    'subheadline'     => 'One email with the PDF. No drip sequence. Unsubscribe any time.',
                    'form_id'         => null,
                    'success_message' => 'Check your inbox — the guide is on its way.',
                ],
            ],
            [
                'type'    => 'faq',
                'content' => [
                    'headline' => 'Before you download',
                    'items'    => [
                        ['question' => 'Is it really free?',       'answer' => 'Yes. One email with the PDF. No credit card. No upsell sequence.'],
                        ['question' => 'Who wrote it?',             'answer' => 'Our team plus 6 guest contributors — VPs of Sales at leading SMB SaaS companies.'],
                        ['question' => 'Will I get sales emails?', 'answer' => 'We send one launch note per quarter, max. Tick the box to opt-in; we don\'t auto-enroll.'],
                    ],
                ],
            ],
        ],
    ],

    'product_launch' => [
        'name'        => 'Product Launch',
        'description' => '6-section announcement page: hero + gallery + feature highlights + testimonials + pricing + CTA. Perfect for a new release or feature drop.',
        'icon'        => 'heroicon-o-rocket-launch',
        'sections'    => [
            [
                'type'    => 'hero',
                'content' => [
                    'eyebrow'      => '🚀 Now live',
                    'headline'     => 'Say hello to v3.',
                    'subheadline'  => 'Faster. Lighter. And genuinely fun to use. The biggest release we\'ve ever shipped.',
                    'cta_label'    => 'See what\'s new',
                    'cta_url'      => '#gallery',
                    'image_url'    => '',
                    'alignment'    => 'center',
                    'background'   => 'gradient',
                ],
            ],
            [
                'type'    => 'gallery',
                'content' => [
                    'headline' => 'A closer look',
                    'items'    => [
                        ['image_url' => '', 'caption' => 'Redesigned dashboard — the metrics you actually watch, finally on one screen.'],
                        ['image_url' => '', 'caption' => 'Command palette (⌘K) — jump to any lead, report or setting in two keystrokes.'],
                        ['image_url' => '', 'caption' => 'Mobile-first capture — snap a business card, leave the rest to us.'],
                    ],
                ],
            ],
            [
                'type'    => 'features',
                'content' => [
                    'headline'    => 'What\'s actually different',
                    'subheadline' => 'Not just a reskin. Three years of listening to you, distilled.',
                    'items'       => [
                        ['title' => '10× faster page loads',    'body' => 'Every view renders in under 200ms. We rebuilt the core on streaming partial hydration.',        'icon' => 'bolt'],
                        ['title' => 'Keyboard-first everywhere', 'body' => 'Every button has a shortcut. Pro power-user muscle memory from HubSpot to Notion, baked in.', 'icon' => 'check'],
                        ['title' => 'AI that writes with you',  'body' => 'Drafts emails, summarizes calls, suggests the next play. Off by default — you\'re in control.',  'icon' => 'sparkles'],
                    ],
                ],
            ],
            [
                'type'    => 'testimonials',
                'content' => [
                    'headline' => 'Early access reactions',
                    'items'    => [
                        [
                            'quote'      => 'It feels like switching from a 2018 Android to a brand-new iPhone. Night and day.',
                            'author'     => 'Dani Volkov',
                            'role'       => 'Head of Revenue, Aperture',
                            'avatar_url' => null,
                        ],
                        [
                            'quote'      => 'The command palette alone is worth the price. I haven\'t touched my mouse in a week.',
                            'author'     => 'Lukas Meyer',
                            'role'       => 'Sales Ops, Halo Metrics',
                            'avatar_url' => null,
                        ],
                    ],
                ],
            ],
            [
                'type'    => 'cta',
                'content' => [
                    'headline'     => 'Ready for v3?',
                    'body'         => 'Existing customers get v3 automatically on April 30th. New here?',
                    'button_label' => 'Start free trial →',
                    'button_url'   => '/register',
                    'background'   => 'indigo',
                ],
            ],
        ],
    ],

];
