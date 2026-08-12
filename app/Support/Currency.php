<?php

namespace App\Support;

/**
 * Currency formatting helper. Keeps the list of supported ISO-4217
 * codes plus their display symbol and default fraction digits in one
 * place so the pricing page, billing dashboard, and email templates
 * all render prices consistently.
 *
 * Zero runtime dependencies — pure PHP, safe to call from blade views.
 */
class Currency
{
    /**
     * Symbol + minor-unit metadata for every currency we enable by default.
     */
    public const MAP = [
        'USD' => ['symbol' => '$',    'decimals' => 2, 'label' => 'US Dollar'],
        'EUR' => ['symbol' => '€',    'decimals' => 2, 'label' => 'Euro'],
        'GBP' => ['symbol' => '£',    'decimals' => 2, 'label' => 'British Pound'],
        'INR' => ['symbol' => '₹',    'decimals' => 2, 'label' => 'Indian Rupee'],
        'NGN' => ['symbol' => '₦',    'decimals' => 2, 'label' => 'Nigerian Naira'],
        'ZAR' => ['symbol' => 'R',    'decimals' => 2, 'label' => 'South African Rand'],
        'KES' => ['symbol' => 'KSh',  'decimals' => 2, 'label' => 'Kenyan Shilling'],
        'GHS' => ['symbol' => 'GH₵',  'decimals' => 2, 'label' => 'Ghanaian Cedi'],
        'BRL' => ['symbol' => 'R$',   'decimals' => 2, 'label' => 'Brazilian Real'],
        'MXN' => ['symbol' => '$',    'decimals' => 2, 'label' => 'Mexican Peso'],
        'AUD' => ['symbol' => 'A$',   'decimals' => 2, 'label' => 'Australian Dollar'],
        'CAD' => ['symbol' => 'C$',   'decimals' => 2, 'label' => 'Canadian Dollar'],
        'AED' => ['symbol' => 'د.إ',  'decimals' => 2, 'label' => 'UAE Dirham'],
        'SAR' => ['symbol' => '﷼',    'decimals' => 2, 'label' => 'Saudi Riyal'],
        'IDR' => ['symbol' => 'Rp',   'decimals' => 0, 'label' => 'Indonesian Rupiah'],
        'PHP' => ['symbol' => '₱',    'decimals' => 2, 'label' => 'Philippine Peso'],
        'MYR' => ['symbol' => 'RM',   'decimals' => 2, 'label' => 'Malaysian Ringgit'],
        'TRY' => ['symbol' => '₺',    'decimals' => 2, 'label' => 'Turkish Lira'],
        'EGP' => ['symbol' => 'E£',   'decimals' => 2, 'label' => 'Egyptian Pound'],
        'PKR' => ['symbol' => '₨',    'decimals' => 2, 'label' => 'Pakistani Rupee'],
        'JPY' => ['symbol' => '¥',    'decimals' => 0, 'label' => 'Japanese Yen'],
        'CNY' => ['symbol' => '¥',    'decimals' => 2, 'label' => 'Chinese Yuan'],
    ];

    /**
     * Format a numeric amount with the right symbol, decimals, and
     * thousands separator for the given currency.
     */
    public static function format(float|int $amount, string $currency = 'USD'): string
    {
        $code = strtoupper($currency);
        $meta = self::MAP[$code] ?? ['symbol' => $code . ' ', 'decimals' => 2];

        $formatted = number_format((float) $amount, $meta['decimals'], '.', ',');

        return $meta['symbol'] . $formatted;
    }

    public static function symbol(string $currency = 'USD'): string
    {
        $code = strtoupper($currency);
        return self::MAP[$code]['symbol'] ?? ($code . ' ');
    }

    public static function decimals(string $currency = 'USD'): int
    {
        $code = strtoupper($currency);
        return (int) (self::MAP[$code]['decimals'] ?? 2);
    }

    public static function label(string $currency = 'USD'): string
    {
        $code = strtoupper($currency);
        $key  = 'currencies.' . strtolower($code);
        $translated = __($key);

        // Fall back to the literal English MAP label if the translator
        // returned the raw key (i.e., the lang entry is missing). Keeps
        // backward compatibility for downstream callers.
        if ($translated === $key) {
            return self::MAP[$code]['label'] ?? $code;
        }

        return (string) $translated;
    }

    /**
     * List of [code => "CODE — Translated Label"] for Filament Select
     * options.  Pulls labels through the translator so the dropdown is
     * locale-aware per CodeCanyon Item 1 ("no hardcoded text").
     */
    public static function options(): array
    {
        $out = [];
        foreach (self::MAP as $code => $meta) {
            $out[$code] = $code . ' — ' . self::label($code);
        }
        return $out;
    }

    /**
     * The currency the script owner has picked as the reporting default.
     * Used by the Billing dashboard when it can't infer a per-tenant code.
     */
    public static function default(): string
    {
        try {
            return strtoupper(app(\App\Settings\GeneralSettings::class)->currency ?? 'USD');
        } catch (\Throwable) {
            return 'USD';
        }
    }

    /**
     * Convenience shortcut for `symbol(default())`. Used by Filament
     * money inputs (`->prefix(fn () => Currency::defaultSymbol())`) so
     * the `$`-style prefix matches whatever currency the script owner
     * configured in General Settings — not a hardcoded dollar sign.
     */
    public static function defaultSymbol(): string
    {
        return self::symbol(self::default());
    }

    /**
     * Resolve the currency to render in for a given tenant context.
     *
     * Lookup order:
     *   1. The tenant's own `currency` column (operator-set in the
     *      tenant settings UI).
     *   2. The global GeneralSettings default — same path as
     *      `Currency::default()`.
     *   3. `'USD'` as the last-resort floor so callers can safely
     *      pass the result to `Currency::format()`.
     *
     * Accepts a nullable tenant so anonymous / public-page contexts
     * (pricing.blade, sales pages) can still call this method without
     * a guard clause.
     */
    public static function forTenant(?\App\Models\Tenant $tenant): string
    {
        $code = is_object($tenant) ? (string) ($tenant->currency ?? '') : '';
        if ($code !== '') {
            return strtoupper($code);
        }

        return self::default();
    }
}
