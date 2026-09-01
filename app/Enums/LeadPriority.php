<?php

declare(strict_types=1);

namespace App\Enums;

/**
 * Lead priority (spec §4).
 *
 * Deliberately mirrors the shape of LeadStatus — value-backed, translatable
 * label(), Filament colour(), options() for Select components — so every
 * call site treats the two the same way.
 */
enum LeadPriority: string
{
    case Low    = 'low';
    case Normal = 'normal';
    case High   = 'high';
    case Urgent = 'urgent';

    /** @return array<string, string> {value => label} for Filament Select options */
    public static function options(): array
    {
        $out = [];
        foreach (self::cases() as $case) {
            $out[$case->value] = $case->label();
        }
        return $out;
    }

    public function label(): string
    {
        return (string) __('enums/lead_priority.' . $this->value);
    }

    public function color(): string
    {
        return match ($this) {
            self::Low    => 'gray',
            self::Normal => 'info',
            self::High   => 'warning',
            self::Urgent => 'danger',
        };
    }

    /** Sort weight — higher is more urgent. Used for "hottest first" ordering. */
    public function weight(): int
    {
        return match ($this) {
            self::Low    => 0,
            self::Normal => 1,
            self::High   => 2,
            self::Urgent => 3,
        };
    }
}
