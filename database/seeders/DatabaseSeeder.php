<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Spatie\Permission\Models\Permission;
use Spatie\Permission\Models\Role;
use Spatie\Permission\PermissionRegistrar;

class DatabaseSeeder extends Seeder
{
    // Role inventory (two-tier team vocabulary):
    //   super_admin — platform-level.  Granted only to SA panel users.
    //   admin       — tenant owner.  Granted by SA "Create Workspace".
    //   manager     — tenant staff with elevated privileges (invite,
    //                 manage leads, edit pipelines).
    //   member      — standard tenant staff.  Can work with leads
    //                 but not administrate the workspace.
    //
    // Legacy `agent` + `viewer` roles were consolidated into member
    // via migration 2026_04_24_180000_consolidate_legacy_roles.
    // They are no longer seeded on fresh installs.
    protected array $roles = [
        'super_admin',
        'admin',
        'manager',
        'member',
    ];

    protected array $permissions = [
        'leads.view', 'leads.create', 'leads.edit', 'leads.delete',
        'leads.export', 'leads.import', 'leads.assign',
        'pipeline.view', 'pipeline.manage',
        'forms.view', 'forms.create', 'forms.edit', 'forms.delete',
        'automations.view', 'automations.create', 'automations.edit', 'automations.delete',
        'integrations.view', 'integrations.manage',
        'reports.view', 'reports.export',
        'team.view', 'team.invite', 'team.manage',
        'settings.view', 'settings.manage',
        'api_keys.manage',
        'webhooks.manage',
    ];

    protected array $rolePermissions = [
        'manager' => [
            'leads.view', 'leads.create', 'leads.edit', 'leads.delete',
            'leads.export', 'leads.import', 'leads.assign',
            'pipeline.view', 'pipeline.manage',
            'forms.view', 'forms.create', 'forms.edit', 'forms.delete',
            'automations.view', 'automations.create', 'automations.edit', 'automations.delete',
            'integrations.view', 'integrations.manage',
            'reports.view', 'reports.export',
            // team.invite lets managers add/remove team members.
            // team.manage (full CRUD including admin removal) is
            // intentionally withheld from manager — only the
            // workspace owner (admin role) can delete admins.
            'team.view', 'team.invite',
            'settings.view',
        ],
        // Member — standard team member permissions.
        'member' => [
            'leads.view', 'leads.create', 'leads.edit', 'leads.assign',
            'pipeline.view',
            'forms.view',
            'automations.view',
            'integrations.view',
            'reports.view',
        ],
    ];

    public function run(): void
    {
        app()[PermissionRegistrar::class]->forgetCachedPermissions();

        foreach ($this->permissions as $permission) {
            Permission::firstOrCreate(['name' => $permission, 'guard_name' => 'web']);
        }

        foreach ($this->roles as $roleName) {
            $role = Role::firstOrCreate(['name' => $roleName, 'guard_name' => 'web']);

            if (in_array($roleName, ['super_admin', 'admin'])) {
                $role->syncPermissions(Permission::all());
            } else {
                $perms = $this->rolePermissions[$roleName] ?? [];
                $role->syncPermissions(
                    Permission::whereIn('name', $perms)->get()
                );
            }
        }

        $this->command->info('Roles and permissions seeded successfully.');

        $this->call([
            LocalesSeeder::class,
            PlansSeeder::class,
            StaticPagesSeeder::class,
            CommercialDemoSeeder::class,
            // After CommercialDemoSeeder so the demo tenant exists —
            // DefaultEmailTemplatesSeeder iterates Tenant::all() and
            // copies the starter template set into each.  Idempotent
            // on (tenant_id, name) so re-runs don't duplicate.
            DefaultEmailTemplatesSeeder::class,
            // Curated free starter templates so /admin/marketplace isn't
            // empty out of the box. After CommercialDemoSeeder so a tenant
            // exists to own them; idempotent on (type, name).
            MarketplaceTemplatesSeeder::class,
        ]);
    }
}
