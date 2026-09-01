<?php

use Illuminate\Database\Migrations\Migration;
use Spatie\Permission\Models\Permission;
use Spatie\Permission\Models\Role;
use Spatie\Permission\PermissionRegistrar;

/**
 * Permissions for the new Sales Teams resource.
 *
 * Granted to admin/super_admin (workspace administration) and, view-only,
 * to manager/member/viewer — a rep needs to see which team a lead sits in
 * without being able to reshape the org chart.
 */
return new class extends Migration {
    public function up(): void
    {
        app()[PermissionRegistrar::class]->forgetCachedPermissions();

        $all = ['teams.view', 'teams.create', 'teams.edit', 'teams.delete'];
        foreach ($all as $name) {
            Permission::firstOrCreate(['name' => $name, 'guard_name' => 'web']);
        }

        foreach (['super_admin', 'admin'] as $roleName) {
            Role::where('name', $roleName)->where('guard_name', 'web')->first()
                ?->givePermissionTo($all);
        }

        foreach (['manager', 'member', 'viewer'] as $roleName) {
            Role::where('name', $roleName)->where('guard_name', 'web')->first()
                ?->givePermissionTo('teams.view');
        }
    }

    public function down(): void
    {
        app()[PermissionRegistrar::class]->forgetCachedPermissions();
        Permission::whereIn('name', ['teams.view', 'teams.create', 'teams.edit', 'teams.delete'])
            ->where('guard_name', 'web')->delete();
    }
};
