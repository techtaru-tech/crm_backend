<?php

namespace App\Console\Commands;

use App\Models\ScheduledReport;
use App\Services\ScheduledReportService;
use Illuminate\Console\Command;

class DeliverScheduledReports extends Command
{
    protected $signature   = 'reports:deliver';
    protected $description = 'Deliver all scheduled reports that are due';

    public function handle(ScheduledReportService $service): int
    {
        $due = ScheduledReport::where('is_active', true)
            ->where(function ($q) {
                $q->whereNull('next_due_at')
                  ->orWhere('next_due_at', '<=', now());
            })
            ->get();

        if ($due->isEmpty()) {
            $this->info('No scheduled reports are due.');
            return self::SUCCESS;
        }

        $this->info("Processing {$due->count()} scheduled report(s)...");
        $success = 0;
        $failed  = 0;

        foreach ($due as $report) {
            $this->line("  → {$report->name} [{$report->report_type}]");

            if ($service->deliver($report)) {
                $this->info("    ✓ Delivered to " . count($report->recipient_emails) . " recipient(s)");
                $success++;
            } else {
                $this->warn("    ✗ Failed to deliver");
                $failed++;
            }
        }

        $this->newLine();
        $this->info("Done — {$success} delivered, {$failed} failed.");

        return $failed > 0 ? self::FAILURE : self::SUCCESS;
    }
}
