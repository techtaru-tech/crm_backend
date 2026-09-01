<?php

declare(strict_types=1);

namespace App\Support;

use App\Enums\LeadPriority;
use App\Enums\LeadStatus;
use Carbon\Carbon;

/**
 * Coerces free-text spreadsheet cells into values the Lead model accepts.
 *
 * Both import paths need this — the "Import from CRM" auto-detect importer
 * and the column-mapping wizard's queue job — and they were drifting apart:
 * the wizard passed `status` through untouched, so a perfectly ordinary CSV
 * saying "New" instead of "new" failed every single row with
 * `"New" is not a valid backing value for enum LeadStatus`.
 *
 * Every method here returns something the model will accept, or null.
 * Import is a bulk operation on data nobody controls; one odd cell must
 * cost that cell, never the row and never the file.
 */
final class LeadFieldNormalizer
{
    /**
     * Map a status cell onto the LeadStatus enum.
     *
     * Vocabularies from other CRMs are wider than ours — Negotiation,
     * Proposal, Working, Nurturing — so they collapse onto the nearest stage
     * we model rather than being rejected. Unrecognised text becomes `new`,
     * which is the safe default for an inbound lead.
     */
    public static function status(?string $raw): string
    {
        $r = strtolower(trim((string) $raw));

        if ($r === '') {
            return LeadStatus::New->value;
        }

        if ($enum = LeadStatus::tryFrom($r)) {
            return $enum->value;
        }

        return match (true) {
            str_contains($r, 'won'), str_contains($r, 'closed - won'), str_contains($r, 'convert')  => LeadStatus::Won->value,
            str_contains($r, 'lost'), str_contains($r, 'closed - lost'), str_contains($r, 'dead')   => LeadStatus::Lost->value,
            str_contains($r, 'qualif'), str_contains($r, 'propos'), str_contains($r, 'pitch'),
            str_contains($r, 'negotiat'), str_contains($r, 'quote')                                 => LeadStatus::Qualified->value,
            str_contains($r, 'contact'), str_contains($r, 'reach'), str_contains($r, 'working'),
            str_contains($r, 'nurtur'), str_contains($r, 'follow')                                  => LeadStatus::Contacted->value,
            default                                                                                  => LeadStatus::New->value,
        };
    }

    /**
     * Map a priority cell onto the LeadPriority enum.
     *
     * Hot / Warm / Cold is a three-step scale and ours has four. Hot becomes
     * High rather than Urgent so Urgent stays what a person escalates a lead
     * to — otherwise every imported hot lead lands at the top of the scale
     * and the level stops carrying information.
     */
    public static function priority(?string $raw): string
    {
        $r = strtolower(trim((string) $raw));

        if ($r === '') {
            return LeadPriority::Normal->value;
        }

        if ($enum = LeadPriority::tryFrom($r)) {
            return $enum->value;
        }

        return match (true) {
            str_contains($r, 'urgent'), str_contains($r, 'critical')  => LeadPriority::Urgent->value,
            str_contains($r, 'hot'), str_contains($r, 'high')         => LeadPriority::High->value,
            str_contains($r, 'cold'), str_contains($r, 'low')         => LeadPriority::Low->value,
            default                                                    => LeadPriority::Normal->value,
        };
    }

    /** Parse a date cell; null rather than an exception on junk. */
    public static function date(?string $raw): ?string
    {
        $raw = trim((string) $raw);

        if ($raw === '') {
            return null;
        }

        try {
            return Carbon::parse($raw)->toDateTimeString();
        } catch (\Throwable) {
            return null;
        }
    }

    /** Strip currency symbols, separators and stray text from an amount cell. */
    public static function money(?string $raw): ?float
    {
        $numeric = preg_replace('/[^0-9.\-]/', '', (string) $raw);

        return ($numeric !== '' && $numeric !== null && is_numeric($numeric))
            ? (float) $numeric
            : null;
    }

    /**
     * Clean a phone cell into something dialable.
     *
     * Real exports are messy: Google Contacts and several WhatsApp CRM tools
     * prefix the channel ("p:+919829998028", "m: 98765 43210"), sheets keep
     * two numbers in one cell separated by / or , and everybody formats with
     * spaces, dashes and brackets.  Stored raw, "p:+9198..." also poisons
     * Lead::phone_normalized (which strips non-digits) and so breaks
     * duplicate detection on phone.
     *
     * Returns null when the cell holds no plausible number, so the caller
     * treats it as "no phone" rather than saving junk.
     */
    public static function phone(?string $raw): ?string
    {
        $value = trim((string) $raw);

        if ($value === '') {
            return null;
        }

        // Leading channel label: p: / m: / w: / tel: / phone: / mobile:
        $value = preg_replace('/^\s*(p|m|w|h|t|tel|ph|phone|mobile|cell|whatsapp)\s*[:\-]\s*/i', '', $value) ?? $value;

        // Multiple numbers in one cell — keep the first, the rest would not
        // fit a single column anyway.
        $value = preg_split('#\s*[/,;|]\s*#', trim($value))[0] ?? '';

        // A leading + is meaningful (country code); nothing else is.
        $plus   = str_starts_with(trim($value), '+');
        $digits = preg_replace('/\D/', '', $value) ?? '';

        // Shorter than this is an extension, a stray "0" or a typo, not a
        // reachable number.
        if (strlen($digits) < 7) {
            return null;
        }

        return ($plus ? '+' : '') . $digits;
    }
}
