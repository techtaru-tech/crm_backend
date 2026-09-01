<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use App\Models\Tenant;
use App\Models\Concerns\BelongsToTenant;

class LeadTask extends Model
{
    use BelongsToTenant;

    public const PRIORITY_LOW    = 'low';
    public const PRIORITY_NORMAL = 'normal';
    public const PRIORITY_HIGH   = 'high';
    public const PRIORITY_URGENT = 'urgent';

    public const PRIORITIES = [
        self::PRIORITY_LOW    => 'Low',
        self::PRIORITY_NORMAL => 'Normal',
        self::PRIORITY_HIGH   => 'High',
        self::PRIORITY_URGENT => 'Urgent',
    ];

    /**
     * Translated priority labels for Filament Select / display.
     * Keys mirror self::PRIORITIES.
     */
    public static function priorityLabels(): array
    {
        return [
            self::PRIORITY_LOW    => __('models/lead_task.priority_low'),
            self::PRIORITY_NORMAL => __('models/lead_task.priority_normal'),
            self::PRIORITY_HIGH   => __('models/lead_task.priority_high'),
            self::PRIORITY_URGENT => __('models/lead_task.priority_urgent'),
        ];
    }

    protected $fillable = [
        'lead_id', 'tenant_id', 'assigned_user_id',
        'title', 'description', 'due_at', 'completed', 'completed_at',
        'reminder_at', 'reminder_sent_at', 'priority', 'status',
    ];

    protected $casts = [
        'status'           => \App\Enums\FollowUpStatus::class,
        'completed'        => 'boolean',
        'due_at'           => 'datetime',
        'completed_at'     => 'datetime',
        'reminder_at'      => 'datetime',
        'reminder_sent_at' => 'datetime',
    ];

    protected static function booted(): void
    {
        static::creating(function (LeadTask $task) {
            if (empty($task->priority)) {
                $task->priority = self::PRIORITY_NORMAL;
            }

            if (empty($task->status)) {
                $task->status = \App\Enums\FollowUpStatus::Pending;
            }

            if ($task->completed === null) {
                $task->completed = false;
            }

            if (empty($task->reminder_at) && ! empty($task->due_at)) {
                $dueAt = $task->due_at instanceof \DateTimeInterface
                    ? \Carbon\Carbon::instance($task->due_at)
                    : \Carbon\Carbon::parse($task->due_at);
                $task->reminder_at = $dueAt->copy()->subHour();
            }
        });

        // `completed` (boolean) and `status` (enum) describe the same thing at
        // different resolutions.  Plenty of existing code still reads the
        // boolean, so rather than migrate every call site at once, whichever
        // one a caller sets drags the other along.
        static::saving(function (LeadTask $task) {
            if ($task->isDirty('status')) {
                $isDone = $task->status === \App\Enums\FollowUpStatus::Completed;
                $task->completed = $isDone;
                if ($isDone && empty($task->completed_at)) {
                    $task->completed_at = now();
                }
            } elseif ($task->isDirty('completed')) {
                $task->status = $task->completed
                    ? \App\Enums\FollowUpStatus::Completed
                    : \App\Enums\FollowUpStatus::Pending;
            }
        });

        // The lead carries a denormalised next_follow_up_at so the lead list
        // can sort on it; any write that could change the earliest open
        // follow-up re-derives it.
        // Resolve the lead without global scopes: this fires from queue
        // workers and console commands too, where there is no authenticated
        // user and BelongsToTenant deliberately fails closed — $task->lead
        // would come back null and the column would silently drift.
        $refreshLead = function (LeadTask $task): void {
            Lead::withoutGlobalScopes()
                ->find($task->lead_id)
                ?->refreshNextFollowUp();
        };

        static::saved($refreshLead);
        static::deleted($refreshLead);
    }

    /** Follow-ups still awaiting action. */
    public function scopeOpen(Builder $query): Builder
    {
        return $query->whereIn('status', \App\Enums\FollowUpStatus::openValues());
    }

    /** Open follow-ups whose due date has passed. */
    public function scopeOverdue(Builder $query): Builder
    {
        return $query->open()->whereNotNull('due_at')->where('due_at', '<', now());
    }

    /** Open follow-ups due today. */
    public function scopeDueToday(Builder $query): Builder
    {
        return $query->open()->whereBetween('due_at', [now()->startOfDay(), now()->endOfDay()]);
    }

    public function lead(): BelongsTo
    {
        return $this->belongsTo(Lead::class);
    }

    public function tenant(): BelongsTo
    {
        return $this->belongsTo(Tenant::class);
    }

    public function assignedUser(): BelongsTo
    {
        return $this->belongsTo(User::class, 'assigned_user_id');
    }

    public function scopeDueForReminder(Builder $query): Builder
    {
        return $query
            ->whereNotNull('reminder_at')
            ->where('reminder_at', '<=', now())
            ->whereNull('reminder_sent_at')
            ->where('completed', false);
    }
}
