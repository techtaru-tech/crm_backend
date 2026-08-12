<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Support\Facades\Schema;

class UserDashboardPreference extends Model
{
    protected $fillable = [
        'user_id',
        'enabled_widgets',
        'widget_order',
    ];

    protected $casts = [
        'enabled_widgets' => 'array',
        'widget_order'    => 'array',
    ];

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    /**
     * Find or create a preference row for the given user, seeded with
     * role-based defaults.
     *
     * Defensive: if the table hasn't been migrated yet (e.g. an upgrade
     * install that didn't run `php artisan migrate` after pulling the
     * 2026_04_17_000033 migration), return an unsaved instance with the
     * defaults instead of crashing the panel. Callers only read
     * `enabled_widgets` off the result.
     */
    public static function getForUser(User $user): self
    {
        if (! Schema::hasTable('user_dashboard_preferences')) {
            return (new self())->forceFill([
                'user_id'         => $user->id,
                'enabled_widgets' => self::defaultsForUser($user),
                'widget_order'    => null,
            ]);
        }

        try {
            $pref = self::where('user_id', $user->id)->first();
            if ($pref) {
                return $pref;
            }

            return self::create([
                'user_id'         => $user->id,
                'enabled_widgets' => self::defaultsForUser($user),
            ]);
        } catch (\Throwable) {
            // Belt-and-suspenders: if something else goes wrong (e.g. a
            // broken column), still hand back a sane in-memory default
            // so the dashboard renders instead of 500-ing.
            return (new self())->forceFill([
                'user_id'         => $user->id,
                'enabled_widgets' => self::defaultsForUser($user),
                'widget_order'    => null,
            ]);
        }
    }

    /**
     * @return array<int,string>
     */
    public static function defaultsForUser(User $user): array
    {
        if ($user->isSuperAdmin()) {
            return self::availableWidgets();
        }

        // Agents get a minimal dashboard; everyone else (managers/admins) sees the full set.
        if (method_exists($user, 'hasRole') && $user->hasRole('agent')) {
            return ['LeadsStatsOverview', 'LiveLeadFeed'];
        }

        return [
            'LeadsStatsOverview',
            'RevenueForecastWidget',
            'LeadsOverTimeChart',
            'LeadStatusChart',
            'LeadsBySourceChart',
            'PipelineDistributionChart',
            'LiveLeadFeed',
        ];
    }

    /**
     * Canonical list of widgets available in the admin dashboard.
     * Each entry is the class basename.
     *
     * @return array<int,string>
     */
    public static function availableWidgets(): array
    {
        return [
            'LeadsStatsOverview',
            'LeadsOverTimeChart',
            'LeadsBySourceChart',
            'LeadStatusChart',
            'PipelineDistributionChart',
            'RevenueForecastWidget',
            'LiveLeadFeed',
        ];
    }

    /**
     * Human-readable labels for widgets shown on the preferences page.
     *
     * @return array<string,string>
     */
    public static function widgetLabels(): array
    {
        return [
            'LeadsStatsOverview'        => 'Leads Stats Overview',
            'LeadsOverTimeChart'        => 'Leads Over Time',
            'LeadsBySourceChart'        => 'Leads by Source',
            'LeadStatusChart'           => 'Lead Status Distribution',
            'PipelineDistributionChart' => 'Pipeline Distribution',
            'RevenueForecastWidget'     => 'Revenue Forecast',
            'LiveLeadFeed'              => 'Live Lead Feed',
        ];
    }
}
