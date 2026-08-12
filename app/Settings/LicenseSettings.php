<?php

namespace App\Settings;

use Spatie\LaravelSettings\Settings;

class LicenseSettings extends Settings
{
    public ?string $license_key      = null;
    public ?string $license_status   = null;
    public ?string $licensed_to      = null;
    public ?string $expires_at       = null;
    public ?string $last_checked_at  = null;

    /**
     * Timestamp of the LAST successful server-side verification ('Y-m-d
     * H:i:s').  Drives the EnforceLicense middleware's grace window:
     * once the licensing server starts returning "invalid", the panel
     * keeps working for config('leadhub.license.grace_days') days
     * counted from this value before the kill-switch fires.
     *
     * Populated for the first time during install.php (which requires
     * a verified key up front), then bumped by every subsequent
     * successful verify — daily via the leadhub:verify-license
     * scheduled command, or on-demand from the License Settings page.
     *
     * Transient outages must NEVER advance this timestamp — only a
     * confirmed-valid response from the licensing server may.
     */
    public ?string $last_valid_at    = null;

    /**
     * Base64-encoded verification_id returned by /api/register.  Decoded
     * value is a pipe-separated string {item_id}|{?}|{buyer}|{purchase_code}.
     * Sent in the body of every /api/validate POST.
     */
    public ?string $verification_id  = null;

    /**
     * HS512-signed JWT returned by /api/register, signed with the
     * buyer's purchase code as the secret.  Decoded locally to surface
     * the server-supplied check_interval and to confirm the response
     * actually came from the licensing server.  Sent in the
     * Authorization header of every /api/validate POST.
     */
    public ?string $product_token    = null;

    /**
     * Base64-encoded JSON {status, id, end_point} written when
     * /api/register or /api/validate returns 5xx/404.  Drives the
     * heartbeat-based grace window: the panel keeps working for
     * config('leadhub.license.heartbeat_hours') hours from
     * last_verification_at while this is non-empty so the buyer
     * isn't kicked out during a licensing-server outage.  Cleared
     * on every confirmed-valid /api/validate response.
     */
    public ?string $heartbeat        = null;

    /**
     * Timestamp of the most recent /api/validate POST attempt (success
     * OR fail).  Distinct from last_valid_at which only advances on a
     * confirmed-valid response; this advances on every attempt and is
     * used to enforce the JWT's check_interval claim (do not re-check
     * more often than the server asked).
     */
    public ?string $last_verification_at = null;

    public static function group(): string
    {
        return 'license';
    }
}
