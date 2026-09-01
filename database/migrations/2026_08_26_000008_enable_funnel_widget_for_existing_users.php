<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

/**
 * Show the new funnel tiles to users who already saved a dashboard layout.
 *
 * user_dashboard_preferences.enabled_widgets is an ALLOW-list: Dashboard::
 * getWidgets() keeps only the widgets named in it.  That means a widget
 * shipped after a user last touched their dashboard is invisible to them
 * forever, even though they never chose to hide it — they simply could not
 * have ticked something that did not exist.
 *
 * FunnelFollowUpOverview is required by the Phase 1 spec (§11), so append it
 * to every existing non-empty list.  Users can still switch it off afterwards;
 * rows with an empty list already mean "show everything" and are left alone.
 *
 * Idempotent — re-running skips lists that already contain the widget.
 */
return new class extends Migration {
    private const WIDGET = 'FunnelFollowUpOverview';

    public function up(): void
    {
        DB::table('user_dashboard_preferences')
            ->select('id', 'enabled_widgets')
            ->orderBy('id')
            ->chunk(200, function ($rows) {
                foreach ($rows as $row) {
                    $widgets = json_decode((string) $row->enabled_widgets, true);

                    if (! is_array($widgets) || $widgets === [] || in_array(self::WIDGET, $widgets, true)) {
                        continue;
                    }

                    // Slot it right after the main stats block so the funnel
                    // tiles read as a continuation of it rather than landing
                    // at the bottom of the page.
                    $position = array_search('LeadsStatsOverview', $widgets, true);
                    $position = $position === false ? 0 : $position + 1;

                    array_splice($widgets, $position, 0, [self::WIDGET]);

                    DB::table('user_dashboard_preferences')
                        ->where('id', $row->id)
                        ->update(['enabled_widgets' => json_encode(array_values($widgets))]);
                }
            });
    }

    public function down(): void
    {
        DB::table('user_dashboard_preferences')
            ->select('id', 'enabled_widgets')
            ->orderBy('id')
            ->chunk(200, function ($rows) {
                foreach ($rows as $row) {
                    $widgets = json_decode((string) $row->enabled_widgets, true);
                    if (! is_array($widgets) || ! in_array(self::WIDGET, $widgets, true)) {
                        continue;
                    }
                    DB::table('user_dashboard_preferences')
                        ->where('id', $row->id)
                        ->update(['enabled_widgets' => json_encode(
                            array_values(array_diff($widgets, [self::WIDGET]))
                        )]);
                }
            });
    }
};
