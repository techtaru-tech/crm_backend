<?php

declare(strict_types=1);

namespace App\Services\Migration;

use App\Models\Lead;
use App\Models\Tenant;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

/**
 * Vendor-aware CSV importer for migrating from competitor CRMs.
 *
 * The existing ProcessLeadImport job handles generic CSVs — operator
 * tells it which column maps to which field.  That works but is
 * friction for the buyer's tenants migrating from a known vendor:
 * they shouldn't have to remember that HubSpot calls it "Email" but
 * Pipedrive calls it "Person email".
 *
 * Three vendors covered out of the box:
 *   - HubSpot   (Contacts CSV export — "First Name", "Last Name",
 *                "Email", "Phone Number", "Lead Status", "Company name")
 *   - Pipedrive (Persons CSV export — "Name", "Email", "Phone",
 *                "Organization - Name", "Owner")
 *   - Salesforce (Lead/Contact CSV export — "FirstName", "LastName",
 *                 "Email", "Phone", "LeadSource", "Company")
 *
 * detect() sniffs the header row and picks the right mapper.  The
 * caller can pass `vendor` explicitly when they know which format
 * they have.
 *
 * Imports happen in batches of 200 inside a transaction so a partial
 * failure rolls back cleanly.
 */
class CrmCsvImporter
{
    public const VENDOR_HUBSPOT    = 'hubspot';
    public const VENDOR_PIPEDRIVE  = 'pipedrive';
    public const VENDOR_SALESFORCE = 'salesforce';
    public const VENDOR_GENERIC    = 'generic';

    /**
     * Brand-only vendor map.  Brand names stay literal (HubSpot,
     * Pipedrive, Salesforce); the generic vendor's display label is
     * translator-resolved at request time via labels() — constants are
     * evaluated at class-load before the translator is bound.
     */
    public const VENDORS = [
        self::VENDOR_HUBSPOT    => 'HubSpot',
        self::VENDOR_PIPEDRIVE  => 'Pipedrive',
        self::VENDOR_SALESFORCE => 'Salesforce',
        self::VENDOR_GENERIC    => 'Generic CSV',
    ];

    /**
     * Translator-resolved vendor labels for UI rendering.
     *
     * @return array<string, string>
     */
    public static function labels(): array
    {
        return [
            self::VENDOR_HUBSPOT    => 'HubSpot',
            self::VENDOR_PIPEDRIVE  => 'Pipedrive',
            self::VENDOR_SALESFORCE => 'Salesforce',
            self::VENDOR_GENERIC    => __('crm_csv_import.vendor_generic_csv'),
        ];
    }

    /**
     * @return array{imported:int, skipped:int, errors:array<string>, vendor:string}
     */
    public function import(string $filePath, Tenant $tenant, ?string $vendor = null): array
    {
        if (! is_readable($filePath)) {
            return $this->result(0, 0, [__('crm_csv_import.file_not_readable', ['path' => $filePath])], $vendor ?? 'unknown');
        }

        $handle = fopen($filePath, 'r');
        if (! $handle) {
            return $this->result(0, 0, [__('crm_csv_import.could_not_open')], $vendor ?? 'unknown');
        }

        $headers = fgetcsv($handle);
        if (! $headers) {
            fclose($handle);
            return $this->result(0, 0, [__('crm_csv_import.no_header_row')], $vendor ?? 'unknown');
        }

        // Strip UTF-8 BOM from first header — Excel and many CRM
        // exports prepend \xEF\xBB\xBF, which silently breaks every
        // column-name comparison if not removed.
        if (isset($headers[0])) {
            $headers[0] = preg_replace('/^\xEF\xBB\xBF/', '', (string) $headers[0]) ?? $headers[0];
        }

        $headers = array_map(fn ($h) => trim((string) $h), $headers);

        $vendor = $vendor ?? $this->detect($headers);
        $mapper = $this->mapperFor($vendor);

        $imported = 0;
        $skipped  = 0;
        $errors   = [];
        $batch    = [];

        try {
            while (($row = fgetcsv($handle)) !== false) {
                if (count($row) < 1 || (count($row) === 1 && $row[0] === '')) {
                    continue;
                }
                $padded = $row + array_fill(0, max(0, count($headers) - count($row)), null);
                $assoc  = @array_combine($headers, $padded);
                if (! $assoc) {
                    $skipped++;
                    continue;
                }

                $mapped = $mapper($assoc);
                if (empty($mapped['email']) && empty($mapped['phone'])) {
                    $skipped++; // need at least one contact channel
                    continue;
                }

                $mapped['tenant_id'] = $tenant->id;
                $batch[] = $mapped;

                if (count($batch) >= 200) {
                    $imported += $this->flushBatch($batch, $errors);
                    $batch = [];
                }
            }

            if (! empty($batch)) {
                $imported += $this->flushBatch($batch, $errors);
            }
        } finally {
            fclose($handle);
        }

        return $this->result($imported, $skipped, $errors, $vendor);
    }

    /**
     * Sniff CSV headers to identify the vendor.  Returns generic when
     * no signature matches.
     */
    public function detect(array $headers): string
    {
        $h = array_map('strtolower', $headers);

        if (in_array('lead status', $h, true) || in_array('hubspot owner', $h, true)) {
            return self::VENDOR_HUBSPOT;
        }

        foreach ($h as $col) {
            if (str_contains($col, 'organization - ') || str_contains($col, 'person - ')) {
                return self::VENDOR_PIPEDRIVE;
            }
        }

        if (in_array('leadsource', $h, true) || in_array('firstname', $h, true)) {
            return self::VENDOR_SALESFORCE;
        }

        return self::VENDOR_GENERIC;
    }

    protected function mapperFor(string $vendor): callable
    {
        return match ($vendor) {
            self::VENDOR_HUBSPOT    => fn (array $r) => [
                'first_name' => $r['First Name']   ?? $r['first_name'] ?? null,
                'last_name'  => $r['Last Name']    ?? $r['last_name']  ?? null,
                'email'      => $r['Email']        ?? null,
                'phone'      => $r['Phone Number'] ?? $r['Mobile Phone Number'] ?? null,
                'source'     => $r['Original Source'] ?? __('crm_csv_import.source_brand_import', ['brand' => 'HubSpot']),
                'status'     => $this->normalizeStatus($r['Lead Status'] ?? null),
                'notes'      => $r['Notes']        ?? null,
            ],
            self::VENDOR_PIPEDRIVE => fn (array $r) => [
                'first_name' => $this->splitFirst($r['Name'] ?? null),
                'last_name'  => $this->splitLast($r['Name']  ?? null),
                'email'      => $r['Email']  ?? null,
                'phone'      => $r['Phone']  ?? null,
                'source'     => $r['Organization - Name'] ?? __('crm_csv_import.source_brand_import', ['brand' => 'Pipedrive']),
                'status'     => 'new',
                'notes'      => $r['Notes']  ?? null,
            ],
            self::VENDOR_SALESFORCE => fn (array $r) => [
                'first_name' => $r['FirstName'] ?? null,
                'last_name'  => $r['LastName']  ?? null,
                'email'      => $r['Email']     ?? null,
                'phone'      => $r['Phone']     ?? $r['MobilePhone'] ?? null,
                'source'     => $r['LeadSource'] ?? __('crm_csv_import.source_brand_import', ['brand' => 'Salesforce']),
                'status'     => $this->normalizeStatus($r['Status'] ?? null),
                'notes'      => $r['Description'] ?? null,
            ],
            default => fn (array $r) => [
                'first_name' => $r['first_name'] ?? $r['First Name'] ?? null,
                'last_name'  => $r['last_name']  ?? $r['Last Name']  ?? null,
                'email'      => $r['email']      ?? $r['Email']      ?? null,
                'phone'      => $r['phone']      ?? $r['Phone']      ?? null,
                'source'     => $r['source']     ?? $r['Source']     ?? __('crm_csv_import.source_csv_import'),
                'status'     => 'new',
                'notes'      => $r['notes']      ?? $r['Notes']      ?? null,
            ],
        };
    }

    protected function normalizeStatus(?string $raw): string
    {
        if (! $raw) return 'new';
        $r = strtolower(trim($raw));
        return match (true) {
            str_contains($r, 'closed - won') || str_contains($r, 'won') => 'won',
            str_contains($r, 'closed - lost') || str_contains($r, 'lost') => 'lost',
            str_contains($r, 'qualif')                    => 'qualified',
            str_contains($r, 'propos') || str_contains($r, 'pitch') => 'proposal',
            str_contains($r, 'contact') || str_contains($r, 'reach') => 'contacted',
            str_contains($r, 'new')                       => 'new',
            default                                        => 'new',
        };
    }

    protected function splitFirst(?string $full): ?string
    {
        if (! $full) return null;
        $parts = preg_split('/\s+/', trim($full), 2);
        return $parts[0] ?? null;
    }

    protected function splitLast(?string $full): ?string
    {
        if (! $full) return null;
        $parts = preg_split('/\s+/', trim($full), 2);
        return $parts[1] ?? null;
    }

    protected function flushBatch(array $batch, array &$errors): int
    {
        try {
            return DB::transaction(function () use ($batch) {
                $inserted = 0;
                foreach ($batch as $row) {
                    try {
                        Lead::create($row);
                        $inserted++;
                    } catch (\Throwable $e) {
                        Log::debug('CrmCsvImporter row failed', ['error' => $e->getMessage()]);
                    }
                }
                return $inserted;
            });
        } catch (\Throwable $e) {
            $errors[] = __('crm_csv_import.batch_failed_prefix') . $e->getMessage();
            return 0;
        }
    }

    protected function result(int $imported, int $skipped, array $errors, string $vendor): array
    {
        return [
            'imported' => $imported,
            'skipped'  => $skipped,
            'errors'   => array_slice($errors, 0, 10),
            'vendor'   => $vendor,
        ];
    }
}
