<?php

declare(strict_types=1);

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

/**
 * 2-way calendar sync — Google Calendar + Outlook (Microsoft Graph).
 *
 * One row per connected calendar.  Each user can connect their own
 * calendar (per-user OAuth) so the system can:
 *   - PUSH outbound: when a MeetingBooking is created, post the event
 *     to the user's external calendar
 *   - PULL inbound: poll the external calendar (every 15 min via cron)
 *     for new/updated/deleted events and surface them as
 *     MeetingBooking rows
 *
 * Token lifecycle:
 *   - access_token: short-lived (~1h Google, ~1h Microsoft) — refreshed
 *     automatically via refresh_token before each API call
 *   - refresh_token: long-lived; only invalidated on revoke
 *   - expires_at: when access_token goes stale; sync logic refreshes
 *     when within 60s of expiry
 *
 * Sync state:
 *   - sync_token: provider-specific cursor for incremental sync
 *     (Google's syncToken, Microsoft's deltaLink).  Saves bandwidth
 *     vs. full-list polling.
 *   - last_synced_at: timestamp of last successful sync
 *   - last_sync_error: most recent error message (null when healthy)
 *
 * Status:
 *   active        — token valid, sync working
 *   needs_reauth  — refresh_token revoked / expired; user must reconnect
 *   error         — transient sync errors; cron retries
 *   disabled      — user paused sync without disconnecting
 */
return new class extends Migration {
    public function up(): void
    {
        Schema::create('calendar_connections', function (Blueprint $table) {
            $table->id();
            $table->foreignId('tenant_id')->constrained()->cascadeOnDelete();
            $table->foreignId('user_id')->constrained()->cascadeOnDelete();
            $table->string('provider', 20);
            $table->string('external_account_email', 255);

            // OAuth tokens
            $table->text('access_token');
            $table->text('refresh_token')->nullable();
            $table->timestamp('expires_at')->nullable();
            $table->json('scopes')->nullable();

            // Sync target + state
            $table->string('calendar_id', 255)->nullable();
            $table->string('sync_token', 1024)->nullable();
            $table->timestamp('last_synced_at')->nullable();
            $table->text('last_sync_error')->nullable();

            $table->string('status', 20)->default('active');

            $table->json('metadata')->nullable();
            $table->timestamps();

            $table->unique(['user_id', 'provider'], 'cal_conn_user_provider_unq');
            $table->index(['tenant_id', 'status']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('calendar_connections');
    }
};
