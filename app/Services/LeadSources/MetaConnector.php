<?php

namespace App\Services\LeadSources;

use App\Models\LeadSourceConnection;
use App\Models\WebhookLog;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class MetaConnector extends BaseConnector
{
    const GRAPH_API_VERSION = 'v19.0';

    public function verifyWebhook(Request $request, LeadSourceConnection $connection): bool
    {
        $credentials = $connection->credentials ?? [];
        $appSecret = $credentials['app_secret'] ?? null;

        if (! $appSecret) {
            return true;
        }

        $signature = $request->header('X-Hub-Signature-256');
        if (! $signature) {
            return false;
        }

        $payload  = $request->getContent();
        $expected = 'sha256=' . hash_hmac('sha256', $payload, $appSecret);

        return hash_equals($expected, $signature);
    }

    public function handleWebhook(Request $request, LeadSourceConnection $connection, WebhookLog $log): array
    {
        $body   = json_decode($request->getContent(), true) ?? [];
        $leads  = [];

        $entries = $body['entry'] ?? [];

        foreach ($entries as $entry) {
            $changes = $entry['changes'] ?? [];
            foreach ($changes as $change) {
                if (($change['field'] ?? '') !== 'leadgen') {
                    continue;
                }

                $leadId = $change['value']['leadgen_id'] ?? null;
                if (! $leadId) {
                    continue;
                }

                $lead = $this->fetchLeadFromGraph($leadId, $connection);
                if ($lead) {
                    $leads[] = $lead;
                }
            }
        }

        return $leads;
    }

    protected function fetchLeadFromGraph(string $leadId, LeadSourceConnection $connection): ?object
    {
        $credentials = $connection->credentials ?? [];
        $accessToken = $credentials['page_access_token'] ?? null;

        if (! $accessToken) {
            return null;
        }

        try {
            $response = Http::get("https://graph.facebook.com/" . self::GRAPH_API_VERSION . "/{$leadId}", [
                'access_token' => $accessToken,
                'fields'       => 'field_data,created_time,ad_id,form_id,page_id',
            ]);

            if (! $response->successful()) {
                Log::warning('Meta lead fetch failed', ['lead_id' => $leadId, 'error' => $response->body()]);
                return null;
            }

            $data       = $response->json();
            $fieldData  = collect($data['field_data'] ?? [])
                ->keyBy('name')
                ->map(fn ($f) => $f['values'][0] ?? null)
                ->all();

            $fullName   = $fieldData['full_name'] ?? '';
            $nameParts  = $this->parseFullName($fullName);

            $fakeLog = new \App\Models\WebhookLog(['source' => $connection->source]);
            return $this->createLead([
                'source_id'  => $leadId,
                'first_name' => $fieldData['first_name'] ?? $nameParts['first_name'],
                'last_name'  => $fieldData['last_name'] ?? $nameParts['last_name'],
                'email'      => $fieldData['email'] ?? null,
                'phone'      => $this->normalizePhone($fieldData['phone_number'] ?? null),
                'raw_data'   => $data,
                'custom_fields' => array_diff_key($fieldData, array_flip([
                    'full_name', 'first_name', 'last_name', 'email', 'phone_number',
                ])),
            ], $connection, $fakeLog);
        } catch (\Throwable $e) {
            Log::error('Meta graph API error', ['error' => $e->getMessage()]);
            return null;
        }
    }

    public function getConnectionFormSchema(): array
    {
        return [
            'app_id'            => self::field('meta', 'app_id',            'string', true,  'App ID'),
            'app_secret'        => self::field('meta', 'app_secret',        'string', true,  'App Secret'),
            'page_access_token' => self::field('meta', 'page_access_token', 'string', false, 'Page Access Token (auto-filled via OAuth)'),
            'page_id'           => self::field('meta', 'page_id',           'string', false, 'Page ID'),
        ];
    }

    public function testConnection(LeadSourceConnection $connection): bool
    {
        $credentials = $connection->credentials ?? [];
        $token       = $credentials['page_access_token'] ?? null;

        if (! $token) {
            return false;
        }

        $response = Http::get('https://graph.facebook.com/me', ['access_token' => $token]);
        return $response->successful();
    }
}
