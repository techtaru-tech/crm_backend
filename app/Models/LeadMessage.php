<?php

namespace App\Models;

use App\Models\Concerns\BelongsToTenant;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class LeadMessage extends Model
{
    use BelongsToTenant;

    protected static function booted(): void
    {
        // Centralised human-reply pause: the instant ANY inbound (lead) or
        // rep-authored (outbound + user_id) message row is created — via the
        // Conversations panel, a messaging webhook, the chatbot, OR a future
        // channel connector that doesn't dispatch NewLeadMessage — hard-pause
        // every live AI SDR enrollment for the lead. Mirrors LeadEmail::booted
        // for the email channel. System/automation sends (outbound, user_id
        // null) are ignored, so the agent never pauses itself.
        static::created(static function (LeadMessage $message): void {
            \App\Listeners\PauseAiSdrOnHumanReply::pauseForMessage($message);
        });
    }

    protected $fillable = [
        'tenant_id',
        'lead_id',
        'user_id',
        'channel',
        'direction',
        'from_identifier',
        'to_identifier',
        'body',
        'media_url',
        'media_type',
        'status',
        'external_id',
        'error_message',
    ];

    public function lead(): BelongsTo
    {
        return $this->belongsTo(Lead::class);
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function tenant(): BelongsTo
    {
        return $this->belongsTo(Tenant::class);
    }

    public function scopeInbound(Builder $query): Builder
    {
        return $query->where('direction', 'inbound');
    }

    public function scopeOutbound(Builder $query): Builder
    {
        return $query->where('direction', 'outbound');
    }
}
