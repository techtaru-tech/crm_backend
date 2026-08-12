<?php

declare(strict_types=1);

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Storage;

/**
 * Liveness/readiness probe used by load balancers, uptime checks,
 * and Filament's System Health dashboard.
 *
 * Exercises the three runtime dependencies the app cannot run without:
 *   - DB        — opens a PDO connection (catches misconfigured creds,
 *                 stale credentials, or a tipped-over MySQL server)
 *   - cache     — round-trips a tiny value through the configured cache
 *                 store (catches Redis/memcached failure)
 *   - storage   — write+read+delete on the default filesystem disk
 *                 (catches full disks, permission drift, S3 misconfig)
 *
 * Queue "ping" is intentionally a config existence check: actually
 * round-tripping a job has too many false-positive failure modes for
 * a 200/503 endpoint.
 *
 * Response shape is stable JSON so Cloudflare / UptimeRobot / Pingdom
 * scrapers can flag specific subsystems instead of just a binary up/down.
 */
class HealthController extends Controller
{
    public function __invoke(): JsonResponse
    {
        $checks = [
            'db'      => $this->checkDatabase(),
            'cache'   => $this->checkCache(),
            'storage' => $this->checkStorage(),
        ];

        $allHealthy = ! in_array(false, $checks, true);
        $status     = $allHealthy ? 'ok' : 'degraded';

        $payload = [
            'status'    => $status,
            'checks'    => $checks,
            'queue'     => (string) (config('queue.default') ?? ''),
            'version'   => (string) config('leadhub.version', '1.0.0'),
            'timestamp' => now()->toIso8601String(),
        ];

        return response()->json($payload, $allHealthy ? 200 : 503);
    }

    private function checkDatabase(): bool
    {
        try {
            DB::connection()->getPdo();
            return true;
        } catch (\Throwable $e) {
            Log::warning('healthcheck.db.failed', ['error' => $e->getMessage()]);
            return false;
        }
    }

    private function checkCache(): bool
    {
        try {
            $store = Cache::store();
            $store->put('healthcheck', '1', 5);
            $value = $store->get('healthcheck');
            $store->forget('healthcheck');
            return $value === '1';
        } catch (\Throwable $e) {
            Log::warning('healthcheck.cache.failed', ['error' => $e->getMessage()]);
            return false;
        }
    }

    private function checkStorage(): bool
    {
        try {
            $disk    = Storage::disk((string) config('filesystems.default'));
            $marker  = '.healthcheck';
            $payload = (string) now()->getTimestamp();

            $disk->put($marker, $payload);
            $read = (string) $disk->get($marker);
            $disk->delete($marker);

            return $read === $payload;
        } catch (\Throwable $e) {
            Log::warning('healthcheck.storage.failed', ['error' => $e->getMessage()]);
            return false;
        }
    }
}
