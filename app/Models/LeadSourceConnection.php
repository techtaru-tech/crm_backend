<?php

namespace App\Models;

use App\Enums\LeadSource;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Support\Str;
use App\Models\Concerns\BelongsToTenant;

class LeadSourceConnection extends Model
{
    use HasFactory, BelongsToTenant;
    protected $fillable = [
        'tenant_id',
        'source',
        'name',
        'webhook_token',
        'credentials',
        'settings',
        'status',
        'last_received_at',
        'last_error',
        'active',
    ];

    protected $casts = [
        'credentials'     => 'encrypted:array',
        'settings'        => 'array',
        'last_received_at' => 'datetime',
        'active'          => 'boolean',
    ];

    protected static function boot(): void
    {
        parent::boot();

        static::creating(function ($model) {
            if (empty($model->webhook_token)) {
                $model->webhook_token = Str::random(48);
            }
        });
    }

    public function tenant(): BelongsTo
    {
        return $this->belongsTo(Tenant::class);
    }

    public function leads(): HasMany
    {
        return $this->hasMany(Lead::class, 'source_connection_id');
    }

    public function webhookLogs(): HasMany
    {
        return $this->hasMany(WebhookLog::class, 'source_connection_id');
    }

    public function getWebhookUrlAttribute(): string
    {
        return route('webhook.inbound', [
            'tenant' => $this->tenant_id,
            'source' => $this->source,
            'token'  => $this->webhook_token,
        ]);
    }

    public function getSourceLabelAttribute(): string
    {
        $enum = LeadSource::tryFrom($this->source);
        if ($enum) {
            return $enum->label();
        }

        // Translator-first fallback: look up
        // `lang/en/lead_sources.<source_key>` so non-enum lead sources
        // (custom values introduced by integrations) still pick up
        // localised labels when present.  ucfirst() of the raw key is
        // the last-resort English fallback.
        $rawKey   = (string) ($this->source ?? '');
        $lookup   = 'lead_sources.' . strtolower($rawKey);
        $resolved = __($lookup);

        return $resolved !== $lookup ? (string) $resolved : ucfirst($rawKey);
    }

    public function isConnected(): bool
    {
        return $this->status === 'connected';
    }

    public function markConnected(): void
    {
        $this->update(['status' => 'connected', 'last_error' => null]);
    }

    public function markError(string $message): void
    {
        $this->update(['status' => 'error', 'last_error' => $message]);
    }

    public function markReceived(): void
    {
        $this->update(['last_received_at' => now(), 'status' => 'connected']);
    }
}
