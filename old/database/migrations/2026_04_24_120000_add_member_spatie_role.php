<?php

use Illuminate\Database\Migrations\Migration;
use Spatie\Permission\Models\Permission;
use Spatie\Permission\Models\Role;
use Spatie\Permission\PermissionRegistrar;

/**
 * Add the `member` Spatie role to existing installs so the unified
 * /admin/users/create + onboarding-wizard invite flows (both
 * restricted to `manager` and `member`) can syncRoles(['member'])
 * without throwing `Spatie\Permission\Exceptions\RoleDoesNotExist`.
 *
 * Fresh installs pick `member` up via DatabaseSeeder; this migration
 * covers upgrade-in-place for production installs that already ran
 * a prior seed without `member`.
 *
 * Idempotent via firstOrCreate — safe to run multiple times.
 */
return new class extends Migration {
    public function up(): void
    {
        app()[PermissionRegistrar::class]->forgetCachedPermissions();

        $role = Role::firstOrCreate([
            'name'       => 'member',
            'guard_name' => 'web',
        ]);

        // Mirror the `agent` permission set: a standard team member
        // can work with leads but not administrate the workspace.
        $permNames = [
            'leads.view', 'leads.create', 'leads.edit', 'leads.assign',
            'pipeline.view',
            'forms.view',
            'automations.view',
            'integrations.view',
            'reports.view',
        ];

        $perms = Permission::whereIn('name', $permNames)
            ->where('guard_name', 'web')
            ->get();

        if ($perms->isNotEmpty()) {
            $role->syncPermissions($perms);
        }
    }

    public function down(): void
    {
        // Intentionally non-destructive: existing user_role pivot
        // rows may reference this role; dropping it would orphan
        // permission checks on live tenants.  If you truly need to
        // remove it, handle reassignment manually in tinker.
    }
};
