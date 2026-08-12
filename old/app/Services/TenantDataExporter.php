<?php

declare(strict_types=1);

namespace App\Services;

use App\Models\Tenant;
use Carbon\Carbon;
use Illuminate\Support\Facades\Log;
use ZipArchive;

/**
 * Builds a GDPR-compliant data export for one tenant.
 *
 * Output is a ZIP at storage/app/exports/tenant_{id}/{timestamp}.zip
 * containing one JSON file per entity type plus a README.txt with the
 * dataset map.  Designed for "right of access" requests — the
 * tenant downloads it from /admin/privacy.
 *
 * Streams in chunks via cursor() to keep memory bounded for tenants
 * with hundreds of thousands of leads.  Single-pass: no temporary
 * intermediate files outside the ZIP.
 */
class TenantDataExporter
{
    /**
     * @return array{path:string, size:int, expires_at:Carbon}
     */
    public function export(Tenant $tenant): array
    {
        $tenant->loadMissing(['owner']);

        $timestamp = Carbon::now()->format('Y-m-d_His');
        $relativePath = "exports/tenant_{$tenant->id}/{$timestamp}.zip";
        $absolutePath = storage_path('app/' . $relativePath);

        $dir = dirname($absolutePath);
        if (! is_dir($dir)) {
            mkdir($dir, 0755, true);
        }

        $zip = new ZipArchive();
        if ($zip->open($absolutePath, ZipArchive::CREATE | ZipArchive::OVERWRITE) !== true) {
            throw new \RuntimeException("Could not open ZIP for writing at {$absolutePath}");
        }

        try {
            $zip->addFromString('README.txt', $this->buildReadme($tenant, $timestamp));

            // Tenant profile
            $zip->addFromString('tenant.json', $this->jsonEncode([
                'id'                  => $tenant->id,
                'name'                => $tenant->name,
                'slug'                => $tenant->slug,
                'plan'                => $tenant->plan,
                'subscription_status' => $tenant->subscription_status,
                'created_at'          => $tenant->created_at?->toIso8601String(),
                'settings'            => $tenant->settings,
                'branding'            => $tenant->branding,
                'owner'               => $tenant->owner ? [
                    'name'  => $tenant->owner->name,
                    'email' => $tenant->owner->email,
                ] : null,
            ]));

            // Members + roles
            $this->dumpQueryAsJson($zip, 'members.json',
                fn () => $tenant->users()
                    ->select('users.id', 'users.name', 'users.email', 'tenant_users.role')
                    ->cursor()
                    ->map(fn ($u) => [
                        'id'    => $u->id,
                        'name'  => $u->name,
                        'email' => $u->email,
                        'role'  => $u->pivot->role ?? null,
                    ]),
            );

            // Leads + child relations
            $this->dumpModelToZip($zip, 'leads.json', \App\Models\Lead::class, $tenant->id);
            $this->dumpModelToZip($zip, 'companies.json', \App\Models\Company::class, $tenant->id);
            $this->dumpModelToZip($zip, 'lead_activities.json', \App\Models\LeadActivity::class, $tenant->id);
            $this->dumpModelToZip($zip, 'lead_notes.json', \App\Models\LeadNote::class, $tenant->id);
            $this->dumpModelToZip($zip, 'lead_tasks.json', \App\Models\LeadTask::class, $tenant->id);
            $this->dumpModelToZip($zip, 'lead_messages.json', \App\Models\LeadMessage::class, $tenant->id);
            $this->dumpModelToZip($zip, 'lead_emails.json', \App\Models\LeadEmail::class, $tenant->id);
            $this->dumpModelToZip($zip, 'lead_calls.json', \App\Models\LeadCall::class, $tenant->id);

            // CRM structure
            $this->dumpModelToZip($zip, 'pipelines.json', \App\Models\Pipeline::class, $tenant->id);
            $this->dumpModelToZip($zip, 'pipeline_stages.json', \App\Models\PipelineStage::class, $tenant->id);
            $this->dumpModelToZip($zip, 'tags.json', \App\Models\Tag::class, $tenant->id);
            $this->dumpModelToZip($zip, 'custom_field_definitions.json', \App\Models\CustomFieldDefinition::class, $tenant->id);

            // Forms + landing
            $this->dumpModelToZip($zip, 'forms.json', \App\Models\Form::class, $tenant->id);
            $this->dumpModelToZip($zip, 'form_submissions.json', \App\Models\FormSubmission::class, $tenant->id);
            $this->dumpModelToZip($zip, 'landing_pages.json', \App\Models\LandingPage::class, $tenant->id);

            // Automations + sequences
            $this->dumpModelToZip($zip, 'automations.json', \App\Models\Automation::class, $tenant->id);
            $this->dumpModelToZip($zip, 'email_sequences.json', \App\Models\EmailSequence::class, $tenant->id);
            $this->dumpModelToZip($zip, 'email_templates.json', \App\Models\EmailTemplate::class, $tenant->id);

            // Commercial
            $this->dumpModelToZip($zip, 'products.json', \App\Models\Product::class, $tenant->id);
            $this->dumpModelToZip($zip, 'quotes.json', \App\Models\Quote::class, $tenant->id);
            $this->dumpModelToZip($zip, 'invoices.json', \App\Models\Invoice::class, $tenant->id);

            // Meetings
            $this->dumpModelToZip($zip, 'meeting_types.json', \App\Models\MeetingType::class, $tenant->id);
            $this->dumpModelToZip($zip, 'meeting_bookings.json', \App\Models\MeetingBooking::class, $tenant->id);

            // LeadBot (AI chatbot).  Configs + conversations carry tenant_id
            // so they use the generic dumper; chat_messages has no tenant_id
            // (scoped through its conversation), so it gets a join-based dump.
            $this->dumpModelToZip($zip, 'chatbot_configs.json', \App\Models\ChatbotConfig::class, $tenant->id);
            $this->dumpModelToZip($zip, 'chat_conversations.json', \App\Models\ChatConversation::class, $tenant->id);
            $this->dumpChatMessagesToZip($zip, 'chat_messages.json', $tenant->id);

            // Integrations / API surface (api_keys redacted)
            $this->dumpModelToZip($zip, 'integrations.json', \App\Models\Integration::class, $tenant->id);
            $this->dumpModelToZip($zip, 'api_keys.json', \App\Models\ApiKey::class, $tenant->id, redact: ['secret', 'api_key', 'token', 'key']);

            $zip->close();
        } catch (\Throwable $e) {
            $zip->close();
            @unlink($absolutePath);
            throw $e;
        }

        $size = filesize($absolutePath) ?: 0;
        $expiresAt = Carbon::now()->addHours(48);

        Log::info('Tenant data export built', [
            'tenant_id' => $tenant->id,
            'path'      => $relativePath,
            'size'      => $size,
        ]);

        return [
            'path'       => $relativePath,
            'size'       => (int) $size,
            'expires_at' => $expiresAt,
        ];
    }

    /**
     * Stream an Eloquent model's tenant-scoped rows as JSON into the
     * ZIP using cursor() to keep memory bounded.  Bypasses the
     * BelongsToTenant global scope explicitly because the export
     * runs in CLI/queue context with no authenticated user.
     *
     * @param  array<string>  $redact  column names to blank out
     */
    protected function dumpModelToZip(
        ZipArchive $zip,
        string $filename,
        string $modelClass,
        int $tenantId,
        array $redact = [],
    ): void {
        if (! class_exists($modelClass)) {
            return;
        }

        try {
            $rows = [];
            $query = $modelClass::query();
            try {
                $query = $query->withoutGlobalScope('tenant');
            } catch (\Throwable) {
                // Model doesn't use that scope — fine.
            }
            $query = $query->where('tenant_id', $tenantId);

            foreach ($query->cursor() as $row) {
                $arr = $row->toArray();
                foreach ($redact as $col) {
                    if (array_key_exists($col, $arr)) {
                        $arr[$col] = '[REDACTED]';
                    }
                }
                $rows[] = $arr;
            }

            $zip->addFromString($filename, $this->jsonEncode($rows));
        } catch (\Throwable $e) {
            $zip->addFromString($filename, $this->jsonEncode([
                '_error' => 'Could not export this entity: ' . $e->getMessage(),
            ]));
            Log::warning('Tenant export entity failed', [
                'tenant_id' => $tenantId,
                'entity'    => $filename,
                'error'     => $e->getMessage(),
            ]);
        }
    }

    /**
     * Dump a tenant's chat_messages.  This table carries NO tenant_id
     * (scoped through its parent chat_conversation), so the generic
     * dumpModelToZip can't filter it — we scope via whereHas on the
     * conversation's tenant_id instead.  cursor() keeps memory bounded.
     */
    protected function dumpChatMessagesToZip(ZipArchive $zip, string $filename, int $tenantId): void
    {
        if (! class_exists(\App\Models\ChatMessage::class)) {
            return;
        }

        try {
            $rows = [];
            $query = \App\Models\ChatMessage::query()
                ->whereHas('conversation', function ($q) use ($tenantId) {
                    $q->withoutGlobalScope('tenant')->where('tenant_id', $tenantId);
                });

            foreach ($query->cursor() as $row) {
                $rows[] = $row->toArray();
            }

            $zip->addFromString($filename, $this->jsonEncode($rows));
        } catch (\Throwable $e) {
            $zip->addFromString($filename, $this->jsonEncode([
                '_error' => 'Could not export this entity: ' . $e->getMessage(),
            ]));
            Log::warning('Tenant export entity failed', [
                'tenant_id' => $tenantId,
                'entity'    => $filename,
                'error'     => $e->getMessage(),
            ]);
        }
    }

    protected function dumpQueryAsJson(ZipArchive $zip, string $filename, \Closure $producer): void
    {
        try {
            $data = $producer();
            $rows = is_iterable($data) ? collect($data)->all() : (array) $data;
            $zip->addFromString($filename, $this->jsonEncode($rows));
        } catch (\Throwable $e) {
            $zip->addFromString($filename, $this->jsonEncode(['_error' => $e->getMessage()]));
        }
    }

    protected function jsonEncode(mixed $value): string
    {
        return json_encode(
            $value,
            JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES,
        ) ?: '[]';
    }

    protected function buildReadme(Tenant $tenant, string $timestamp): string
    {
        // Readme is generated under the tenant owner's preferred locale so
        // GDPR Article 15 access exports arrive in the buyer's language
        // rather than the queue worker's app.locale.  Owners who never
        // picked a locale fall through to config('app.locale').
        $locale = $tenant->owner?->preferredLocale() ?: config('app.locale');
        $appName = config('app.name', 'LeadHub');

        $line = fn (string $key, array $params = []): string => (string) trans($key, $params, $locale);

        $title       = $line('data_privacy.readme_title', ['app' => $appName]);
        $divider     = $line('data_privacy.readme_divider');
        $workspace   = $line('data_privacy.readme_workspace', ['workspace' => $tenant->name]);
        $generated   = $line('data_privacy.readme_generated', ['timestamp' => $timestamp]);
        $intro       = $line('data_privacy.readme_intro');
        $mapHeader   = $line('data_privacy.readme_file_map_header');
        $subdivider  = $line('data_privacy.readme_subdivider');
        $rowReadme   = $line('data_privacy.readme_row_readme');
        $rowTenant   = $line('data_privacy.readme_row_tenant');
        $rowMembers  = $line('data_privacy.readme_row_members');
        $rowLeads    = $line('data_privacy.readme_row_leads');
        $rowComp     = $line('data_privacy.readme_row_companies');
        $rowAct      = $line('data_privacy.readme_row_activities');
        $rowNotes    = $line('data_privacy.readme_row_notes');
        $rowTasks    = $line('data_privacy.readme_row_tasks');
        $rowMsgs     = $line('data_privacy.readme_row_messages');
        $rowEmails   = $line('data_privacy.readme_row_emails');
        $rowCalls    = $line('data_privacy.readme_row_calls');
        $rowPipes    = $line('data_privacy.readme_row_pipelines');
        $rowStages   = $line('data_privacy.readme_row_pipeline_stages');
        $rowTags     = $line('data_privacy.readme_row_tags');
        $rowCfDefs   = $line('data_privacy.readme_row_custom_fields');
        $rowForms    = $line('data_privacy.readme_row_forms');
        $rowSubs     = $line('data_privacy.readme_row_form_submissions');
        $rowLanding  = $line('data_privacy.readme_row_landing_pages');
        $rowAuto     = $line('data_privacy.readme_row_automations');
        $rowSeq      = $line('data_privacy.readme_row_email_sequences');
        $rowTpl      = $line('data_privacy.readme_row_email_templates');
        $rowProd     = $line('data_privacy.readme_row_products');
        $rowQuotes   = $line('data_privacy.readme_row_quotes');
        $rowInv      = $line('data_privacy.readme_row_invoices');
        $rowMt       = $line('data_privacy.readme_row_meeting_types');
        $rowMb       = $line('data_privacy.readme_row_meeting_bookings');
        $rowInt      = $line('data_privacy.readme_row_integrations');
        $rowKeys     = $line('data_privacy.readme_row_api_keys');
        $notesHeader = $line('data_privacy.readme_notes_header');
        $noteIso     = $line('data_privacy.readme_note_iso8601');
        $noteRedact  = $line('data_privacy.readme_note_redaction');
        $noteAtt     = $line('data_privacy.readme_note_attachments');
        $noteSnap    = $line('data_privacy.readme_note_snapshot', ['timestamp' => $timestamp]);

        return <<<TXT
{$title}
{$divider}

{$workspace}
{$generated}

{$intro}

{$mapHeader}
{$subdivider}
{$rowReadme}
{$rowTenant}
{$rowMembers}

{$rowLeads}
{$rowComp}
{$rowAct}
{$rowNotes}
{$rowTasks}
{$rowMsgs}
{$rowEmails}
{$rowCalls}

{$rowPipes}
{$rowStages}
{$rowTags}
{$rowCfDefs}

{$rowForms}
{$rowSubs}
{$rowLanding}

{$rowAuto}
{$rowSeq}
{$rowTpl}

{$rowProd}
{$rowQuotes}
{$rowInv}

{$rowMt}
{$rowMb}

{$rowInt}
{$rowKeys}

{$notesHeader}
{$subdivider}
{$noteIso}
{$noteRedact}
{$noteAtt}
{$noteSnap}
TXT;
    }
}
