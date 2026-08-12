<?php

declare(strict_types=1);

namespace Database\Seeders;

use App\Models\SharedTemplate;
use App\Models\Tenant;
use Illuminate\Database\Seeder;

/**
 * Seeds a small set of curated, free, public starter templates into the
 * marketplace (shared_templates) so /admin/marketplace is functional out of
 * the box instead of empty.
 *
 * Why: the marketplace UI (MarketplacePage) lists every is_public row, but
 * nothing in the app populates that table — no contribute UI, no other
 * seeder — so buyers and the demo both saw an empty marketplace.
 *
 * Notes:
 *   - Idempotent: keyed on (type, name) via firstOrCreate, so re-runs and
 *     in-app re-seeds never duplicate.
 *   - Owned by the first tenant. The browse list is owner-agnostic (filters
 *     on is_public only), so every workspace sees and can install these.
 *     shared_templates.owner_tenant_id is NOT NULL with an FK, so a real
 *     tenant must own them; if none exists yet, seeding is skipped.
 *   - Payloads use the exact keys MarketplaceInstaller reads, so each
 *     template installs cleanly — verified by MarketplaceTemplatesSeederTest.
 */
class MarketplaceTemplatesSeeder extends Seeder
{
    public function run(): void
    {
        $owner = Tenant::query()->oldest('id')->first();
        if (! $owner) {
            return; // No tenant to attribute curated templates to yet.
        }

        foreach ($this->templates() as $tpl) {
            SharedTemplate::query()->firstOrCreate(
                ['type' => $tpl['type'], 'name' => $tpl['name']],
                [
                    'owner_tenant_id' => $owner->id,
                    'description'     => $tpl['description'],
                    'category'        => $tpl['category'],
                    'payload'         => $tpl['payload'],
                    'price'           => 0,
                    'currency'        => 'USD',
                    'is_public'       => true,
                    'is_featured'     => $tpl['is_featured'] ?? false,
                ],
            );
        }
    }

    /** @return array<int, array<string, mixed>> */
    private function templates(): array
    {
        return [
            [
                'type'        => SharedTemplate::TYPE_AUTOMATION,
                'name'        => 'Welcome New Leads',
                'category'    => 'Automation',
                'is_featured' => true,
                'description' => 'Tag every new lead and send a branded welcome email the moment they arrive.',
                'payload'     => [
                    'name'    => 'Welcome New Leads',
                    'trigger' => 'lead_created',
                    'steps'   => [
                        ['type' => 'action', 'config' => ['action_type' => 'add_tag', 'tag' => 'new']],
                        ['type' => 'action', 'config' => [
                            'action_type' => 'send_email',
                            'subject'     => 'Thanks for reaching out',
                            'body'        => 'Hi {{lead.first_name}}, thanks for getting in touch — we will be in contact shortly.',
                        ]],
                    ],
                ],
            ],
            [
                'type'        => SharedTemplate::TYPE_FORM,
                'name'        => 'Contact Us',
                'category'    => 'Forms',
                'is_featured' => true,
                'description' => 'A clean three-field contact form ready to embed on any site or landing page.',
                'payload'     => [
                    'name'            => 'Contact Us',
                    'success_message' => 'Thanks! We received your message and will reply soon.',
                    'fields'          => [
                        ['field_key' => 'name',    'label' => 'Your name',        'type' => 'text',     'required' => true],
                        ['field_key' => 'email',   'label' => 'Email address',    'type' => 'email',    'required' => true],
                        ['field_key' => 'message', 'label' => 'How can we help?', 'type' => 'textarea', 'required' => false],
                    ],
                ],
            ],
            [
                'type'        => SharedTemplate::TYPE_PIPELINE,
                'name'        => 'B2B Sales Pipeline',
                'category'    => 'Sales',
                'description' => 'A six-stage B2B sales pipeline with sensible default win probabilities.',
                'payload'     => [
                    'name'   => 'B2B Sales Pipeline',
                    'stages' => [
                        ['name' => 'New',       'color' => '#94a3b8', 'win_probability' => 10],
                        ['name' => 'Contacted', 'color' => '#60a5fa', 'win_probability' => 25],
                        ['name' => 'Qualified', 'color' => '#a855f7', 'win_probability' => 50],
                        ['name' => 'Proposal',  'color' => '#f59e0b', 'win_probability' => 75],
                        ['name' => 'Won',       'color' => '#10b981', 'win_probability' => 100, 'is_won'  => true],
                        ['name' => 'Lost',      'color' => '#ef4444', 'win_probability' => 0,   'is_lost' => true],
                    ],
                ],
            ],
            [
                'type'        => SharedTemplate::TYPE_EMAIL_SEQUENCE,
                'name'        => '3-Day Onboarding Drip',
                'category'    => 'Onboarding',
                'description' => 'A three-email onboarding sequence that nurtures a new lead over their first days.',
                'payload'     => [
                    'name'          => '3-Day Onboarding Drip',
                    'stop_on_reply' => true,
                    'stop_on_won'   => true,
                    'steps'         => [
                        ['delay_days' => 0, 'subject' => 'Welcome aboard',             'body_html' => '<p>Great to have you. Here is how to get started.</p>'],
                        ['delay_days' => 1, 'subject' => 'A quick tip',                'body_html' => '<p>Here is one thing most customers love on day one.</p>'],
                        ['delay_days' => 3, 'subject' => 'Anything we can help with?', 'body_html' => '<p>Reply any time — we are here to help.</p>'],
                    ],
                ],
            ],
        ];
    }
}
