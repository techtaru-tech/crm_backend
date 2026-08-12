<?php

declare(strict_types=1);

namespace App\Support;

use App\Models\Tenant;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;

/**
 * Resolves a tenant's branding logo to a base64 `data:` URI that DomPDF can
 * embed in invoice / quote PDFs WITHOUT a public/storage symlink and WITHOUT a
 * remote HTTP fetch (both unreliable on shared hosting).
 *
 * The stored logo_url comes in several shapes (see {@see PublicMedia}): an
 * absolute "…/storage/<path>" URL, a root-relative "/storage/<path>", the
 * symlink-free "/media/<path>" route, a bare public-disk path, a bundled root
 * asset ("/favicon.svg"), or a genuine external URL. We read the first five
 * straight off disk; an external URL (or anything unreadable) returns null so
 * the caller falls back to the workspace name.
 */
final class PdfBranding
{
    public static function logoDataUri(?Tenant $tenant): ?string
    {
        if (! $tenant) {
            return null;
        }

        $value = trim((string) $tenant->getBranding('logo_url', ''));
        if ($value === '') {
            return null;
        }

        // A genuine external logo (operator pasted a remote URL) — never fetch
        // a remote resource during a PDF render.
        if (Str::startsWith($value, ['http://', 'https://']) && ! Str::contains($value, '/storage/')) {
            return null;
        }

        [$location, $path] = self::locate($value);

        // Only ever embed a real image file. This is the primary guard against a
        // crafted logo_url (e.g. "/../.env", "/../config/app.php") turning a PDF
        // render into an arbitrary-file read that lands secret bytes in a public
        // document. Extension-based, so it needs no fileinfo extension.
        $mime = self::imageMime($path);
        if ($mime === null) {
            return null;
        }

        try {
            if ($location === 'public' && Storage::disk('public')->exists($path)) {
                // Flysystem normalises and rejects "../" traversal on this disk.
                return self::encode((string) Storage::disk('public')->get($path), $mime);
            }

            if ($location === 'root') {
                $full    = public_path($path);
                $real    = realpath($full);
                $allowed = realpath(public_path());
                // Containment: the resolved file must live under public/.
                if ($real !== false && $allowed !== false
                    && str_starts_with($real, $allowed . DIRECTORY_SEPARATOR)
                    && is_file($real) && is_readable($real)) {
                    return self::encode((string) file_get_contents($real), $mime);
                }
            }
        } catch (\Throwable $e) {
            Log::warning('[PdfBranding] logo read failed; PDF falls back to workspace name', [
                'tenant_id' => $tenant->id,
                'logo_url'  => $value,
                'error'     => $e->getMessage(),
            ]);

            return null;
        }

        // Configured but not found / not permitted on disk — a breadcrumb so an
        // operator can diagnose "my logo isn't showing on the invoice".
        Log::warning('[PdfBranding] logo configured but not readable on disk', [
            'tenant_id' => $tenant->id,
            'logo_url'  => $value,
            'location'  => $location,
        ]);

        return null;
    }

    /**
     * Map a stored logo value to a [location, path] pair, where location is
     * 'public' (the public storage disk) or 'root' (a file under public/).
     *
     * @return array{0: 'public'|'root', 1: string}
     */
    private static function locate(string $value): array
    {
        if (Str::contains($value, '/storage/')) {
            return ['public', ltrim(Str::after($value, '/storage/'), '/')];
        }
        if (Str::startsWith($value, '/media/')) {
            return ['public', ltrim(Str::after($value, '/media/'), '/')];
        }
        if (Str::startsWith($value, '/')) {
            return ['root', ltrim($value, '/')];
        }

        return ['public', ltrim($value, '/')];
    }

    /**
     * Allowed image extensions → MIME. Doubles as the whitelist: a non-image
     * extension returns null and is never embedded. Avoids the fileinfo
     * extension, which is often absent on shared hosting.
     */
    private static function imageMime(string $path): ?string
    {
        return match (strtolower(pathinfo($path, PATHINFO_EXTENSION))) {
            'png'         => 'image/png',
            'jpg', 'jpeg' => 'image/jpeg',
            'gif'         => 'image/gif',
            'webp'        => 'image/webp',
            'svg'         => 'image/svg+xml',
            default       => null,
        };
    }

    private static function encode(string $contents, string $mime): ?string
    {
        if ($contents === '') {
            return null;
        }

        return 'data:' . $mime . ';base64,' . base64_encode($contents);
    }
}
