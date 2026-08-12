<?php

namespace App\Services;

use App\Models\ScheduledReport;
use App\Models\Tenant;
use Carbon\Carbon;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Mail;

class ScheduledReportService
{
    public function __construct(private readonly ReportService $reports) {}

    public function deliver(ScheduledReport $report): bool
    {
        try {
            $tenant = Tenant::find($report->tenant_id);
            if (!$tenant) {
                return false;
            }

            [$from, $to] = $this->dateRange($report->frequency);

            $data = $this->fetchReportData($report, $tenant->id, $from, $to);
            if (empty($data)) {
                return false;
            }

            $csv     = $this->generateCsv($data);
            $subject = __('scheduled_reports.mail_subject', [
                'report' => $report->name,
                'date'   => now()->translatedFormat('M j, Y'),
            ]);
            $body = __('scheduled_reports.mail_body', [
                'report'    => $report->name,
                'generated' => now()->translatedFormat('M j, Y H:i'),
                'period'    => $from->translatedFormat('M j') . ' – ' . $to->translatedFormat('M j, Y'),
            ]);

            foreach ($report->recipient_emails as $email) {
                Mail::raw(
                    $body,
                    function ($message) use ($email, $subject, $csv, $report) {
                        $message->to($email)
                                ->subject($subject)
                                ->attachData($csv, $report->name . '_' . now()->format('Y-m-d') . '.csv', [
                                    'mime' => 'text/csv',
                                ]);
                    }
                );
            }

            $report->update([
                'last_sent_at' => now(),
                'next_due_at'  => $report->calculateNextDue(),
            ]);

            Log::info('ScheduledReportService: delivered report', [
                'report_id'   => $report->id,
                'report_type' => $report->report_type,
                'recipients'  => count($report->recipient_emails),
            ]);

            return true;
        } catch (\Throwable $e) {
            Log::error('ScheduledReportService: failed to deliver report', [
                'report_id' => $report->id,
                'error'     => $e->getMessage(),
            ]);
            return false;
        }
    }

    private function fetchReportData(ScheduledReport $report, int $tenantId, Carbon $from, Carbon $to): array
    {
        return match ($report->report_type) {
            'dashboard_stats'       => $this->flatten($this->reports->dashboardStats($tenantId)),
            'leads_by_source'       => $this->reports->leadsBySource($tenantId, $from, $to),
            'pipeline_distribution' => $this->reports->pipelineDistribution($tenantId),
            'agent_performance'     => $this->reports->agentPerformance($tenantId, $from, $to),
            'source_performance'    => $this->reports->sourcePerformance($tenantId, $from, $to),
            'automation_stats'      => $this->flatten($this->reports->automationStats($tenantId, $from, $to)),
            'form_analytics'        => $this->reports->formAnalytics($tenantId, $from, $to),
            default                 => [],
        };
    }

    private function flatten(array $data): array
    {
        return array_map(fn($key, $value) => ['metric' => $key, 'value' => $value], array_keys($data), $data);
    }

    private function generateCsv(array $data): string
    {
        if (empty($data)) {
            return '';
        }

        $output  = fopen('php://temp', 'r+');
        // CSV formula-injection fix: every cell (headers + rows)
        // goes through CsvSafety::neutralize so a tenant-supplied "+CMD()"
        // / "=HYPERLINK(...)" / "@SUM(...)" string can't fire formulas when
        // the recipient opens the scheduled-report CSV in Excel.  Same
        // OWASP guidance applied in ExportController and LeadsExport.
        $headers = CsvSafety::neutralizeRow(array_keys(reset($data)));
        fputcsv($output, $headers);

        foreach ($data as $row) {
            fputcsv($output, CsvSafety::neutralizeRow(array_values($row)));
        }

        rewind($output);
        $csv = stream_get_contents($output);
        fclose($output);

        return $csv;
    }

    private function dateRange(string $frequency): array
    {
        return match ($frequency) {
            'daily'   => [now()->subDay()->startOfDay(),   now()->subDay()->endOfDay()],
            'weekly'  => [now()->subWeek()->startOfWeek(), now()->subWeek()->endOfWeek()],
            'monthly' => [now()->subMonth()->startOfMonth(), now()->subMonth()->endOfMonth()],
            default   => [now()->subDay()->startOfDay(),   now()->subDay()->endOfDay()],
        };
    }
}
