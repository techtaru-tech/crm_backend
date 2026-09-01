<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Support\Str;

class Tenant extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'name',
        'slug',
        'owner_id',
        'parent_tenant_id',
        'domain',
        'subdomain',
        'max_seats',
        'seat_count',
        'subscription_status',
        'plan',
        'trial_ends_at',
        'subscription_ends_at',
        'settings',
        'branding',
        'active',
        'referral_code',
        'referred_by_tenant_id',
        'business_name',
        'vat_number',
        'billing_address',
        'billing_email',
        'billing_country',
        'currency',
        'default_locale',
    ];

    protected static function booted(): void
    {
        static::creating(function (self $tenant) {
            if (empty($tenant->referral_code)) {
                do {
                    $code = strtoupper(Str::random(10));
                } while (static::where('referral_code', $code)->exists());
                $tenant->referral_code = $code;
            }
        });
    }

    public function referredBy(): \Illuminate\Database\Eloquent\Relations\BelongsTo
    {
        return $this->belongsTo(self::class, 'referred_by_tenant_id');
    }

    public function referrals(): \Illuminate\Database\Eloquent\Relations\HasMany
    {
        return $this->hasMany(self::class, 'referred_by_tenant_id');
    }

    /**
     * Hierarchy: agency-style parent-child workspace relationships.
     * Agency tenant has parent_tenant_id=null and many children.
     * Sub-client tenants point parent_tenant_id at the agency.
     */
    public function parent(): \Illuminate\Database\Eloquent\Relations\BelongsTo
    {
        return $this->belongsTo(self::class, 'parent_tenant_id');
    }

    public function children(): \Illuminate\Database\Eloquent\Relations\HasMany
    {
        return $this->hasMany(self::class, 'parent_tenant_id');
    }

    public function isAgency(): bool
    {
        return $this->children()->exists();
    }

    public function isSubAccount(): bool
    {
        return $this->parent_tenant_id !== null;
    }

    protected $casts = [
        // H8: typed subscription_status — see App\Enums\SubscriptionStatus.
        // Every code path that writes the column now goes through
        // transitionSubscriptionStatus() (this model) which takes an
        // enum, and existing rows have one of the 7 enum values.
        // Reads return SubscriptionStatus instances; whereIn('subscription_status',
        // [enum values]) still works because Eloquent serialises
        // bound enum cases to their string value at the DB boundary.
        'subscription_status'  => \App\Enums\SubscriptionStatus::class,
        'settings'             => 'array',
        'branding'             => 'array',
        'active'               => 'boolean',
        'trial_ends_at'        => 'datetime',
        'subscription_ends_at' => 'datetime',
        // GDPR Article 17 erasure timestamps. Without these casts the daily
        // erasure cron called ->toIso8601String() on a raw string and fatally
        // crashed on the first eligible tenant.
        'deletion_requested_at' => 'datetime',
        'deletion_scheduled_at' => 'datetime',
        // activation_signals is a json column; without the array cast
        // ActivationTracker::record() read back a raw JSON string, warned on
        // foreach and then fataled on `$signals[] = ...` — swallowed by its
        // own try/catch, so every signal after the first was silently lost.
        'activated_at'          => 'datetime',
        'activation_signals'    => 'array',
    ];

    public function owner()
    {
        return $this->belongsTo(User::class, 'owner_id');
    }

    public function apiKeys(): \Illuminate\Database\Eloquent\Relations\HasMany
    {
        return $this->hasMany(ApiKey::class);
    }

    public function outboundWebhooks(): \Illuminate\Database\Eloquent\Relations\HasMany
    {
        return $this->hasMany(OutboundWebhook::class);
    }

    public function users()
    {
        return $this->belongsToMany(User::class, 'tenant_users')
            ->withPivot('role')
            ->withTimestamps();
    }

    public function hasSeatAvailable(): bool
    {
        return $this->seat_count < $this->max_seats;
    }

    /**
     * Recount `seat_count` from the actual tenant_users pivot.
     * Call this after any attach/detach on $tenant->users() so
     * the cached column stays in sync with reality.  Replaces
     * the scattered $tenant->increment/decrement('seat_count')
     * calls that were drifting with each edge case (invite
     * accept to existing user, admin delete via SA panel,
     * removed from team-settings, etc.).
     *
     * Idempotent — safe to call multiple times.
     */
    public function recountSeats(): int
    {
        $count = $this->users()->count();

        // Avoid the write when the cache already matches — skips
        // an UPDATE query on the hot path of the Team & Seats
        // page load.
        if ((int) $this->seat_count !== $count) {
            $this->forceFill(['seat_count' => $count])->save();
        }

        return $count;
    }

    /**
     * H8: single audited writer for subscription_status.  Every gateway
     * driver and the lifecycle cron should route status mutations
     * through this method instead of bare
     * `$tenant->update(['subscription_status' => 'X'])` so:
     *   1. the value is type-checked at the call site (typo-resistant
     *      against 'canceled' vs 'cancelled' divergence)
     *   2. every transition writes an audit-log row tagged
     *      'subscription,billing' so the SA can reconstruct the
     *      lifecycle of any tenant from /admin/super/audit
     *
     * No-ops if the new status equals the current one — protects the
     * audit log from spam during webhook retries.
     */
    public function transitionSubscriptionStatus(\App\Enums\SubscriptionStatus $to): void
    {
        // subscription_status is cast to the App\Enums\SubscriptionStatus
        // backed enum (see $casts above), so $this->subscription_status
        // is an enum object, not a string.  A raw (string) cast on a
        // BackedEnum throws "Object of class ... could not be converted
        // to string", which would crash every webhook-driven status
        // change.  Unwrap via ->value, and fall back to a string for
        // any legacy row that somehow skipped the cast.
        $rawFrom = $this->subscription_status;
        $from    = $rawFrom instanceof \BackedEnum
            ? (string) $rawFrom->value
            : (string) ($rawFrom ?? '');
        if ($from === $to->value) {
            return;
        }

        $this->forceFill(['subscription_status' => $to->value])->save();

        try {
            \App\Models\AuditLog::record(
                action: 'subscription.status_changed',
                auditable: $this,
                oldValues: ['subscription_status' => $from],
                newValues: ['subscription_status' => $to->value],
                tags: 'subscription,billing',
            );
        } catch (\Throwable $e) {
            // Never let an audit-log write failure abort a billing
            // transition — the tenant's subscription state is the
            // source of truth, the audit row is observability.
            \Illuminate\Support\Facades\Log::warning(
                '[Tenant::transitionSubscriptionStatus] audit log failed: ' . $e->getMessage(),
            );
        }
    }

    public function getBranding(string $key, mixed $default = null): mixed
    {
        return data_get($this->branding ?? [], $key, $default);
    }

    public function getSetting(string $key, mixed $default = null): mixed
    {
        return data_get($this->settings ?? [], $key, $default);
    }

    /** Default tax/VAT rate (%) pre-filled onto new invoices and quotes. */
    public function defaultTaxRate(): float
    {
        return (float) $this->getSetting('default_tax_rate', 0);
    }

    public function getAppName(): string
    {
        return $this->getBranding('app_name', config('leadhub.branding.app_name'));
    }

    public function getPrimaryColor(): string
    {
        return $this->getBranding('primary_color', config('leadhub.branding.primary_color'));
    }

    /*
    |--------------------------------------------------------------------------
    | Plan / Subscription Helpers
    |--------------------------------------------------------------------------
    |
    | These four methods are intentional thin convenience proxies onto
    | App\Services\PlanService — the canonical owner of plan / feature /
    | limit logic.  Blade templates and Filament resource closures
    | benefit from the `$tenant->hasFeature('X')` ergonomics that the
    | service-locator-flavoured calls preserve.  Code that needs to be
    | unit-tested in isolation should constructor-inject PlanService
    | and call the typed methods on it directly:
    |
    |     PlanService::getPlan(Tenant $t): array
    |     PlanService::hasFeature(Tenant $t, string $feature): bool
    |     PlanService::getLimit(Tenant $t, string $key): int
    |     PlanService::canCreate(Tenant $t, string $resourceType): array
    |
    | Don't add new business logic to the proxies; extend PlanService
    | and add a one-line proxy here only if the caller ergonomics
    | demand it.
    */

    /**
     * Convenience proxy.  Canonical home: {@see \App\Services\PlanService::getPlan()}.
     */
    public function getPlanDefinition(): array
    {
        return app(\App\Services\PlanService::class)->getPlan($this);
    }

    /**
     * Convenience proxy.  Canonical home: {@see \App\Services\PlanService::hasFeature()}.
     */
    public function hasFeature(string $feature): bool
    {
        return app(\App\Services\PlanService::class)->hasFeature($this, $feature);
    }

    /**
     * Convenience proxy.  Canonical home: {@see \App\Services\PlanService::getLimit()}.
     * -1 = unlimited, 0 = disabled.
     */
    public function getLimit(string $key): int
    {
        return app(\App\Services\PlanService::class)->getLimit($this, $key);
    }

    /**
     * Convenience proxy.  Canonical home: {@see \App\Services\PlanService::canCreate()}.
     */
    public function canCreate(string $resourceType): array
    {
        return app(\App\Services\PlanService::class)->canCreate($this, $resourceType);
    }

    /*
    |--------------------------------------------------------------------------
    | Subscription-state helpers
    |--------------------------------------------------------------------------
    | All routing goes through App\Services\TenantSubscriptionState so the
    | logic has ONE authoritative implementation.  Keep the public method
    | signatures stable — existing callers depend on them.
    */
    public function subscriptionState(): \App\Services\TenantSubscriptionState
    {
        return \App\Services\TenantSubscriptionState::of($this);
    }

    public function onTrial(): bool
    {
        return $this->subscriptionState()->isOnTrial();
    }

    public function trialExpired(): bool
    {
        return $this->subscriptionState()->isTrialExpired();
    }

    public function hasActiveSubscription(): bool
    {
        return $this->subscriptionState()->state() === \App\Services\TenantSubscriptionState::ACTIVE_PAID;
    }

    public function canAccessApp(): bool
    {
        return $this->subscriptionState()->isActive();
    }

    public function getStatusLabel(): string
    {
        return $this->subscriptionState()->label();
    }
}
