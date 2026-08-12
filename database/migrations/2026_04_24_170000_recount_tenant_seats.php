<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

/**
 * One-time data migration: corrects seat_count drift by recounting
 * from the tenant_users pivot on every existing tenant.  Historical
 * code path used $tenant->increment('seat_count') + decrement()
 * which drifted silently across edge cases (re-invite of existing
 * user, SA-panel user deletes, etc.).
 *
 * Going forward, Tenant::recountSeats() is called at the right
 * points so drift can't re-accumulate.
 */
return new class extends Migration {
    public function up(): void
    {
        if (! \Illuminate\Support\Facades\Schema::hasTable('tenants')) {
            return;
        }
        if (! \Illuminate\Support\Facades\Schema::hasTable('tenant_users')) {
            return;
        }

        $pivotCounts = DB::table('tenant_users')
            ->select('tenant_id', DB::raw('COUNT(*) as c'))
            ->groupBy('tenant_id')
            ->get()
            ->pluck('c', 'tenant_id');

        DB::table('tenants')
            ->select('id', 'seat_count')
            ->orderBy('id')
            ->chunk(200, function ($rows) use ($pivotCounts) {
                foreach ($rows as $row) {
                    $correct = (int) ($pivotCounts[$row->id] ?? 0);
                    if ((int) $row->seat_count !== $correct) {
                        DB::table('tenants')
                            ->where('id', $row->id)
                            ->update(['seat_count' => $correct]);
                    }
                }
            });
    }

    public function down(): void
    {
        // No down path — we're correcting drift, not re-introducing it.
    }
};
