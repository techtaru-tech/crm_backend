<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

/**
 * Frontend registration (/register → RegistrationController::store)
 * creates the tenant first, THEN the owner user, then stamps
 * tenant.owner_id.  Without a nullable owner_id the initial INSERT
 * throws "Field 'owner_id' doesn't have a default value".
 *
 * Making the column nullable matches real business logic too:
 *  - A tenant CAN momentarily exist with no owner during provisioning.
 *  - A tenant whose owner is deleted should be a soft/orphan state,
 *    not a cascading delete of the tenant itself.
 */
return new class extends Migration {
    public function up(): void
    {
        Schema::table('tenants', function (Blueprint $table) {
            $table->unsignedBigInteger('owner_id')->nullable()->change();
        });
    }

    public function down(): void
    {
        // Don't re-force NOT NULL on rollback — rows that were created
        // in the interim may legitimately have no owner yet.
    }
};
