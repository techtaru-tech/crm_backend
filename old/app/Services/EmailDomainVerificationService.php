<?php

declare(strict_types=1);

namespace App\Services;

use App\Models\Tenant;
use Illuminate\Support\Carbon;
use Illuminate\Support\Str;

/**
 * DNS-challenge email domain verification.
 *
 * Without this, every tenant can configure from_address =
 * "support@anybody.com" and our outbound mail will fail SPF/DKIM
 * silently, landing in spam or hard-bounced.  Worse, an unscrupulous
 * tenant could send mail "From: ceo@apple.com" and damage the SaaS
 * sender reputation.
 *
 * Flow:
 *   1) Tenant enters their from-domain (extracted from from_address)
 *   2) mintToken($domain) returns a random token + the exact DNS
 *      record they need to publish:
 *        _leadhub-verify.<domain> TXT "leadhub-verify=<token>"
 *   3) Tenant publishes that TXT record at their DNS host
 *   4) verify($tenant, $domain) does a DNS lookup; if the token
 *      matches, marks the domain verified and stamps verified_at
 *
 * Verified domains live in tenants.settings.email.verified_domains
 * as an assoc array { domain => verified_at_iso8601 }.  Tokens live
 * in tenants.settings.email.verification_tokens (transient — wiped
 * after successful verification).
 *
 * BrandedMailable / outbound senders should refuse to use a
 * from_address whose domain isn't in verified_domains, falling back
 * to the SaaS default sender so deliverability stays intact.
 */
class EmailDomainVerificationService
{
    /**
     * Generate a verification token for the given domain and stash
     * it in the tenant's settings so verify() can compare against
     * the published TXT record.
     *
     * @return array{token: string, dns_name: string, dns_value: string}
     */
    public function mintToken(Tenant $tenant, string $domain): array
    {
        $domain = $this->normalizeDomain($domain);
        if ($domain === '') {
            throw new \InvalidArgumentException('Invalid domain.');
        }

        $token = Str::lower(Str::random(32));

        $tokens = (array) ($tenant->getSetting('email.verification_tokens') ?? []);
        $tokens[$domain] = [
            'token'      => $token,
            'minted_at'  => Carbon::now()->toIso8601String(),
        ];

        app(SettingsService::class)
            ->forTenant($tenant)
            ->set('email.verification_tokens', $tokens);

        return [
            'token'     => $token,
            'dns_name'  => '_leadhub-verify.' . $domain,
            'dns_value' => 'leadhub-verify=' . $token,
        ];
    }

    /**
     * Look up the TXT record at _leadhub-verify.<domain> and check
     * whether it contains the token we minted.  On match, mark the
     * domain verified.
     *
     * @return array{ok: bool, verified_at: ?string, reason: ?string}
     */
    public function verify(Tenant $tenant, string $domain): array
    {
        $domain = $this->normalizeDomain($domain);
        if ($domain === '') {
            return ['ok' => false, 'verified_at' => null, 'reason' => 'invalid_domain'];
        }

        $tokens = (array) ($tenant->getSetting('email.verification_tokens') ?? []);
        $expected = $tokens[$domain]['token'] ?? null;
        if (! $expected) {
            return ['ok' => false, 'verified_at' => null, 'reason' => 'no_token_minted'];
        }

        // Tokens expire 72 hours after minting — without this, an
        // attacker who briefly controlled a tenant account could mint
        // a token for a victim domain, lose access, and still verify
        // weeks later when DNS happened to drift in their favour.
        $mintedAt = $tokens[$domain]['minted_at'] ?? null;
        if ($mintedAt) {
            try {
                $ageHours = Carbon::parse($mintedAt)->diffInHours(now());
                if ($ageHours > 72) {
                    unset($tokens[$domain]);
                    app(SettingsService::class)->forTenant($tenant)
                        ->set('email.verification_tokens', $tokens);
                    return ['ok' => false, 'verified_at' => null, 'reason' => 'token_expired'];
                }
            } catch (\Throwable) {
                // Unparseable minted_at — treat as if no token present.
                return ['ok' => false, 'verified_at' => null, 'reason' => 'token_expired'];
            }
        }

        $records = @dns_get_record('_leadhub-verify.' . $domain, DNS_TXT);
        if (! is_array($records)) {
            return ['ok' => false, 'verified_at' => null, 'reason' => 'dns_lookup_failed'];
        }

        $needle = 'leadhub-verify=' . $expected;
        $found = false;
        foreach ($records as $record) {
            $value = (string) ($record['txt'] ?? $record['entries'][0] ?? '');
            if ($value === $needle) {
                $found = true;
                break;
            }
        }

        if (! $found) {
            return ['ok' => false, 'verified_at' => null, 'reason' => 'txt_record_not_found'];
        }

        $verifiedAt = Carbon::now()->toIso8601String();

        $verified = (array) ($tenant->getSetting('email.verified_domains') ?? []);
        $verified[$domain] = $verifiedAt;
        app(SettingsService::class)->forTenant($tenant)
            ->set('email.verified_domains', $verified);

        // Token is single-use — clear it once consumed.
        unset($tokens[$domain]);
        app(SettingsService::class)->forTenant($tenant)
            ->set('email.verification_tokens', $tokens);

        return ['ok' => true, 'verified_at' => $verifiedAt, 'reason' => null];
    }

    /**
     * Has this tenant verified this domain at least once?  Used by
     * BrandedMailable to gate from_address usage so we never send
     * outbound mail with a spoofed-looking sender.
     */
    public function isVerified(Tenant $tenant, string $domain): bool
    {
        $domain = $this->normalizeDomain($domain);
        if ($domain === '') return false;

        $verified = (array) ($tenant->getSetting('email.verified_domains') ?? []);
        return isset($verified[$domain]);
    }

    /**
     * Strip protocol, path, leading "@" / "www.", lowercase, validate.
     */
    public function normalizeDomain(string $input): string
    {
        $input = strtolower(trim($input));
        if ($input === '') return '';

        // Accept "user@example.com" by extracting the host.
        if (str_contains($input, '@')) {
            $input = explode('@', $input, 2)[1] ?? '';
        }

        // Strip scheme/path leftovers.
        $input = preg_replace('#^https?://#', '', $input) ?? $input;
        $input = preg_replace('#/.*$#', '', $input) ?? $input;
        $input = preg_replace('#^www\.#', '', $input) ?? $input;

        return preg_match('/^[a-z0-9.\-]+\.[a-z]{2,}$/', $input) === 1 ? $input : '';
    }
}
