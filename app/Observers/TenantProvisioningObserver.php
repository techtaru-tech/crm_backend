<?php

declare(strict_types=1);

namespace App\Observers;

use App\Models\Tenant;
use Database\Seeders\DefaultEmailTemplatesSeeder;

/**
 * Provisioning steps that should run once when a tenant is created.
 *
 * Right now: copy the starter email-template set into the new tenant
 * so its first user has working templates without manual seeding.
 *
 * Why an observer (not a Filament action ->after, not a controller
 * post-create hook):
 *   - Catches the SA's "Create Workspace" UI path     (Filament UI)
 *   - Catches the public RegistrationController path  (self-signup)
 *   - Catches future API endpoints                    (free)
 *   - Catches a hypothetical "make:tenant" CLI cmd    (free)
 *
 * Demo seeder bypasses this via Tenant::withoutEvents() because the
 * demo refresh seeds its own template fixtures and doesn't want the
 * starter set to clutter every refresh cycle.
 *
 * The seeding itself is idempotent on (tenant_id, name) - re-running
 * the observer for the same tenant is a no-op, so factory-created
 * tenants in tests can fire this safely without causing duplicates.
 */
class TenantProvisioningObserver
{
    public function created(Tenant $tenant): void
    {
        DefaultEmailTemplatesSeeder::seedFor($tenant);
    }
}
