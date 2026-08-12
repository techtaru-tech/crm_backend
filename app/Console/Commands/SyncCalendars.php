<?php

declare(strict_types=1);

namespace App\Console\Commands;

use App\Models\CalendarConnection;
use App\Services\Calendar\GoogleCalendarSyncService;
use App\Services\Calendar\OutlookCalendarSyncService;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Schema;

/**
 * Polls each active calendar connection for new/updated/cancelled
 * events and surfaces them as MeetingBooking rows.
 *
 * Wire-up: register in routes/console.php as
 *
 *   Schedule::command('leadhub:sync-calendars')->everyFifteenMinutes();
 *
 * Per-connection failures are logged + the connection is flagged
 * (status='error' or 'needs_reauth') — they don't kill the whole
 * sweep.  Re-runs are safe: incremental sync via the saved syncToken
 * means duplicate work is bounded by the polling cadence.
 *
 * Outlook polling is a follow-up that needs OutlookCalendarSyncService;
 * this command currently dispatches Google connections only and skips
 * Outlook rows quietly so they don't appear as failures.
 */
class SyncCalendars extends Command
{
    protected $signature   = 'leadhub:sync-calendars
                              {--dry-run : Print what would happen without writing changes}
                              {--user= : Only sync the connection for this user id}';
    protected $description = 'Pull new/updated/cancelled events from connected calendars into MeetingBookings';

    protected bool $dryRun = false;

    public function handle(GoogleCalendarSyncService $google, OutlookCalendarSyncService $outlook): int
    {
        $this->dryRun = (bool) $this->option('dry-run');

        if (! Schema::hasTable('calendar_connections')) {
            $this->warn('calendar_connections table missing — run migrations.');
            return self::FAILURE;
        }

        $userFilter = $this->option('user');

        $query = CalendarConnection::query()->active();

        if ($userFilter) {
            $query->where('user_id', (int) $userFilter);
        }

        $totalEvents = 0;
        $totalConns  = 0;
        $errors      = 0;

        $query->chunk(50, function ($connections) use ($google, $outlook, &$totalEvents, &$totalConns, &$errors) {
            foreach ($connections as $conn) {
                $totalConns++;

                if ($this->dryRun) {
                    $this->line("[dry-run] would sync user={$conn->user_id} provider={$conn->provider}");
                    continue;
                }

                try {
                    $count = match ($conn->provider) {
                        CalendarConnection::PROVIDER_OUTLOOK => $outlook->pullEvents($conn),
                        default                              => $google->pullEvents($conn),
                    };
                    $totalEvents += $count;
                    $this->line("[sync] user={$conn->user_id} provider={$conn->provider} events={$count}");
                } catch (\Throwable $e) {
                    $errors++;
                    Log::error('SyncCalendars: connection failed', [
                        'connection_id' => $conn->id,
                        'user_id'       => $conn->user_id,
                        'provider'      => $conn->provider,
                        'error'         => $e->getMessage(),
                    ]);
                }
            }
        });

        $this->info("Done. {$totalConns} connection(s) processed, {$totalEvents} event(s) synced, {$errors} error(s).");
        return self::SUCCESS;
    }
}
