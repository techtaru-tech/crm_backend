<?php

declare(strict_types=1);

namespace App\Models;

use App\Models\Concerns\BelongsToTenant;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

/**
 * An autonomous "AI SDR" agent definition.
 *
 * One row = one configured follow-up persona a rep can enroll leads
 * into. The cron runner (ProcessAiSdrEnrollments) makes at most one AI
 * decision per due enrollment per tick and performs at most one action.
 *
 * MVP is EMAIL-ONLY and goal=qualify; the `channel` / `goal` columns are
 * strings so v1 can extend the vocabulary without a migration. Every
 * guardrail (max_touches, business hours, opt-out, human-reply pause) is
 * enforced in code, not just the prompt.
 */
class AiSdrAgent extends Model
{
    use BelongsToTenant;
    use HasFactory;

    protected $table = 'ai_sdr_agents';

    public const GOAL_QUALIFY = 'qualify';
    public const GOAL_BOOK    = 'book';

    public const CHANNEL_EMAIL    = 'email';
    public const CHANNEL_SMS      = 'sms';
    public const CHANNEL_WHATSAPP = 'whatsapp';

    /** Phone-based channels that send via SendLeadMessage, not email. */
    public const MESSAGING_CHANNELS = [self::CHANNEL_SMS, self::CHANNEL_WHATSAPP];

    /** Every channel an agent may be configured to use. */
    public const CHANNELS = [self::CHANNEL_EMAIL, self::CHANNEL_SMS, self::CHANNEL_WHATSAPP];

    protected $fillable = [
        'tenant_id',
        'name',
        'goal',
        'active',
        'channel',
        'persona',
        'offer_context',
        'max_touches',
        'min_hours_between_touches',
        'respect_business_hours',
        'assign_to_user_id',
        'meeting_type_id',
    ];

    protected $casts = [
        'active'                    => 'boolean',
        'respect_business_hours'    => 'boolean',
        'max_touches'               => 'integer',
        'min_hours_between_touches' => 'integer',
    ];

    public function enrollments(): HasMany
    {
        return $this->hasMany(AiSdrEnrollment::class, 'agent_id');
    }

    public function assignee(): BelongsTo
    {
        return $this->belongsTo(User::class, 'assign_to_user_id');
    }

    public function meetingType(): BelongsTo
    {
        return $this->belongsTo(MeetingType::class);
    }

    /** True when this agent reaches leads over a phone channel (SMS/WhatsApp). */
    public function usesMessagingChannel(): bool
    {
        return in_array($this->channel, self::MESSAGING_CHANNELS, true);
    }

    /**
     * Can this agent reach the given lead on its configured channel? Email
     * needs an address; SMS/WhatsApp need a phone number.
     */
    public function canReach(Lead $lead): bool
    {
        if ($this->usesMessagingChannel()) {
            return ! empty($lead->phone_normalized) || ! empty($lead->phone);
        }

        return ! empty($lead->email);
    }

    /** True when this agent should steer the lead toward booking a meeting. */
    public function wantsBooking(): bool
    {
        return $this->goal === self::GOAL_BOOK && $this->meeting_type_id !== null;
    }
}
