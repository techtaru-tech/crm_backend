<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

/**
 * H7: collapse the divergent lead-status vocabulary.
 *
 * Before this migration the leads.status column had at least three
 * different values mixed together depending on which surface wrote
 * the row:
 *
 *   - Filament LeadResource form  → 'new', 'contacted', 'qualified',
 *                                    'converted', 'lost'
 *   - ReportService               → expects 'won' / 'lost' to
 *                                    classify revenue
 *   - SampleDataGenerator         → emitted 'proposal' (which never
 *                                    appeared in the UI dropdown)
 *
 * The user-visible bug: a tenant marks a lead "Converted" via the
 * form, and the won-revenue chart silently undercounts because the
 * aggregation filter is `where('status', 'won')`.  Coerce existing
 * rows to the canonical vocabulary; the new App\Enums\LeadStatus +
 * model cast prevent regression.
 */
return new class extends Migration {
    public function up(): void
    {
        // Map legacy 'converted' → 'won' (UI used both interchangeably,
        // ReportService only counts 'won').
        DB::table('leads')->where('status', 'converted')->update(['status' => 'won']);

        // 'proposal' was a SampleDataGenerator-only artifact that
        // never appeared in the UI options.  Coerce to 'qualified'
        // (closest live-pipeline equivalent).
        DB::table('leads')->where('status', 'proposal')->update(['status' => 'qualified']);
    }

    public function down(): void
    {
        // Best-effort reversal — original vocabulary cannot be
        // recovered from a 'won' row (could have been 'won' OR
        // 'converted' originally).  Leave rows alone; the down
        // migration is informational.
    }
};
