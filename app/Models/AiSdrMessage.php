<?php

declare(strict_types=1);

namespace App\Models;

use App\Models\Concerns\BelongsToTenant;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

/**
 * Immutable audit row for one AI SDR decision.
 *
 * The runner writes exactly one of these per enrollment per tick — every
 * decision (send / wait / qualify / handoff / stop), including the
 * deterministic fallback and fail-closed "wait", so the decision log is a
 * complete, self-contained trail (token usage + cost included).
 */
class AiSdrMessage extends Model
{
    use BelongsToTenant;

    protected $table = 'ai_sdr_messages';

    public const ACTION_SEND    = 'send';
    public const ACTION_WAIT    = 'wait';
    public const ACTION_QUALIFY = 'qualify';
    public const ACTION_HANDOFF = 'handoff';
    public const ACTION_STOP    = 'stop';

    protected $fillable = [
        'tenant_id',
        'enrollment_id',
        'lead_id',
        'action',
        'reason',
        'intent_score',
        'subject',
        'body',
        'model',
        'prompt_tokens',
        'completion_tokens',
        'cost_usd',
        'was_fallback',
    ];

    protected $casts = [
        'intent_score'      => 'integer',
        'prompt_tokens'     => 'integer',
        'completion_tokens' => 'integer',
        'cost_usd'          => 'decimal:6',
        'was_fallback'      => 'boolean',
    ];

    public function enrollment(): BelongsTo
    {
        return $this->belongsTo(AiSdrEnrollment::class, 'enrollment_id');
    }

    public function lead(): BelongsTo
    {
        return $this->belongsTo(Lead::class);
    }
}
