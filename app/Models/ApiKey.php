<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Str;
use App\Models\Concerns\BelongsToTenant;

class ApiKey extends Model
{
    use BelongsToTenant;
    protected $fillable = [
        'tenant_id',
        'name',
        'key_prefix',
        'key_hash',
        'scopes',
        'rate_limit_per_hour',
        'expires_at',
        'last_used_at',
        'revoked_at',
    ];

    protected $casts = [
        'scopes'       => 'array',
        'expires_at'   => 'datetime',
        'last_used_at' => 'datetime',
        'revoked_at'   => 'datetime',
    ];

    public const ALL_SCOPES = [
        'read:leads'         => 'Read Leads',
        'write:leads'        => 'Write Leads',
        'delete:leads'       => 'Delete Leads',
        'read:pipelines'     => 'Read Pipelines',
        'write:pipelines'    => 'Write Pipelines',
        'read:tags'          => 'Read Tags',
        'write:tags'         => 'Write Tags',
        'read:users'         => 'Read Users',
        'write:users'        => 'Create & Update Users',
        'delete:users'       => 'Delete Users',
        'read:forms'         => 'Read Forms',
        'write:forms'        => 'Create, Update & Submit Forms',
        'delete:forms'       => 'Delete Forms',
        'read:automations'   => 'Read Automations',
        'write:automations'  => 'Create & Update Automations',
        'delete:automations' => 'Delete Automations',
        'read:integrations'  => 'Read Integrations',
        'write:integrations' => 'Create, Update & Delete Integrations',
    ];

    /**
     * Translated scope labels for Filament Select / display.
     * Keys mirror self::ALL_SCOPES (colon-delimited scope tokens).
     */
    public static function scopeLabels(): array
    {
        return [
            'read:leads'         => __('models/api_key.scope_read_leads'),
            'write:leads'        => __('models/api_key.scope_write_leads'),
            'delete:leads'       => __('models/api_key.scope_delete_leads'),
            'read:pipelines'     => __('models/api_key.scope_read_pipelines'),
            'write:pipelines'    => __('models/api_key.scope_write_pipelines'),
            'read:tags'          => __('models/api_key.scope_read_tags'),
            'write:tags'         => __('models/api_key.scope_write_tags'),
            'read:users'         => __('models/api_key.scope_read_users'),
            'write:users'        => __('models/api_key.scope_write_users'),
            'delete:users'       => __('models/api_key.scope_delete_users'),
            'read:forms'         => __('models/api_key.scope_read_forms'),
            'write:forms'        => __('models/api_key.scope_write_forms'),
            'delete:forms'       => __('models/api_key.scope_delete_forms'),
            'read:automations'   => __('models/api_key.scope_read_automations'),
            'write:automations'  => __('models/api_key.scope_write_automations'),
            'delete:automations' => __('models/api_key.scope_delete_automations'),
            'read:integrations'  => __('models/api_key.scope_read_integrations'),
            'write:integrations' => __('models/api_key.scope_write_integrations'),
        ];
    }

    public function tenant(): BelongsTo
    {
        return $this->belongsTo(Tenant::class);
    }

    public function isRevoked(): bool
    {
        return $this->revoked_at !== null;
    }

    public function isExpired(): bool
    {
        return $this->expires_at !== null && $this->expires_at->isPast();
    }

    public function isActive(): bool
    {
        return ! $this->isRevoked() && ! $this->isExpired();
    }

    public function hasScope(string $scope): bool
    {
        if (empty($this->scopes)) {
            return true;
        }

        return in_array($scope, (array) $this->scopes, true);
    }

    public static function generateKey(): array
    {
        $raw    = 'lh_' . Str::random(40);
        $prefix = substr($raw, 0, 8);
        $hash   = hash('sha256', $raw);

        return compact('raw', 'prefix', 'hash');
    }

    public static function findByKey(string $rawKey): ?self
    {
        $hash = hash('sha256', $rawKey);

        // Fix note: bypass the BelongsToTenant global scope here.
        // API authentication runs BEFORE tenant context is resolved
        // (the `api` middleware group has no ResolveTenant, and no
        // auth()->user() is established yet — the API key IS what
        // establishes both).  The scope's fail-closed `whereRaw('0=1')`
        // when no tenant is resolved would otherwise make every
        // legitimate API key return null.  After lookup the caller
        // (ApiKeyAuthentication middleware) sets the resolved tenant
        // context from $apiKey->tenant_id.
        //
        // This bypass is safe because:
        //   1. key_hash is a SHA-256 of a 40-char random string —
        //      collision-resistant; an attacker can't guess another
        //      tenant's key.
        //   2. The caller filters by tenant after lookup (every API
        //      controller scopes its query by $apiKey->tenant_id).
        return static::query()->withoutGlobalScopes()->where('key_hash', $hash)->first();
    }

    /**
     * Updates last_used_at at most once per minute per API key.
     *
     * A plain markUsed() fires a DB write on every single API request.
     * At high throughput this creates write pressure and index churn on the
     * api_keys table for a column that consumers only care about in aggregate.
     * We skip the write when a cache sentinel exists for this key, limiting
     * writes to at most once per minute regardless of request rate.
     */
    public function debounceMarkUsed(): void
    {
        $cacheKey = "api_key_used:{$this->id}";

        if (Cache::add($cacheKey, 1, 60)) {
            $this->updateQuietly(['last_used_at' => now()]);
        }
    }
}
