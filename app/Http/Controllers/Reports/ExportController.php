<?php

namespace App\Http\Controllers\Reports;

use App\Http\Controllers\Controller;
use App\Models\Tenant;
use App\Services\ReportService;
use App\Services\SvgChartRenderer;
use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Support\Carbon;

class ExportController extends Controller
{
    /**
     * Reports the export endpoint will produce.  Anything outside this
     * list is rejected at validation time so an attacker cannot probe
     * for hidden report types via crafted ?report= params.
     */
    private const ALLOWED_REPORTS = [
        'lead-volume',
        'source-performance',
        'pipeline-funnel',
        'agent-performance',
        'automation-stats',
        'form-analytics',
        'response-time',
    ];

    /**
     * Range presets understood by ReportService::dateRange().  `custom`
     * pairs with explicit dateFrom/dateTo strings.  Values mirror the
     * match arms in ReportService — anything else falls through to the
     * 30-day default which would otherwise hide a malformed param.
     */
    private const ALLOWED_RANGES = ['7', '30', '60', '90', 'this_month', 'last_month', 'custom'];

    public function __construct(private ReportService $svc) {}

    public function csv(Request $request): Response|\Symfony\Component\HttpFoundation\StreamedResponse
    {
        $tenantId = auth()->user()?->tenant_id;
        if (! $tenantId) {
            abort(403);
        }

        $valid = $this->validateInputs($request);

        $report    = $valid['report'];
        $dateRange = $valid['dateRange'];
        $dateFrom  = $valid['dateFrom'] ?? null;
        $dateTo    = $valid['dateTo']   ?? null;

        [$from, $to] = $this->svc->dateRange($dateRange, $dateFrom, $dateTo);

        [$headers, $rows] = $this->getData($report, $tenantId, $from, $to, $request);

        $filename = $report . '-' . now()->format('Y-m-d') . '.csv';

        return response()->streamDownload(function () use ($headers, $rows) {
            $handle = fopen('php://output', 'w');
            // M-S2: CSV-injection neutralisation.  Lead/UTM/source
            // strings come from public form input — when a tenant
            // admin opens the export in Excel/Numbers/LibreOffice,
            // a cell starting with `=`, `+`, `-`, `@`, tab, or CR
            // is interpreted as a formula and can fire DDE/HYPERLINK
            // exfil payloads.  Prefix a single-quote so the cell
            // becomes a literal string — Excel hides the prefix at
            // render time so the buyer sees the correct value.
            $headers = array_map([$this, 'csvSafe'], $headers);
            fputcsv($handle, $headers);
            foreach ($rows as $row) {
                fputcsv($handle, array_map([$this, 'csvSafe'], $row));
            }
            fclose($handle);
        }, $filename, ['Content-Type' => 'text/csv']);
    }

    /**
     * Neutralise CSV-injection per OWASP guidance.  Run every cell
     * through this helper before handing to fputcsv so a tenant-
     * supplied "+CMD()" / "=HYPERLINK(...)" / "@SUM(...)" string
     * doesn't fire formulas in the spreadsheet client.
     */
    private function csvSafe(mixed $value): string
    {
        $str = (string) $value;
        if ($str === '') {
            return $str;
        }
        $first = $str[0];
        if ($first === '=' || $first === '+' || $first === '-' || $first === '@' || $first === "\t" || $first === "\r") {
            return "'" . $str;
        }
        return $str;
    }

    public function pdf(Request $request): Response
    {
        $tenantId = auth()->user()?->tenant_id;
        if (! $tenantId) {
            abort(403);
        }

        $valid = $this->validateInputs($request);

        $report    = $valid['report'];
        $dateRange = $valid['dateRange'];
        $dateFrom  = $valid['dateFrom'] ?? null;
        $dateTo    = $valid['dateTo']   ?? null;

        [$from, $to] = $this->svc->dateRange($dateRange, $dateFrom, $dateTo);

        [$headers, $rows] = $this->getData($report, $tenantId, $from, $to, $request);

        // M-I18N: titles must support locale switching; translator first with English fallback.
        $reportLabels = [
            'lead-volume'        => __('reports_export.titles.lead_volume'),
            'source-performance' => __('reports_export.titles.source_performance'),
            'pipeline-funnel'    => __('reports_export.titles.pipeline_funnel'),
            'agent-performance'  => __('reports_export.titles.agent_performance'),
            'automation-stats'   => __('reports_export.titles.automation_stats'),
            'form-analytics'     => __('reports_export.titles.form_analytics'),
            'response-time'      => __('reports_export.titles.response_time'),
        ];

        $activeFilters = $this->describeFilters($request, $from, $to);
        $chartSvg      = $this->buildChartSvg($report, $tenantId, $from, $to, $request);

        $tenant    = Tenant::find($tenantId);
        $brandName = $tenant?->getAppName() ?? config('leadhub.branding.app_name', 'LeadHub');
        $brandColor = $tenant?->getPrimaryColor() ?? config('leadhub.branding.primary_color', '#4f46e5');
        $logoUrl   = $tenant?->getBranding('logo_url') ?? config('leadhub.branding.logo_url');

        // Translator-first fallback so an unknown report id (which should be impossible after
        // validateInputs() but kept as a defensive fallback) doesn't leak an untranslated string.
        $fallbackTitle = ucfirst(str_replace('-', ' ', $report));
        $translatedFallback = __('reports_export.titles.' . str_replace('-', '_', $report));
        $defaultTitle = is_string($translatedFallback)
            && $translatedFallback !== 'reports_export.titles.' . str_replace('-', '_', $report)
            ? $translatedFallback
            : $fallbackTitle;

        $pdf = Pdf::loadView('exports.pdf-report', [
            'reportTitle'   => $reportLabels[$report] ?? $defaultTitle,
            'dateFrom'      => $from->translatedFormat('M j, Y'),
            'dateTo'        => $to->translatedFormat('M j, Y'),
            'headers'       => $headers,
            'rows'          => $rows,
            'brandName'     => $brandName,
            'brandColor'    => $brandColor,
            'logoUrl'       => $logoUrl,
            'exportedAt'    => now()->translatedFormat('M j, Y H:i'),
            'activeFilters' => $activeFilters,
            'chartSvg'      => $chartSvg,
        ])->setPaper('a4', 'landscape');

        $filename = $report . '-' . now()->format('Y-m-d') . '.pdf';

        return $pdf->download($filename);
    }

    private function getData(string $report, int $tenantId, Carbon $from, Carbon $to, Request $request): array
    {
        return match ($report) {
            'lead-volume'        => $this->leadVolumeData($tenantId, $from, $to, $request),
            'source-performance' => $this->sourceData($tenantId, $from, $to),
            'pipeline-funnel'    => $this->funnelData($tenantId, $request->input('pipeline_id')),
            'agent-performance'  => $this->agentData($tenantId, $from, $to),
            'automation-stats'   => $this->automationData($tenantId, $from, $to),
            'form-analytics'     => $this->formData($tenantId, $from, $to),
            'response-time'      => $this->responseData($tenantId, $from, $to, $request),
            default              => [[], []],
        };
    }

    /**
     * Strict request validation for both csv() and pdf().
     *
     * Threat model: the export endpoints accept enums + dates straight
     * from query strings.  Without validation, a crafted `?dateRange=`
     * silently falls through to ReportService::dateRange()'s 30-day
     * default, and a malformed `?dateFrom=` would crash Carbon::parse()
     * inside the `custom` branch with an unfiltered exception in the
     * stack trace.  The whitelisted enums + `date_format:Y-m-d`
     * checks make those failure modes impossible — every bad input
     * returns a 422 before any service call runs.
     *
     * @return array<string, mixed>
     */
    private function validateInputs(Request $request): array
    {
        return $request->validate([
            'report'      => ['required', 'string', 'in:' . implode(',', self::ALLOWED_REPORTS)],
            'dateRange'   => ['required', 'string', 'in:' . implode(',', self::ALLOWED_RANGES)],
            'dateFrom'    => ['nullable', 'date_format:Y-m-d', 'required_if:dateRange,custom'],
            'dateTo'      => ['nullable', 'date_format:Y-m-d', 'required_if:dateRange,custom', 'after_or_equal:dateFrom'],
            'source'      => ['nullable', 'string', 'max:100'],
            'userId'      => ['nullable', 'integer', 'min:1'],
            'groupBy'     => ['nullable', 'in:day,week,month'],
            'pipeline_id' => ['nullable', 'integer', 'min:1'],
        ]);
    }

    private function describeFilters(Request $request, Carbon $from, Carbon $to): array
    {
        // M-I18N: filter keys are rendered in the PDF header — must respect tenant locale.
        // translatedFormat() localizes month abbreviations ('M' token) per current locale.
        $filters = [
            (string) __('reports_export.filter_keys.date_range') => $from->translatedFormat('M j, Y') . ' – ' . $to->translatedFormat('M j, Y'),
        ];
        if ($request->input('source')) {
            $filters[(string) __('reports_export.filter_keys.source')] = $request->input('source');
        }
        if ($request->input('userId')) {
            $filters[(string) __('reports_export.filter_keys.agent_id')] = $request->input('userId');
        }
        if ($request->input('groupBy')) {
            // Translator-first value: map day|week|month to the localised label
            // so the PDF filter table never leaks raw English in a tenant's locale.
            // ucfirst() remains as the safety net for unknown future buckets.
            $groupByValue       = (string) $request->input('groupBy');
            $groupByKey         = 'reports_export.group_by_values.' . $groupByValue;
            $groupByTranslated  = __($groupByKey);
            $groupByLabel       = is_string($groupByTranslated) && $groupByTranslated !== $groupByKey
                ? $groupByTranslated
                : ucfirst($groupByValue);
            $filters[(string) __('reports_export.filter_keys.group_by')] = $groupByLabel;
        }
        if ($request->input('pipeline_id')) {
            $filters[(string) __('reports_export.filter_keys.pipeline_id')] = $request->input('pipeline_id');
        }
        return $filters;
    }

    /**
     * Build an SVG chart for embedding in PDF — no external service calls.
     */
    private function buildChartSvg(string $report, int $tenantId, Carbon $from, Carbon $to, Request $request): ?string
    {
        try {
            $renderer = app(SvgChartRenderer::class);

            return match ($report) {
                'lead-volume' => (function () use ($renderer, $tenantId, $from, $to, $request): string {
                    $groupBy = $request->input('groupBy', 'day');
                    $source  = $request->input('source') ?: null;
                    $result  = $this->svc->leadsOverTime($tenantId, $from, $to, $groupBy, $source);
                    return $renderer->lineChart($result['labels'], $result['data']);
                })(),

                'source-performance' => (function () use ($renderer, $tenantId, $from, $to): string {
                    $rows = $this->svc->sourcePerformance($tenantId, $from, $to);
                    return $renderer->barChart(array_column($rows, 'source'), array_column($rows, 'total_leads'), (string) __('reports_export.chart_axes.leads'));
                })(),

                'automation-stats' => (function () use ($renderer, $tenantId, $from, $to): string {
                    $rows = $this->svc->automationStats($tenantId, $from, $to);
                    return $renderer->groupedBarChart(
                        array_column($rows, 'name'),
                        [
                            ['label' => (string) __('reports_export.chart_axes.total_runs'),   'data' => array_column($rows, 'total_runs'),   'color' => '#6366f1'],
                            ['label' => (string) __('reports_export.chart_axes.success_runs'), 'data' => array_column($rows, 'success_runs'), 'color' => '#10b981'],
                        ]
                    );
                })(),

                'response-time' => (function () use ($renderer, $tenantId, $from, $to, $request): string {
                    $result = $this->svc->responseTimeDistribution(
                        $tenantId, $from, $to,
                        $request->input('source') ?: null,
                        $request->input('userId') ? (int) $request->input('userId') : null
                    );
                    // Chart x-axis routed through bucket_labels so non-English exports render localised bucket labels.
                    $chartLabels = array_values($result['bucket_labels'] ?? array_keys($result['buckets']));
                    return $renderer->barChart($chartLabels, array_values($result['buckets']), (string) __('reports_export.chart_axes.leads'));
                })(),

                'form-analytics' => (function () use ($renderer, $tenantId, $from, $to): string {
                    $result = $this->svc->formSubmissionTrend($tenantId, $from, $to);
                    return $renderer->barChart($result['labels'], $result['data'], (string) __('reports_export.chart_axes.submissions'));
                })(),

                'pipeline-funnel' => (function () use ($renderer, $tenantId, $request): string {
                    $rows = $this->svc->pipelineFunnel($tenantId, $request->input('pipeline_id') ? (int)$request->input('pipeline_id') : null);
                    return $renderer->barChart(array_column($rows, 'stage'), array_column($rows, 'count'), (string) __('reports_export.chart_axes.leads'));
                })(),

                'agent-performance' => (function () use ($renderer, $tenantId, $from, $to): string {
                    $rows = $this->svc->agentPerformance($tenantId, $from, $to);
                    return $renderer->barChart(array_column($rows, 'agent'), array_column($rows, 'assigned'), (string) __('reports_export.chart_axes.assigned_leads'));
                })(),

                default => null,
            };
        } catch (\Throwable) {
            return null;
        }
    }

    private function leadVolumeData(int $tid, Carbon $from, Carbon $to, Request $request): array
    {
        $groupBy = $request->input('groupBy', 'day');
        $source  = $request->input('source') ?: null;
        $result  = $this->svc->leadsOverTime($tid, $from, $to, $groupBy, $source);
        $headers = [
            (string) __('reports_export.columns.period'),
            (string) __('reports_export.columns.lead_count'),
        ];
        $rows    = array_map(fn($l, $d) => [$l, $d], $result['labels'], $result['data']);
        return [$headers, $rows];
    }

    private function sourceData(int $tid, Carbon $from, Carbon $to): array
    {
        $data    = $this->svc->sourcePerformance($tid, $from, $to);
        $headers = [
            (string) __('reports_export.columns.source'),
            (string) __('reports_export.columns.total_leads'),
            (string) __('reports_export.columns.vs_prev_period_pct'),
            (string) __('reports_export.columns.converted'),
            (string) __('reports_export.columns.conversion_rate_pct'),
            (string) __('reports_export.columns.avg_score'),
        ];
        $rows    = array_map(fn($r) => [
            $r['source'],
            $r['total_leads'],
            $r['trend_pct'] !== null ? ($r['trend_pct'] >= 0 ? '+' : '') . $r['trend_pct'] . '%' : '—',
            $r['converted'],
            $r['conversion_rate'],
            $r['avg_score'],
        ], $data);
        return [$headers, $rows];
    }

    private function funnelData(int $tid, ?string $pipelineId): array
    {
        $data    = $this->svc->pipelineFunnel($tid, $pipelineId ? (int) $pipelineId : null);
        $headers = [
            (string) __('reports_export.columns.stage'),
            (string) __('reports_export.columns.lead_count'),
            (string) __('reports_export.columns.drop_off_pct'),
            (string) __('reports_export.columns.avg_days_in_stage'),
        ];
        $rows    = array_map(fn($r) => [$r['stage'], $r['count'], $r['drop_off'] ?? '—', $r['avg_days']], $data);
        return [$headers, $rows];
    }

    private function agentData(int $tid, Carbon $from, Carbon $to): array
    {
        $data    = $this->svc->agentPerformance($tid, $from, $to);
        $headers = [
            (string) __('reports_export.columns.agent'),
            (string) __('reports_export.columns.assigned_leads'),
            (string) __('reports_export.columns.won'),
            (string) __('reports_export.columns.win_rate_pct'),
            (string) __('reports_export.columns.avg_response_min'),
            (string) __('reports_export.columns.avg_close_days'),
            (string) __('reports_export.columns.activities'),
        ];
        $rows    = array_map(fn($r) => [
            $r['agent'],
            $r['assigned'],
            $r['won'],
            $r['win_rate'],
            $r['avg_response_min'] > 0 ? $r['avg_response_min'] : '—',
            $r['avg_close_days'] > 0 ? $r['avg_close_days'] : '—',
            $r['activities'],
        ], $data);
        return [$headers, $rows];
    }

    private function automationData(int $tid, Carbon $from, Carbon $to): array
    {
        $data    = $this->svc->automationStats($tid, $from, $to);
        $headers = [
            (string) __('reports_export.columns.automation'),
            (string) __('reports_export.columns.trigger'),
            (string) __('reports_export.columns.total_runs'),
            (string) __('reports_export.columns.success_runs'),
            (string) __('reports_export.columns.success_rate_pct'),
            (string) __('reports_export.columns.avg_run_time_s'),
            (string) __('reports_export.columns.enabled'),
        ];
        $yes = (string) __('reports_export.cells.yes');
        $no  = (string) __('reports_export.cells.no');
        $rows    = array_map(fn($r) => [
            $r['name'],
            $r['trigger_type'] ?? '—',
            $r['total_runs'],
            $r['success_runs'],
            $r['success_rate'],
            $r['avg_run_seconds'] > 0 ? $r['avg_run_seconds'] : '—',
            $r['enabled'] ? $yes : $no,
        ], $data);
        return [$headers, $rows];
    }

    private function formData(int $tid, Carbon $from, Carbon $to): array
    {
        $data    = $this->svc->formAnalytics($tid, $from, $to);
        $headers = [
            (string) __('reports_export.columns.form_name'),
            (string) __('reports_export.columns.submissions'),
            (string) __('reports_export.columns.active'),
        ];
        $yes = (string) __('reports_export.cells.yes');
        $no  = (string) __('reports_export.cells.no');
        $rows    = array_map(fn($r) => [$r['name'], $r['submissions'], $r['active'] ? $yes : $no], $data);
        return [$headers, $rows];
    }

    private function responseData(int $tid, Carbon $from, Carbon $to, Request $request): array
    {
        $result  = $this->svc->responseTimeDistribution(
            $tid, $from, $to,
            $request->input('source') ?: null,
            $request->input('userId') ? (int) $request->input('userId') : null
        );
        $headers = [
            (string) __('reports_export.columns.bucket'),
            (string) __('reports_export.columns.lead_count'),
            (string) __('reports_export.columns.pct_of_total'),
        ];
        $total   = $result['total'];
        $labels  = $result['bucket_labels'] ?? [];
        // CSV/PDF bucket cell routed through bucket_labels so the exported
        // file carries tenant-locale labels instead of canonical slugs.
        $rows    = array_map(fn($bucket, $count) => [
            $labels[$bucket] ?? $bucket,
            $count,
            $total > 0 ? round($count / $total * 100, 1) . '%' : '0%',
        ], array_keys($result['buckets']), $result['buckets']);

        $minSuffix = (string) __('reports_export.summary.minutes_suffix');
        $rows[] = ['', '', ''];
        $rows[] = [(string) __('reports_export.summary.median_response'), $result['median'] . $minSuffix, ''];
        $rows[] = [(string) __('reports_export.summary.p90_response'),    $result['p90'] . $minSuffix,    ''];
        $rows[] = [(string) __('reports_export.summary.total_analysed'),  $total,                          ''];

        return [$headers, $rows];
    }
}
