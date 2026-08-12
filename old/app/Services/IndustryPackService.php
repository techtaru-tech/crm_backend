<?php

namespace App\Services;

use App\Models\Automation;
use App\Models\AutomationStep;
use App\Models\CustomFieldDefinition;
use App\Models\EmailTemplate;
use App\Models\Form;
use App\Models\FormField;
use App\Models\Pipeline;
use App\Models\PipelineStage;
use App\Models\Tag;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Str;

/**
 * Loads industry verticalization packs from config/industry_packs/*.php
 * and installs them into a tenant (pipelines, stages, custom fields,
 * tags, email templates, automations, forms).
 */
class IndustryPackService
{
    /**
     * Return all available packs keyed by their 'key' with the pack-level
     * `name` and `description` fields routed through the translator using
     * Pattern B (translator-first-with-fallback).
     *
     * Sub-content (pipeline names, stage names, tag names, email-template
     * subjects/bodies, form labels) is intentionally NOT translated here:
     * once installed those values are saved as tenant-owned rows that
     * buyers edit through the admin panel.  See lang/en/industry_packs.php
     * for the full design rationale.
     *
     * @return array<string, array<string, mixed>>
     */
    public function packs(): array
    {
        $dir = config_path('industry_packs');
        if (! is_dir($dir)) {
            return [];
        }

        $packs = [];
        foreach (File::files($dir) as $file) {
            if ($file->getExtension() !== 'php') {
                continue;
            }
            /** @var array<string, mixed> $pack */
            $pack = require $file->getPathname();
            if (! is_array($pack) || empty($pack['key'])) {
                continue;
            }

            $packKey             = (string) $pack['key'];
            $pack['name']        = $this->translatePackField($packKey, 'name', $pack['name'] ?? null);
            $pack['description'] = $this->translatePackField($packKey, 'description', $pack['description'] ?? null);

            $packs[$packKey] = $pack;
        }

        return $packs;
    }

    /**
     * Translator-first lookup for a pack display field.  Returns the
     * localized string when lang/<locale>/industry_packs.<pack>.<field>
     * is defined, otherwise the literal config value.
     *
     * Mirrors the pattern used by PlanService and Currency::label().
     */
    protected function translatePackField(string $packKey, string $field, ?string $fallback): ?string
    {
        $langKey    = "industry_packs.{$packKey}.{$field}";
        $translated = __($langKey);

        if (is_string($translated) && $translated !== $langKey) {
            return $translated;
        }

        return $fallback;
    }

    public function find(string $key): ?array
    {
        return $this->packs()[$key] ?? null;
    }

    /**
     * Install a pack for the given tenant.
     *
     * $options:
     *   - skip: array of category keys to skip
     *           ('pipelines','custom_fields','tags','email_templates','automations','forms')
     *
     * @return array<string, int>  counts per category
     */
    public function install(string $key, int $tenantId, array $options = []): array
    {
        $pack = $this->find($key);
        if (! $pack) {
            throw new \InvalidArgumentException(__('services/industry_pack.unknown_pack', ['key' => $key]));
        }

        $skip = array_flip($options['skip'] ?? []);

        return DB::transaction(function () use ($pack, $tenantId, $skip) {
            $counts = [
                'pipelines'        => 0,
                'stages'           => 0,
                'custom_fields'    => 0,
                'tags'             => 0,
                'email_templates'  => 0,
                'automations'      => 0,
                'forms'            => 0,
            ];

            if (! isset($skip['pipelines'])) {
                [$pipelineCount, $stageCount] = $this->installPipelines($pack, $tenantId);
                $counts['pipelines'] = $pipelineCount;
                $counts['stages']    = $stageCount;
            }

            if (! isset($skip['custom_fields'])) {
                $counts['custom_fields'] = $this->installCustomFields($pack, $tenantId);
            }

            if (! isset($skip['tags'])) {
                $counts['tags'] = $this->installTags($pack, $tenantId);
            }

            if (! isset($skip['email_templates'])) {
                $counts['email_templates'] = $this->installEmailTemplates($pack, $tenantId);
            }

            if (! isset($skip['automations'])) {
                $counts['automations'] = $this->installAutomations($pack, $tenantId);
            }

            if (! isset($skip['forms'])) {
                $counts['forms'] = $this->installForms($pack, $tenantId);
            }

            return $counts;
        });
    }

    /** @return array{0:int,1:int} */
    private function installPipelines(array $pack, int $tenantId): array
    {
        $pCount = 0;
        $sCount = 0;

        foreach (($pack['pipelines'] ?? []) as $pipelineDef) {
            $pipeline = Pipeline::create([
                'tenant_id'   => $tenantId,
                'name'        => $pipelineDef['name'],
                'description' => $pipelineDef['description'] ?? null,
                'is_default'  => (bool) ($pipelineDef['is_default'] ?? false),
            ]);
            $pCount++;

            foreach (($pipelineDef['stages'] ?? []) as $index => $stageDef) {
                PipelineStage::create([
                    'pipeline_id'     => $pipeline->id,
                    'tenant_id'       => $tenantId,
                    'name'            => $stageDef['name'],
                    'color'           => $stageDef['color'] ?? '#94a3b8',
                    'sort_order'      => $stageDef['sort_order'] ?? $index,
                    'is_won'          => (bool) ($stageDef['is_won']  ?? false),
                    'is_lost'         => (bool) ($stageDef['is_lost'] ?? false),
                    'win_probability' => $stageDef['win_probability'] ?? null,
                ]);
                $sCount++;
            }
        }

        return [$pCount, $sCount];
    }

    private function installCustomFields(array $pack, int $tenantId): int
    {
        $count = 0;
        foreach (($pack['custom_fields'] ?? []) as $index => $fieldDef) {
            $existing = CustomFieldDefinition::query()
                ->where('tenant_id', $tenantId)
                ->where('entity_type', $fieldDef['entity_type'] ?? 'lead')
                ->where('key', $fieldDef['key'])
                ->first();

            if ($existing) {
                continue;
            }

            CustomFieldDefinition::create([
                'tenant_id'   => $tenantId,
                'entity_type' => $fieldDef['entity_type'] ?? 'lead',
                'name'        => $fieldDef['name'],
                'key'         => $fieldDef['key'],
                'field_type'  => $fieldDef['field_type'],
                'options'     => $fieldDef['options']     ?? null,
                'required'    => (bool) ($fieldDef['required']    ?? false),
                'show_in_table'   => (bool) ($fieldDef['show_in_table']   ?? true),
                'show_in_form'    => (bool) ($fieldDef['show_in_form']    ?? true),
                'show_in_filters' => (bool) ($fieldDef['show_in_filters'] ?? true),
                'placeholder' => $fieldDef['placeholder'] ?? null,
                'help_text'   => $fieldDef['help_text']   ?? null,
                'sort_order'  => $fieldDef['sort_order']  ?? $index,
            ]);
            $count++;
        }
        return $count;
    }

    private function installTags(array $pack, int $tenantId): int
    {
        $count = 0;
        foreach (($pack['tags'] ?? []) as $tagDef) {
            $existing = Tag::where('tenant_id', $tenantId)
                ->where('name', $tagDef['name'])
                ->first();
            if ($existing) {
                continue;
            }
            Tag::create([
                'tenant_id' => $tenantId,
                'name'      => $tagDef['name'],
                'color'     => $tagDef['color'] ?? '#3b82f6',
            ]);
            $count++;
        }
        return $count;
    }

    private function installEmailTemplates(array $pack, int $tenantId): int
    {
        $count = 0;
        foreach (($pack['email_templates'] ?? []) as $tplDef) {
            $existing = EmailTemplate::where('tenant_id', $tenantId)
                ->where('name', $tplDef['name'])
                ->first();
            if ($existing) {
                continue;
            }
            EmailTemplate::create([
                'tenant_id' => $tenantId,
                'name'      => $tplDef['name'],
                'subject'   => $tplDef['subject']   ?? '',
                'body_html' => $tplDef['body_html'] ?? '',
                'body_text' => $tplDef['body_text'] ?? null,
            ]);
            $count++;
        }
        return $count;
    }

    private function installAutomations(array $pack, int $tenantId): int
    {
        $count = 0;
        $automationTemplates = config('automation_templates', []);
        $lookup = [];
        foreach ($automationTemplates as $tpl) {
            if (! empty($tpl['key'])) {
                $lookup[$tpl['key']] = $tpl;
            }
        }

        foreach (($pack['automation_templates'] ?? []) as $entry) {
            // Reference by key to a template in config/automation_templates.php
            if (isset($entry['key']) && ! isset($entry['trigger_type'])) {
                if (! isset($lookup[$entry['key']])) {
                    continue;
                }
                $entry = $lookup[$entry['key']];
            }

            $automation = Automation::create([
                'tenant_id'      => $tenantId,
                'name'           => $entry['name'],
                'description'    => $entry['description']    ?? null,
                'enabled'        => false,
                'trigger_type'   => $entry['trigger_type'],
                'trigger_config' => $entry['trigger_config'] ?? [],
            ]);

            foreach (($entry['steps'] ?? []) as $i => $step) {
                AutomationStep::create([
                    'automation_id' => $automation->id,
                    'type'          => $step['type'],
                    'config'        => $step['config'] ?? [],
                    'sort_order'    => $i,
                ]);
            }
            $count++;
        }
        return $count;
    }

    private function installForms(array $pack, int $tenantId): int
    {
        $count = 0;
        foreach (($pack['forms'] ?? []) as $formDef) {
            $slug = $formDef['slug'] ?? Str::slug($formDef['name']);

            $existing = Form::where('tenant_id', $tenantId)
                ->where('slug', $slug)
                ->first();
            if ($existing) {
                continue;
            }

            $form = Form::create([
                'tenant_id'         => $tenantId,
                'name'              => $formDef['name'],
                'slug'              => $slug,
                'title'             => $formDef['title']             ?? $formDef['name'],
                'description'       => $formDef['description']       ?? null,
                'submit_label'      => $formDef['submit_label']      ?? __('services/industry_pack.form_submit_default'),
                'thank_you_message' => $formDef['thank_you_message'] ?? __('services/industry_pack.form_thank_you_default'),
                'active'            => true,
            ]);

            foreach (($formDef['fields'] ?? []) as $i => $fieldDef) {
                FormField::create([
                    'form_id'     => $form->id,
                    'type'        => $fieldDef['type'],
                    'label'       => $fieldDef['label'],
                    'placeholder' => $fieldDef['placeholder'] ?? null,
                    'required'    => (bool) ($fieldDef['required'] ?? false),
                    'field_key'   => $fieldDef['field_key'] ?? Str::slug($fieldDef['label'], '_'),
                    'options'     => $fieldDef['options'] ?? null,
                    'sort_order'  => $fieldDef['sort_order'] ?? $i,
                    'step_number' => $fieldDef['step_number'] ?? 1,
                    'locked'      => (bool) ($fieldDef['locked'] ?? false),
                ]);
            }

            $count++;
        }
        return $count;
    }
}
