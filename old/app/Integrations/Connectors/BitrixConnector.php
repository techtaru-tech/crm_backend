<?php

namespace App\Integrations\Connectors;

use App\Integrations\BaseConnector;
use App\Models\Lead;
use Illuminate\Support\Facades\Http;

class BitrixConnector extends BaseConnector
{
    private function webhookUrl(): string
    {
        return rtrim($this->config['webhook_url'] ?? '', '/');
    }

    public function testConnection(): bool
    {
        try {
            $res = Http::timeout(10)->get("{$this->webhookUrl()}/crm.lead.list.json", ['start' => 0]);
            return $res->successful();
        } catch (\Throwable) { return false; }
    }

    public function pushLead(Lead $lead, array $fieldMapping = [], array $filterConfig = []): array
    {
        if (! $this->leadsFilter($lead, $filterConfig)) return ['skipped' => true];

        $fields = $this->applyFieldMapping([
            'NAME'          => $lead->first_name,
            'LAST_NAME'     => $lead->last_name,
            'EMAIL'         => [['VALUE' => $lead->email, 'VALUE_TYPE' => 'WORK']],
            'PHONE'         => $lead->phone ? [['VALUE' => $lead->phone, 'VALUE_TYPE' => 'WORK']] : [],
            'COMPANY_TITLE' => $lead->company,
            'SOURCE_ID'     => 'OTHER',
            'STATUS_ID'     => 'NEW',
        ], $lead, $fieldMapping);
        $res = Http::timeout(15)->post("{$this->webhookUrl()}/crm.lead.add.json", ['fields' => $fields]);

        return ['status_code' => $res->status(), 'body' => $res->json()];
    }
}
