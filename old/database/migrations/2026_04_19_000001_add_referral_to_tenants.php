<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('tenants', function (Blueprint $table) {
            $table->string('referral_code', 32)->nullable()->unique()->after('slug');
            $table->foreignId('referred_by_tenant_id')->nullable()->after('referral_code')->constrained('tenants')->nullOnDelete();
        });
    }

    public function down(): void
    {
        Schema::table('tenants', function (Blueprint $table) {
            $table->dropConstrainedForeignId('referred_by_tenant_id');
            $table->dropColumn('referral_code');
        });
    }
};
