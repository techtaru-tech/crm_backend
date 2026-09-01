<?php

namespace App\Filament\Widgets;

use App\Enums\FollowUpStatus;
use App\Models\Lead;
use App\Models\LeadTask;
use Filament\Widgets\StatsOverviewWidget;
use Filament\Widgets\StatsOverviewWidget\Stat;

/**
 * The four funnel tiles the Phase 1 spec (§11) asked for that the dashboard
 * did not already have: unassigned leads, today's follow-ups, overdue
 * follow-ups, and the per-rep split.
 *
 * Every count runs through the normal Eloquent scopes, so a rep sees their
 * own numbers and a team manager sees their teams' — the tiles agree with
 * whatever that user sees in the lead list rather than quoting a
 * workspace-wide figure they cannot drill into.
 */
class FunnelFollowUpOverview extends StatsOverviewWidget
{
    protected static ?int $sort = 2;

    protected function getColumns(): int
    {
        return 4;
    }

    protected function getStats(): array
    {
        if (! auth()->user()?->tenant_id) {
            return [];
        }

        $unassigned = Lead::whereNull('assigned_user_id')->count();

        $dueToday = LeadTask::query()
            ->whereIn('status', FollowUpStatus::openValues())
            ->whereBetween('due_at', [now()->startOfDay(), now()->endOfDay()])
            ->count();

        $overdue = LeadTask::query()
            ->whereIn('status', FollowUpStatus::openValues())
            ->whereNotNull('due_at')
            ->where('due_at', '<', now())
            ->count();

        // Top owners by open lead count.  Rendered as the tile's description
        // so one tile carries the whole "leads by assigned user" breakdown
        // instead of spawning a tile per rep.
        $byUser = Lead::query()
            ->selectRaw('assigned_user_id, count(*) as aggregate')
            ->whereNotNull('assigned_user_id')
            ->groupBy('assigned_user_id')
            ->orderByDesc('aggregate')
            ->with('assignedUser:id,name')
            ->limit(4)
            ->get();

        $byUserLine = $byUser
            ->map(fn ($row) => ($row->assignedUser?->name ?? '—') . ': ' . $row->aggregate)
            ->implode(' · ');

        return [
            Stat::make(__('filament/widget_funnel.unassigned'), (string) $unassigned)
                ->description(__('filament/widget_funnel.unassigned_help'))
                ->descriptionIcon('heroicon-m-user-minus')
                ->color($unassigned > 0 ? 'warning' : 'success'),

            Stat::make(__('filament/widget_funnel.due_today'), (string) $dueToday)
                ->description(__('filament/widget_funnel.due_today_help'))
                ->descriptionIcon('heroicon-m-calendar-days')
                ->color($dueToday > 0 ? 'info' : 'gray'),

            Stat::make(__('filament/widget_funnel.overdue'), (string) $overdue)
                ->description(__('filament/widget_funnel.overdue_help'))
                ->descriptionIcon('heroicon-m-exclamation-triangle')
                ->color($overdue > 0 ? 'danger' : 'success'),

            Stat::make(__('filament/widget_funnel.by_user'), (string) $byUser->sum('aggregate'))
                ->description($byUserLine !== '' ? $byUserLine : __('filament/widget_funnel.by_user_empty'))
                ->descriptionIcon('heroicon-m-users')
                ->color('gray'),
        ];
    }
}
