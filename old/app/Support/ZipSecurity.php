<?php

namespace App\Support;

use ZipArchive;

/**
 * Guards against zip-slip and other archive-based path-traversal attacks.
 *
 * Call validateEntries() BEFORE extractTo() on any user-uploaded zip.
 */
class ZipSecurity
{
    /**
     * Verify that no entry in the archive contains path-traversal sequences.
     *
     * @throws \RuntimeException if any entry is unsafe
     */
    public static function validateEntries(ZipArchive $zip): void
    {
        for ($i = 0; $i < $zip->numFiles; $i++) {
            $entry = $zip->getNameIndex($i);

            if ($entry === false) {
                continue;
            }

            $normalized = str_replace('\\', '/', $entry);

            if (
                str_starts_with($normalized, '/')
                || str_contains($normalized, '../')
                || preg_match('#(^|/)\.\.(/|$)#', $normalized)
            ) {
                $zip->close();
                throw new \RuntimeException((string) __('support/zip_security.unsafe_path', ['entry' => $entry]));
            }
        }
    }
}
