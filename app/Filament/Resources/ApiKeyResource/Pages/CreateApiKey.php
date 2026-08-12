<?php

namespace App\Filament\Resources\ApiKeyResource\Pages;

use App\Filament\Resources\ApiKeyResource;
use App\Models\ApiKey;
use Filament\Resources\Pages\CreateRecord;

class CreateApiKey extends CreateRecord
{
    protected static string $resource = ApiKeyResource::class;

    private string $rawKey = '';

    protected function mutateFormDataBeforeCreate(array $data): array
    {
        $tenantId = \App\Support\TenantContext::currentId();

        ['raw' => $raw, 'prefix' => $prefix, 'hash' => $hash] = ApiKey::generateKey();

        $this->rawKey = $raw;

        return array_merge($data, [
            'tenant_id'  => $tenantId,
            'key_prefix' => $prefix,
            'key_hash'   => $hash,
        ]);
    }

    protected function afterCreate(): void
    {
        session()->flash('new_api_key', $this->rawKey);
    }

    protected function getRedirectUrl(): string
    {
        return $this->getResource()::getUrl('index');
    }
}
