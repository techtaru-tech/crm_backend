<?php

declare(strict_types=1);

namespace App\Services;

/**
 * VAT / Tax computation for SaaS-side invoices.
 *
 * Rules implemented:
 *   1. Operator's home country = where they're tax-registered.
 *      Set via BillingSettings->operator_country.
 *
 *   2. Customer in operator's home country → charge home VAT rate.
 *
 *   3. Customer in EU, business with valid VAT ID → REVERSE CHARGE
 *      (tax_rate=0, "VAT shifted to recipient" note on invoice).
 *      Operator stays out of EU MOSS scheme.
 *
 *   4. Customer in EU, B2C (no VAT ID) → charge customer's country
 *      VAT rate (under EU MOSS / OSS the operator collects on
 *      behalf of the customer's country).
 *
 *   5. Customer outside EU → no VAT charged.
 *
 * VAT-ID validation against VIES (https://ec.europa.eu/taxation_customs/vies/)
 * is OUT OF SCOPE for v1 — this service trusts the supplied vat_id
 * was already validated upstream.  The looksLikeValidVatId() check
 * is a lightweight regex sanity test only.
 *
 * Rates table = January 2026 EU standard rates.  Operator should
 * update annually — the constants are intentionally flat so it's
 * a one-line maintenance edit.
 */
class TaxCalculator
{
    /** EU member state ISO-3166 alpha-2 codes → standard VAT rate (%) */
    public const EU_VAT_RATES = [
        'AT' => 20.00, 'BE' => 21.00, 'BG' => 20.00, 'HR' => 25.00,
        'CY' => 19.00, 'CZ' => 21.00, 'DK' => 25.00, 'EE' => 22.00,
        'FI' => 25.50, 'FR' => 20.00, 'DE' => 19.00, 'GR' => 24.00,
        'HU' => 27.00, 'IE' => 23.00, 'IT' => 22.00, 'LV' => 21.00,
        'LT' => 21.00, 'LU' => 17.00, 'MT' => 18.00, 'NL' => 21.00,
        'PL' => 23.00, 'PT' => 23.00, 'RO' => 19.00, 'SK' => 23.00,
        'SI' => 22.00, 'ES' => 21.00, 'SE' => 25.00,
    ];

    /**
     * @return array{
     *   net: float,
     *   tax_rate: float,
     *   tax_amount: float,
     *   gross: float,
     *   reverse_charge: bool,
     *   note: ?string,
     * }
     */
    public function calculate(
        float $amount,
        ?string $country,
        ?string $vatId = null,
        ?string $operatorCountry = null,
    ): array {
        $amount = max(0.0, $amount);
        $country = $country ? strtoupper($country) : null;
        $operatorCountry = $operatorCountry ? strtoupper($operatorCountry) : null;
        $vatId = $vatId ? trim($vatId) : null;

        // Outside EU → no VAT
        if (! $country || ! $this->isEu($country)) {
            return $this->shape($amount, 0.00, false, null);
        }

        // Customer in operator's home country → home rate
        if ($operatorCountry && $country === $operatorCountry) {
            return $this->shape($amount, $this->rateFor($country), false, null);
        }

        // EU B2B with valid VAT ID → REVERSE CHARGE
        if ($vatId && $this->looksLikeValidVatId($vatId, $country)) {
            return $this->shape(
                $amount,
                0.00,
                true,
                (string) __('services/tax_calculator.vat_reverse_charged_note'),
            );
        }

        // EU B2C → charge customer's country rate (MOSS / OSS scheme)
        return $this->shape($amount, $this->rateFor($country), false, null);
    }

    public function isEu(string $countryAlpha2): bool
    {
        return array_key_exists(strtoupper($countryAlpha2), self::EU_VAT_RATES);
    }

    public function rateFor(string $countryAlpha2): float
    {
        return (float) (self::EU_VAT_RATES[strtoupper($countryAlpha2)] ?? 0.00);
    }

    /**
     * Lightweight format check — does NOT contact VIES.  Operator is
     * responsible for validating against VIES before storing.
     * Format: 2-letter country code + 8-12 alphanumeric chars.
     */
    public function looksLikeValidVatId(string $vatId, ?string $country = null): bool
    {
        $vatId = strtoupper(preg_replace('/[\s\-]/', '', $vatId) ?? '');
        if ($country) {
            $country = strtoupper($country);
            if (! str_starts_with($vatId, $country)) {
                return false;
            }
        }
        return (bool) preg_match('/^[A-Z]{2}[A-Z0-9]{8,12}$/', $vatId);
    }

    protected function shape(float $net, float $rate, bool $reverseCharge, ?string $note): array
    {
        $taxAmount = round($net * $rate / 100, 2);
        return [
            'net'            => round($net, 2),
            'tax_rate'       => round($rate, 2),
            'tax_amount'     => $reverseCharge ? 0.0 : $taxAmount,
            'gross'          => $reverseCharge ? round($net, 2) : round($net + $taxAmount, 2),
            'reverse_charge' => $reverseCharge,
            'note'           => $note,
        ];
    }
}
