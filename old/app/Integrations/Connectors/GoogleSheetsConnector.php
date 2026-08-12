<?php

namespace App\Integrations\Connectors;

use App\Integrations\BaseConnector;
use App\Models\Lead;
use Illuminate\Support\Facades\Http;

class GoogleSheetsConnector extends BaseConnector
{
    private string $baseUrl = 'https://sheets.googleapis.com/v4/spreadsheets';

    private function headers(): array
    {
        return ['Authorization' => 'Bearer ' . ($this->config['access_token'] ?? '')];
    }

    public function testConnection(): bool
    {
        try {
            $spreadsheetId = $this->config['spreadsheet_id'] ?? '';
            if (empty($spreadsheetId)) return false;
            $res = Http::withHeaders($this->headers())->timeout(10)
                ->get("{$this->baseUrl}/{$spreadsheetId}");
            return $res->successful();
        } catch (\Throwable) { return false; }
    }

    public function pushLead(Lead $lead, array $fieldMapping = [], array $filterConfig = []): array
    {
        if (! $this->leadsFilter($lead, $filterConfig)) return ['skipped' => true];

        $spreadsheetId = $this->config['spreadsheet_id'] ?? '';
        $sheetName     = $this->config['sheet_name'] ?? 'Sheet1';
        if (empty($spreadsheetId)) throw new \RuntimeException(self::tx('integration_connectors.not_configured.google_sheets', 'Google Sheets spreadsheet ID not configured.'));

        $payload = $this->leadToPayload($lead, $fieldMapping);
        $values  = array_values($payload);

        $res = Http::withHeaders($this->headers())->timeout(15)
            ->withQueryParameters(['valueInputOption' => 'RAW', 'insertDataOption' => 'INSERT_ROWS'])
            ->post("{$this->baseUrl}/{$spreadsheetId}/values/{$sheetName}:append", [
                'range'          => $sheetName,
                'majorDimension' => 'ROWS',
                'values'         => [$values],
            ]);

        return ['status_code' => $res->status(), 'body' => $res->json()];
    }
}
