<?php

namespace App\Services;

use App\Models\Lead;
use App\Models\LeadDuplicate;
use App\Models\LeadSourceConnection;
use App\Models\WebhookLog;
use Illuminate\Support\Facades\Log;

/**
 * Lead Ingestion Service
 *
 * Duplicate detection behaviour (by design):
 *   - Exact source+source_id match   → skipped entirely; returns null; no DB write.
 *   - Email OR phone matches existing → NO new lead created; a lead_duplicates record
 *     is inserted (original_lead_id = existing lead, duplicate_lead_id = null,
 *     attempted_data = raw payload); the original lead is touched; returns null.
 *   - Unique contact                  → new Lead row; is_duplicate=false; returns Lead.
 */
class LeadIngestionService
{
    public function __construct(protected LeadDuplicateDetector $duplicateDetector) {}

    public function ingestLead(array $data, LeadSourceConnection $connection, WebhookLog $log): ?Lead
    {
        $email = isset($data['email']) ? strtolower(trim($data['email'])) : null;
        $phone = $data['phone'] ?? null;

        if (! empty($data['source_id'])) {
            $existingBySourceId = Lead::where('tenant_id', $connection->tenant_id)
                ->where('source', $connection->source)
                ->where('source_id', $data['source_id'])
                ->first();

            if ($existingBySourceId) {
                Log::debug('[LeadIngestion] Skipped: exact source+source_id duplicate', [
                    'source_id'   => $data['source_id'],
                    'source'      => $connection->source,
                    'existing_id' => $existingBySourceId->id,
                ]);
                return null;
            }
        }

        $original = $this->duplicateDetector->findExisting(
            $connection->tenant_id,
            $email,
            $phone,
        );

        if ($original) {
            $matchField = $this->duplicateDetector->matchField(
                $original->email,
                $original->phone,
                $email,
                $phone
            );

            LeadDuplicate::create([
                'tenant_id'         => $connection->tenant_id,
                'original_lead_id'  => $original->id,
                'duplicate_lead_id' => null,
                'match_field'       => $matchField,
                'attempted_data'    => [
                    'source'       => $connection->source,
                    'first_name'   => $data['first_name'] ?? null,
                    'last_name'    => $data['last_name'] ?? null,
                    'email'        => $email,
                    'phone'        => $phone,
                    'source_id'    => $data['source_id'] ?? null,
                    'custom_fields' => $data['custom_fields'] ?? null,
                ],
            ]);

            $original->touch();

            Log::info('[LeadIngestion] Duplicate detected — no new lead created; lead_duplicates record stored', [
                'original_lead_id' => $original->id,
                'match_field'      => $matchField,
                'source'           => $connection->source,
            ]);

            return null;
        }

        $lead = Lead::create([
            'tenant_id'            => $connection->tenant_id,
            'source'               => $connection->source,
            'source_id'            => $data['source_id'] ?? null,
            'source_connection_id' => $connection->id,
            'first_name'           => $data['first_name'] ?? null,
            'last_name'            => $data['last_name'] ?? null,
            'email'                => $email,
            'phone'                => $phone,
            'status'               => 'new',
            'is_duplicate'         => false,
            'raw_data'             => $data['raw_data'] ?? null,
            'custom_fields'        => $data['custom_fields'] ?? null,
        ]);

        Log::info('[LeadIngestion] New lead created', [
            'lead_id' => $lead->id,
            'source'  => $connection->source,
        ]);

        return $lead;
    }

    public function createOrSkipDuplicate(array $data, LeadSourceConnection $connection, WebhookLog $log): ?Lead
    {
        return $this->ingestLead($data, $connection, $log);
    }

    public function createLead(array $data, LeadSourceConnection $connection, WebhookLog $log): Lead
    {
        $lead = Lead::create([
            'tenant_id'            => $connection->tenant_id,
            'source'               => $connection->source,
            'source_id'            => $data['source_id'] ?? null,
            'source_connection_id' => $connection->id,
            'first_name'           => $data['first_name'] ?? null,
            'last_name'            => $data['last_name'] ?? null,
            'email'                => isset($data['email']) ? strtolower(trim($data['email'])) : null,
            'phone'                => $data['phone'] ?? null,
            'status'               => 'new',
            'raw_data'             => $data['raw_data'] ?? null,
            'custom_fields'        => $data['custom_fields'] ?? null,
        ]);

        $this->duplicateDetector->detectAndLink($lead);

        return $lead;
    }
}
