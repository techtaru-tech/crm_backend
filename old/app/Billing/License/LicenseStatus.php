<?php

declare(strict_types=1);

namespace App\Billing\License;

/**
 * Typed snapshot of the SaaS-license validation result.
 *
 * The legacy LicenseService::validate()/getStatus() return associative
 * arrays.  Those array shapes are still consumed directly by
 * resources/views/filament/pages/settings/license-settings-page.blade.php
 * (lots of `$licenseStatus['buyer']` etc.), so M-A3 introduces this
 * DTO as an additive opt-in: new PHP callers prefer the typed object
 * (`$svc->statusDto()->isValid()`) while the blade keeps using array
 * access via `toArray()`.
 *
 * Migrating the blade off array access is a follow-up; the value of
 * landing the DTO now is that any new isValid()-style branch in PHP
 * can typecheck without a stringly-typed map lookup.
 */
final readonly class LicenseStatus
{
    public function __construct(
        public string  $status,                  // 'valid' | 'invalid' | 'unlicensed' | 'demo'
        public string  $message,
        public ?string $buyer           = null,
        public ?string $licenseType     = null,  // 'Regular License' | 'Extended License' | …
        public ?string $itemName        = null,
        public ?string $purchasedAt     = null,
        public ?string $supportedUntil  = null,
        public ?string $expiresAt       = null,
        public ?string $domain          = null,
        public ?string $plan            = null,  // 'regular' | 'extended' | …
    ) {}

    public static function fromArray(array $data): self
    {
        return new self(
            status:         (string) ($data['status']  ?? 'invalid'),
            message:        (string) ($data['message'] ?? ''),
            buyer:          self::nullableString($data['buyer'] ?? null),
            licenseType:    self::nullableString($data['license_type'] ?? null),
            itemName:       self::nullableString($data['item_name'] ?? null),
            purchasedAt:    self::nullableString($data['purchased_at'] ?? null),
            supportedUntil: self::nullableString($data['supported_until'] ?? null),
            expiresAt:      self::nullableString($data['expires_at'] ?? null),
            domain:         self::nullableString($data['domain'] ?? null),
            plan:           self::nullableString($data['plan'] ?? null),
        );
    }

    public function isValid(): bool
    {
        return $this->status === 'valid';
    }

    public function isUnlicensed(): bool
    {
        return $this->status === 'unlicensed';
    }

    /**
     * True when the install is running in public-demo mode
     * (LEADHUB_DEMO_MODE=true) — the LicenseService short-circuits
     * any server verification and returns this status so the panel
     * stays open without a key.
     */
    public function isDemo(): bool
    {
        return $this->status === 'demo';
    }

    /**
     * Broader "should the admin panel allow this user in?" check.
     * True for any of: confirmed-valid licence, demo install.
     * For grace-window logic (was-valid-recently, fresh-install
     * window), call LicenseService::isAccessPermitted() instead — it
     * needs to consult LicenseSettings and installed.lock, which
     * this value object intentionally doesn't know about.
     */
    public function isPermitted(): bool
    {
        return $this->status === 'valid' || $this->status === 'demo';
    }

    /**
     * Return the same array shape produced by the legacy
     * LicenseService::getStatus() so blade templates / cache writers
     * that already key on those names keep working.
     *
     * @return array<string, mixed>
     */
    public function toArray(): array
    {
        return [
            'status'          => $this->status,
            'message'         => $this->message,
            'buyer'           => $this->buyer,
            'license_type'    => $this->licenseType,
            'item_name'       => $this->itemName,
            'purchased_at'    => $this->purchasedAt,
            'supported_until' => $this->supportedUntil,
            'expires_at'      => $this->expiresAt,
            'domain'          => $this->domain,
            'plan'            => $this->plan,
        ];
    }

    private static function nullableString(mixed $v): ?string
    {
        if ($v === null) {
            return null;
        }
        $s = (string) $v;
        return $s === '' ? null : $s;
    }
}
