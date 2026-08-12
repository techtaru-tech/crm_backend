<?php

namespace App\Services;

use App\Models\Tenant;
use Illuminate\Support\Facades\Config;

/**
 * Dynamically applies a tenant's SMTP settings to Laravel's mail config
 * so all emails sent within a request use the tenant's mailer.
 */
class TenantSmtpManager
{
    public function applyForTenant(Tenant $tenant): void
    {
        $smtp = $tenant->getSetting('smtp', []);

        if (empty($smtp['host'])) {
            return;
        }

        $password = '';
        if (! empty($smtp['password'])) {
            try {
                $password = decrypt($smtp['password']);
            } catch (\Throwable) {
                $password = $smtp['password'];
            }
        }

        Config::set('mail.mailers.tenant_smtp', [
            'transport'  => 'smtp',
            'host'       => $smtp['host'],
            'port'       => (int) ($smtp['port'] ?? 587),
            'encryption' => $smtp['encryption'] ?? 'tls',
            'username'   => $smtp['username'] ?? '',
            'password'   => $password,
            'timeout'    => 30,
        ]);

        Config::set('mail.default', 'tenant_smtp');

        if (! empty($smtp['from_address'])) {
            Config::set('mail.from.address', $smtp['from_address']);
            Config::set('mail.from.name', $smtp['from_name'] ?? $tenant->getAppName());
        }
    }

    public function reset(): void
    {
        Config::set('mail.default', config('mail.default') === 'tenant_smtp' ? 'smtp' : config('mail.default'));
    }
}
