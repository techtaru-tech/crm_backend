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
     * @return array{imported:int, skipped:int, duplicates:int, errors:array<string>, vendor:string}
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

        $imported   = 0;
        $skipped    = 0;
        $duplicates = 0;
        $errors     = [];
        $batch      = [];

        /** @var array<int, string> headers this file carries that map to no Lead attribute */
        $unmappedSeen = [];

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

                // Vendor mappers read a fixed handful of columns; this fills
                // everything else in the file and parks the leftovers.
                [$mapped, $followUpAt, $unmappedHeaders] = $this->enrichFromRow($mapped, $assoc, $tenant);
                $unmappedSeen = array_unique([...$unmappedSeen, ...$unmappedHeaders]);

                if (empty($mapped['email']) && empty($mapped['phone'])) {
                    $skipped++; // need at least one contact channel
                    continue;
                }

                $mapped['tenant_id'] = $tenant->id;
                $batch[] = ['attributes' => $mapped, 'follow_up_at' => $followUpAt];

                if (count($batch) >= 200) {
                    [$ins, $dup] = $this->flushBatch($batch, $errors);
                    $imported   += $ins;
                    $duplicates += $dup;
                    $batch = [];
                }
            }

            if (! empty($batch)) {
                [$ins, $dup] = $this->flushBatch($batch, $errors);
                $imported   += $ins;
                $duplicates += $dup;
            }
        } finally {
            fclose($handle);
        }

        // Register the leftover columns as custom fields so the values that
        // landed in custom_fields are actually visible on the lead page and
        // editable in the form — otherwise "imported" would mean "stored
        // where nobody can see it".
        $this->ensureCustomFields($tenant, $unmappedSeen);

        return $this->result($imported, $skipped, $errors, $vendor, $duplicates);
    }

    /**
     * Sniff CSV headers to identify the vendor.  Returns generic when
     * no signature matches.
     */
    public function detect(array $headers): string
    {
        $h = array_map('strtolower', $headers);

        // "Lead Status" alone used to trigger this branch, but that header
        // appears in almost every hand-rolled CSV — a plain spreadsheet was
        // being treated as a HubSpot export, which overwrote its real Lead
        // Source column with the literal "HubSpot Import".  Match only on
        // headers HubSpot actually emits.
        foreach (['hubspot owner', 'record id', 'associated company', 'original source'] as $signature) {
            if (in_array($signature, $h, true)) {
                return self::VENDOR_HUBSPOT;
            }
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

    /**
     * Header aliases for the columns a real-world lead export actually
     * carries, keyed by the Lead attribute they land in.
     *
     * The vendor mappers above only ever read seven fields, so a spreadsheet
     * with City, Company, Budget, Assigned To and Next Follow Up quietly lost
     * ten of its seventeen columns.  enrichFromRow() below fills anything the
     * vendor mapper left empty using this table, so every vendor benefits and
     * a plain CSV no longer arrives half-empty.
     *
     * Aliases are compared after normalisation (lowercase, letters and digits
     * only), so "Lead Source", "lead_source" and "LeadSource" all match.
     *
     * @var array<string, array<int, string>>
     */
    /**
     * @deprecated Headings are matched by {@see \App\Support\CsvHeaderMatcher},
     * which the mapping wizard shares.  Kept as an alias of that table so any
     * caller reading this constant still sees the live list.
     */
    protected const FIELD_ALIASES = \App\Support\CsvHeaderMatcher::ALIASES;

    /** Lowercase, letters+digits only — so header spelling stops mattering. */
    protected function normalizeHeader(string $header): string
    {
        return preg_replace('/[^a-z0-9]/', '', strtolower($header)) ?? '';
    }

    /**
     * Fill everything the vendor mapper did not, and make sure no column is
     * silently dropped.
     *
     * Columns that match no alias go to `custom_fields` keyed by a slug of
     * their header, and the untouched original row is kept in `raw_data`.
     * Returns the follow-up date separately: it becomes a LeadTask after the
     * lead exists, because Lead::next_follow_up_at is derived from tasks and
     * writing it directly would be overwritten by the first task that saves.
     *
     * @return array{0: array<string, mixed>, 1: ?string, 2: array<int, string>}
     */
    protected function enrichFromRow(array $mapped, array $assoc, Tenant $tenant): array
    {
        $byField  = [];
        $unmapped = [];

        foreach ($assoc as $header => $value) {
            $header = (string) $header;
            if ($header === '') {
                continue;
            }

            $field = \App\Support\CsvHeaderMatcher::match($header);

            if ($field === null) {
                if (filled($value)) {
                    $unmapped[$header] = $value;
                }
                continue;
            }

            if (! array_key_exists($field, $byField) && filled($value)) {
                $byField[$field] = trim((string) $value);
            }
        }

        $fill = function (string $attr, ?string $value) use (&$mapped): void {
            if (filled($value) && blank($mapped[$attr] ?? null)) {
                $mapped[$attr] = $value;
            }
        };

        // A single "Name" column still has to become two fields.
        if (blank($mapped['first_name'] ?? null) && isset($byField['full_name'])) {
            $mapped['first_name'] = $this->splitFirst($byField['full_name']);
            $mapped['last_name']  = $mapped['last_name'] ?: $this->splitLast($byField['full_name']);
        }

        $fill('first_name', $byField['first_name'] ?? null);
        $fill('last_name',  $byField['last_name']  ?? null);
        $fill('email',      $byField['email']      ?? null);
        $fill('phone',      $byField['phone']      ?? null);
        $fill('company',    $byField['company']    ?? null);
        $fill('city',       $byField['city']       ?? null);
        $fill('source_id',  $byField['source_id']  ?? null);
        $fill('notes',      $byField['notes']      ?? null);

        // The vendor mappers hardcode a fallback source ("HubSpot Import",
        // "CSV Import").  A real Lead Source column in the file beats it.
        if (isset($byField['source'])) {
            $mapped['source'] = $byField['source'];
        }

        if (isset($byField['status'])) {
            $mapped['status'] = $this->normalizeStatus($byField['status']);
        }

        if (isset($byField['priority'])) {
            $mapped['priority'] = $this->normalizePriority($byField['priority']);
        }

        if (isset($byField['deal_value'])) {
            if (($amount = \App\Support\LeadFieldNormalizer::money($byField['deal_value'])) !== null) {
                $mapped['deal_value'] = $amount;
            }
        }

        if (isset($byField['assigned'])) {
            // Keep the spreadsheet's own text either way — if the person is
            // not a user here, the name is still worth having.
            $mapped['assigned_to'] = $byField['assigned'];
            if ($userId = $this->resolveUserId($tenant->id, $byField['assigned'])) {
                $mapped['assigned_user_id'] = $userId;
            }
        }

        if (isset($byField['contacted']) && $date = $this->parseDate($byField['contacted'])) {
            $mapped['contacted_at'] = $date;
        }

        if ($unmapped !== []) {
            $custom = [];
            foreach ($unmapped as $header => $value) {
                $custom[$this->customKey($header)] = $value;
            }
            $mapped['custom_fields'] = array_merge($mapped['custom_fields'] ?? [], $custom);
        }

        // Phone last, so it catches both the vendor mappers (which assign
        // $mapped['phone'] directly) and the alias fill above.  Channel
        // prefixes like "p:+9198..." are normal in Google Contacts and
        // WhatsApp exports; stored raw they also poison phone_normalized
        // and so break duplicate detection on phone.
        if (filled($mapped['phone'] ?? null)) {
            $mapped['phone'] = \App\Support\LeadFieldNormalizer::phone((string) $mapped['phone']);
        }

        // Full original row, verbatim — the audit trail for "what did the
        // file actually say" once values have been normalised above.
        $mapped['raw_data'] = $assoc;

        $followUpAt = isset($byField['follow_up'])
            ? $this->parseDate($byField['follow_up'])
            : null;

        return [$mapped, $followUpAt, array_keys($unmapped)];
    }

    /** Slug a CSV header into a custom_fields key. */
    protected function customKey(string $header): string
    {
        return \Illuminate\Support\Str::slug($header, '_') ?: $this->normalizeHeader($header);
    }

    /** @var array<string, int|null> one lookup per distinct name in the file */
    private array $userLookupCache = [];

    /** Resolve an "Assigned To" cell to a user in this workspace. */
    protected function resolveUserId(int $tenantId, string $value): ?int
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

    protected function normalizeStatus(?string $raw): string
    {
        return \App\Support\LeadFieldNormalizer::status($raw);
    }

    protected function normalizePriority(?string $raw): string
    {
        return \App\Support\LeadFieldNormalizer::priority($raw);
    }

    protected function parseDate(?string $raw): ?string
    {
        return \App\Support\LeadFieldNormalizer::date($raw);
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

    /**
     * Insert one batch.  Returns [inserted, duplicates].
     *
     * A row whose (tenant, source, source_id) already exists raises a 1062
     * unique violation.  That used to be caught and written to Log::debug and
     * nothing else, so re-importing a file the tenant had already loaded
     * reported "0 leads imported" with no hint that every row was a known
     * lead — indistinguishable from a broken mapping.  Duplicates are now
     * counted and reported; anything else still lands in $errors so a real
     * failure is not filed away as "already had it".
     *
     * @return array{0:int, 1:int}
     */
    protected function flushBatch(array $batch, array &$errors): array
    {
        try {
            return DB::transaction(function () use ($batch, &$errors) {
                $inserted   = 0;
                $duplicates = 0;

                foreach ($batch as $row) {
                    try {
                        $lead = Lead::create($row['attributes']);
                        $inserted++;

                        // A "Next Follow Up" column becomes a real follow-up,
                        // not just a date on the lead: Lead::next_follow_up_at
                        // is derived from open tasks, so a bare date would be
                        // wiped by the next task that saves — and this way the
                        // imported dates show up in the dashboard's Today /
                        // Overdue tiles straight away.
                        if ($row['follow_up_at']) {
                            \App\Models\LeadTask::create([
                                'tenant_id'        => $lead->tenant_id,
                                'lead_id'          => $lead->id,
                                'assigned_user_id' => $lead->assigned_user_id,
                                'title'            => __('crm_csv_import.followup_title'),
                                'due_at'           => $row['follow_up_at'],
                            ]);
                        }
                    } catch (\Illuminate\Database\UniqueConstraintViolationException $e) {
                        $duplicates++;
                    } catch (\Throwable $e) {
                        // MySQL 1062 also arrives as a plain QueryException on
                        // driver/version combinations that predate Laravel's
                        // dedicated exception.
                        if ($this->isDuplicateViolation($e)) {
                            $duplicates++;
                        } else {
                            Log::debug('CrmCsvImporter row failed', ['error' => $e->getMessage()]);
                            $errors[] = $e->getMessage();
                        }
                    }
                }

                return [$inserted, $duplicates];
            });
        } catch (\Throwable $e) {
            $errors[] = __('crm_csv_import.batch_failed_prefix') . $e->getMessage();

            return [0, 0];
        }
    }

    /** True when a write failed because the row already exists. */
    protected function isDuplicateViolation(\Throwable $e): bool
    {
        if ($e instanceof \Illuminate\Database\UniqueConstraintViolationException) {
            return true;
        }

        if ($e instanceof \Illuminate\Database\QueryException) {
            return ($e->errorInfo[1] ?? null) === 1062
                || str_contains($e->getMessage(), 'Integrity constraint violation: 1062');
        }

        return false;
    }

    /**
     * Create a CustomFieldDefinition for each leftover CSV column that does
     * not already have one.
     *
     * The lead page only renders custom values whose key matches a defined
     * field, so without this the imported values would sit in the database
     * invisible.  Fields are created hidden from the table (a wide import
     * would otherwise add a dozen columns to the lead list) but visible on
     * the record and in the form, where they can be renamed or deleted.
     *
     * @param  array<int, string>  $headers
     */
    protected function ensureCustomFields(Tenant $tenant, array $headers): void
    {
        if ($headers === []) {
            return;
        }

        try {
            $existing = \App\Models\CustomFieldDefinition::withoutGlobalScopes()
                ->where('tenant_id', $tenant->id)
                ->where('entity_type', 'lead')
                ->pluck('key')
                ->all();

            $sort = 100;

            foreach ($headers as $header) {
                $key = $this->customKey($header);

                if ($key === '' || in_array($key, $existing, true)) {
                    continue;
                }

                \App\Models\CustomFieldDefinition::create([
                    'tenant_id'       => $tenant->id,
                    'entity_type'     => 'lead',
                    'name'            => $header,
                    'key'             => $key,
                    'field_type'      => 'text',
                    'required'        => false,
                    'show_in_table'   => false,
                    'show_in_form'    => true,
                    'show_in_filters' => false,
                    'sort_order'      => $sort++,
                ]);

                $existing[] = $key;
            }
        } catch (\Throwable $e) {
            // Never let field bookkeeping fail an otherwise good import —
            // the values are already stored on the leads either way.
            Log::warning('CrmCsvImporter: custom field registration failed', ['error' => $e->getMessage()]);
        }
    }

    protected function result(int $imported, int $skipped, array $errors, string $vendor, int $duplicates = 0): array
    {
        return [
            'imported'   => $imported,
            'skipped'    => $skipped,
            // Rows the file carried that this tenant already has, kept apart
            // from `skipped` (no contact channel) so the operator can tell
            // "you already imported this file" from "these rows are unusable".
            'duplicates' => $duplicates,
            'errors'     => array_slice($errors, 0, 10),
            'vendor'     => $vendor,
        ];
    }
}
