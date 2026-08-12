<?php

declare(strict_types=1);

namespace App\Console\Commands;

use App\Services\LicenseService;
use App\Support\DemoMode;
use Illuminate\Console\Command;

/**
 * leadhub:verify-license — refresh the cached licence status.
 *
 * Scheduled daily from routes/console.php.  Hits the licensing server,
 * updates LicenseSettings.last_valid_at on success, and emits a
 * structured log line so dashboards / log aggregators can alert on
 * sustained verification failures.
 *
 * Demo installs (LEADHUB_DEMO_MODE=true) skip the network call
 * entirely — the public preview at theleadhub.app must keep working
 * with no key configured and we don't want pointless outbound traffic
 * to the licensing server from the demo box.
 */
class VerifyLicense extends Command
{
    protected $signature   = 'leadhub:verify-license {--quiet-no-key : Skip the warning log line when no key is configured.}';
    protected $description = 'Refresh the cached LeadHub licence status from the licensing server.';

    public function handle(LicenseService $service): int
    {
        if (DemoMode::isOn()) {
            $this->info('Demo mode is on — skipping licence verification.');
            return self::SUCCESS;
        }

        $key = (string) config('leadhub.license.key', '');
        if ($key === '') {
            if (! $this->option('quiet-no-key')) {
                $this->warn('No licence key configured. Set LEADHUB_LICENSE_KEY in .env or via Settings → License.');
            }
            return self::SUCCESS;
        }

        // recheck() honours the JWT's check_interval claim so it
        // won't actually hit /api/validate more often than the server
        // asked for, even when run hourly from cron.
        $service->clearCache();
        $result  = $service->recheck();
        $verdict = (string) ($result['status']  ?? 'unknown');
        $msg     = (string) ($result['message'] ?? '');

        $this->line("Licence: {$verdict} — {$msg}");

        // Surface non-valid results to laravel.log so log aggregators
        // can alert when a previously-valid install suddenly flips.
        // 'invalid_transient' (heartbeat-grace mode) and 'demo' are
        // expected non-valid states; only 'invalid' is alert-worthy.
        if ($verdict === 'invalid') {
            logger()->warning('[leadhub:verify-license] invalid status', [
                'status'  => $verdict,
                'message' => $msg,
                'domain'  => parse_url((string) config('app.url'), PHP_URL_HOST),
            ]);
        }

        return self::SUCCESS;
    }
}
