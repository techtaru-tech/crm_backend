<?php

namespace App\Services\LeadSources;

use App\Models\LeadSourceConnection;
use App\Models\WebhookLog;
use Illuminate\Http\Request;

class TypeformConnector extends BaseConnector
{
    public function verifyWebhook(Request $request, LeadSourceConnection $connection): bool
    {
        $credentials = $connection->credentials ?? [];
        $secret      = $credentials['webhook_secret'] ?? null;

        if (! $secret) {
            return true;
        }

        $signature = $request->header('Typeform-Signature');
        if (! $signature) {
            return false;
        }

        $payload  = $request->getContent();
        $expected = 'sha256=' . base64_encode(hash_hmac('sha256', $payload, $secret, true));

        return hash_equals($expected, $signature);
    }

    public function handleWebhook(Request $request, LeadSourceConnection $connection, WebhookLog $log): array
    {
        $body    = json_decode($request->getContent(), true) ?? [];
        $leads   = [];
        $mapping = $connection->settings['field_mapping'] ?? [];

        $formResponse = $body['form_response'] ?? [];
        $answers      = $formResponse['answers'] ?? [];
        $definition   = collect($formResponse['definition']['fields'] ?? [])
            ->keyBy('id')
            ->all();

        $fields = [];
        foreach ($answers as $answer) {
            $fieldId   = $answer['field']['id'] ?? null;
            $fieldDef  = $definition[$fieldId] ?? null;
            $fieldRef  = $fieldDef['ref'] ?? $fieldId;
            $fieldTitle = $fieldDef['title'] ?? $fieldId;

            $value = $answer['text'] ??
                $answer['email'] ??
                $answer['phone_number'] ??
                $answer['number'] ??
                ($answer['choice']['label'] ?? null) ??
                null;

            $fields[$fieldRef] = $value;
            $fields[$fieldTitle] = $value;
        }

        $leadData = [
            'source_id'  => $formResponse['token'] ?? null,
            'first_name' => null,
            'last_name'  => null,
            'email'      => null,
            'phone'      => null,
            'raw_data'   => $body,
            'custom_fields' => [],
        ];

        foreach ($fields as $key => $value) {
            $mapped = $mapping[$key] ?? null;
            if ($mapped && in_array($mapped, ['first_name', 'last_name', 'email', 'phone'])) {
                $leadData[$mapped] = $mapped === 'phone' ? $this->normalizePhone($value) : $value;
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
            'access_token'   => self::field('typeform', 'access_token',   'string', true,  'Access Token'),
            'webhook_secret' => self::field('typeform', 'webhook_secret', 'string', false, 'Webhook Secret'),
            'field_mapping'  => self::field('typeform', 'field_mapping',  'array',  false, 'Field Mapping (field_ref => lead_field)'),
        ];
    }
}
