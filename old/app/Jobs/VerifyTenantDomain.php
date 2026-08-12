<?php

namespace App\Jobs;

use App\Models\TenantDomain;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;

class VerifyTenantDomain implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    public function __construct(private int $tenantDomainId) {}

    public function handle(): void
    {
        $domainRecord = TenantDomain::find($this->tenantDomainId);
        if (! $domainRecord || $domainRecord->isVerified()) {
            return;
        }

        $domain  = $domainRecord->domain;
        $appHost = parse_url(config('app.url'), PHP_URL_HOST) ?? '';

        $cnameDnsRecords = @dns_get_record($domain, DNS_CNAME | DNS_A | DNS_AAAA);

        if ($cnameDnsRecords !== false && ! empty($cnameDnsRecords)) {
            foreach ($cnameDnsRecords as $entry) {
                if (isset($entry['target']) && $appHost && str_contains($entry['target'], $appHost)) {
                    $domainRecord->update(['verified_at' => now()]);
                    return;
                }
            }
        }

        $txtDnsRecords = @dns_get_record('_leadhub-verify.' . $domain, DNS_TXT) ?: [];
        $expectedToken = $domainRecord->verification_token ?? '';

        foreach ($txtDnsRecords as $txtEntry) {
            if (isset($txtEntry['txt']) && $txtEntry['txt'] === $expectedToken) {
                $domainRecord->update(['verified_at' => now()]);
                return;
            }
        }
    }
}
