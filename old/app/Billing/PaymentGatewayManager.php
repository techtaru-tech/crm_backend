<?php

namespace App\Billing;

use App\Billing\Contracts\PaymentGateway;
use App\Billing\Gateways\ManualGateway;
use App\Billing\Gateways\PayPalGateway;
use App\Billing\Gateways\PaystackGateway;
use App\Billing\Gateways\RazorpayGateway;
use App\Billing\Gateways\StripeGateway;
use App\Settings\BillingSettings;
use Illuminate\Support\Facades\Log;

/**
 * Registry / resolver for payment drivers. Anywhere in the codebase
 * that needs to list available gateways or pick one for a checkout
 * call goes through this class — it's the only place that knows
 * which driver classes exist.
 */
class PaymentGatewayManager
{
    /**
     * Canonical gateway id strings.  Use these constants instead of
     * literal `'stripe'` / `'paypal'` etc. anywhere a gateway id is
     * compared or stored — Filament selects, blade conditionals,
     * webhook routers, demo seeders.  Renaming a driver later then
     * means one edit here, not a sweep across the UI layer.
     */
    public const GATEWAY_STRIPE   = 'stripe';
    public const GATEWAY_PAYPAL   = 'paypal';
    public const GATEWAY_RAZORPAY = 'razorpay';
    public const GATEWAY_PAYSTACK = 'paystack';
    public const GATEWAY_MANUAL   = 'manual';

    /**
     * Class map of every driver the script ships with.
     *
     * @var array<string, class-string<PaymentGateway>>
     */
    protected array $drivers = [
        self::GATEWAY_STRIPE   => StripeGateway::class,
        self::GATEWAY_PAYPAL   => PayPalGateway::class,
        self::GATEWAY_RAZORPAY => RazorpayGateway::class,
        self::GATEWAY_PAYSTACK => PaystackGateway::class,
        self::GATEWAY_MANUAL   => ManualGateway::class,
    ];

    /**
     * Canonical id list returned in the order drivers register, so
     * UI surfaces (pricing page tab order, SA settings accordion)
     * stay deterministic across deploys.
     *
     * @return array<int, string>
     */
    public static function ids(): array
    {
        return [
            self::GATEWAY_STRIPE,
            self::GATEWAY_PAYPAL,
            self::GATEWAY_RAZORPAY,
            self::GATEWAY_PAYSTACK,
            self::GATEWAY_MANUAL,
        ];
    }

    /**
     * Resolve a driver instance by id. Throws InvalidArgumentException
     * so the caller can 404 gracefully.
     */
    public function driver(string $id): PaymentGateway
    {
        if (! isset($this->drivers[$id])) {
            throw new \InvalidArgumentException(__('services/payment_gateway.unknown_gateway', ['id' => $id]));
        }

        return app($this->drivers[$id]);
    }

    /**
     * All drivers, whether configured or not — used by the Settings UI
     * to render tabs/fields for every possible gateway.
     *
     * @return array<string, PaymentGateway>
     */
    public function all(): array
    {
        $out = [];
        foreach ($this->drivers as $id => $class) {
            $out[$id] = app($class);
        }
        return $out;
    }

    /**
     * Gateways that are both (a) marked enabled in BillingSettings AND
     * (b) have credentials filled in. This is the list the pricing page
     * should offer to tenants.
     *
     * @return array<string, PaymentGateway>
     */
    public function enabled(): array
    {
        // BillingSettings throws SettingDoesNotExist if the Spatie
        // settings rows haven't been seeded.  Return [] so the pricing
        // page renders an empty-state instead of 500-ing — the SA can
        // then enable a gateway from /super-admin/billing-settings.
        try {
            $enabled = app(BillingSettings::class)->enabled_gateways;
        } catch (\Throwable $e) {
            Log::warning('[gateway:manager] enabled_gateways unavailable', [
                'error' => $e->getMessage(),
            ]);
            return [];
        }

        $out = [];
        foreach ($enabled as $id) {
            if (! isset($this->drivers[$id])) continue;

            $driver = app($this->drivers[$id]);
            if ($driver->isConfigured()) {
                $out[$id] = $driver;
            }
        }

        return $out;
    }

    /**
     * The default gateway to preselect on the pricing / checkout page.
     */
    public function default(): ?PaymentGateway
    {
        $enabled = $this->enabled();
        return $enabled ? reset($enabled) : null;
    }

    /**
     * True when at least one gateway is both enabled and configured.
     */
    public function anyConfigured(): bool
    {
        return ! empty($this->enabled());
    }
}
