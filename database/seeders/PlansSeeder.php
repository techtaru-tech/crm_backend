<?php

namespace Database\Seeders;

use App\Models\Plan;
use Illuminate\Database\Seeder;

/**
 * Seeds the plans table from the legacy `config/plans.php` array.
 * Idempotent — uses updateOrCreate on the plan `key`, so running it
 * on an existing install refreshes names/prices without losing any
 * gateway price IDs the operator may have entered.
 */
class PlansSeeder extends Seeder
{
    public function run(): void
    {
        $plans = config('plans.plans', []);

        if (empty($plans)) {
            // Minimal fallback catalog so a brand-new install still has
            // something to sell on day one.
            $plans = $this->fallbackCatalog();
        }

        $i = 0;
        foreach ($plans as $key => $plan) {
            // Pattern B (translator-first-with-fallback): route display
            // fields through __() at INSERT time so the DB row holds the
            // localized string based on the active app locale.  Once
            // stored, Plan::name/description are returned verbatim — no
            // runtime translation — so seeding with the desired locale
            // active is how the buyer gets localized starter content.
            $rawName        = $plan['name']        ?? ucfirst($key);
            $rawDescription = $plan['description'] ?? null;

            Plan::updateOrCreate(
                ['key' => $key],
                [
                    'name'        => self::translatePlanField($key, 'name', $rawName),
                    'description' => self::translatePlanField($key, 'description', $rawDescription),
                    'price'       => (float) ($plan['price'] ?? 0),
                    'currency'    => $plan['currency']    ?? 'USD',
                    'interval'    => $plan['interval']    ?? 'month',
                    'is_public'   => (bool) ($plan['is_public'] ?? true),
                    'is_active'   => (bool) ($plan['is_active'] ?? true),
                    'highlight'   => (bool) ($plan['highlight'] ?? false),
                    'sort_order'  => (int)  ($plan['sort'] ?? $i),
                    'features'    => $plan['features'] ?? [],
                    'limits'      => $plan['limits'] ?? [],
                    'meta'        => $plan['meta'] ?? [],
                ]
            );
            $i++;
        }
    }

    /**
     * Translator-first lookup for a plan display field, mirroring the
     * helper inside PlanService.  Returns the localized value when
     * lang/<locale>/plans.<key>.<field> is defined, otherwise the
     * literal fallback value.
     */
    private static function translatePlanField(string $planKey, string $field, ?string $fallback): ?string
    {
        $langKey    = "plans.{$planKey}.{$field}";
        $translated = __($langKey);

        if (is_string($translated) && $translated !== $langKey) {
            return $translated;
        }

        return $fallback;
    }

    private function fallbackCatalog(): array
    {
        return [
            'trial' => [
                'name'        => 'Free Trial',
                'description' => '14 days free. No card required. Full Professional feature set so teams can evaluate the product before committing.',
                'price'       => 0,
                'sort'        => 0,
                'is_public'   => false,
                'limits'      => [
                    'max_seats'                      => 3,
                    'max_leads'                      => 500,
                    'max_forms'                      => 3,
                    'max_pipelines'                  => 2,
                    'max_automations'                => 3,
                    'max_integrations'               => 2,
                    'max_api_keys'                   => 1,
                    'max_custom_domains'             => 0,
                    'marketplace_installs_per_month' => 3,
                ],
                'features'    => [
                    'automations'         => true,
                    'api_access'          => true,
                    'custom_domain'       => false,
                    'white_label'         => false,
                    'webhooks_outbound'   => true,
                    'reports_advanced'    => false,
                    'priority_support'    => false,
                    'integrations'        => true,
                    'marketplace_install' => true,
                ],
                'meta' => ['trial_days' => 14, 'badge' => 'Free for 14 days'],
            ],
            'starter' => [
                'name'        => 'Starter',
                'description' => 'Built for solo founders and small teams closing their first 100 deals. Everything you need to capture, qualify, and nurture leads end to end.',
                'price'       => 29,
                'sort'        => 1,
                'limits'      => [
                    'max_seats'                      => 5,
                    'max_leads'                      => 2500,
                    'max_forms'                      => 10,
                    'max_pipelines'                  => 3,
                    'max_automations'                => 10,
                    'max_integrations'               => 5,
                    'max_api_keys'                   => 2,
                    'max_custom_domains'             => 1,
                    'marketplace_installs_per_month' => 10,
                ],
                'features'    => [
                    'automations'         => true,
                    'api_access'          => true,
                    'custom_domain'       => true,
                    'white_label'         => false,
                    'webhooks_outbound'   => true,
                    'reports_advanced'    => false,
                    'priority_support'    => false,
                    'integrations'        => true,
                    'marketplace_install' => true,
                ],
                'meta' => ['tagline' => 'For teams up to 5'],
            ],
            'professional' => [
                'name'        => 'Professional',
                'description' => 'For growing sales organizations that need advanced automations, AI-powered insights, and full white-label control — without enterprise-grade overhead.',
                'price'       => 79,
                'sort'        => 2,
                'highlight'   => true,
                'limits'      => [
                    'max_seats'                      => 25,
                    'max_leads'                      => 50000,
                    'max_forms'                      => 50,
                    'max_pipelines'                  => 10,
                    'max_automations'                => 50,
                    'max_integrations'               => 20,
                    'max_api_keys'                   => 10,
                    'max_custom_domains'             => 5,
                    'marketplace_installs_per_month' => 50,
                ],
                'features'    => [
                    'automations'         => true,
                    'api_access'          => true,
                    'custom_domain'       => true,
                    'white_label'         => true,
                    'webhooks_outbound'   => true,
                    'reports_advanced'    => true,
                    'priority_support'    => false,
                    'integrations'        => true,
                    'marketplace_install' => true,
                ],
                'meta' => ['tagline' => 'Most popular', 'badge' => 'Most Popular'],
            ],
            'business' => [
                'name'        => 'Business',
                'description' => 'Scale confidently with priority support, unlimited automations, SSO/SAML sign-in, and custom audit-log retention for regulated industries.',
                'price'       => 199,
                'sort'        => 3,
                'limits'      => [
                    'max_seats'                      => 100,
                    'max_leads'                      => 250000,
                    'max_forms'                      => -1,
                    'max_pipelines'                  => -1,
                    'max_automations'                => -1,
                    'max_integrations'               => -1,
                    'max_api_keys'                   => 50,
                    'max_custom_domains'             => 25,
                    'marketplace_installs_per_month' => 200,
                ],
                'features'    => [
                    'automations'         => true,
                    'api_access'          => true,
                    'custom_domain'       => true,
                    'white_label'         => true,
                    'webhooks_outbound'   => true,
                    'reports_advanced'    => true,
                    'priority_support'    => true,
                    'integrations'        => true,
                    'marketplace_install' => true,
                ],
                'meta' => ['tagline' => 'For 100+ user orgs'],
            ],
            'enterprise' => [
                'name'        => 'Enterprise',
                'description' => 'Tailored for Fortune-grade teams. Unlimited seats, dedicated infrastructure, custom SLA, on-prem or private-cloud deployment, and a named CSM.',
                'price'       => 499,
                'sort'        => 4,
                'limits'      => [
                    'max_seats'                      => -1,
                    'max_leads'                      => -1,
                    'max_forms'                      => -1,
                    'max_pipelines'                  => -1,
                    'max_automations'                => -1,
                    'max_integrations'               => -1,
                    'max_api_keys'                   => -1,
                    'max_custom_domains'             => -1,
                    'marketplace_installs_per_month' => -1,
                ],
                'features'    => [
                    'automations'         => true,
                    'api_access'          => true,
                    'custom_domain'       => true,
                    'white_label'         => true,
                    'webhooks_outbound'   => true,
                    'reports_advanced'    => true,
                    'priority_support'    => true,
                    'integrations'        => true,
                    'marketplace_install' => true,
                ],
                'meta' => ['tagline' => 'Contact sales', 'contact_sales' => true],
            ],
        ];
    }
}
