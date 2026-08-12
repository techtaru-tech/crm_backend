<?php

use Spatie\LaravelSettings\Migrations\SettingsMigration;

return new class extends SettingsMigration
{
    public function up(): void
    {
        $this->migrator->add('landing.hero_eyebrow', null);
        $this->migrator->add('landing.hero_headline', null);
        $this->migrator->add('landing.hero_headline_highlight', null);
        $this->migrator->add('landing.hero_subtext', null);
        $this->migrator->add('landing.hero_cta_label', null);
        $this->migrator->add('landing.hero_note', null);

        $this->migrator->add('landing.features_headline', null);
        $this->migrator->add('landing.features_subtext', null);
        $this->migrator->add('landing.features', [
            ['title' => 'Smart lead capture',        'body' => 'Embed forms, widgets, or connect inbound email & webhooks. Every lead lands in one unified inbox.'],
            ['title' => 'Pipelines & automations',   'body' => 'Drag cards between stages, auto-assign reps, fire follow-up sequences — no code, no nonsense.'],
            ['title' => 'Reports that matter',       'body' => 'Source attribution, conversion funnels, rep performance — scheduled to your inbox on autopilot.'],
            ['title' => 'Security you can audit',    'body' => '2FA, session controls, full audit trail, tenant isolation & SSO-ready.'],
            ['title' => 'API & integrations',        'body' => 'REST API, outbound webhooks, plus native connectors for the tools your team already uses.'],
            ['title' => 'White-label ready',         'body' => 'Your logo, your colors, your custom domain. Resell to clients or run a multi-brand shop.'],
        ]);

        $this->migrator->add('landing.pricing_headline', null);
        $this->migrator->add('landing.pricing_subtext', null);

        $this->migrator->add('landing.cta_headline', null);
        $this->migrator->add('landing.cta_subtext', null);
    }
};
