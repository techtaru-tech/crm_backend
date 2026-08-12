<?php

declare(strict_types=1);

namespace Database\Seeders;

use App\Models\EmailTemplate;
use App\Models\Tenant;
use Illuminate\Database\Seeder;

/**
 * STARTER CONTENT — buyers replace this from the admin panel.
 * English defaults intentionally kept literal; the SA editor lets
 * buyers paste any locale's content as plain text.
 *
 * Starter email templates for every tenant.
 *
 * The schema requires tenant_id NOT NULL (and BelongsToTenant fails
 * closed on null), so we can't use a single global "system" row that
 * every tenant reads through.  Instead we per-tenant-clone the default
 * set into any tenant that has no templates yet — idempotent on
 * (tenant_id, name), and gated by an emptiness check so existing
 * tenants who already curated their own template list aren't
 * disturbed.
 *
 * Called from DatabaseSeeder once.  When a new tenant is provisioned
 * after this initial seed, the tenant-creation flow should call
 * seedFor($tenant) directly to copy the starter set in.
 */
class DefaultEmailTemplatesSeeder extends Seeder
{
    /**
     * @return list<array{name:string, subject:string, body_html:string, body_text:string}>
     */
    public static function templates(): array
    {
        return [
            [
                'name'      => 'Welcome — new lead',
                'subject'   => 'Welcome, {{lead.first_name}}',
                'body_html' => "<p>Hi {{lead.first_name}},</p>\n<p>Thanks for getting in touch. Someone from our team will be in contact with you shortly.</p>",
                'body_text' => "Hi {{lead.first_name}},\n\nThanks for getting in touch. Someone from our team will be in contact with you shortly.",
            ],
            [
                'name'      => 'Follow-up — no reply',
                'subject'   => 'Following up, {{lead.first_name}}',
                'body_html' => "<p>Hi {{lead.first_name}},</p>\n<p>Just checking in to see if you had any questions about our last conversation.</p>",
                'body_text' => "Hi {{lead.first_name}},\n\nJust checking in to see if you had any questions about our last conversation.",
            ],
            [
                'name'      => 'Quote sent',
                'subject'   => 'Your quote from {{lead.company}}',
                'body_html' => "<p>Hi {{lead.first_name}},</p>\n<p>Your quote is attached. Let us know if anything looks off.</p>",
                'body_text' => "Hi {{lead.first_name}},\n\nYour quote is attached. Let us know if anything looks off.",
            ],
            [
                'name'      => 'Meeting request',
                'subject'   => 'Got a few minutes this week?',
                'body_html' => "<p>Hi {{lead.first_name}},</p>\n<p>I'd love to set up a quick call to walk you through how we can help. Would Tuesday or Thursday work better for you?</p>",
                'body_text' => "Hi {{lead.first_name}},\n\nI'd love to set up a quick call to walk you through how we can help. Would Tuesday or Thursday work better for you?",
            ],
        ];
    }

    /**
     * Seed the default template set into the given tenant.  Idempotent —
     * existing rows with the same (tenant_id, name) are left alone.
     */
    public static function seedFor(Tenant $tenant): void
    {
        foreach (self::templates() as $tpl) {
            EmailTemplate::firstOrCreate(
                ['tenant_id' => $tenant->id, 'name' => $tpl['name']],
                [
                    'subject'   => $tpl['subject'],
                    'body_html' => $tpl['body_html'],
                    'body_text' => $tpl['body_text'],
                ],
            );
        }
    }

    public function run(): void
    {
        // Iterate every tenant.  withoutGlobalScope('tenant') because
        // there's no current_tenant binding in a php artisan db:seed run.
        Tenant::withoutGlobalScope('tenant')->chunk(100, function ($tenants) {
            foreach ($tenants as $tenant) {
                self::seedFor($tenant);
            }
        });
    }
}
