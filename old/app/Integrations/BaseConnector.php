<?php

declare(strict_types=1);

namespace App\Integrations;

use App\Integrations\Contracts\IntegrationContract;
use App\Models\Lead;
use Illuminate\Http\Client\Factory as HttpFactory;
use Illuminate\Support\Facades\Http;

abstract class BaseConnector implements IntegrationContract
{
    protected array $config = [];

    /**
     * Translator-first lookup with English fallback. Used by connector
     * subclasses so the CodeCanyon "no hardcoded text" rule is satisfied
     * while keeping the English literals as the canonical source of truth
     * for missing-translation fallback. Mirrors the pattern in
     * App\Integrations\IntegrationRegistry::tx() and
     * App\Services\LeadSources\BaseConnector::tx().
     */
    protected static function tx(string $key, string $fallback): string
    {
        $translated = __($key);
        return $translated === $key ? $fallback : (string) $translated;
    }

    /**
     * Slug used when building per-connector translation keys. Subclasses
     * override this to match the slug used in
     * App\Integrations\IntegrationRegistry::INTEGRATIONS (e.g. 'hubspot',
     * 'zoho_crm', 'activecampaign'). The default value points field
     * labels at the shared `integration_connectors.default.*` namespace
     * used by the BaseConnector's own getAvailableFields() fallback.
     */
    protected function connectorSlug(): string
    {
        return 'default';
    }

    /**
     * Translator-first field label lookup. The label is read from
     * `integration_connectors.<slug>.<field>` and falls back to the
     * supplied English literal when no translation exists. Used by
     * concrete connectors so each getAvailableFields() entry stays a
     * one-line key => fallback pair.
     */
    protected function fieldLabel(string $field, string $fallback): string
    {
        return self::tx(
            'integration_connectors.' . $this->connectorSlug() . '.' . $field,
            $fallback
        );
    }

    public function connect(array $config): void
    {
        $this->config = $config;
    }

    protected function http(): HttpFactory
    {
        return Http::getFacadeRoot();
    }

    protected function leadToPayload(Lead $lead, array $fieldMapping = []): array
    {
        $defaults = [
            'first_name'  => $lead->first_name,
            'last_name'   => $lead->last_name,
            'email'       => $lead->email,
            'phone'       => $lead->phone,
            'source'      => $lead->source,
            'status'      => $lead->status?->value,
            'lead_score'  => $lead->lead_score,
            'company'     => $lead->company,
            'website'     => $lead->website,
            'address'     => $lead->address,
            'city'        => $lead->city,
            'country'     => $lead->country,
            'notes'       => $lead->notes,
            'created_at'  => $lead->created_at?->toIso8601String(),
        ];

        if (empty($fieldMapping)) {
            return $defaults;
        }

        $mapped = [];
        foreach ($fieldMapping as $row) {
            $src = $row['source_field'] ?? null;
            $dst = $row['target_field'] ?? null;
            if ($src && $dst && isset($defaults[$src])) {
                $mapped[$dst] = $defaults[$src];
            }
        }
        return $mapped;
    }

    /**
     * Apply user field mapping on top of a connector's default payload.
     * If user has defined field mapping, it overrides specific keys in $default.
     * If a key in the user mapping exists in $default, it's replaced; new keys are added.
     */
    protected function applyFieldMapping(array $default, Lead $lead, array $fieldMapping): array
    {
        if (empty($fieldMapping)) return $default;

        $src = [
            'first_name' => $lead->first_name,
            'last_name'  => $lead->last_name,
            'email'      => $lead->email,
            'phone'      => $lead->phone,
            'company'    => $lead->company,
            'source'     => $lead->source,
            'status'     => $lead->status?->value,
            'lead_score' => $lead->lead_score,
            'address'    => $lead->address,
            'city'       => $lead->city,
            'country'    => $lead->country,
            'notes'      => $lead->notes,
        ];

        foreach ($fieldMapping as $row) {
            $sourceKey = $row['source_field'] ?? null;
            $targetKey = $row['target_field'] ?? null;
            if ($sourceKey && $targetKey && array_key_exists($sourceKey, $src)) {
                $default[$targetKey] = $src[$sourceKey];
            }
        }

        return $default;
    }

    protected function interpolateTemplate(string $template, Lead $lead): string
    {
        $pipelineName = $lead->pipeline?->name ?? '';
        $stageName    = $lead->pipelineStage?->name ?? '';

        $vars = [
            '{{lead.first_name}}'     => $lead->first_name ?? '',
            '{{lead.last_name}}'      => $lead->last_name ?? '',
            '{{lead.email}}'          => $lead->email ?? '',
            '{{lead.phone}}'          => $lead->phone ?? '',
            '{{lead.company}}'        => $lead->company ?? '',
            '{{lead.source}}'         => $lead->source ?? '',
            '{{lead.status}}'         => $lead->status?->value ?? '',
            '{{lead.lead_score}}'     => (string) ($lead->lead_score ?? 0),
            '{{lead.address}}'        => $lead->address ?? '',
            '{{lead.city}}'           => $lead->city ?? '',
            '{{lead.country}}'        => $lead->country ?? '',
            '{{lead.pipeline}}'       => $pipelineName,
            '{{lead.pipeline_stage}}' => $stageName,
            '{{lead.stage}}'          => $stageName,
        ];
        return str_replace(array_keys($vars), array_values($vars), $template);
    }

    protected function leadsFilter(Lead $lead, array $filterConfig): bool
    {
        if (empty($filterConfig)) return true;

        $sources = $filterConfig['sources'] ?? [];
        if ($sources && ! in_array($lead->source, $sources, true)) return false;

        $tags = $filterConfig['tags'] ?? [];
        if ($tags) {
            $leadTags = $lead->tags->pluck('name')->toArray();
            if (empty(array_intersect($tags, $leadTags))) return false;
        }

        $pipelines = $filterConfig['pipelines'] ?? [];
        if ($pipelines) {
            $leadPipelineId = (string) ($lead->pipeline_id ?? '');
            if (! $leadPipelineId || ! in_array($leadPipelineId, array_map('strval', $pipelines), true)) {
                return false;
            }
        }

        return true;
    }

    public function getAvailableFields(): array
    {
        return [
            'first_name' => $this->fieldLabel('first_name', 'First Name'),
            'last_name'  => $this->fieldLabel('last_name', 'Last Name'),
            'email'      => $this->fieldLabel('email', 'Email'),
            'phone'      => $this->fieldLabel('phone', 'Phone'),
            'company'    => $this->fieldLabel('company', 'Company'),
            'source'     => $this->fieldLabel('source', 'Lead Source'),
            'status'     => $this->fieldLabel('status', 'Status'),
            'lead_score' => $this->fieldLabel('lead_score', 'Lead Score'),
            'address'    => $this->fieldLabel('address', 'Address'),
            'city'       => $this->fieldLabel('city', 'City'),
            'country'    => $this->fieldLabel('country', 'Country'),
            'notes'      => $this->fieldLabel('notes', 'Notes'),
        ];
    }
}
