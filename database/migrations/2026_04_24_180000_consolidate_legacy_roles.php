<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;
use Spatie\Permission\Models\Role;
use Spatie\Permission\PermissionRegistrar;

/**
 * Consolidate the legacy `agent` + `viewer` Spatie roles into the
 * new two-tier vocabulary (`manager` / `member`).  Policy:
 *   agent  → member
 *   viewer → member  (narrower permission set, but a tenant admin
 *                     who promoted them to 'viewer' can just demote
 *                     back if needed — keeps us from deleting
 *                     legacy role rows that model_has_roles still
 *                     FKs onto.)
 *
 * The legacy role rows stay in DB so any pivot row still referencing
 * them continues resolving — rename-by-copy is safer than delete.
 * Seeders updated separately to stop creating agent/viewer on
 * fresh installs.
 */
return new class extends Migration {
    public function up(): void
    {
        app()[PermissionRegistrar::class]->forgetCachedPermissions();

        // Only migrate if the member role exists (earlier
        // migration from this same patch series).
        $member = Role::where('name', 'member')->where('guard_name', 'web')->first();
        if (! $member) {
            return;
        }

        // Move every model_has_roles pivot from agent/viewer → member
        // in one UPDATE per legacy role.
        foreach (['agent', 'viewer'] as $legacyName) {
            $legacy = Role::where('name', $legacyName)->where('guard_name', 'web')->first();
            if (! $legacy) {
                continue;
            }

            DB::table('model_has_roles')
                ->where('role_id', $legacy->id)
                ->update(['role_id' => $member->id]);
        }

        // tenant_users pivot table has a freeform `role` column
        // (not an FK to roles.id) — rename in place.
        if (\Illuminate\Support\Facades\Schema::hasTable('tenant_users')
            && \Illuminate\Support\Facades\Schema::hasColumn('tenant_users', 'role')) {
            DB::table('tenant_users')
                ->whereIn('role', ['agent', 'viewer'])
                ->update(['role' => 'member']);
        }

        // invitations.role too.
        if (\Illuminate\Support\Facades\Schema::hasTable('invitations')
            && \Illuminate\Support\Facades\Schema::hasColumn('invitations', 'role')) {
            DB::table('invitations')
                ->whereIn('role', ['agent', 'viewer'])
                ->update(['role' => 'member']);
        }
    }

    public function down(): void
    {
        // Intentionally no-op — un-consolidating would require
        // remembering which users were originally `agent` vs
        // `viewer`, and we don't keep that distinction.
    }
};
