<?php

namespace Database\Seeders;

use App\Services\AutomationTemplateService;
use Illuminate\Database\Seeder;

/**
 * Installs the curated automation templates for a given tenant. This seeder
 * is NOT run as part of the default seed chain — it is invoked on-demand
 * from the UI (see AutomationTemplateBrowser).
 */
class AutomationTemplatesSeeder extends Seeder
{
    public function run(?int $tenantId = null): void
    {
        if (! $tenantId) {
            $this->command?->warn('No tenant_id provided — skipping automation template install.');
            return;
        }

        $service = app(AutomationTemplateService::class);

        foreach ($service->templates() as $tpl) {
            try {
                $service->install($tpl['key'], $tenantId);
                $this->command?->info("Installed template: {$tpl['name']}");
            } catch (\Throwable $e) {
                $this->command?->warn("Failed to install {$tpl['key']}: {$e->getMessage()}");
            }
        }
    }
}
