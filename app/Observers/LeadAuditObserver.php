<?php

declare(strict_types=1);

namespace App\Observers;

use App\Models\AuditLog;
use App\Models\Lead;

/**
 * Audit-log writer for Lead lifecycle events (M-A4).
 *
 * Pre-M-A4 these AuditLog::record() calls were inline in LeadObserver
 * alongside automations, integrations, broadcasting, scoring, and
 * activity-feed bookkeeping.  Splitting them out lets:
 *   - audit policy change without touching observer hot path
 *   - tests that just exercise audit behaviour use a focused fixture
 *   - PII/redaction concerns live next to the audit code itself
 *
 * Eloquent supports multiple observers per model — this one is
 * registered alongside LeadObserver in AppServiceProvider.  Order of
 * dispatch isn't significant here since AuditLog rows are append-only
 * and don't read mutable state from other observers.
 */
class LeadAuditObserver
{
    public function created(Lead $lead): void
    {
        AuditLog::record(
            'created',
            $lead,
            [],
            $lead->only(['first_name', 'last_name', 'email', 'source', 'status']),
        );
    }

    public function updated(Lead $lead): void
    {
        AuditLog::record('updated', $lead, $lead->getOriginal(), $lead->getDirty());
    }

    public function deleted(Lead $lead): void
    {
        AuditLog::record(
            'deleted',
            $lead,
            $lead->only(['id', 'email', 'first_name', 'last_name']),
            [],
        );
    }
}
