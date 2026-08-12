<?php

namespace App\Services\LeadSources;

use App\Models\LeadSourceConnection;
use App\Models\WebhookLog;
use Illuminate\Http\Request;

class JotFormConnector extends BaseConnector
{
    public function verifyWebhook(Request $request, LeadSourceConnection $connection): bool
    {
        return true;
    }

    public function handleWebhook(Request $request, LeadSourceConnection $connection, WebhookLog $log): array
    {
        $body    = $request->all();
        $mapping = $connection->settings['field_mapping'] ?? [];
        $leads   = [];

        $rawRequest  = $body['rawRequest'] ?? null;
        $formFields  = $rawRequest ? json_decode($rawRequest, true) : $body;

        if (! $formFields) {
            return $leads;
        }

        $leadData = [
            'source_id'    => $body['submissionID'] ?? null,
            'first_name'   => null,
            'last_name'    => null,
            'email'        => null,
            'phone'        => null,
            'raw_data'     => $body,
            'custom_fields' => [],
        ];

        foreach ($formFields as $key => $value) {
            if (! is_string($value) && ! is_numeric($value)) {
                continue;
            }

            $mapped = $mapping[$key] ?? null;
            if (! $mapped) {
                $keyLower = strtolower($key);
                if (str_contains($keyLower, 'email')) {
                    $mapped = 'email';
                } elseif (str_contains($keyLower, 'phone') || str_contains($keyLower, 'mobile')) {
                    $mapped = 'phone';
                } elseif (str_contains($keyLower, 'firstname') || $keyLower === 'first') {
                    $mapped = 'first_name';
                } elseif (str_contains($keyLower, 'lastname') || $keyLower === 'last') {
                    $mapped = 'last_name';
                } elseif (str_contains($keyLower, 'fullname') || str_contains($keyLower, 'name')) {
                    $parts = $this->parseFullName((string) $value);
                    $leadData['first_name'] = $parts['first_name'];
                    $leadData['last_name']  = $parts['last_name'];
                    continue;
                }
            }

            if ($mapped && in_array($mapped, ['first_name', 'last_name', 'email', 'phone'])) {
                $leadData[$mapped] = $mapped === 'phone' ? $this->normalizePhone((string) $value) : (string) $value;
            } else {
                $leadData['custom_fields'][$key] = $value;
            }
        }

        $lead = $this->createLead($leadData, $connection, $log);

        if ($lead) {
            $leads[] = $lead;
        }

        return $leads;
    }

    public function getConnectionFormSchema(): array
    {
        return [
            'api_key'       => self::field('jotform', 'api_key',       'string', true,  'API Key'),
            'form_id'       => self::field('jotform', 'form_id',       'string', false, 'Form ID'),
            'field_mapping' => self::field('jotform', 'field_mapping', 'array',  false, 'Field Mapping (field_name => lead_field)'),
        ];
    }
}
