<?php

namespace App\Models;

use App\Models\Concerns\BelongsToTenant;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Support\Str;

class LeadEmail extends Model
{
    use BelongsToTenant;

    protected $fillable = [
        'tenant_id',
        'lead_id',
        'user_id',
        'direction',
        'ai_sdr',
        'ai_sdr_enrollment_id',
        'message_id',
        'in_reply_to',
        'subject',
        'body_html',
        'body_text',
        'from_address',
        'from_name',
        'to_addresses',
        'cc_addresses',
        'bcc_addresses',
        'attachments',
        'sent_at',
        'opened_at',
        'clicked_at',
        'bounced_at',
        'bounce_reason',
        'tracking_token',
    ];

    protected $casts = [
        'ai_sdr'        => 'boolean',
        'to_addresses'  => 'array',
        'cc_addresses'  => 'array',
        'bcc_addresses' => 'array',
        'attachments'   => 'array',
        'sent_at'       => 'datetime',
        'opened_at'     => 'datetime',
        'clicked_at'    => 'datetime',
        'bounced_at'    => 'datetime',
    ];

    protected static function booted(): void
    {
        static::creating(function (self $email) {
            if ($email->direction === 'outbound' && empty($email->tracking_token)) {
                $email->tracking_token = Str::random(32);
            }
        });

        static::created(function (self $email) {
            // When an inbound email lands, unenroll the lead from any active sequences
            // whose stop_on_reply flag is true.
            if ($email->direction !== 'inbound') {
                return;
            }

            $enrollments = EmailSequenceEnrollment::where('lead_id', $email->lead_id)
                ->where('tenant_id', $email->tenant_id)
                ->where('status', 'active')
                ->with('sequence')
                ->get();

            foreach ($enrollments as $enrollment) {
                if ($enrollment->sequence && $enrollment->sequence->stop_on_reply) {
                    // i18n: translate at write time so the persisted
                    // reason matches the active locale.  See .
                    $enrollment->update([
                        'status'          => 'replied',
                        'unenroll_reason' => __('filament/leads.unenroll_reason_replied'),
                    ]);
                }
            }

            // AI SDR: a human (the lead) replying by email HARD-PAUSES
            // every live enrollment for this lead immediately, regardless
            // of cadence. The agent never auto-resumes from paused_human —
            // a rep decides what happens next. This intentionally ignores
            // the agent's OWN outbound sends (direction='outbound'),
            // because only inbound rows reach this branch.
            \App\Models\AiSdrEnrollment::where('lead_id', $email->lead_id)
                ->where('tenant_id', $email->tenant_id)
                ->whereIn('state', \App\Models\AiSdrEnrollment::LIVE_STATES)
                ->update([
                    'state'         => \App\Models\AiSdrEnrollment::STATE_PAUSED_HUMAN,
                    'paused_reason' => 'lead_replied_email',
                    'next_action_at' => null,
                ]);
        });
    }

    public function lead(): BelongsTo
    {
        return $this->belongsTo(Lead::class);
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }
}
