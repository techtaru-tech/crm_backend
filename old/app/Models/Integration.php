<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Support\Facades\Crypt;
use App\Models\Concerns\BelongsToTenant;

class Integration extends Model
{
    use BelongsToTenant;
    protected $fillable = [
        'tenant_id', 'type', 'name', 'enabled', 'config',
        'status', 'last_synced_at', 'field_mapping', 'filter_config',
    ];

    protected $casts = [
        'enabled'       => 'boolean',
        'field_mapping' => 'array',
        'filter_config' => 'array',
        'last_synced_at' => 'datetime',
    ];

    public const STATUS_CONNECTED    = 'connected';
    public const STATUS_DISCONNECTED = 'disconnected';
    public const STATUS_ERROR        = 'error';

    public function tenant(): BelongsTo
    {
        return $this->belongsTo(Tenant::class);
    }

    public function syncLogs(): HasMany
    {
        return $this->hasMany(IntegrationSyncLog::class);
    }

    public function getConfig(): array
    {
        if (empty($this->config)) {
            return [];
        }

        try {
            return json_decode(Crypt::decryptString($this->config), true) ?? [];
        } catch (\Throwable) {
            return [];
        }
    }

    public function setConfig(array $config): void
    {
        $this->config = Crypt::encryptString(json_encode($config));
    }

    public function getLabel(): string
    {
        return \App\Integrations\IntegrationRegistry::getLabel($this->type);
    }
}
