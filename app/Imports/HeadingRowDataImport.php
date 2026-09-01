<?php

namespace App\Imports;

use Maatwebsite\Excel\Concerns\ToArray;
use Maatwebsite\Excel\Concerns\WithHeadingRow;

/**
 * Reads a spreadsheet's DATA rows keyed by its heading row.
 *
 * Maatwebsite's own HeadingRowImport is WithStartRow(1) + WithLimit(1), so
 * Excel::toArray(new HeadingRowImport, ...) returns nothing but the heading
 * row itself — useful for discovering column names, useless for reading
 * content.  Pair the two: HeadingRowImport for the header list, this import
 * for the rows underneath it (keyed by the same slugged headings, so a
 * column_mapping captured off one lines up with the other).
 */
class HeadingRowDataImport implements ToArray, WithHeadingRow
{
    /**
     * @param  array<int, array<string, mixed>>  $rows
     * @return array<int, array<string, mixed>>
     */
    public function array(array $rows): array
    {
        return $rows;
    }
}
