<?php

declare(strict_types=1);

namespace App\Console\Commands;

use App\Models\Tenant;
use App\Services\EmailDomainVerificationService;
use Illuminate\Console\Command;

/**
 * Operator-side helper for the email-domain DNS challenge flow.
 *
 *   # Mint a token + show the DNS record the tenant must publish:
 *   php artisan leadhub:verify-email-domain 17 example.com --mint
 *
 *   # Re-attempt verification (after the tenant publishes the TXT):
 *   php artisan leadhub:verify-email-domain 17 example.com
 *
 * Useful for support cases where the tenant's UI verification button
 * times out (DNS propagation can take 5-60 minutes) and they want
 * the operator to retry from the server side.  Also handy for
 * scripted onboarding of B2B customers who sign their DPA with the
 * domain pre-arranged.
 */
class VerifyEmailDomain extends Command
{
    protected $signature   = 'leadhub:verify-email-domain
                              {tenant_id : Tenant ID owning the domain}
                              {domain    : Email domain to verify (e.g. example.com)}
                              {--mint    : Mint a fresh token + print DNS record instructions}';

    protected $description = 'Mint or check an email-domain DNS verification challenge for a tenant';

    public function handle(EmailDomainVerificationService $verifier): int
    {
        $tenantId = (int) $this->argument('tenant_id');
        $domain   = (string) $this->argument('domain');

        $tenant = Tenant::query()->find($tenantId);
        if (! $tenant) {
            $this->error("Tenant {$tenantId} not found.");
            return self::FAILURE;
        }

        if ($this->option('mint')) {
            try {
                $issued = $verifier->mintToken($tenant, $domain);
            } catch (\InvalidArgumentException $e) {
                $this->error($e->getMessage());
                return self::FAILURE;
            }

            $this->info('Token minted.  Ask the tenant to publish this TXT record:');
            $this->line('');
            $this->line('  Name:  ' . $issued['dns_name']);
            $this->line('  Type:  TXT');
            $this->line('  Value: ' . $issued['dns_value']);
            $this->line('');
            $this->line('Then run this command without --mint to verify.');
            return self::SUCCESS;
        }

        $result = $verifier->verify($tenant, $domain);

        if ($result['ok']) {
            $this->info("Verified ✓  ({$result['verified_at']})");
            return self::SUCCESS;
        }

        $this->error('Verification failed: ' . ($result['reason'] ?? 'unknown'));
        return self::FAILURE;
    }
}
