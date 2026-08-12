<?php

namespace App\Services;

use App\Models\Lead;
use App\Models\LeadDuplicate;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class LeadMergeService
{
    /**
     * Merge $duplicate into $primary, keeping specified fields from each.
     * $fieldOwnership: ['email' => 'primary'|'duplicate', ...]
     */
    public function merge(Lead $primary, Lead $duplicate, array $fieldOwnership = []): Lead
    {
        return DB::transaction(function () use ($primary, $duplicate, $fieldOwnership) {
            $mergeable = [
                'first_name', 'last_name', 'email', 'phone', 'company',
                'job_title', 'linkedin_url', 'country', 'industry', 'company_size',
                'notes', 'status', 'lead_score', 'pipeline_id', 'pipeline_stage_id',
                'assigned_user_id', 'source', 'consented_at', 'consent_text',
            ];

            $updates = [];
            foreach ($mergeable as $field) {
                $owner = $fieldOwnership[$field] ?? null;
                if ($owner === 'duplicate') {
                    $val = $duplicate->getAttribute($field);
                    if ($val !== null && $val !== '') {
                        $updates[$field] = $val;
                    }
                } elseif ($owner === 'primary') {
                    // already on primary, keep it
                } else {
                    // auto: use whichever is not null, prefer primary
                    if (($primary->getAttribute($field) === null || $primary->getAttribute($field) === '') &&
                        ($duplicate->getAttribute($field) !== null && $duplicate->getAttribute($field) !== '')) {
                        $updates[$field] = $duplicate->getAttribute($field);
                    }
                }
            }

            // Merge custom_fields (deep merge, primary wins on conflicts)
            $primaryCF   = $primary->custom_fields  ?? [];
            $duplicateCF = $duplicate->custom_fields ?? [];
            $mergedCF    = array_merge($duplicateCF, $primaryCF);
            if ($mergedCF !== $primaryCF) {
                $updates['custom_fields'] = $mergedCF;
            }

            // Merge tags
            $primaryTags   = $primary->tags()->pluck('id')->toArray();
            $duplicateTags = $duplicate->tags()->pluck('id')->toArray();
            $allTags       = array_unique(array_merge($primaryTags, $duplicateTags));

            // Merge enrichment: if primary not enriched, copy from duplicate
            if (empty($primary->enriched_at) && !empty($duplicate->enriched_at)) {
                $updates['company']         = $updates['company']         ?? $duplicate->company;
                $updates['job_title']       = $updates['job_title']       ?? $duplicate->job_title;
                $updates['linkedin_url']    = $updates['linkedin_url']    ?? $duplicate->linkedin_url;
                $updates['country']         = $updates['country']         ?? $duplicate->country;
                $updates['industry']        = $updates['industry']        ?? $duplicate->industry;
                $updates['company_size']    = $updates['company_size']    ?? $duplicate->company_size;
                $updates['enriched_at']     = $duplicate->enriched_at;
                $updates['enrichment_data'] = $duplicate->enrichment_data;
            }

            if (!empty($updates)) {
                $primary->update($updates);
            }

            // Re-parent all related records from duplicate → primary
            $this->reParent($primary->id, $duplicate->id);

            // Sync merged tags
            $primary->tags()->sync($allTags);

            // Mark duplicate as merged/archived
            $duplicate->update([
                'is_duplicate' => true,
                'notes'        => trim(($duplicate->notes ?? '') . "\n[Merged into lead #{$primary->id} on " . now()->toDateString() . ']'),
            ]);
            $duplicate->delete();

            // Clean up the duplicate record
            LeadDuplicate::where(function ($q) use ($primary, $duplicate) {
                $q->where('original_lead_id', $duplicate->id)
                  ->orWhere('duplicate_lead_id', $duplicate->id);
            })->where(function ($q) use ($primary) {
                $q->where('original_lead_id', $primary->id)
                  ->orWhere('duplicate_lead_id', $primary->id);
            })->delete();

            Log::info('LeadMergeService: merged lead', [
                'primary_id'   => $primary->id,
                'duplicate_id' => $duplicate->id,
            ]);

            return $primary->fresh();
        });
    }

    private function reParent(int $primaryId, int $duplicateId): void
    {
        $tables = [
            'lead_activities'    => 'lead_id',
            'lead_tasks'         => 'lead_id',
            'lead_notes'         => 'lead_id',
            'automation_runs'    => 'lead_id',
            'lead_form_submissions' => 'lead_id',
            'webhook_logs'       => 'lead_id',
        ];

        foreach ($tables as $table => $column) {
            try {
                if (DB::getSchemaBuilder()->hasTable($table)) {
                    DB::table($table)
                      ->where($column, $duplicateId)
                      ->update([$column => $primaryId]);
                }
            } catch (\Throwable $e) {
                Log::warning("LeadMergeService: failed to re-parent {$table}", ['error' => $e->getMessage()]);
            }
        }
    }
}
