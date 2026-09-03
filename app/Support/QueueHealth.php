<?php

namespace App\Support;

use Illuminate\Support\Facades\DB;

/**
 * Is anything actually draining the queue?
 *
 * Automations, imports, exports and follow-up alerts are all queued jobs.
 * When no worker runs they do not fail — they simply sit in the jobs table
 * forever, and every screen goes on looking healthy.  That is how a tenant
 * ends up believing their automations are broken when the automations are
 * fine and nothing is executing them.
 */
class QueueHealth
{
    /** A job older than this with nothing reserving it means no worker. */
    public const STALE_AFTER_SECONDS = 300;

    /**
     * Null when the queue looks healthy (or cannot be inspected), otherwise
     * ['pending' => int, 'oldest_minutes' => int].
     *
     * @return array{pending:int, oldest_minutes:int}|null
     */
    public static function backlog(): ?array
    {
        // Only the database driver keeps a table we can read.  'sync' runs
        // inline and can never back up; redis/sqs are someone else's to watch.
        if (config('queue.default') !== 'database') {
            return null;
        }

        try {
            $oldest = DB::table('jobs')->min('available_at');

            if (! $oldest) {
                return null;
            }

            $ageSeconds = time() - (int) $oldest;

            if ($ageSeconds < self::STALE_AFTER_SECONDS) {
                return null;
            }

            return [
                'pending'        => (int) DB::table('jobs')->count(),
                'oldest_minutes' => (int) floor($ageSeconds / 60),
            ];
        } catch (\Throwable) {
            // No jobs table (a fresh install, or a driver swapped mid-flight).
            return null;
        }
    }
}
