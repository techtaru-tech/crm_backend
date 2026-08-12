<?php

declare(strict_types=1);

namespace App\Http\Controllers;

use App\Jobs\ExportTenantDataJob;
use App\Models\AuditLog;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Storage;
use Symfony\Component\HttpFoundation\BinaryFileResponse;

/**
 * Serves the GDPR Article 20 workspace data export ZIP.
 *
 * Token flow:
 *   - ExportTenantDataJob mints a 64-char opaque token and stashes
 *     {tenant_id, user_id, path, expires_at} in the cache for 24h
 *   - User clicks the link in TenantDataExportReady (database
 *     notification), landing on /admin/data-export/download/{token}
 *   - We look up the cache entry, verify the auth user matches
 *     payload.user_id, stream the file, then delete both the file
 *     and the cache entry so the link is single-use
 *
 * Single-use is critical — the ZIP contains every personal-data
 * record we hold for the tenant.  Leaving the file on disk and the
 * URL live indefinitely would be a credential-leak waiting to
 * happen.  The 24h cache TTL is a backstop in case the user never
 * clicks (job orphans get garbage-collected).
 */
class DataExportController extends Controller
{
    public function download(Request $request, string $token): BinaryFileResponse
    {
        $user = $request->user();
        abort_unless($user, 401, __('controllers/data_export.auth_required'));

        $cacheKey = ExportTenantDataJob::CACHE_PREFIX . $token;
        $payload  = Cache::get($cacheKey);

        if (! is_array($payload)) {
            abort(404, __('messages.export_link_invalid'));
        }

        // Belt-and-braces: cache TTL should already enforce this,
        // but if Laravel's cache backend ever serves a stale entry
        // (e.g. file-driver clock-skew) the explicit check fails
        // closed.
        $expiresAt = $payload['expires_at'] ?? null;
        if ($expiresAt && Carbon::parse($expiresAt)->isPast()) {
            Cache::forget($cacheKey);
            abort(410, __('messages.export_link_expired'));
        }

        // Authorization — the token alone isn't enough, the
        // requesting user must match the user who originally
        // dispatched the export.  Guards against link sharing and
        // hijacked sessions.
        $payloadUserId = (int) ($payload['user_id'] ?? 0);
        if ($payloadUserId !== (int) $user->id) {
            abort(403, __('messages.export_link_wrong_user'));
        }

        $relativePath = (string) ($payload['path'] ?? '');
        if ($relativePath === '' || ! Storage::disk('local')->exists($relativePath)) {
            Cache::forget($cacheKey);
            abort(404, __('messages.export_file_unavailable'));
        }

        $absolutePath = storage_path('app/' . $relativePath);
        $tenantId     = (int) ($payload['tenant_id'] ?? 0);
        $downloadName = sprintf(
            'workspace-data-export-%d-%s.zip',
            $tenantId,
            Carbon::now()->format('Y-m-d'),
        );

        // Audit the access before we destroy evidence — if streaming
        // fails downstream we still have a record that the user did
        // exercise their portability right.
        $this->audit($tenantId, $user->id, $relativePath);

        // Forget the cache entry first so a concurrent retry can't
        // re-download.  `deleteFileAfterSend(true)` removes the
        // artifact from disk after the response body is fully sent.
        Cache::forget($cacheKey);

        return response()
            ->download($absolutePath, $downloadName, [
                'Content-Type'           => 'application/zip',
                'X-Content-Type-Options' => 'nosniff',
            ])
            ->deleteFileAfterSend(true);
    }

    protected function audit(int $tenantId, int $userId, string $path): void
    {
        try {
            AuditLog::query()->withoutGlobalScopes()->create([
                'tenant_id'      => $tenantId,
                'user_id'        => $userId,
                'user_name'      => auth()->user()?->name,
                'action'         => 'data.export_downloaded',
                'auditable_type' => \App\Models\Tenant::class,
                'auditable_id'   => $tenantId,
                'old_values'     => [],
                'new_values'     => ['path' => $path],
                'ip_address'     => request()->ip(),
                'user_agent'     => request()->userAgent(),
                'url'            => request()->fullUrl(),
                'tags'           => 'gdpr,privacy,article-20',
            ]);
        } catch (\Throwable $e) {
            Log::warning('Audit log write failed for data export download', [
                'tenant_id' => $tenantId,
                'user_id'   => $userId,
                'error'     => $e->getMessage(),
            ]);
        }
    }
}
