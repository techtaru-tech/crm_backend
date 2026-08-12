<?php

declare(strict_types=1);

namespace App\Jobs;

use App\Models\Tenant;
use App\Models\User;
use App\Notifications\TenantDataExportReady;
use Carbon\Carbon;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Notification;
use Illuminate\Support\Facades\Storage;
use ZipArchive;

/**
 * GDPR Article 20 — Right to Data Portability.
 *
 * Builds a ZIP of every tenant-owned record in a portable JSON format
 * AND attaches every uploaded file the tenant owns so the export is
 * actually re-importable into another system.
 *
 * Flow:
 *   1. Build a temp directory under storage/app/exports/{tenant_id}/
 *   2. Stream each tenant-owned table into a separate JSON file
 *      (leads, forms, pipelines+stages, automations+steps,
 *      email_sequences+steps+enrollments, users, notes, meetings,
 *      invoices, audit_logs, etc.)
 *   3. Walk every tenant-owned upload folder and copy each file into
 *      the ZIP under attachments/, form-uploads/, imports/ — bounded
 *      by LH_EXPORT_MAX_BYTES so a malicious / runaway tenant cannot
 *      blow up the host disk
 *   4. Add a manifest.json (export-time, tenant-id, GDPR attribution,
 *      total files, total bytes, skipped files)
 *   5. Zip everything to storage/app/exports/tenant-{id}-{date}.zip
 *   6. Generate a 64-char opaque token, store in cache for 24h
 *      with {tenant_id, user_id, expires_at, path}
 *   7. Notify the requesting user (database channel) with the
 *      download URL the DataExportController serves
 *
 * The job runs in CLI / queue context with no authenticated user so
 * Eloquent global scopes are bypassed via withoutGlobalScopes().
 * Queries are still constrained by an explicit `tenant_id` WHERE
 * clause so a misconfigured scope can never leak rows from another
 * tenant.
 *
 * Wraps the export in DB::beginTransaction()/rollback for read
 * consistency on InnoDB — without it the ZIP could include rows
 * inserted halfway through the export, producing an inconsistent
 * snapshot a user couldn't re-import cleanly.
 */
class ExportTenantDataJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    /** Token cache key prefix — read by DataExportController. */
    public const CACHE_PREFIX = 'data_export:';

    /** Download link TTL — 24 hours per GDPR Article 20 best practice. */
    public const DOWNLOAD_TTL_SECONDS = 86400;

    /** Default ZIP size cap — 2 GB. Override via LH_EXPORT_MAX_BYTES. */
    public const DEFAULT_MAX_EXPORT_BYTES = 2 * 1024 * 1024 * 1024;

    /** Stream tenant attachments in chunks of this size to bound memory. */
    public const FILE_CHUNK_SIZE = 500;

    /** GDPR attribution string surfaced in the manifest. */
    public const ARTICLE_20_ATTRIBUTION =
        'Generated under GDPR Article 20 — Right to Data Portability. '
        . 'Data is provided in a structured, commonly used and machine-readable format.';

    public int $tries = 2;
    public int $timeout = 1800; // 30 min for tenants with large attachment trees

    /**
     * Counters mutated while files are being added to the ZIP. Collected
     * into the manifest at the end so the user can see exactly what was
     * included and what was skipped, and why.
     *
     * @var array<int, array{path:string, reason:string, size:int}>
     */
    protected array $skippedFiles = [];

    protected int $filesIncluded = 0;
    protected int $bytesIncluded = 0;

    public function __construct(
        public Tenant $tenant,
        public User $requester,
    ) {}

    public function handle(): void
    {
        $tenant = Tenant::query()->withoutGlobalScopes()->find($this->tenant->id);
        if (! $tenant) {
            Log::warning('ExportTenantDataJob: tenant not found', ['tenant_id' => $this->tenant->id]);
            return;
        }

        $requester = User::query()->withoutGlobalScopes()->find($this->requester->id);
        if (! $requester) {
            Log::warning('ExportTenantDataJob: requester not found', [
                'tenant_id'    => $tenant->id,
                'requester_id' => $this->requester->id,
            ]);
            return;
        }

        // Mark status as processing so the polling UI on
        // /admin/privacy can show a "we're working on it" state.
        // (Folded in from the now-deleted ExportTenantData job.)
        $tenant->forceFill(['data_export_status' => 'processing'])->save();

        $relativePath = sprintf(
            'exports/tenant-%d-%s.zip',
            $tenant->id,
            Carbon::now()->format('Y-m-d_His'),
        );
        $absolutePath = storage_path('app/' . $relativePath);

        $dir = dirname($absolutePath);
        if (! is_dir($dir)) {
            mkdir($dir, 0755, true);
        }

        $zip = new ZipArchive();
        if ($zip->open($absolutePath, ZipArchive::CREATE | ZipArchive::OVERWRITE) !== true) {
            throw new \RuntimeException("Could not open ZIP for writing at {$absolutePath}");
        }

        $maxBytes = $this->maxExportBytes();

        // Snapshot the dataset under a transaction so concurrent
        // writes don't produce a torn export.  REPEATABLE READ on
        // InnoDB is enough to keep the cursor()s consistent.
        DB::beginTransaction();

        try {
            $this->writeTenantProfile($zip, $tenant);
            $this->writeTenantUsers($zip, $tenant);

            // Lead graph
            $this->dumpTenantTable($zip, 'leads.json', \App\Models\Lead::class, $tenant->id);
            $this->dumpTenantTable($zip, 'companies.json', \App\Models\Company::class, $tenant->id);
            $this->dumpTenantTable($zip, 'lead_activities.json', \App\Models\LeadActivity::class, $tenant->id);
            $this->dumpTenantTable($zip, 'notes.json', \App\Models\LeadNote::class, $tenant->id);
            $this->dumpTenantTable($zip, 'lead_tasks.json', \App\Models\LeadTask::class, $tenant->id);
            $this->dumpTenantTable($zip, 'lead_messages.json', \App\Models\LeadMessage::class, $tenant->id);
            $this->dumpTenantTable($zip, 'lead_emails.json', \App\Models\LeadEmail::class, $tenant->id);
            $this->dumpTenantTable($zip, 'lead_calls.json', \App\Models\LeadCall::class, $tenant->id);
            $this->dumpTenantTable($zip, 'lead_attachments.json', \App\Models\LeadAttachment::class, $tenant->id);

            // Embed the actual binary files referenced from the JSON
            // dumps. Without this step the JSON points at server-only
            // paths the user can never read back.
            $this->attachLeadAttachmentFiles($zip, $tenant->id, $maxBytes);
            $this->attachFormUploads($zip, $tenant->id, $maxBytes);
            $this->attachLeadImportFiles($zip, $tenant->id, $maxBytes);

            // Pipelines + stages (eager-load stages on each pipeline)
            $this->dumpTenantTable($zip, 'pipelines.json', \App\Models\Pipeline::class, $tenant->id, with: ['stages']);
            $this->dumpTenantTable($zip, 'tags.json', \App\Models\Tag::class, $tenant->id);
            $this->dumpTenantTable($zip, 'custom_field_definitions.json', \App\Models\CustomFieldDefinition::class, $tenant->id);

            // Forms
            $this->dumpTenantTable($zip, 'forms.json', \App\Models\Form::class, $tenant->id);
            $this->dumpTenantTable($zip, 'form_submissions.json', \App\Models\FormSubmission::class, $tenant->id);
            $this->dumpTenantTable($zip, 'landing_pages.json', \App\Models\LandingPage::class, $tenant->id);

            // Automations + steps
            $this->dumpTenantTable($zip, 'automations.json', \App\Models\Automation::class, $tenant->id, with: ['steps']);
            $this->dumpTenantTable($zip, 'automation_runs.json', \App\Models\AutomationRun::class, $tenant->id);

            // Email sequences + steps + enrollments
            $this->dumpTenantTable($zip, 'email_sequences.json', \App\Models\EmailSequence::class, $tenant->id, with: ['steps', 'enrollments']);
            $this->dumpTenantTable($zip, 'email_templates.json', \App\Models\EmailTemplate::class, $tenant->id);

            // Commercial
            $this->dumpTenantTable($zip, 'products.json', \App\Models\Product::class, $tenant->id);
            $this->dumpTenantTable($zip, 'quotes.json', \App\Models\Quote::class, $tenant->id);
            $this->dumpTenantTable($zip, 'invoices.json', \App\Models\Invoice::class, $tenant->id);

            // Meetings
            $this->dumpTenantTable($zip, 'meetings.json', \App\Models\MeetingBooking::class, $tenant->id);
            $this->dumpTenantTable($zip, 'meeting_types.json', \App\Models\MeetingType::class, $tenant->id);

            // Integrations & API surface (secrets redacted)
            $this->dumpTenantTable($zip, 'integrations.json', \App\Models\Integration::class, $tenant->id);
            $this->dumpTenantTable(
                $zip,
                'api_keys.json',
                \App\Models\ApiKey::class,
                $tenant->id,
                redact: ['secret', 'api_key', 'token', 'key', 'hashed_key'],
            );

            // Audit log
            $this->dumpTenantTable($zip, 'audit_logs.json', \App\Models\AuditLog::class, $tenant->id);

            // Manifest is written LAST so it can include the final
            // file/byte counts and the list of skipped files.
            $this->writeManifest($zip, $tenant, $maxBytes);

            $zip->close();
            DB::commit();
        } catch (\Throwable $e) {
            DB::rollBack();
            $zip->close();
            @unlink($absolutePath);
            // Mirror status into the polling UI so the operator sees
            // the failure rather than a stuck 'processing' state.
            $tenant->forceFill(['data_export_status' => 'failed'])->save();
            throw $e;
        }

        $size = filesize($absolutePath) ?: 0;
        $token = $this->mintToken($tenant, $requester, $relativePath);

        // Stamp the polling-UI columns with the finished export so
        // /admin/privacy can offer the in-page download even before
        // the user opens the notification email.
        $tenant->forceFill([
            'data_export_status'     => 'ready',
            'data_export_path'       => $relativePath,
            'data_export_size_bytes' => (int) $size,
            'data_export_expires_at' => Carbon::now()->addSeconds(self::DOWNLOAD_TTL_SECONDS),
        ])->save();

        // Build the URL after the token is in cache so a race-y
        // download attempt always sees a valid cache entry.
        $downloadUrl = url("/admin/data-export/download/{$token}");

        try {
            Notification::send($requester, new TenantDataExportReady(
                downloadUrl: $downloadUrl,
                expiresAt:   Carbon::now()->addSeconds(self::DOWNLOAD_TTL_SECONDS),
                sizeBytes:   (int) $size,
                tenantName:  $tenant->name,
            ));
        } catch (\Throwable $e) {
            // Notification failure must not lose the artifact —
            // the user can still re-request from /admin/privacy.
            Log::warning('Tenant export notification failed', [
                'tenant_id' => $tenant->id,
                'error'     => $e->getMessage(),
            ]);
        }

        Log::info('Tenant data export ready', [
            'tenant_id'      => $tenant->id,
            'requester_id'   => $requester->id,
            'path'           => $relativePath,
            'size'           => $size,
            'files_included' => $this->filesIncluded,
            'bytes_included' => $this->bytesIncluded,
            'files_skipped'  => count($this->skippedFiles),
        ]);
    }

    /**
     * Final-failure hook called by Laravel after all retries exhaust.
     * Mirrors the status column so the polling UI doesn't stay pinned
     * on "processing" forever when the queue eventually gives up.
     */
    public function failed(\Throwable $e): void
    {
        $tenant = Tenant::query()->withoutGlobalScopes()->find($this->tenant->id);
        if ($tenant) {
            $tenant->forceFill(['data_export_status' => 'failed'])->save();
        }
    }

    /**
     * Mint a 64-char opaque token and stash {tenant_id, user_id,
     * expires_at, path} in cache for 24h.  The DataExportController
     * reads this entry to serve the file.
     */
    protected function mintToken(Tenant $tenant, User $requester, string $relativePath): string
    {
        $token = bin2hex(random_bytes(32));

        Cache::put(
            self::CACHE_PREFIX . $token,
            [
                'tenant_id'  => $tenant->id,
                'user_id'    => $requester->id,
                'path'       => $relativePath,
                'expires_at' => Carbon::now()->addSeconds(self::DOWNLOAD_TTL_SECONDS)->toIso8601String(),
            ],
            self::DOWNLOAD_TTL_SECONDS,
        );

        return $token;
    }

    protected function writeManifest(ZipArchive $zip, Tenant $tenant, int $maxBytes): void
    {
        $zip->addFromString('manifest.json', $this->jsonEncode([
            'gdpr_article'     => 20,
            'gdpr_attribution' => self::ARTICLE_20_ATTRIBUTION,
            'description'      => 'Right to Data Portability — full workspace export in machine-readable JSON.',
            'app_name'         => config('app.name', 'LeadHub'),
            'app_version'      => config('leadhub.version', '1.0.0'),
            'tenant_id'        => $tenant->id,
            'tenant_name'      => $tenant->name,
            'tenant_slug'      => $tenant->slug,
            'exported_at'      => Carbon::now()->toIso8601String(),
            'requester_id'     => $this->requester->id,
            'format_version'   => '1.1',
            'datetime_format'  => 'ISO-8601',
            'files'            => [
                'included_count' => $this->filesIncluded,
                'included_bytes' => $this->bytesIncluded,
                'max_bytes'      => $maxBytes,
                'skipped_count'  => count($this->skippedFiles),
                'skipped'        => $this->skippedFiles,
            ],
        ]));
    }

    protected function writeTenantProfile(ZipArchive $zip, Tenant $tenant): void
    {
        $zip->addFromString('tenant.json', $this->jsonEncode([
            'id'                  => $tenant->id,
            'name'                => $tenant->name,
            'slug'                => $tenant->slug,
            'plan'                => $tenant->plan,
            'subscription_status' => $tenant->subscription_status,
            'created_at'          => $tenant->created_at?->toIso8601String(),
            'settings'            => $tenant->settings,
            'branding'            => $tenant->branding,
        ]));
    }

    /**
     * Dump only the users that belong to THIS tenant (via the
     * tenant_users pivot OR users.tenant_id).  Avoids leaking
     * super-admin / cross-tenant accounts.  Password hashes,
     * 2FA secrets, and remember tokens are dropped.
     */
    protected function writeTenantUsers(ZipArchive $zip, Tenant $tenant): void
    {
        try {
            $users = User::query()
                ->withoutGlobalScopes()
                ->where(function ($q) use ($tenant) {
                    $q->where('tenant_id', $tenant->id)
                      ->orWhereHas('tenants', fn ($pivot) => $pivot->where('tenants.id', $tenant->id));
                })
                ->get()
                ->map(fn (User $u) => [
                    'id'         => $u->id,
                    'name'       => $u->name,
                    'email'      => $u->email,
                    'tenant_id'  => $u->tenant_id,
                    'timezone'   => $u->timezone,
                    'created_at' => $u->created_at?->toIso8601String(),
                ])
                ->all();

            $zip->addFromString('users.json', $this->jsonEncode($users));
        } catch (\Throwable $e) {
            $zip->addFromString('users.json', $this->jsonEncode([
                '_error' => 'Could not export users: ' . $e->getMessage(),
            ]));
        }
    }

    /**
     * Stream rows from a tenant-scoped Eloquent model into the ZIP.
     * Bypasses BelongsToTenant global scope (no auth user in queue
     * context) but still constrains by an explicit tenant_id WHERE.
     *
     * @param  array<string>      $redact  Columns to blank out
     * @param  array<int, string> $with    Eloquent relations to eager-load and serialize
     */
    protected function dumpTenantTable(
        ZipArchive $zip,
        string $filename,
        string $modelClass,
        int $tenantId,
        array $redact = [],
        array $with = [],
    ): void {
        if (! class_exists($modelClass)) {
            return;
        }

        try {
            $query = $modelClass::query()
                ->withoutGlobalScopes()
                ->where('tenant_id', $tenantId);

            if (! empty($with)) {
                $query = $query->with($with);
            }

            $rows = [];
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
     * Copy every lead_attachments row's underlying file into the ZIP at
     * `attachments/{id}_{originalName}`. Chunked so a workspace with
     * thousands of attachments cannot OOM the queue worker. Each file
     * is size-checked against the running total before it is added —
     * over the cap, it is recorded in `$skippedFiles` and the manifest
     * surfaces the omission.
     */
    protected function attachLeadAttachmentFiles(ZipArchive $zip, int $tenantId, int $maxBytes): void
    {
        if (! class_exists(\App\Models\LeadAttachment::class)) {
            return;
        }

        try {
            \App\Models\LeadAttachment::query()
                ->withoutGlobalScopes()
                ->where('tenant_id', $tenantId)
                ->orderBy('id')
                ->chunk(self::FILE_CHUNK_SIZE, function ($attachments) use ($zip, $maxBytes) {
                    foreach ($attachments as $attachment) {
                        $disk = $attachment->disk ?: 'local';
                        $path = (string) ($attachment->path ?? '');
                        $name = (string) ($attachment->original_name ?? $attachment->filename ?? ('file_' . $attachment->id));
                        $entry = sprintf('attachments/%d_%s', $attachment->id, $this->safeFilename($name));

                        $this->copyFileToZip($zip, $disk, $path, $entry, $maxBytes);
                    }
                });
        } catch (\Throwable $e) {
            Log::warning('Tenant export: lead_attachments file copy failed', [
                'tenant_id' => $tenantId,
                'error'     => $e->getMessage(),
            ]);
        }
    }

    /**
     * Public form file uploads land under `form-uploads/{form_id}/...`
     * on the local disk and are referenced from `form_submission_values`
     * rows belonging to file-typed fields. Walks those values for the
     * tenant's forms and copies each pointed-to file into the ZIP.
     */
    protected function attachFormUploads(ZipArchive $zip, int $tenantId, int $maxBytes): void
    {
        $valueClass      = \App\Models\FormSubmissionValue::class;
        $submissionClass = \App\Models\FormSubmission::class;
        $fieldClass      = \App\Models\FormField::class;

        if (! class_exists($valueClass) || ! class_exists($submissionClass) || ! class_exists($fieldClass)) {
            return;
        }

        try {
            $tenantSubmissionIds = $submissionClass::query()
                ->withoutGlobalScopes()
                ->where('tenant_id', $tenantId)
                ->pluck('id');

            if ($tenantSubmissionIds->isEmpty()) {
                return;
            }

            $fileFieldIds = $fieldClass::query()
                ->withoutGlobalScopes()
                ->where('type', 'file')
                ->pluck('id');

            if ($fileFieldIds->isEmpty()) {
                return;
            }

            $valueClass::query()
                ->withoutGlobalScopes()
                ->whereIn('form_submission_id', $tenantSubmissionIds)
                ->whereIn('form_field_id', $fileFieldIds)
                ->orderBy('id')
                ->chunk(self::FILE_CHUNK_SIZE, function ($values) use ($zip, $maxBytes) {
                    foreach ($values as $value) {
                        $path = (string) ($value->value ?? '');
                        if ($path === '') {
                            continue;
                        }
                        $entry = 'form-uploads/' . $value->id . '_' . $this->safeFilename(basename($path));
                        $this->copyFileToZip($zip, 'local', $path, $entry, $maxBytes);
                    }
                });
        } catch (\Throwable $e) {
            Log::warning('Tenant export: form upload copy failed', [
                'tenant_id' => $tenantId,
                'error'     => $e->getMessage(),
            ]);
        }
    }

    /**
     * CSV / XLSX import payloads — the original spreadsheet a tenant
     * uploaded for ProcessLeadImport. Restored alongside the import
     * row's metadata so the user could re-run the same import.
     */
    protected function attachLeadImportFiles(ZipArchive $zip, int $tenantId, int $maxBytes): void
    {
        if (! class_exists(\App\Models\LeadImport::class)) {
            return;
        }

        try {
            \App\Models\LeadImport::query()
                ->withoutGlobalScopes()
                ->where('tenant_id', $tenantId)
                ->orderBy('id')
                ->chunk(self::FILE_CHUNK_SIZE, function ($imports) use ($zip, $maxBytes) {
                    foreach ($imports as $import) {
                        $path = (string) ($import->file_path ?? '');
                        if ($path === '') {
                            continue;
                        }
                        $name = (string) ($import->original_filename ?? basename($path));
                        $entry = sprintf('imports/%d_%s', $import->id, $this->safeFilename($name));
                        $this->copyFileToZip($zip, 'local', $path, $entry, $maxBytes);
                    }
                });
        } catch (\Throwable $e) {
            Log::warning('Tenant export: lead_imports file copy failed', [
                'tenant_id' => $tenantId,
                'error'     => $e->getMessage(),
            ]);
        }
    }

    /**
     * Copy a single file from a Laravel disk into a ZIP entry, honoring
     * the running byte cap. Missing files are recorded in the manifest's
     * skipped list with a clear reason so the caller can investigate
     * without surprises.
     */
    protected function copyFileToZip(
        ZipArchive $zip,
        string $disk,
        string $sourcePath,
        string $entry,
        int $maxBytes,
    ): void {
        try {
            if ($sourcePath === '' || ! Storage::disk($disk)->exists($sourcePath)) {
                $this->skippedFiles[] = [
                    'path'   => $entry,
                    'reason' => 'source-missing',
                    'size'   => 0,
                ];
                return;
            }

            $size = (int) Storage::disk($disk)->size($sourcePath);

            if ($this->bytesIncluded + $size > $maxBytes) {
                $this->skippedFiles[] = [
                    'path'   => $entry,
                    'reason' => 'over-size-cap',
                    'size'   => $size,
                ];
                return;
            }

            $absolute = Storage::disk($disk)->path($sourcePath);
            if (! is_file($absolute) || ! is_readable($absolute)) {
                $this->skippedFiles[] = [
                    'path'   => $entry,
                    'reason' => 'unreadable',
                    'size'   => $size,
                ];
                return;
            }

            if (! $zip->addFile($absolute, $entry)) {
                $this->skippedFiles[] = [
                    'path'   => $entry,
                    'reason' => 'zip-add-failed',
                    'size'   => $size,
                ];
                return;
            }

            $this->filesIncluded++;
            $this->bytesIncluded += $size;
        } catch (\Throwable $e) {
            $this->skippedFiles[] = [
                'path'   => $entry,
                'reason' => 'exception:' . $e->getMessage(),
                'size'   => 0,
            ];
        }
    }

    /**
     * Strip path separators and control characters from an upload's
     * original name so it can safely become a ZIP entry name. Falls
     * back to a generic placeholder when the cleaned value is empty.
     */
    protected function safeFilename(string $name): string
    {
        $clean = preg_replace('/[^A-Za-z0-9._\- ]+/', '_', basename($name));
        $clean = trim((string) $clean);
        return $clean !== '' ? $clean : 'file';
    }

    /**
     * Resolved size cap for the export, in bytes. Reads
     * `leadhub.export_max_bytes` (wired from LH_EXPORT_MAX_BYTES in
     * config/leadhub.php); non-positive values fall back to the 2 GB
     * default so a misconfigured value never produces an empty export.
     *
     * Uses config() rather than env() directly so the cap still applies
     * after `php artisan config:cache` — env() outside config files
     * returns null once the config is cached.
     */
    protected function maxExportBytes(): int
    {
        $configured = (int) config('leadhub.export_max_bytes', self::DEFAULT_MAX_EXPORT_BYTES);
        return $configured > 0 ? $configured : self::DEFAULT_MAX_EXPORT_BYTES;
    }

    protected function jsonEncode(mixed $value): string
    {
        return json_encode(
            $value,
            JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES,
        ) ?: '[]';
    }
}
