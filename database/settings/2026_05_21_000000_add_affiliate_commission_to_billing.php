<?php

use Spatie\LaravelSettings\Migrations\SettingsMigration;

/**
 * Add the affiliate-program commission rate to the billing settings
 * group.
 *
 * Until now the rate was a hard-coded constant
 * (AbstractGateway::DEFAULT_COMMISSION_PCT = 20.00) with no operator
 * UI to change it.  Storing it as a setting lets the Super Admin
 * tune the affiliate payout percentage from the billing settings
 * page; every gateway reads it via AbstractGateway::commissionPercent()
 * when booking an AffiliateReferral row.
 *
 * Default 20.0 preserves the previous hard-coded behaviour for
 * existing installs.
 */
return new class extends SettingsMigration
{
    public function up(): void
    {
        $this->migrator->add('billing.affiliate_commission_percent', 20.0);
    }
};
