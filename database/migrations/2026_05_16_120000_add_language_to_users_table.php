<?php

declare(strict_types=1);

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

/*
|--------------------------------------------------------------------------
| Add `language` column to users table
|--------------------------------------------------------------------------
| Backing column for App\Models\User::preferredLocale() — fulfills the
| Illuminate\Contracts\Translation\HasLocalePreference contract that
| Laravel inspects when dispatching queued Notifications. Without this,
| every queued notification (LeadAssigned, LeadTaskReminder, IntegrationSync
| Failed, etc.) renders in the queue worker's app.locale (=en) regardless
| of the recipient's selected UI language — a silent Item-1 leak class
| flagged by paranoid pass 31.
|
| Nullable so existing rows don't need to backfill; SetLocale middleware
| treats null as "fall through to session / GeneralSettings default".
|
| Length 10 covers BCP-47 codes like `pt-BR` and `zh-Hant` with room to
| spare; matches the `string(10)` shape used for other locale columns in
| the schema.
*/

return new class extends Migration {
    public function up(): void
    {
        Schema::table('users', function (Blueprint $table) {
            if (! Schema::hasColumn('users', 'language')) {
                $table->string('language', 10)
                    ->nullable()
                    ->after('email_verified_at');
            }
        });
    }

    public function down(): void
    {
        Schema::table('users', function (Blueprint $table) {
            if (Schema::hasColumn('users', 'language')) {
                $table->dropColumn('language');
            }
        });
    }
};
