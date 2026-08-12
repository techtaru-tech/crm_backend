<?php

namespace App\Services\LeadSources;

use App\Models\LeadSourceConnection;
use App\Models\WebhookLog;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;

class GoogleAdsConnector extends BaseConnector
{
    public function verifyWebhook(Request $request, LeadSourceConnection $connection): bool
    {
        $credentials = $connection->credentials ?? [];
        $secret      = $credentials['webhook_secret'] ?? null;

        if (! $secret) {
            return true;
        }

        $token = $request->header('Authorization') ?? $request->get('token', '');
        return hash_equals($secret, $token);
    }

    public function handleWebhook(Request $request, LeadSourceConnection $connection, WebhookLog $log): array
    {
        $body  = json_decode($request->getContent(), true) ?? [];
        $leads = [];

        $userData  = $body['user_column_data'] ?? [];
        $fields    = collect($userData)
            ->keyBy('column_name')
            ->map(fn ($f) => $f['string_value'] ?? null)
            ->all();

        $fullName  = $fields['Full Name'] ?? $fields['FULL_NAME'] ?? '';
        $nameParts = $this->parseFullName($fullName);

        $lead = $this->createLead([
            'source_id'  => $body['lead_id'] ?? null,
            'first_name' => $fields['First Name'] ?? $nameParts['first_name'],
            'last_name'  => $fields['Last Name'] ?? $nameParts['last_name'],
            'email'      => $fields['Email'] ?? $fields['EMAIL'] ?? null,
            'phone'      => $this->normalizePhone($fields['Phone Number'] ?? $fields['PHONE_NUMBER'] ?? null),
            'raw_data'   => $body,
            'custom_fields' => array_diff_key($fields, array_flip([
                'Full Name', 'First Name', 'Last Name', 'Email', 'Phone Number',
                'FULL_NAME', 'FIRST_NAME', 'LAST_NAME', 'EMAIL', 'PHONE_NUMBER',
            ])),
        ], $connection, $log);

        if ($lead) {
            $leads[] = $lead;
        }

        return $leads;
    }

    public function getConnectionFormSchema(): array
    {
        return [
            'developer_token' => self::field('google_ads', 'developer_token', 'string', true,  'Developer Token'),
            'client_id'       => self::field('google_ads', 'client_id',       'string', true,  'Client ID'),
            'client_secret'   => self::field('google_ads', 'client_secret',   'string', true,  'Client Secret'),
            'refresh_token'   => self::field('google_ads', 'refresh_token',   'string', true,  'Refresh Token'),
            'customer_id'     => self::field('google_ads', 'customer_id',     'string', true,  'Customer ID'),
            'webhook_secret'  => self::field('google_ads', 'webhook_secret',  'string', false, 'Webhook Secret'),
        ];
    }
}
