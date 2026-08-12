<?php

namespace App\Settings;

use App\Models\Tenant;
use Illuminate\Support\Facades\DB;
use Spatie\LaravelSettings\SettingsRepositories\SettingsRepository;

/**
 * Tenant-scoped settings repository for spatie/laravel-settings.
 *
 * All reads/writes are scoped to the currently-resolved tenant.
 * When no tenant is resolved (e.g. super-admin panel), tenant_id is NULL.
 */
class TenantSettingsRepository implements SettingsRepository
{
    private string $table;

    public function __construct(array $config = [])
    {
        $this->table = $config['table'] ?? 'settings';
    }

    private function tenantId(): ?int
    {
        if (! app()->bound('current_tenant')) {
            return null;
        }

        $tenant = app('current_tenant');

        return $tenant instanceof Tenant ? $tenant->id : null;
    }

    private function query(): \Illuminate\Database\Query\Builder
    {
        return DB::table($this->table)->where('tenant_id', $this->tenantId());
    }

    public function getPropertiesInGroup(string $group): array
    {
        return $this->query()
            ->where('group', $group)
            ->pluck('payload', 'name')
            ->map(fn ($payload) => json_decode($payload, true))
            ->toArray();
    }

    public function checkIfPropertyExists(string $group, string $name): bool
    {
        return $this->query()
            ->where('group', $group)
            ->where('name', $name)
            ->exists();
    }

    public function getPropertyPayload(string $group, string $name): mixed
    {
        $payload = $this->query()
            ->where('group', $group)
            ->where('name', $name)
            ->value('payload');

        return $payload !== null ? json_decode($payload, true) : null;
    }

    public function createProperty(string $group, string $name, mixed $payload): void
    {
        DB::table($this->table)->insert([
            'tenant_id'  => $this->tenantId(),
            'group'      => $group,
            'name'       => $name,
            'payload'    => json_encode($payload),
            'locked'     => false,
            'created_at' => now(),
            'updated_at' => now(),
        ]);
    }

    public function updatePropertiesPayload(string $group, array $properties): void
    {
        foreach ($properties as $name => $value) {
            $this->query()
                ->where('group', $group)
                ->where('name', $name)
                ->update([
                    'payload'    => json_encode($value),
                    'updated_at' => now(),
                ]);
        }
    }

    public function deleteProperty(string $group, string $name): void
    {
        $this->query()
            ->where('group', $group)
            ->where('name', $name)
            ->delete();
    }

    public function lockProperties(string $group, array $properties): void
    {
        $this->query()
            ->where('group', $group)
            ->whereIn('name', $properties)
            ->update(['locked' => true]);
    }

    public function unlockProperties(string $group, array $properties): void
    {
        $this->query()
            ->where('group', $group)
            ->whereIn('name', $properties)
            ->update(['locked' => false]);
    }

    public function getLockedProperties(string $group): array
    {
        return $this->query()
            ->where('group', $group)
            ->where('locked', true)
            ->pluck('name')
            ->toArray();
    }
}
