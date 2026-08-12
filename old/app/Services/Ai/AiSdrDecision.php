<?php

declare(strict_types=1);

namespace App\Services\Ai;

use App\Models\AiSdrMessage;

/**
 * Immutable result of one AI SDR decision.
 *
 * Returned by AiSdrDecisionService and consumed by ProcessAiSdrEnrollment,
 * which is the only place that mutates state / sends mail / writes audit
 * rows. Keeping the decision a pure value object makes the service trivial
 * to fake in tests (bind a stub that returns a fixed AiSdrDecision).
 */
final class AiSdrDecision
{
    /**
     * @param  'send'|'wait'|'qualify'|'handoff'|'stop'  $action
     */
    public function __construct(
        public readonly string $action,
        public readonly ?string $subject = null,
        public readonly ?string $body = null,
        public readonly ?string $reason = null,
        public readonly ?int $intentScore = null,
        public readonly bool $wasFallback = false,
        public readonly ?string $model = null,
        public readonly ?int $promptTokens = null,
        public readonly ?int $completionTokens = null,
        public readonly ?float $costUsd = null,
    ) {}

    public static function wait(string $reason, bool $wasFallback = false): self
    {
        return new self(
            action: AiSdrMessage::ACTION_WAIT,
            reason: $reason,
            wasFallback: $wasFallback,
        );
    }

    public static function send(string $subject, string $body, ?string $reason = null, ?int $intentScore = null, bool $wasFallback = false): self
    {
        return new self(
            action: AiSdrMessage::ACTION_SEND,
            subject: $subject,
            body: $body,
            reason: $reason,
            intentScore: $intentScore,
            wasFallback: $wasFallback,
        );
    }

    public function isSend(): bool
    {
        return $this->action === AiSdrMessage::ACTION_SEND;
    }
}
