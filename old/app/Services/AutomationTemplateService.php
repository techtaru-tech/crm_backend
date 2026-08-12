<?php

namespace App\Services;

use App\Models\Automation;
use App\Models\AutomationStep;
use Illuminate\Support\Facades\DB;

class AutomationTemplateService
{
    /**
     * Return all curated templates from config/automation_templates.php
     * with display fields (name, description) routed through the
     * translator using Pattern B (translator-first-with-fallback).
     *
     * When lang/<locale>/automation_templates.<key>.<field> is defined
     * the localized value is used; otherwise the literal config value is
     * returned so unknown / buyer-added template keys keep working.
     *
     * @return array<int, array<string, mixed>>
     */
    public function templates(): array
    {
        $templates = config('automation_templates', []);

        foreach ($templates as &$tpl) {
            $key = $tpl['key'] ?? null;
            if (! is_string($key) || $key === '') {
                continue;
            }
            $tpl['name']        = $this->translateField($key, 'name', $tpl['name'] ?? null);
            $tpl['description'] = $this->translateField($key, 'description', $tpl['description'] ?? null);
        }
        unset($tpl);

        return $templates;
    }

    public function find(string $key): ?array
    {
        foreach ($this->templates() as $tpl) {
            if (($tpl['key'] ?? null) === $key) {
                return $tpl;
            }
        }
        return null;
    }

    /**
     * Translator-first lookup for a template display field.  Mirrors the
     * pattern used by PlanService::translateDisplay and Currency::label().
     */
    protected function translateField(string $templateKey, string $field, ?string $fallback): ?string
    {
        $langKey    = "automation_templates.{$templateKey}.{$field}";
        $translated = __($langKey);

        if (is_string($translated) && $translated !== $langKey) {
            return $translated;
        }

        return $fallback;
    }

    /**
     * Create an Automation + AutomationSteps for the given tenant from a template.
     */
    public function install(string $key, int $tenantId): Automation
    {
        $template = $this->find($key);
        if (! $template) {
            throw new \InvalidArgumentException(__('services/automation_template.unknown_template', ['key' => $key]));
        }

        return DB::transaction(function () use ($template, $tenantId) {
            $automation = Automation::create([
                'tenant_id'      => $tenantId,
                'name'           => $template['name'],
                'description'    => $template['description'] ?? null,
                'enabled'        => false, // install disabled so the tenant can review first
                'trigger_type'   => $template['trigger_type'],
                'trigger_config' => $template['trigger_config'] ?? [],
            ]);

            foreach (($template['steps'] ?? []) as $index => $step) {
                AutomationStep::create([
                    'automation_id' => $automation->id,
                    'type'          => $step['type'],
                    'config'        => $step['config'] ?? [],
                    'sort_order'    => $index,
                ]);
            }

            return $automation;
        });
    }
}
