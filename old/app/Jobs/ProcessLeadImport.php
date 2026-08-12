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
use Maatwebsite\Excel\Facades\Excel;
use Maatwebsite\Excel\HeadingRowImport;

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
            $sheets  = Excel::toArray(new HeadingRowImport, $path);
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
                        foreach ($mapping as $csvCol => $leadField) {
                            if (! $leadField || ! isset($row[$csvCol]) || $row[$csvCol] === '') {
                                continue;
                            }
                            // "custom_fields.<key>" targets go into the JSON
                            // column; everything else is a real Lead attribute.
                            if (str_starts_with($leadField, 'custom_fields.')) {
                                $customFields[substr($leadField, 14)] = $row[$csvCol];
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
                            'source'       => 'csv_import',
                            'is_duplicate' => false,
                        ]));

                        $imported++;
                        $activity->logImported($lead, $import->original_filename);

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
