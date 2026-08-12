<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use RecursiveDirectoryIterator;
use RecursiveIteratorIterator;

/**
 * Static analysis: scans tenant-facing PHP files for illegal calls to
 * `withoutGlobalScope('tenant')`. The BelongsToTenant trait "fails
 * closed" when no tenant is resolved, so bypassing it in tenant code
 * is an anti-pattern that has already caused real data leaks (see
 * commit 9c1ff8a0 where four such bugs were fixed).
 *
 * Exit codes:
 *   0  — no offenders found
 *   1  — offenders found (prints each with file:line)
 *
 * Wire into CI:  php artisan leadhub:check-tenant-scope
 * Exits non-zero on violations so pipelines fail fast.
 *
 * Allow-listed paths (these intentionally bypass the scope):
 *   • app/Filament/SuperAdmin/                — cross-tenant admin views
 *   • app/Models/Concerns/BelongsToTenant.php — the trait itself
 *   • app/Console/Commands/*                  — ops/maintenance commands
 *   • app/Providers/*                         — boot-time scope registration
 */
class CheckTenantScope extends Command
{
    protected $signature = 'leadhub:check-tenant-scope {--path=app : Root path to scan}';
    protected $description = "Fail if withoutGlobalScope('tenant') is used outside allow-listed directories.";

    /**
     * Paths that legitimately bypass the tenant scope because they run
     * in contexts WITHOUT an authenticated user (public webhooks,
     * email tracking pixels, portal magic-link auth, public booking/
     * quote/landing forms) or are operator-facing by design.
     *
     * Files matched here are skipped entirely by the scanner.
     */
    private array $allowList = [
        '/Filament/SuperAdmin/',                          // Cross-tenant admin
        '/Models/Concerns/BelongsToTenant.php',           // The trait itself
        '/Console/Commands/',                             // Ops + maintenance jobs
        '/Providers/',                                    // Boot-time setup
        '/Http/Controllers/Api/',                         // Webhooks, no auth context
        '/Http/Controllers/Portal/',                      // Magic-link lead-facing portal
        '/Http/Controllers/PublicBookingController.php',  // Anonymous booking flow
        '/Http/Controllers/PublicFormController.php',     // Anonymous form submissions
        '/Http/Controllers/PublicLandingPageController.php', // Anonymous landing views
        '/Http/Controllers/PublicQuoteController.php',    // Anonymous quote view/accept
        '/Http/Controllers/PublicWidgetController.php',   // Widget loader + preview by UUID
        '/Http/Controllers/EmailTrackingController.php',  // Open/click pixels
        '/Http/Controllers/WebhookController.php',        // Inbound webhooks
        '/Http/Controllers/RegistrationController.php',   // Tenant signup (no tenant yet)
        '/Jobs/',                                         // Queue workers, no request context
        '/Observers/',                                    // Model lifecycle hooks
        '/Listeners/',                                    // Event subscribers
        '/Services/LeadSources/',                         // IMAP / external pullers
        '/Models/PortalAccessToken.php',                  // Magic-link relationship
        '/Models/PortalSession.php',                      // Magic-link session
        '/Http/Controllers/PublicInvoiceController.php',  // Anonymous invoice view/pay
        '/Http/Controllers/TrackingController.php',       // Analytics beacon (no auth)
        '/Http/Middleware/PortalAuth.php',                // Portal session resolution
    ];

    public function handle(): int
    {
        $root = base_path($this->option('path'));
        if (! is_dir($root)) {
            $this->error("Path not found: {$root}");
            return self::FAILURE;
        }

        $offenders = [];
        $scanned   = 0;

        $iter = new RecursiveIteratorIterator(new RecursiveDirectoryIterator($root));
        foreach ($iter as $file) {
            if (! $file->isFile() || $file->getExtension() !== 'php') {
                continue;
            }

            $path = $this->normalize($file->getPathname());
            if ($this->isAllowListed($path)) {
                continue;
            }

            $scanned++;
            $lines = @file($path);
            if ($lines === false) {
                continue;
            }

            foreach ($lines as $idx => $line) {
                if (! preg_match("/withoutGlobalScope\s*\(\s*(['\"])tenant\\1\s*\)/", $line)) {
                    continue;
                }

                // Accept the call if a ->where('tenant_id', ...) is
                // chained within the next 6 lines — that's an explicit
                // re-filter on the same query, which is the documented
                // safe escape hatch.
                $lookAhead = array_slice($lines, $idx, 7);
                $chainHasTenantFilter = false;
                foreach ($lookAhead as $ahead) {
                    if (preg_match("/->\s*where\s*\(\s*(['\"])tenant_id\\1/", $ahead)) {
                        $chainHasTenantFilter = true;
                        break;
                    }
                }

                if ($chainHasTenantFilter) {
                    continue;
                }

                $offenders[] = [
                    'file' => $path,
                    'line' => $idx + 1,
                    'src'  => trim($line),
                ];
            }
        }

        $this->info("Scanned {$scanned} PHP files under " . $root);

        if (empty($offenders)) {
            $this->info('No tenant-scope bypass violations found.');
            return self::SUCCESS;
        }

        $this->error("Found " . count($offenders) . " tenant-scope bypass(es):\n");
        foreach ($offenders as $o) {
            $rel = $this->relative($o['file']);
            $this->line("  <fg=red>{$rel}:{$o['line']}</>");
            $this->line("    <fg=gray>{$o['src']}</>");
        }
        $this->line('');
        $this->warn('Fix by removing withoutGlobalScope(\'tenant\') OR by chaining an explicit ');
        $this->warn('->where(\'tenant_id\', $tenantId) on the same query. Allow-listed directories:');
        foreach ($this->allowList as $p) {
            $this->line("  • {$p}");
        }

        return self::FAILURE;
    }

    private function normalize(string $path): string
    {
        return str_replace('\\', '/', $path);
    }

    private function isAllowListed(string $normalizedPath): bool
    {
        foreach ($this->allowList as $needle) {
            if (str_contains($normalizedPath, $needle)) {
                return true;
            }
        }
        return false;
    }

    private function relative(string $absolute): string
    {
        $base = $this->normalize(base_path()) . '/';
        return str_starts_with($absolute, $base)
            ? substr($absolute, strlen($base))
            : $absolute;
    }
}
