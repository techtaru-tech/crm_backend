<?php

declare(strict_types=1);

namespace App\Support;

/**
 * CSV formula-injection guard (OWASP "CSV injection" / "Formula injection").
 *
 * A spreadsheet cell whose first character is one of `=`, `+`, `-`, `@`,
 * tab (`\t`), or carriage-return (`\r`) is interpreted by Excel /
 * LibreOffice / Google Sheets as a formula — which can fire
 * `=HYPERLINK(evil)`, `=cmd|/c calc!A1` (DDE), `=IMPORTXML(...)`,
 * `+webservice(...)`, etc., the moment an admin opens the export.
 * Real CVEs have shipped from exactly this (CVE-2023-31055, the
 * 2014 Google Apps disclosure, and the long-running OWASP advisory).
 *
 * Neutralise by prefixing a single quote — Excel hides the prefix at
 * render time so the buyer still sees the original value, but the
 * cell evaluates as a string instead of a formula.
 *
 * Use everywhere user-controlled data lands in a CSV / XLSX cell:
 *   - {@see \App\Http\Controllers\Reports\ExportController::csvSafe}
 *   - {@see \App\Services\ScheduledReportService::generateCsv}
 *   - {@see \App\Exports\LeadsExport::map}
 *   - any future export that touches `fputcsv()` or Maatwebsite Excel.
 *
 * Reference: https://owasp.org/www-community/attacks/CSV_Injection
 */
final class CsvSafety
{
    /**
     * Return a cell value safe for direct inclusion in a CSV/XLSX export.
     *
     * Idempotent: repeated calls leave already-prefixed strings alone.
     */
    public static function neutralize(mixed $value): string
    {
        $str = (string) ($value ?? '');
        if ($str === '') {
            return $str;
        }

        $first = $str[0];
        if ($first === '=' || $first === '+' || $first === '-' || $first === '@' || $first === "\t" || $first === "\r") {
            return "'" . $str;
        }

        return $str;
    }

    /**
     * Map an array of cells through {@see neutralize()} in one call.
     * Preserves array keys (useful for fputcsv headers).
     */
    public static function neutralizeRow(array $row): array
    {
        return array_map([self::class, 'neutralize'], $row);
    }
}
