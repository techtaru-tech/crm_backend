<?php

namespace App\Integrations\Connectors;

use App\Integrations\BaseConnector;
use App\Models\Lead;
use Illuminate\Support\Facades\Http;

class MondayConnector extends BaseConnector
{
    private string $apiUrl = 'https://api.monday.com/v2';

    private function headers(): array
    {
        return ['Authorization' => $this->config['api_key'] ?? ''];
    }

    public function testConnection(): bool
    {
        try {
            $res = Http::withHeaders($this->headers())->timeout(10)
                ->post($this->apiUrl, ['query' => '{ me { name } }']);
            return $res->successful() && isset($res->json()['data']['me']);
        } catch (\Throwable) { return false; }
    }

    public function pushLead(Lead $lead, array $fieldMapping = [], array $filterConfig = []): array
    {
        if (! $this->leadsFilter($lead, $filterConfig)) return ['skipped' => true];

        $boardId = $this->config['board_id'] ?? '';
        if (empty($boardId)) throw new \RuntimeException(self::tx('integration_connectors.not_configured.monday', 'Monday.com board ID not configured.'));

        $name    = trim("{$lead->first_name} {$lead->last_name}");
        $columns = $this->applyFieldMapping([
            'email' => ['email' => $lead->email, 'text' => $lead->email],
            'phone' => ['phone' => $lead->phone ?? '', 'countryShortName' => 'US'],
            'text'  => $lead->company ?? '',
        ], $lead, $fieldMapping);
        $colVals = json_encode($columns);
        $query = 'mutation { create_item(board_id: ' . $boardId . ', item_name: "' . addslashes($name) . '", column_values: ' . json_encode($colVals) . ') { id } }';
        $res = Http::withHeaders($this->headers())->timeout(15)->post($this->apiUrl, ['query' => $query]);

        return ['status_code' => $res->status(), 'body' => $res->json()];
    }
}
