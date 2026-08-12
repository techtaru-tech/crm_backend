<?php

declare(strict_types=1);

namespace App\Support;

/**
 * iCalendar (RFC 5545) TEXT value escape helper.
 *
 * Note: ICS generation in PublicBookingController and
 * MeetingBookedMail stripped `\r\n` from meeting summary/description
 * but RFC 5545 § 3.3.11 requires escaping of `;`, `,`, and `\\` in TEXT
 * properties.  A meeting type whose name contains a `;` is parsed by
 * strict iCal clients (Apple Calendar, Outlook, Thunderbird) as a
 * property-parameter separator, which breaks the calendar entry.  Not
 * an XSS vector (calendar clients don't run JS) but a malformed-data /
 * defensive coding issue.
 *
 * Reference: https://datatracker.ietf.org/doc/html/rfc5545#section-3.3.11
 */
final class IcsSafety
{
    /**
     * Escape a string for use as the value of a TEXT property
     * (SUMMARY, DESCRIPTION, LOCATION, COMMENT, …) in an ICS file.
     *
     * Order matters: escape backslash first, otherwise the later
     * replacements that produce `\,` / `\;` / `\n` would be themselves
     * re-escaped on a second pass.
     */
    public static function escapeText(string $value): string
    {
        return strtr($value, [
            '\\'   => '\\\\',
            ';'    => '\\;',
            ','    => '\\,',
            "\r\n" => '\\n',
            "\n"   => '\\n',
            "\r"   => '\\n',
        ]);
    }
}
