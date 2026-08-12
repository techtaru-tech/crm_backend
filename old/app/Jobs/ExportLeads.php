<?php

namespace App\Jobs;

use App\Exports\LeadsExport;
use App\Models\User;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Queue\Queueable;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\URL;
use Maatwebsite\Excel\Facades\Excel;

class ExportLeads implements ShouldQueue
{
    use Queueable;

    public int $timeout = 300;
    public int $tries   = 1;

    public function __construct(
        public int $tenantId,
        public int $userId,
        public array $filters = [],
    ) {
        $this->onQueue('exports');
    }

    public function handle(): void
    {
        $user = User::where('id', $this->userId)
            ->where('tenant_id', $this->tenantId)
            ->first();

        if (! $user) {
            Log::warning('ExportLeads: user not found or not in tenant', [
                'user_id'   => $this->userId,
                'tenant_id' => $this->tenantId,
            ]);
            return;
        }

        $filename = 'exports/leads_' . $this->tenantId . '_' . now()->format('Ymd_His') . '.csv';

        Excel::store(
            new LeadsExport($this->tenantId, $this->filters),
            $filename,
            'local'
        );

        $signedUrl = URL::temporarySignedRoute(
            'export.download',
            now()->addHours(24),
            ['file' => $filename]
        );

        $user->notify(new \App\Notifications\LeadsExportReady($signedUrl, $filename));
    }

    public function failed(\Throwable $exception): void
    {
        Log::error('ExportLeads job failed', [
            'tenant_id' => $this->tenantId,
            'user_id'   => $this->userId,
            'error'     => $exception->getMessage(),
        ]);

        $user = User::where('id', $this->userId)
            ->where('tenant_id', $this->tenantId)
            ->first();

        if ($user) {
            $user->notify(new \App\Notifications\ExportFailedNotification($exception->getMessage()));
        }
    }
}
