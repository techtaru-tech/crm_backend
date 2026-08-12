<?php

use Spatie\LaravelSettings\Migrations\SettingsMigration;

return new class extends SettingsMigration
{
    public function up(): void
    {
        $this->migrator->add('billing.enabled_gateways', []);

        // Stripe
        $this->migrator->add('billing.stripe_public_key', null);
        $this->migrator->add('billing.stripe_secret_key', null);
        $this->migrator->add('billing.stripe_webhook_secret', null);
        $this->migrator->add('billing.stripe_test_mode', true);

        // PayPal
        $this->migrator->add('billing.paypal_client_id', null);
        $this->migrator->add('billing.paypal_client_secret', null);
        $this->migrator->add('billing.paypal_webhook_id', null);
        $this->migrator->add('billing.paypal_sandbox', true);

        // Razorpay
        $this->migrator->add('billing.razorpay_key_id', null);
        $this->migrator->add('billing.razorpay_key_secret', null);
        $this->migrator->add('billing.razorpay_webhook_secret', null);

        // Paystack
        $this->migrator->add('billing.paystack_public_key', null);
        $this->migrator->add('billing.paystack_secret_key', null);

        // Manual
        $this->migrator->add('billing.manual_bank_name', null);
        $this->migrator->add('billing.manual_account_name', null);
        $this->migrator->add('billing.manual_account_number', null);
        $this->migrator->add('billing.manual_iban', null);
        $this->migrator->add('billing.manual_swift', null);
        $this->migrator->add('billing.manual_extra_instructions', null);
    }
};
