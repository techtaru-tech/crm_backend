<?php

namespace App\Integrations\Connectors;

use App\Integrations\BaseConnector;
use App\Models\Lead;
use Illuminate\Support\Facades\Http;

class MicrosoftTeamsConnector extends BaseConnector
{
    public function testConnection(): bool
    {
        $url = $this->config['webhook_url'] ?? '';
        if (empty($url)) return false;
        try {
            $res = Http::timeout(10)->post($url, ['text' => __('integration_messaging_templates.teams.test_message')]);
            return $res->successful();
        } catch (\Throwable) { return false; }
    }

    public function pushLead(Lead $lead, array $fieldMapping = [], array $filterConfig = []): array
    {
        if (! $this->leadsFilter($lead, $filterConfig)) return ['skipped' => true];

        $url = $this->config['webhook_url'] ?? '';
        if (empty($url)) throw new \RuntimeException(self::tx('integration_connectors.not_configured.microsoft_teams', 'Microsoft Teams webhook URL not configured.'));

        $template = $this->config['message_template'] ?? null;
        if ($template) {
            $res = Http::timeout(15)->post($url, ['text' => $this->interpolateTemplate($template, $lead)]);
            return ['status_code' => $res->status(), 'body' => $res->body()];
        }

        $pipeline = $lead->pipeline?->name ?? '—';
        $stage    = $lead->pipelineStage?->name ?? '—';
        $leadName = trim(($lead->first_name ?? '') . ' ' . ($lead->last_name ?? ''));
        $card = [
            '@type'      => 'MessageCard',
            '@context'   => 'http://schema.org/extensions',
            'summary'    => __('integration_messaging_templates.teams.summary_new_lead'),
            'themeColor' => '0078D7',
            'title'      => __('integration_messaging_templates.teams.card_title_new_lead', ['name' => $leadName]),
            'sections'   => [[
                'facts' => [
                    ['name' => __('integration_messaging_templates.teams.field_email'),    'value' => $lead->email ?? '—'],
                    ['name' => __('integration_messaging_templates.teams.field_phone'),    'value' => $lead->phone ?? '—'],
                    ['name' => __('integration_messaging_templates.teams.field_source'),   'value' => $lead->source ?? '—'],
                    ['name' => __('integration_messaging_templates.teams.field_score'),    'value' => (string) $lead->lead_score],
                    ['name' => __('integration_messaging_templates.teams.field_pipeline'), 'value' => $pipeline],
                    ['name' => __('integration_messaging_templates.teams.field_stage'),    'value' => $stage],
                ],
            ]],
        ];

        $res = Http::timeout(15)->post($url, $card);
        return ['status_code' => $res->status(), 'body' => $res->body()];
    }
}
