<?php

namespace App\Services\LeadSources;

use App\Models\LeadSourceConnection;
use App\Models\WebhookLog;
use Illuminate\Http\Request;

class WebFormConnector extends BaseConnector
{
    public function verifyWebhook(Request $request, LeadSourceConnection $connection): bool
    {
        return true;
    }

    public function handleWebhook(Request $request, LeadSourceConnection $connection, WebhookLog $log): array
    {
        $body  = json_decode($request->getContent(), true) ?? $request->all();
        $leads = [];

        $lead = $this->createLead([
            'source_id'    => $body['form_id'] ?? null,
            'first_name'   => $body['first_name'] ?? null,
            'last_name'    => $body['last_name'] ?? null,
            'email'        => $body['email'] ?? null,
            'phone'        => $this->normalizePhone($body['phone'] ?? null),
            'raw_data'     => $body,
            'custom_fields' => array_diff_key($body, array_flip([
                'first_name', 'last_name', 'email', 'phone', 'form_id', '_token',
            ])),
        ], $connection, $log);

        if ($lead) {
            $leads[] = $lead;
        }

        return $leads;
    }

    public function getConnectionFormSchema(): array
    {
        return [];
    }
}
