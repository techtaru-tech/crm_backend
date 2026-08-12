<?php

namespace App\Services\LeadSources;

use App\Models\LeadSourceConnection;
use App\Models\WebhookLog;
use Illuminate\Http\Request;

/**
 * "Manual" lead source — a no-credentials, no-OAuth inbound webhook bucket
 * for leads pushed by hand or by any external system that just POSTs a flat
 * JSON body to the connection's webhook URL. Mirrors WebFormConnector.
 *
 * Why it exists: LeadSource::Manual is a selectable source and every manual
 * connection is issued a real webhook URL, but there was no connector for it
 * — so resolve('manual') threw "No connector registered for source: manual"
 * the moment the Test action (or a webhook hit) ran. Registering this
 * connector makes manual connections testable (testConnection() -> true via
 * BaseConnector) and lets the webhook actually ingest leads.
 */
class ManualConnector extends BaseConnector
{
    public function verifyWebhook(Request $request, LeadSourceConnection $connection): bool
    {
        // No signing secret for a manual bucket — the unguessable webhook
        // token in the URL is the shared secret (same model as WebForm).
        return true;
    }

    public function handleWebhook(Request $request, LeadSourceConnection $connection, WebhookLog $log): array
    {
        $body  = json_decode($request->getContent(), true) ?: $request->all();
        $leads = [];

        $lead = $this->createLead([
            'source_id'     => $body['source_id'] ?? $body['id'] ?? null,
            'first_name'    => $body['first_name'] ?? null,
            'last_name'     => $body['last_name'] ?? null,
            'email'         => $body['email'] ?? null,
            'phone'         => $this->normalizePhone($body['phone'] ?? null),
            'raw_data'      => $body,
            'custom_fields' => array_diff_key($body, array_flip([
                'id', 'source_id', 'first_name', 'last_name', 'email', 'phone', '_token',
            ])),
        ], $connection, $log);

        if ($lead) {
            $leads[] = $lead;
        }

        return $leads;
    }

    public function getConnectionFormSchema(): array
    {
        // No credentials required — the connection is a passive webhook
        // receiver; the form renders the "no credentials required" notice.
        return [];
    }
}
