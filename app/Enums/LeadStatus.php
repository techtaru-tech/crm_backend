<?php

declare(strict_types=1);

namespace App\Enums;

/**
 * Canonical lead-status vocabulary (H7).
 *
 * Pre-H7 the codebase used a mix of `'converted'` (Filament resource
 * status options + UI labels) and `'won'` (ReportService aggregations,
 * LeadObserver state-machine, automations).  The drift meant tenants
 * could mark a lead "converted" via the form and the won-revenue chart
 * would silently undercount.  An accompanying migration coerces every
 * legacy `'converted'` row to `'won'` and the model cast forces all
 * future writes through this enum.
 */
enum LeadStatus: string
{
    case New        = 'new';
    case Contacted  = 'contacted';
    case Qualified  = 'qualified';
    case Won        = 'won';
    case Lost       = 'lost';

    /** @return array<int, self> */
    public static function activeCases(): array
    {
        return [self::New, self::Contacted, self::Qualified];
    }

    /** @return array<int, self> */
    public static function terminalCases(): array
    {
        return [self::Won, self::Lost];
    }

    /** @return array<int, string> string values for whereNotIn / option arrays */
    public static function terminalValues(): array
    {
        return array_map(fn (self $c) => $c->value, self::terminalCases());
    }

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
        // Translatable via lang/en/enums/lead_status.php.  EN default
        // matches the original hard-coded values byte-for-byte so
        // existing call sites (Filament option arrays, kanban
        // column headings, automation rule UI) keep working.
        return (string) __('enums/lead_status.' . $this->value);
    }

    public function color(): string
    {
        return match ($this) {
            self::New        => 'gray',
            self::Contacted  => 'info',
            self::Qualified  => 'warning',
            self::Won        => 'success',
            self::Lost       => 'danger',
        };
    }

    public function isTerminal(): bool
    {
        return in_array($this, self::terminalCases(), true);
    }
}
