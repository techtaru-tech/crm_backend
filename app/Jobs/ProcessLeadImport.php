<?php

namespace App\Jobs;

use App\Models\Lead;
use App\Models\LeadImport;
use App\Services\LeadActivityService;
use App\Services\LeadDuplicateDetector;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Queue\Queueable;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Storage;
use App\Imports\HeadingRowDataImport;
use App\Support\LeadFieldNormalizer;
use Maatwebsite\Excel\Facades\Excel;

class ProcessLeadImport implements ShouldQueue
{
    use Queueable;

    public int $timeout = 600;
    public int $tries   = 1;

    // $tenantId is read by AppServiceProvider's Queue::before hook
    // (reflection on the resolved command) to bind `current_tenant`
    // for the duration of the worker run, so BelongsToTenant queries
    // inside handle() don't fail closed.  Default null keeps existing
    // positional dispatches working until the dispatcher catches up.
    public function __construct(public int $importId, public ?int $tenantId = null) {}

    /** Hard cap so a malicious or runaway CSV can't OOM PHP-FPM. */
    private const MAX_ROWS = 100_000;

    /** PHP chunk size for in-memory progress-flush cadence. */
    private const CHUNK_SIZE = 500;

    public function handle(LeadActivityService $activity, LeadDuplicateDetector $detector): void
    {
        $import = LeadImport::find($this->importId);
        if (! $import) {
            return;
        }

        $import->update(['status' => 'processing']);

        try {
            // Read from the SAME 'local' disk the CreateLeadImport upload
            // wrote to (not the default disk), so a non-local FILESYSTEM_DISK
            // can't make the worker miss the file.
            $path = Storage::disk('local')->path($import->file_path);

            // H18: hard-cap before allocating PHP memory.  fgets-based
            // line counter peeks at the file without loading it through
            // PhpSpreadsheet — much cheaper than Excel::toArray's full
            // parse, and rejects mass-stuffed CSVs early.  Counts the
            // header row, so we subtract 1 for the user-visible row
            // count.  Fails open if fopen errors (returns 0) so a
            // permissions glitch doesn't fail every import; Excel's
            // own memory_limit ceiling still applies in that case.
            $rowCount = $this->countDataRows($path);
            if ($rowCount > self::MAX_ROWS) {
                $import->update([
                    'status' => 'failed',
                    'errors' => [['error' => (string) __('filament/lead_imports.job_row_cap_exceeded', [
                        'max'  => number_format(self::MAX_ROWS),
                        'rows' => number_format($rowCount),
                    ])]],
                ]);
                return;
            }

            $mapping = $import->column_mapping ?? [];
            // HeadingRowImport is WithStartRow(1) + WithLimit(1) — it returns
            // the heading row and nothing else, so this loop used to walk a
            // single list-keyed row, match no mapped column and import zero
            // leads.  HeadingRowDataImport returns the data rows keyed by the
            // same slugged headings the column_mapping was captured against.
            $sheets  = Excel::toArray(new HeadingRowDataImport, $path);
            $rows    = $sheets[0] ?? [];

            $total    = count($rows);
            $imported = 0;
            $dupes    = 0;
            $errors   = [];

            $import->update(['total_rows' => $total]);

            // Process in chunks of CHUNK_SIZE so progress flushes back
            // to the import row periodically — a 50k-row CSV no longer
            // hides its progress bar at 0% for the entire job duration.
            // The polling status widget on /admin/imports picks up
            // imported_count/duplicate_count/error_count between chunks.
            foreach (array_chunk($rows, self::CHUNK_SIZE, true) as $chunk) {
                foreach ($chunk as $index => $row) {
                    try {
                        $data = [];
                        $customFields = [];
                        $followUpAt = null;
                        foreach ($mapping as $csvCol => $leadField) {
                            if (! $leadField || ! isset($row[$csvCol]) || $row[$csvCol] === '') {
                                continue;
                            }
                            // "custom_fields.<key>" targets go into the JSON
                            // column; everything else is a real Lead attribute.
                            if (str_starts_with($leadField, 'custom_fields.')) {
                                $customFields[substr($leadField, 14)] = $row[$csvCol];
                            } elseif ($leadField === 'assigned_user') {
                                // Spreadsheets carry a person's name or email,
                                // not a user id.  Unresolvable names leave the
                                // lead unassigned rather than failing the row —
                                // an import of 500 leads must not die because
                                // one rep left the company.
                                if ($userId = $this->resolveUserId($import->tenant_id, (string) $row[$csvCol])) {
                                    $data['assigned_user_id'] = $userId;
                                }
                            } elseif ($leadField === 'next_follow_up') {
                                // Held back: it becomes a LeadTask once the lead
                                // exists.  Lead::next_follow_up_at is derived from
                                // open tasks, so writing the column directly would
                                // be overwritten by the first task that saves.
                                $followUpAt = LeadFieldNormalizer::date((string) $row[$csvCol]);
                            } elseif ($leadField === 'contacted_at') {
                                if ($when = LeadFieldNormalizer::date((string) $row[$csvCol])) {
                                    $data['contacted_at'] = $when;
                                }
                            } elseif ($leadField === 'deal_value') {
                                if (($amount = LeadFieldNormalizer::money((string) $row[$csvCol])) !== null) {
                                    $data['deal_value'] = $amount;
                                }
                            } elseif ($leadField === 'full_name') {
                                // One name column becomes two fields; an
                                // explicit First/Last mapping in the same file
                                // still wins (see the array_merge below).
                                $parts = preg_split('/\\s+/', trim((string) $row[$csvCol]), 2) ?: [];
                                if (($parts[0] ?? '') !== '') {
                                    $data['first_name'] = $data['first_name'] ?? $parts[0];
                                }
                                if (($parts[1] ?? '') !== '') {
                                    $data['last_name'] = $data['last_name'] ?? $parts[1];
                                }
                            } elseif ($leadField === 'phone') {
                                // "p:+9198...", "98765 43210", two numbers in
                                // one cell — all normal in real exports, and
                                // all break phone_normalized (and so duplicate
                                // detection) if stored verbatim.
                                if ($clean = LeadFieldNormalizer::phone((string) $row[$csvCol])) {
                                    $data['phone'] = $clean;
                                }
                            } elseif ($leadField === 'priority') {
                                $data['priority'] = LeadFieldNormalizer::priority((string) $row[$csvCol]);
                            } elseif ($leadField === 'status') {
                                // Spreadsheets write "New", "Negotiation",
                                // "Closed - Won".  Passed through raw these hit
                                // the LeadStatus enum cast and killed the row
                                // with a ValueError — every row, every file.
                                $data['status'] = LeadFieldNormalizer::status((string) $row[$csvCol]);
                            } else {
                                $data[$leadField] = $row[$csvCol];
                            }
                        }
                        if ($customFields) {
                            $data['custom_fields'] = $customFields;
                        }

                        if (empty($data)) {
                            continue;
                        }

                        $email = $data['email'] ?? null;
                        $phone = $data['phone'] ?? null;

                        $existing = $detector->findExisting($import->tenant_id, $email, $phone);
                        if ($existing) {
                            $dupes++;
                            continue;
                        }

                        $lead = Lead::create(array_merge($data, [
                            'tenant_id'    => $import->tenant_id,
                            // Only fall back to 'csv_import' when the file
                            // carried no Lead Source column — array_merge put
                            // this last, so a mapped source was being thrown
                            // away and every imported lead looked like it came
                            // from nowhere.
                            'source'       => $data['source'] ?? 'csv_import',
                            'is_duplicate' => false,
                        ]));

                        $imported++;
                        $activity->logImported($lead, $import->original_filename);

                        if ($followUpAt) {
                            \App\Models\LeadTask::create([
                                'tenant_id'        => $lead->tenant_id,
                                'lead_id'          => $lead->id,
                                'assigned_user_id' => $lead->assigned_user_id,
                                'title'            => __('crm_csv_import.followup_title'),
                                'due_at'           => $followUpAt,
                            ]);
                        }

                    } catch (\Throwable $e) {
                        Log::warning('Lead import row error', [
                            'import_id' => $this->importId,
                            'row'       => $index + 2,
                            'error'     => $e->getMessage(),
                        ]);
                        $errors[] = ['row' => $index + 2, 'error' => $e->getMessage()];
                    }
                }

                // Mid-import progress flush.
                $import->update([
                    'imported_count'  => $imported,
                    'duplicate_count' => $dupes,
                    'error_count'     => count($errors),
                ]);
            }

            $import->update([
                'status'          => 'completed',
                'imported_count'  => $imported,
                'duplicate_count' => $dupes,
                'error_count'     => count($errors),
                'errors'          => $errors ?: null,
            ]);
        } catch (\Throwable $e) {
            $import->update(['status' => 'failed', 'errors' => [['error' => $e->getMessage()]]]);
            Log::error('Lead import failed', ['import_id' => $this->importId, 'error' => $e->getMessage()]);
        }
    }

    /**
     * Cheap line-count pre-check using fgets — avoids loading the
     * whole spreadsheet through PhpSpreadsheet just to learn the row
     * count.  Returns the count of DATA rows (excluding the header).
     * Returns 0 on fopen failure so the import still attempts to run
     * (Excel::toArray will then enforce PHP's memory_limit).
     */
    /** @var array<string, int|null> memo so a 5k-row import does one lookup per distinct name */
    private array $userLookupCache = [];

    /**
     * Resolve a CSV "Assigned User" cell to a user id within this tenant.
     * Email first (unambiguous), then an exact name match.
     */
    private function resolveUserId(int $tenantId, string $value): ?int
    {
        $value = trim($value);
        if ($value === '') {
            return null;
        }

        if (array_key_exists($value, $this->userLookupCache)) {
            return $this->userLookupCache[$value];
        }

        $query = \App\Models\User::withoutGlobalScopes()->where('tenant_id', $tenantId);

        $id = str_contains($value, '@')
            ? (clone $query)->where('email', $value)->value('id')
            : (clone $query)->where('name', $value)->value('id');

        return $this->userLookupCache[$value] = $id ? (int) $id : null;
    }

    private function countDataRows(string $absPath): int
    {
        $fp = @fopen($absPath, 'r');
        if ($fp === false) {
            return 0;
        }
        $count = 0;
        while (fgets($fp) !== false) {
            $count++;
        }
        fclose($fp);
        return max(0, $count - 1);
    }

    public function failed(\Throwable $exception): void
    {
        Log::error('ProcessLeadImport job failed at queue level', [
            'import_id' => $this->importId,
            'error'     => $exception->getMessage(),
        ]);

        LeadImport::where('id', $this->importId)
            ->whereIn('status', ['processing', 'pending'])
            ->update([
                'status' => 'failed',
                'errors' => json_encode([['error' => $exception->getMessage()]]),
            ]);
    }
}
