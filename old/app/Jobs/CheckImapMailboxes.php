<?php

namespace App\Jobs;

use App\Models\LeadSourceConnection;
use App\Services\ConnectorRegistry;
use App\Services\LeadSources\EmailImapConnector;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Queue\Queueable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Support\Facades\Log;

class CheckImapMailboxes implements ShouldQueue
{
    use Queueable, InteractsWithQueue;

    public int $tries = 1;

    // $tenantId is read by AppServiceProvider's Queue::before hook
    // (reflection on the resolved command) to bind `current_tenant`
    // for the duration of the worker run.  This job is currently
    // dispatched as a cross-tenant scheduler (null tenantId — the
    // poller iterates LeadSourceConnection rows directly) but the
    // property is here so a future per-tenant dispatcher can use it
    // without needing a constructor change.
    public function __construct(public ?int $tenantId = null)
    {
        $this->onQueue('notifications');
    }

    public function handle(ConnectorRegistry $registry): void
    {
        $connections = LeadSourceConnection::where('source', 'email')
            ->where('active', true)
            ->get();

        foreach ($connections as $connection) {
            try {
                $connector = $registry->resolve('email');
                if ($connector instanceof EmailImapConnector) {
                    $leads = $connector->pollMailbox($connection);
                    Log::info("IMAP check complete", [
                        'connection_id' => $connection->id,
                        'leads_found'   => count($leads),
                    ]);
                }
            } catch (\Throwable $e) {
                Log::error("IMAP check failed", [
                    'connection_id' => $connection->id,
                    'error'         => $e->getMessage(),
                ]);
            }
        }
    }
}
