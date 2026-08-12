<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('invitations', function (Blueprint $table) {
            $table->id();
            $table->foreignId('tenant_id')->constrained()->cascadeOnDelete();
            $table->foreignId('invited_by')->constrained('users')->cascadeOnDelete();
            $table->string('email');
            $table->string('role')->default('agent');
            $table->string('token', 64)->unique();
            $table->timestamp('accepted_at')->nullable();
            // useCurrent() so MySQL/MariaDB strict mode (NO_ZERO_DATE
            // + NO_ZERO_IN_DATE, enabled by Laravel's default
            // 'strict' => true) accepts the column.  Without an
            // explicit default the server tries to use
            // 0000-00-00 00:00:00 and throws 1067 "Invalid default
            // value".  App code in InvitationController always sets
            // expires_at to now()->addDays(7) on insert, so the
            // column default is never used in production — it just
            // satisfies the DDL safety net.
            $table->timestamp('expires_at')->useCurrent();
            $table->timestamps();

            $table->index(['token', 'expires_at']);
            $table->unique(['tenant_id', 'email']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('invitations');
    }
};
