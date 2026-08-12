<?php

declare(strict_types=1);

namespace App\Support;

use Illuminate\Support\Str;

/**
 * Resolves a stored branding asset value (logo / favicon) to a URL that
 * works WITHOUT the public/storage symlink.
 *
 * Filament's FileUpload stores branding assets on the `public` disk
 * (storage/app/public), normally exposed at /storage/... via the
 * `php artisan storage:link` symlink. On shared hosting that symlink is
 * frequently unavailable or not honored, so an operator's uploaded logo
 * 404s and "doesn't show" — a recurring buyer report.
 *
 * Stored values come in several shapes across versions:
 *   - absolute URL "https://site.com/storage/branding/logos/x.png"
 *   - root-relative "/storage/branding/logos/x.png"
 *   - bare public-disk path "branding/logos/x.png"
 *   - bundled root asset "/favicon.svg"
 *   - an external URL an operator pasted in
 *
 * The first three are rewritten to the symlink-free /media/<path> route
 * (routes/web.php -> media.serve), which streams the file straight from
 * the public disk on any host. Bundled root assets and genuine external
 * URLs pass through unchanged. Empty input returns null so callers can
 * apply their own fallback.
 */
final class PublicMedia
{
    public static function url(?string $value): ?string
    {
        $value = is_string($value) ? trim($value) : '';

        if ($value === '') {
            return null;
        }

        // Our own absolute "<scheme>://host/storage/<path>" -> /media/<path>.
        if (Str::startsWith($value, ['http://', 'https://'])) {
            if (Str::contains($value, '/storage/')) {
                return url('media/' . ltrim(Str::after($value, '/storage/'), '/'));
            }

            // Genuine external URL (operator pasted a remote logo) — leave it.
            return $value;
        }

        // Root-relative "/storage/<path>" -> /media/<path>.
        if (Str::startsWith($value, '/storage/')) {
            return url('media/' . ltrim(Str::after($value, '/storage/'), '/'));
        }

        // Any other root-relative / bundled asset (e.g. "/favicon.svg") — leave it.
        if (Str::startsWith($value, '/')) {
            return $value;
        }

        // Bare public-disk path, e.g. "branding/logos/<ulid>.png".
        return url('media/' . ltrim($value, '/'));
    }
}
