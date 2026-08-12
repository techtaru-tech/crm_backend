<?php

/*
|--------------------------------------------------------------------------
| LeadHub Shared-Hosting Cron Runner
|--------------------------------------------------------------------------
|
| This file replaces the need for long-running artisan commands on shared
| hosting. Add ONE cron job in your hosting control panel (cPanel, Plesk,
| DirectAdmin, etc.) that runs every minute:
|
|   * * * * * php /home/yourusername/public_html/cron.php >> /dev/null 2>&1
|
| What this script does each time it runs:
|   1. Processes up to 30 queued jobs (emails, webhooks, automations, etc.)
|   2. Runs the Laravel scheduler (IMAP checks, report delivery, etc.)
|
| This is ONLY needed on shared hosting. On VPS/dedicated servers you
| can use the standard Laravel queue worker and scheduler instead:
|   php artisan queue:work --daemon
|   php artisan schedule:run
|
| SECURITY: This file should NOT be accessible from the web. It lives
| outside the public/ directory by default. If you placed it inside
| public_html/, protect it with .htaccess (see below).
|
*/

// ── Prevent web access ──────────────────────────────────────────────────────
if (php_sapi_name() !== 'cli') {
    // Allow web-based cron services (cPanel, Plesk cron via HTTP) by checking
    // for a secret token. This prevents random visitors from triggering it.
    $expectedToken = null;

    // Read token from .env if available (CRON_SECRET=your-random-string)
    $envFile = __DIR__ . '/.env';
    if (file_exists($envFile)) {
        $lines = file($envFile, FILE_IGNORE_NEW_LINES | FILE_SKIP_EMPTY_LINES);
        foreach ($lines as $line) {
            if (str_starts_with($line, 'CRON_SECRET=')) {
                $expectedToken = trim(substr($line, strlen('CRON_SECRET=')));
                break;
            }
        }
    }

    if ($expectedToken) {
        $providedToken = $_GET['token'] ?? $_SERVER['HTTP_X_CRON_TOKEN'] ?? null;
        if ($providedToken !== $expectedToken) {
            http_response_code(403);
            echo 'Forbidden';
            exit(1);
        }
    } else {
        // No CRON_SECRET set — block all web access for safety.
        http_response_code(403);
        echo 'Forbidden. Run this from CLI or set CRON_SECRET in .env for web-based cron.';
        exit(1);
    }
}

// ── Bootstrap Laravel ───────────────────────────────────────────────────────
define('LARAVEL_START', microtime(true));

// Detect app root — this file can live in the project root or in public_html/
if (file_exists(__DIR__ . '/vendor/autoload.php')) {
    $basePath = __DIR__;
} elseif (file_exists(__DIR__ . '/../vendor/autoload.php')) {
    $basePath = dirname(__DIR__);
} else {
    echo "Error: Cannot find vendor/autoload.php. Make sure cron.php is in the project root.\n";
    exit(1);
}

require $basePath . '/vendor/autoload.php';

/** @var \Illuminate\Foundation\Application $app */
$app = require $basePath . '/bootstrap/app.php';

$kernel = $app->make(\Illuminate\Contracts\Console\Kernel::class);

// ── Check if LeadHub is installed ───────────────────────────────────────────
$lockFile = $app->storagePath('installed.lock');
if (!file_exists($lockFile)) {
    echo "LeadHub is not installed yet. Visit /install in your browser first.\n";
    exit(0);
}

// ── 1. Process Queued Jobs ──────────────────────────────────────────────────
// Process up to 30 jobs per cron run (prevents timeout on shared hosting).
// Shared hosting typically allows 30-60 seconds per cron execution.
$maxJobs  = (int) ($argv[1] ?? 30);
$processed = 0;
$startTime = time();
$maxSeconds = 55; // Leave 5-second buffer before typical 60s cron timeout

try {
    $queue = $app->make('queue');
    $connection = $queue->connection();
    $queues = ['default', 'webhooks', 'automations', 'integrations', 'notifications'];

    while ($processed < $maxJobs && (time() - $startTime) < $maxSeconds) {
        $foundJob = false;

        foreach ($queues as $queueName) {
            $job = $connection->pop($queueName);

            if ($job) {
                $foundJob = true;
                $processed++;

                try {
                    $job->fire();
                    $job->delete();
                } catch (\Throwable $e) {
                    // Mark the job as failed if it errors out
                    if ($job->attempts() >= ($job->maxTries() ?: 3)) {
                        $job->fail($e);
                    } else {
                        $job->release(30); // Retry after 30 seconds
                    }

                    // Log the error
                    $app->make('log')->error('[Cron] Job failed: ' . $e->getMessage(), [
                        'job'   => get_class($job),
                        'queue' => $queueName,
                        'trace' => $e->getTraceAsString(),
                    ]);
                }
            }
        }

        // No jobs found in any queue — stop early
        if (!$foundJob) {
            break;
        }
    }
} catch (\Throwable $e) {
    $app->make('log')->error('[Cron] Queue processing error: ' . $e->getMessage());
}

// ── 2. Run the Laravel Scheduler ────────────────────────────────────────────
// This triggers scheduled tasks like IMAP checks, report delivery, etc.
try {
    $kernel->call('schedule:run');
} catch (\Throwable $e) {
    $app->make('log')->error('[Cron] Scheduler error: ' . $e->getMessage());
}

// ── Done ────────────────────────────────────────────────────────────────────
$elapsed = round(microtime(true) - LARAVEL_START, 2);
echo "[" . date('Y-m-d H:i:s') . "] Cron complete: {$processed} jobs processed in {$elapsed}s\n";
