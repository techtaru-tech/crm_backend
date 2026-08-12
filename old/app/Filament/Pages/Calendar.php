<?php

namespace App\Filament\Pages;

use App\Filament\Resources\LeadResource;
use App\Models\LeadTask;
use App\Models\User;
use Carbon\Carbon;
use Filament\Pages\Page;
use Illuminate\Support\Facades\Auth;
use UnitEnum;

class Calendar extends Page
{
    public function getView(): string
    {
        return 'filament.pages.calendar';
    }

    protected static string|\BackedEnum|null $navigationIcon = 'heroicon-o-calendar-days';
    protected static string|UnitEnum|null $navigationGroup = 'Leads';
    protected static ?int $navigationSort = 5;

    public static function getNavigationLabel(): string
    {
        return __('filament/calendar.nav_label');
    }

    public string $rangePreset = 'month';
    public string $assignedFilter = '';
    public string $statusFilter = '';
    public string $rangeStart = '';
    public string $rangeEnd = '';

    public function mount(): void
    {
        $this->applyRangePreset();
    }

    public function applyRangePreset(): void
    {
        $now = Carbon::now();

        $this->rangeStart = match ($this->rangePreset) {
            'week'  => $now->copy()->startOfWeek()->toDateString(),
            'month' => $now->copy()->startOfMonth()->toDateString(),
            'next30' => $now->copy()->toDateString(),
            default => $now->copy()->startOfMonth()->toDateString(),
        };

        $this->rangeEnd = match ($this->rangePreset) {
            'week'  => $now->copy()->endOfWeek()->toDateString(),
            'month' => $now->copy()->endOfMonth()->toDateString(),
            'next30' => $now->copy()->addDays(30)->toDateString(),
            default => $now->copy()->endOfMonth()->toDateString(),
        };
    }

    public function updatedRangePreset(): void
    {
        $this->applyRangePreset();
        $this->dispatch('calendar-refresh');
    }

    public function updatedAssignedFilter(): void
    {
        $this->dispatch('calendar-refresh');
    }

    public function updatedStatusFilter(): void
    {
        $this->dispatch('calendar-refresh');
    }

    /**
     * Called from the Blade view when FullCalendar navigates dates.
     */
    public function updateDateRange(string $start, string $end): void
    {
        $this->rangeStart = $start;
        $this->rangeEnd = $end;
        $this->dispatch('calendar-refresh');
    }

    public function getEvents(): array
    {
        $tenantId = \App\Support\TenantContext::currentId();

        $tasks = LeadTask::query()
            ->where('tenant_id', $tenantId)
            ->with('lead')
            ->when($this->assignedFilter, fn ($q) => $q->where('assigned_user_id', $this->assignedFilter))
            ->when($this->statusFilter === 'completed', fn ($q) => $q->where('completed', true))
            ->when($this->statusFilter === 'pending', fn ($q) => $q->where('completed', false))
            ->when($this->statusFilter === 'overdue', fn ($q) => $q->where('completed', false)->where('due_at', '<', now()))
            ->when(
                $this->rangeStart && $this->rangeEnd,
                // Bound the range to inclusive end-of-day on rangeEnd so
                // tasks whose due_at is after midnight on the last day
                // still appear (the strict review flagged an off-by-one).
                fn ($q) => $q->whereBetween('due_at', [
                    Carbon::parse($this->rangeStart)->startOfDay(),
                    Carbon::parse($this->rangeEnd)->endOfDay(),
                ])
            )
            ->get();

        return $tasks->map(function (LeadTask $task) {
            $isOverdue = ! $task->completed && $task->due_at && $task->due_at->isPast();
            $priority  = $task->priority ?? LeadTask::PRIORITY_NORMAL;

            $color = match (true) {
                $task->completed => '#059669',       // green
                $isOverdue       => '#dc2626',       // red (overdue)
                $priority === LeadTask::PRIORITY_URGENT => '#dc2626', // red
                $priority === LeadTask::PRIORITY_HIGH   => '#ea580c', // orange
                $priority === LeadTask::PRIORITY_LOW    => '#6b7280', // gray
                default                                 => '#4f46e5', // indigo (normal)
            };

            $title = $task->title;
            if ($task->lead) {
                $title .= ' — ' . $task->lead->full_name;
            }

            return [
                'id'    => $task->id,
                'title' => $title,
                'start' => $task->due_at?->toIso8601String(),
                'url'   => $task->lead_id
                    ? LeadResource::getUrl('view', ['record' => $task->lead_id])
                    : null,
                'color' => $color,
            ];
        })->all();
    }

    protected function getViewData(): array
    {
        $tenantId = \App\Support\TenantContext::currentId();
        $users = User::where('tenant_id', $tenantId)->pluck('name', 'id');

        return [
            'events' => $this->getEvents(),
            'users'  => $users,
        ];
    }
}
