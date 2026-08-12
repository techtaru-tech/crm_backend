<?php

namespace App\Services\LeadSources;

use App\Models\LeadSourceConnection;
use App\Models\WebhookLog;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class TikTokConnector extends BaseConnector
{
    public function verifyWebhook(Request $request, LeadSourceConnection $connection): bool
    {
        $credentials = $connection->credentials ?? [];
        $appSecret   = $credentials['client_secret'] ?? $credentials['app_secret'] ?? null;

        if (! $appSecret) {
            return true;
        }

        $signature = $request->header('X-TikTok-Signature');
        if (! $signature) {
            return false;
        }

        $payload  = $request->getContent();
        $expected = hash_hmac('sha256', $payload, $appSecret);

        return hash_equals($expected, $signature);
    }

    public function handleWebhook(Request $request, LeadSourceConnection $connection, WebhookLog $log): array
    {
        $body  = json_decode($request->getContent(), true) ?? [];
        $leads = [];

        $formSubmissions = $body['data']['form_submissions'] ?? [$body];

        foreach ($formSubmissions as $submission) {
            $fields    = collect($submission['fields'] ?? [])
                ->keyBy('name')
                ->map(fn ($f) => $f['value'] ?? null)
                ->all();

            $fullName  = $fields['full_name'] ?? $fields['name'] ?? '';
            $nameParts = $this->parseFullName($fullName);

            $lead = $this->createLead([
                'source_id'  => $submission['lead_id'] ?? $submission['id'] ?? null,
                'first_name' => $fields['first_name'] ?? $nameParts['first_name'],
                'last_name'  => $fields['last_name'] ?? $nameParts['last_name'],
                'email'      => $fields['email'] ?? null,
                'phone'      => $this->normalizePhone($fields['phone_number'] ?? $fields['phone'] ?? null),
                'raw_data'   => $submission,
                'custom_fields' => array_diff_key($fields, array_flip([
                    'full_name', 'name', 'first_name', 'last_name', 'email', 'phone_number', 'phone',
                ])),
            ], $connection, $log);

            if ($lead) {
                $leads[] = $lead;
            }
        }

        return $leads;
    }

    public function getConnectionFormSchema(): array
    {
        return [
            'client_key'    => self::field('tiktok', 'client_key',    'string', true,  'Client Key (App ID)'),
            'client_secret' => self::field('tiktok', 'client_secret', 'string', true,  'Client Secret (App Secret)'),
            'access_token'  => self::field('tiktok', 'access_token',  'string', false, 'Access Token (auto-filled via OAuth)'),
        ];
    }

    public function testConnection(LeadSourceConnection $connection): bool
    {
        $credentials = $connection->credentials ?? [];
        $token       = $credentials['access_token'] ?? null;

        if (! $token) {
            return false;
        }

        $response = Http::withHeaders(['Access-Token' => $token])
            ->get('https://business-api.tiktok.com/open_api/v1.3/user/info/');

        return $response->successful();
    }
}
