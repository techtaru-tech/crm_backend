<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

/**
 * Relax (or add) users.tenant_id foreign key to SET NULL on delete.
 *
 * Handles three possible pre-existing states of users.tenant_id:
 *   (a) No FK at all — original migration never called ->constrained().
 *       Just ADD the FK with nullOnDelete.
 *   (b) FK exists under Laravel's conventional name
 *       (`users_tenant_id_foreign`) with RESTRICT or CASCADE behaviour.
 *       DROP it, then re-add with nullOnDelete.
 *   (c) FK exists under a different name. We detect any FK on this
 *       column via information_schema and drop it by its real name.
 *
 * The first attempt at this migration used a try/catch around
 * dropForeign(), but Laravel defers the DROP to the ALTER TABLE
 * statement executed at Blueprint close — so the exception fires
 * outside the try block and the migration aborts.  This version
 * introspects information_schema first.
 */
return new class extends Migration {
    public function up(): void
    {
        if (Schema::getConnection()->getDriverName() === 'sqlite') {
            return; // tests only, nothing to do
        }

        if (! Schema::hasTable('users') || ! Schema::hasColumn('users', 'tenant_id')) {
            return;
        }

        // 1. Find any FK on users.tenant_id regardless of its name.
        $existing = DB::selectOne("
            SELECT CONSTRAINT_NAME AS name
            FROM information_schema.KEY_COLUMN_USAGE
            WHERE TABLE_SCHEMA = DATABASE()
              AND TABLE_NAME   = 'users'
              AND COLUMN_NAME  = 'tenant_id'
              AND REFERENCED_TABLE_NAME = 'tenants'
            LIMIT 1
        ");

        // 2. Drop any existing FK first (if present) so we can freely
        //    mutate the column values in step 3.
        if ($existing) {
            Schema::table('users', function (Blueprint $table) use ($existing) {
                $table->dropForeign($existing->name);
            });
        }

        // 3. Clean up orphan pointers BEFORE adding the new FK —
        //    MySQL validates existing data when the constraint is
        //    created, so any users.tenant_id that doesn't match a
        //    real tenants.id would block the constraint.
        DB::statement("
            UPDATE users u
            LEFT JOIN tenants t ON t.id = u.tenant_id
            SET u.tenant_id = NULL
            WHERE u.tenant_id IS NOT NULL AND t.id IS NULL
        ");

        // 4. Now add the FK with nullOnDelete.
        Schema::table('users', function (Blueprint $table) {
            $table->foreign('tenant_id')
                ->references('id')->on('tenants')
                ->nullOnDelete();
        });
    }

    public function down(): void
    {
        if (Schema::getConnection()->getDriverName() === 'sqlite') {
            return;
        }

        if (! Schema::hasTable('users') || ! Schema::hasColumn('users', 'tenant_id')) {
            return;
        }

        $existing = DB::selectOne("
            SELECT CONSTRAINT_NAME AS name
            FROM information_schema.KEY_COLUMN_USAGE
            WHERE TABLE_SCHEMA = DATABASE()
              AND TABLE_NAME   = 'users'
              AND COLUMN_NAME  = 'tenant_id'
              AND REFERENCED_TABLE_NAME = 'tenants'
            LIMIT 1
        ");

        if ($existing) {
            Schema::table('users', function (Blueprint $table) use ($existing) {
                $table->dropForeign($existing->name);
            });
        }

        Schema::table('users', function (Blueprint $table) {
            $table->foreign('tenant_id')
                ->references('id')->on('tenants')
                ->restrictOnDelete();
        });
    }
};
