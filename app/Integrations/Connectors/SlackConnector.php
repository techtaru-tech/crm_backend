<?php

namespace App\Integrations\Connectors;

use App\Integrations\BaseConnector;
use App\Models\Lead;
use Illuminate\Support\Facades\Http;

class SlackConnector extends BaseConnector
{
    public function testConnection(): bool
    {
        $url = $this->config['webhook_url'] ?? '';
        if (empty($url)) return false;
        try {
            $res = Http::timeout(10)->post($url, ['text' => __('integration_messaging_templates.slack.test_message')]);
            return $res->successful();
        } catch (\Throwable) { return false; }
    }

    public function pushLead(Lead $lead, array $fieldMapping = [], array $filterConfig = []): array
    {
        if (! $this->leadsFilter($lead, $filterConfig)) return ['skipped' => true];

        $url = $this->config['webhook_url'] ?? '';
        if (empty($url)) throw new \RuntimeException(self::tx('integration_connectors.not_configured.slack', 'Slack webhook URL not configured.'));

        $template = $this->config['message_template'] ?? null;
        if ($template) {
            $text = $this->interpolateTemplate($template, $lead);
        } else {
            $pipeline = $lead->pipeline?->name ?? '—';
            $stage    = $lead->pipelineStage?->name ?? '—';
            $leadName = trim(($lead->first_name ?? '') . ' ' . ($lead->last_name ?? ''));
            $text = __('integration_messaging_templates.slack.default_template', [
                'name'     => $leadName,
                'email'    => (string) ($lead->email ?? ''),
                'phone'    => (string) ($lead->phone ?? ''),
                'source'   => (string) ($lead->source ?? ''),
                'score'    => (string) $lead->lead_score,
                'pipeline' => $pipeline,
                'stage'    => $stage,
            ]);
        }

        $res = Http::timeout(15)->post($url, ['text' => $text]);
        return ['status_code' => $res->status(), 'body' => $res->body()];
    }
}
