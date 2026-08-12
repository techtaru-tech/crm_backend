<?php

declare(strict_types=1);

namespace App\Models;

use App\Models\Concerns\BelongsToTenant;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

/**
 * A single lead's run through an AI SDR agent.
 *
 * State machine:
 *   active        — eligible for the next AI decision (cron picks these up)
 *   awaiting_reply— agent sent a touch, now waiting; the cadence delay
 *                   lives in next_action_at so it auto-returns to "active"
 *                   on the next due tick
 *   paused_human  — a human (lead reply OR rep reply) intervened; the
 *                   agent NEVER auto-resumes from here
 *   qualified     — agent decided the lead is qualified (transient → handoff)
 *   handed_off    — assigned to a human rep with a summary note (terminal)
 *   opted_out     — lead hit STOP/UNSUBSCRIBE or was opted out (terminal)
 *   completed     — max touches exhausted / agent chose stop (terminal)
 */
class AiSdrEnrollment extends Model
{
    use BelongsToTenant;

    protected $table = 'ai_sdr_enrollments';

    public const STATE_ACTIVE         = 'active';
    public const STATE_AWAITING_REPLY = 'awaiting_reply';
    public const STATE_PAUSED_HUMAN   = 'paused_human';
    public const STATE_QUALIFIED      = 'qualified';
    public const STATE_HANDED_OFF     = 'handed_off';
    public const STATE_OPTED_OUT      = 'opted_out';
    public const STATE_COMPLETED      = 'completed';

    /**
     * States from which the agent can still act. A human reply pauses the
     * agent regardless of which of these it is currently in.
     *
     * @var list<string>
     */
    public const LIVE_STATES = [
        self::STATE_ACTIVE,
        self::STATE_AWAITING_REPLY,
    ];

    /**
     * Terminal states — never touched again by the runner.
     *
     * @var list<string>
     */
    public const TERMINAL_STATES = [
        self::STATE_HANDED_OFF,
        self::STATE_OPTED_OUT,
        self::STATE_COMPLETED,
    ];

    protected $fillable = [
        'tenant_id',
        'agent_id',
        'lead_id',
        'state',
        'next_action_at',
        'touches_sent',
        'last_touch_at',
        'intent_score',
        'paused_reason',
        'handoff_summary',
        'enrolled_by_user_id',
        'enrolled_at',
        'completed_at',
    ];

    protected $casts = [
        'next_action_at' => 'datetime',
        'last_touch_at'  => 'datetime',
        'enrolled_at'    => 'datetime',
        'completed_at'   => 'datetime',
        'touches_sent'   => 'integer',
        'intent_score'   => 'integer',
    ];

    public function agent(): BelongsTo
    {
        return $this->belongsTo(AiSdrAgent::class, 'agent_id');
    }

    public function lead(): BelongsTo
    {
        return $this->belongsTo(Lead::class);
    }

    public function messages(): HasMany
    {
        return $this->hasMany(AiSdrMessage::class, 'enrollment_id')->latest();
    }

    /**
     * Due enrollments the cron runner should process this tick: any LIVE
     * state (active OR awaiting_reply) whose next_action_at has arrived.
     *
     * Including awaiting_reply is what makes the multi-touch cadence work —
     * after a send the row sits in awaiting_reply with next_action_at re-armed
     * to honour min_hours_between_touches, and this re-selects it once that
     * spacing has elapsed (mirrors the ProcessEmailSequences cursor). A human
     * reply moves the row to paused_human, which is NOT live and is therefore
     * never re-selected.
     */
    public function scopeDue(Builder $query): Builder
    {
        return $query
            ->whereIn('state', self::LIVE_STATES)
            ->whereNotNull('next_action_at')
            ->where('next_action_at', '<=', now());
    }

    public function isLive(): bool
    {
        return in_array($this->state, self::LIVE_STATES, true);
    }
}
