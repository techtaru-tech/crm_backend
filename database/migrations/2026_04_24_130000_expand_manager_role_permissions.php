<?php

use Illuminate\Database\Migrations\Migration;
use Spatie\Permission\Models\Permission;
use Spatie\Permission\Models\Role;
use Spatie\Permission\PermissionRegistrar;

/**
 * Expand the `manager` role's permission set on existing installs
 * so managers can actually manage team + pipelines + delete leads.
 * Previously manager had only view on many resources, making it
 * indistinguishable from member once Filament's permission gates
 * started enforcing.
 *
 * Idempotent: syncPermissions replaces the set each run, so this
 * works even if run multiple times.  Non-destructive on down() —
 * we don't shrink permissions on rollback because that would
 * immediately break managers' workflows on a real deployment.
 */
return new class extends Migration {
    public function up(): void
    {
        app()[PermissionRegistrar::class]->forgetCachedPermissions();

        $manager = Role::where('name', 'manager')->where('guard_name', 'web')->first();
        if (! $manager) {
            return;
        }

        $managerPermNames = [
            'leads.view', 'leads.create', 'leads.edit', 'leads.delete',
            'leads.export', 'leads.import', 'leads.assign',
            'pipeline.view', 'pipeline.manage',
            'forms.view', 'forms.create', 'forms.edit', 'forms.delete',
            'automations.view', 'automations.create', 'automations.edit', 'automations.delete',
            'integrations.view', 'integrations.manage',
            'reports.view', 'reports.export',
            'team.view', 'team.invite',
            'settings.view',
        ];

        $perms = Permission::whereIn('name', $managerPermNames)
            ->where('guard_name', 'web')
            ->get();

        if ($perms->isNotEmpty()) {
            $manager->syncPermissions($perms);
        }
    }

    public function down(): void
    {
        // Non-destructive: shrinking manager permissions on rollback
        // would immediately lock real managers out of features they
        // actively use.  If truly needed, handle in tinker.
    }
};
