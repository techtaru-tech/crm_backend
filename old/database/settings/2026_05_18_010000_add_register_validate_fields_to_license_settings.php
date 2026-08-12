<?php

use Spatie\LaravelSettings\Migrations\SettingsMigration;

/**
 * Add the /api/register + /api/validate protocol fields to the
 * license settings group.
 *
 * verification_id      base64-encoded "{item_id}|{?}|{buyer}|{purchase_code}"
 *                      string returned by /api/register.  Sent in the
 *                      body of every /api/validate POST.
 * product_token        HS512-signed JWT returned by /api/register and
 *                      signed with the buyer's purchase code as the
 *                      secret.  Sent in the Authorization header of
 *                      every /api/validate POST.
 * heartbeat            base64-encoded JSON {status, id, end_point} —
 *                      written when /api/register or /api/validate
 *                      returns 5xx/404, drives the heartbeat grace
 *                      window (default 168 hours / 7 days), cleared
 *                      on every confirmed-valid response.
 * last_verification_at ISO datetime of the most recent /api/validate
 *                      attempt (success OR fail).  Used to honour the
 *                      server-supplied check_interval claim so we
 *                      don't hammer the licensing server.
 */
return new class extends SettingsMigration
{
    public function up(): void
    {
        $this->migrator->add('license.verification_id', null);
        $this->migrator->add('license.product_token', null);
        $this->migrator->add('license.heartbeat', null);
        $this->migrator->add('license.last_verification_at', null);
    }
};
