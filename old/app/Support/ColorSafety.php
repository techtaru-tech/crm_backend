<?php

declare(strict_types=1);

namespace App\Support;

/**
 * Hex-color validation helper — defends against CSS-context injection
 * via tenant-admin authored color fields.
 *
 * Note: Filament `ColorPicker` validates on the client only.
 * Server-side `save()` just persists whatever string lands in the
 * Livewire payload.  A tenant admin tampering the JSON payload (or any
 * future code path that mass-assigns the column) can store
 * `primary_color = 'red; }; body{display:none}'`, which then renders
 * INTO an inline `<style>` block on every public landing page / form /
 * booking page that the tenant owns.  CSS doesn't decode HTML entities,
 * so Blade `{{ }}` HTML-escaping does NOT protect against CSS-context
 * payloads.
 *
 * Use {@see safeHex()} at SAVE time on every color column and at RENDER
 * time as defense-in-depth.
 *
 * Reference: https://owasp.org/www-community/attacks/Code_Injection
 * (CSS-injection sub-class).
 */
final class ColorSafety
{
    /**
     * Return the value if it matches a strict CSS hex-color shape
     * (`#rgb`, `#rgba`, `#rrggbb`, `#rrggbbaa`), otherwise return the
     * supplied default.
     *
     * Strips leading/trailing whitespace; lowercases the result to
     * match the canonical form Filament ColorPicker emits.
     *
     * Examples:
     *   safeHex('#fff')                  → '#fff'
     *   safeHex('#ABCDEF')               → '#abcdef'
     *   safeHex(null, '#4f46e5')         → '#4f46e5'
     *   safeHex('red; })', '#4f46e5')    → '#4f46e5'  ← payload rejected
     *   safeHex('javascript:..', '#000') → '#000'    ← payload rejected
     */
    public static function safeHex(mixed $value, string $default = '#000000'): string
    {
        if (! is_string($value)) {
            return $default;
        }
        $candidate = strtolower(trim($value));
        if (preg_match('/^#[0-9a-f]{3,8}$/', $candidate)) {
            return $candidate;
        }
        return $default;
    }

    /**
     * Return TRUE iff `$value` is a syntactically-valid hex color.
     * Use this for boolean checks at validation time.
     */
    public static function isValidHex(mixed $value): bool
    {
        return is_string($value) && (bool) preg_match('/^#[0-9a-f]{3,8}$/i', trim($value));
    }

    /**
     * Return a value safe to interpolate inside a CSS `background:` /
     * similar property — accepts the hex shapes from safeHex() PLUS
     * linear-gradient / radial-gradient / rgb / rgba / well-known
     * CSS keywords (transparent, inherit, etc.).  Anything else falls
     * back to $default.
     *
     * Use case: BrandedMailable email header background can be
     * a gradient configured by the SuperAdmin via the BrandingSettings
     * UI.  The SA UI is server-validated so the gradient string is
     * trusted — but a tenant-level override (email_header_color) is
     * free-text.  This helper accepts both shapes and rejects
     * everything else (which would otherwise let a tenant inject
     * `red; }; body { background: url(...) }` and exfil recipient
     * email-open metadata via CSS background-url tracking).
     */
    public static function safeCssBackground(mixed $value, string $default = '#4f46e5'): string
    {
        if (! is_string($value)) {
            return $default;
        }
        $candidate = trim($value);
        if ($candidate === '') {
            return $default;
        }

        // Case A: hex color.
        if (preg_match('/^#[0-9a-f]{3,8}$/i', $candidate)) {
            return strtolower($candidate);
        }

        // Case B: rgb / rgba (numbers + commas + spaces only inside parens).
        if (preg_match('/^rgba?\(\s*\d{1,3}\s*,\s*\d{1,3}\s*,\s*\d{1,3}\s*(?:,\s*(?:0|1|0?\.\d+)\s*)?\)$/i', $candidate)) {
            return strtolower($candidate);
        }

        // Case C: linear-gradient / radial-gradient — only allow
        // ASCII letters/digits/commas/spaces/percentages/hex-colors
        // inside the parens; absolutely no quote/semicolon/brace chars.
        if (preg_match('/^(?:linear|radial)-gradient\([\s0-9a-zA-Z%#,\.\-\(\)]+\)$/', $candidate)
            && ! preg_match('/[\'";{}]/', $candidate)
        ) {
            return $candidate;
        }

        // Case D: well-known CSS keywords.
        $keywords = ['transparent', 'inherit', 'initial', 'unset', 'currentcolor', 'none'];
        if (in_array(strtolower($candidate), $keywords, true)) {
            return strtolower($candidate);
        }

        return $default;
    }
}
