<?php

namespace App\Models;

use App\Models\Concerns\BelongsToTenant;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;

class Company extends Model
{
    use HasFactory, SoftDeletes, BelongsToTenant;

    protected $fillable = [
        'tenant_id',
        'name',
        'domain',
        'industry',
        'size',
        'website',
        'phone',
        'address',
        'city',
        'country',
        'assigned_user_id',
        'custom_fields',
        'notes',
    ];

    protected $casts = [
        'custom_fields' => 'array',
    ];

    /**
     * Generic email domains that should not be used as a company identifier.
     */
    protected static array $genericDomains = [
        'gmail.com',
        'googlemail.com',
        'hotmail.com',
        'hotmail.co.uk',
        'yahoo.com',
        'yahoo.co.uk',
        'outlook.com',
        'live.com',
        'icloud.com',
        'me.com',
        'aol.com',
        'proton.me',
        'protonmail.com',
    ];

    public function leads(): HasMany
    {
        return $this->hasMany(Lead::class);
    }

    public function assignedUser(): BelongsTo
    {
        return $this->belongsTo(User::class, 'assigned_user_id');
    }

    public function getOpenDealsValueAttribute(): float
    {
        return (float) $this->leads()
            ->whereNotIn('status', ['converted', 'lost'])
            ->sum('deal_value');
    }

    public function getWonDealsValueAttribute(): float
    {
        return (float) $this->leads()->sum('won_value');
    }

    /**
     * Extract the domain portion from an email and return (or create) a Company
     * for that domain, scoped to the given tenant.
     *
     * Returns null for generic/free-email domains.
     */
    public static function findOrCreateByDomain(?string $email, int $tenantId): ?self
    {
        if (! $email || ! str_contains($email, '@')) {
            return null;
        }

        $domain = strtolower(trim(substr(strrchr($email, '@'), 1)));
        if ($domain === '' || in_array($domain, static::$genericDomains, true)) {
            return null;
        }

        // CRITICAL: bypass the BelongsToTenant global scope explicitly.
        // This method is invoked from contexts without auth context
        // (seeders, webhooks, Lead::creating boot hook). Without this
        // override, the scope fail-closes to `WHERE 0=1`, misses the
        // existing Company, and then the INSERT below crashes on the
        // (tenant_id, domain) unique constraint.
        $lookup = fn () => static::query()
            ->withoutGlobalScope('tenant')
            ->where('tenant_id', $tenantId)
            ->where('domain', $domain)
            ->first();

        if ($existing = $lookup()) {
            return $existing;
        }

        // Derive a friendly display name from the domain
        $name = ucfirst(explode('.', $domain)[0] ?? $domain);

        // Race-safe create: between our SELECT and INSERT a concurrent
        // request (or a lead-creating boot hook in the same seeder run)
        // may have inserted the row. Catch the unique violation and
        // re-read instead of propagating a 500.
        try {
            return static::create([
                'tenant_id' => $tenantId,
                'name'      => $name,
                'domain'    => $domain,
            ]);
        } catch (\Illuminate\Database\QueryException $e) {
            // Laravel's integrityConstraintViolation returns SQLSTATE 23000.
            if ($e->getCode() === '23000' || str_contains($e->getMessage(), 'Duplicate entry')) {
                return $lookup();
            }
            throw $e;
        }
    }
}
