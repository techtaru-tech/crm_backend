<?php

declare(strict_types=1);

namespace App\Enums;

/**
 * Follow-up lifecycle (spec §10).
 *
 * lead_tasks kept a plain `completed` boolean, which cannot tell apart
 * "not done yet" from "the rep never showed up" from "moved to Friday".
 * The boolean is still maintained for backwards compatibility; this enum
 * is what the funnel reports against.
 */
enum FollowUpStatus: string
{
    case Pending     = 'pending';
    case Completed   = 'completed';
    case Missed      = 'missed';
    case Rescheduled = 'rescheduled';

    /** @return array<string, string> */
    public static function options(): array
    {
        $out = [];
        foreach (self::cases() as $case) {
            $out[$case->value] = $case->label();
        }
        return $out;
    }

    /** Statuses that still need someone to act. */
    public static function openValues(): array
    {
        return [self::Pending->value, self::Rescheduled->value];
    }

    public function label(): string
    {
        return (string) __('enums/follow_up_status.' . $this->value);
    }

    public function color(): string
    {
        return match ($this) {
            self::Pending     => 'warning',
            self::Completed   => 'success',
            self::Missed      => 'danger',
            self::Rescheduled => 'info',
        };
    }

    public function isOpen(): bool
    {
        return in_array($this->value, self::openValues(), true);
    }
}
