<?php

namespace App\Jobs;

use App\Models\Company;
use App\Models\CompanyImport;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Queue\Queueable;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Storage;
use Maatwebsite\Excel\Facades\Excel;
use Maatwebsite\Excel\HeadingRowImport;

/**
 * Processes a queued CompanyImport batch: reads the uploaded CSV/Excel,
 * maps columns to Company fields, and creates Company rows (skipping
 * domain/name duplicates). Mirrors ProcessLeadImport, adapted to the
 * Company model (no LeadActivity, domain-based dedup, no Lead-only columns).
 */
class ProcessCompanyImport implements ShouldQueue
{
    use Queueable;

    public int $timeout = 600;
    public int $tries   = 1;

    // $tenantId is read by AppServiceProvider's Queue::before hook
    // (reflection on the resolved command) to bind `current_tenant` for the
    // duration of the worker run, so BelongsToTenant queries inside handle()
    // (e.g. CompanyImport::find) don't fail closed.
    public function __construct(public int $importId, public ?int $tenantId = null) {}

    /** Hard cap so a malicious or runaway CSV can't OOM PHP-FPM. */
    private const MAX_ROWS = 100_000;

    /** PHP chunk size for in-memory progress-flush cadence. */
    private const CHUNK_SIZE = 500;

    public function handle(): void
    {
        $import = CompanyImport::find($this->importId);
        if (! $import) {
            return;
        }

        $import->update(['status' => 'processing']);

        try {
            // Read from the SAME 'local' disk the upload wrote to (not the
            // default disk), so a non-local FILESYSTEM_DISK can't make the
            // worker miss the file.
            $path = Storage::disk('local')->path($import->file_path);

            // Cheap line-count pre-check before allocating PHP memory.
            $rowCount = $this->countDataRows($path);
            if ($rowCount > self::MAX_ROWS) {
                $import->update([
                    'status' => 'failed',
                    'errors' => [['error' => (string) __('filament/company_imports.job_row_cap_exceeded', [
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

            foreach (array_chunk($rows, self::CHUNK_SIZE, true) as $chunk) {
                foreach ($chunk as $index => $row) {
                    try {
                        $data = [];
                        $customFields = [];
                        foreach ($mapping as $csvCol => $companyField) {
                            if (! $companyField || ! isset($row[$csvCol]) || $row[$csvCol] === '') {
                                continue;
                            }
                            // "custom_fields.<key>" targets go into the JSON
                            // column; everything else is a real Company attribute.
                            if (str_starts_with($companyField, 'custom_fields.')) {
                                $customFields[substr($companyField, 14)] = $row[$csvCol];
                            } else {
                                $data[$companyField] = $row[$csvCol];
                            }
                        }
                        if ($customFields) {
                            $data['custom_fields'] = $customFields;
                        }

                        // companies.name is NOT NULL — skip rows without one.
                        if (empty($data['name'])) {
                            continue;
                        }

                        // Normalise domain the same way CreateCompany does so the
                        // dedup key matches UI-created records.
                        if (! empty($data['domain'])) {
                            $data['domain'] = strtolower(trim((string) $data['domain']));
                        }

                        if ($this->isDuplicate($import->tenant_id, $data)) {
                            $dupes++;
                            continue;
                        }

                        Company::create(array_merge($data, [
                            'tenant_id' => $import->tenant_id,
                        ]));

                        $imported++;

                    } catch (\Throwable $e) {
                        Log::warning('Company import row error', [
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
            Log::error('Company import failed', ['import_id' => $this->importId, 'error' => $e->getMessage()]);
        }
    }

    /**
     * Domain-first duplicate check, mirroring Company::findOrCreateByDomain's
     * (tenant_id, domain) identity. Falls back to (tenant_id, name) when the
     * row has no domain. withoutGlobalScope('tenant') because the queue worker
     * may lack an auth context (the tenant is passed + matched explicitly).
     */
    private function isDuplicate(int $tenantId, array $data): bool
    {
        $domain = $data['domain'] ?? null;
        $name   = $data['name'] ?? null;

        $base = Company::query()
            ->withoutGlobalScope('tenant')
            ->where('tenant_id', $tenantId);

        if (! empty($domain)) {
            return (clone $base)->where('domain', $domain)->exists();
        }

        if (! empty($name)) {
            return (clone $base)->where('name', $name)->exists();
        }

        return false;
    }

    /**
     * Cheap fgets line-count (excluding header) so we can reject mass-stuffed
     * CSVs before PhpSpreadsheet loads the whole file. Returns 0 on fopen
     * failure (fails open — Excel's memory_limit still applies).
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
        Log::error('ProcessCompanyImport job failed at queue level', [
            'import_id' => $this->importId,
            'error'     => $exception->getMessage(),
        ]);

        CompanyImport::where('id', $this->importId)
            ->whereIn('status', ['processing', 'pending'])
            ->update([
                'status' => 'failed',
                'errors' => json_encode([['error' => $exception->getMessage()]]),
            ]);
    }
}
