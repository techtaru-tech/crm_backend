<?php

use Illuminate\Database\Migrations\Migration;
use Spatie\Permission\Models\Permission;
use Spatie\Permission\Models\Role;
use Spatie\Permission\PermissionRegistrar;

/**
 * Add the `viewer` role (spec §3): read-only access to permitted leads
 * and dashboards.
 *
 * Deliberately holds only *.view permissions — no create/edit/delete/export.
 * Export is excluded on purpose: "read-only" in the spec means look, not
 * take a copy of the database away.
 *
 * Note on the sibling role: `manager` becomes the spec's "Team Manager".
 * It already existed, already carries the right permission set, and no user
 * is assigned to it, so it is reused rather than duplicated — what makes it
 * team-scoped is LeadVisibilityScope reading team_user.is_manager, not the
 * permission list.
 *
 * Idempotent via firstOrCreate.
 */
return new class extends Migration {
    public function up(): void
    {
        app()[PermissionRegistrar::class]->forgetCachedPermissions();

        $role = Role::firstOrCreate([
            'name'       => 'viewer',
            'guard_name' => 'web',
        ]);

        $permNames = [
            'leads.view',
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
        // Non-destructive, same reasoning as the `member` role migration:
        // dropping a role that users are assigned to orphans permission
        // checks on live workspaces.
    }
};
