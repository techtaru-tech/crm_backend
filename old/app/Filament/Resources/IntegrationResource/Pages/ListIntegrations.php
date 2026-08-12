<?php

namespace App\Filament\Resources\IntegrationResource\Pages;

use App\Filament\Resources\IntegrationResource;
use App\Integrations\IntegrationRegistry;
use App\Models\Integration;
use Filament\Notifications\Notification;
use Filament\Resources\Pages\Page;
use Illuminate\Support\Facades\Auth;

class ListIntegrations extends Page
{
    protected static string $resource = IntegrationResource::class;
    protected string $view = 'filament.resources.integration-resource.pages.list-integrations';

    public array $integrationsByCategory = [];
    public ?int $editingId = null;
    public bool $setupModalOpen = false;
    public string $setupType = '';
    public string $setupName = '';
    public bool $setupEnabled = false;
    public array $setupConfig = [];
    public array $setupFieldMapping = [];
    public array $setupFilterSources    = [];
    public array $setupFilterTags       = [];
    public array $setupFilterPipelines  = [];
    public string $activeCategory = '';
    public array $availableTargetFields = [];

    public function mount(): void
    {
        $this->loadIntegrations();
        $this->activeCategory = array_key_first($this->integrationsByCategory) ?? '';
    }

    public function loadIntegrations(): void
    {
        $tenantId      = Auth::user()?->tenant_id;
        $existingByType = Integration::where('tenant_id', $tenantId)->get()->keyBy('type');

        $categories = [];
        foreach (IntegrationRegistry::INTEGRATIONS as $type => $class) {
            // Use the registry's *translator-routing* accessors instead of
            // reading the canonical English const tables directly — the
            // const tables exist for category match-expressions and
            // English fallback only.  CodeCanyon Item 1: no hardcoded
            // English in UI surfaces.
            $category = IntegrationRegistry::CATEGORIES[$type] ?? __('integrations_registry.categories.other');
            $existing = $existingByType[$type] ?? null;

            $categories[$category][] = [
                'type'        => $type,
                'label'       => IntegrationRegistry::getLabel($type),
                'description' => IntegrationRegistry::getDescription($type),
                'category'    => $category,
                'integration' => $existing ? [
                    'id'             => $existing->id,
                    'enabled'        => $existing->enabled,
                    'status'         => $existing->status,
                    'last_synced_at' => $existing->last_synced_at?->diffForHumans() ?? __('filament/integrations.last_sync_never'),
                ] : null,
            ];
        }

        $order = ['Automation', 'CRM', 'Email Marketing', 'Communication', 'Data & Productivity'];
        uksort($categories, fn($a, $b) => (array_search($a, $order) ?? 99) <=> (array_search($b, $order) ?? 99));

        $this->integrationsByCategory = $categories;
    }

    public function setActiveCategory(string $category): void
    {
        $this->activeCategory = $category;
    }

    public function openSetupModal(string $type): void
    {
        $tenantId    = Auth::user()?->tenant_id;
        $existing    = Integration::where('tenant_id', $tenantId)->where('type', $type)->first();

        $this->setupType          = $type;
        $this->editingId          = $existing?->id;
        $this->setupName          = $existing?->name ?? IntegrationRegistry::getLabel($type);
        $this->setupEnabled       = (bool) ($existing?->enabled ?? false);
        $this->setupConfig        = $existing ? $existing->getConfig() : [];
        $this->setupFieldMapping  = $existing?->field_mapping ?? [];
        $this->setupFilterSources   = $existing?->filter_config['sources']    ?? [];
        $this->setupFilterTags      = $existing?->filter_config['tags']       ?? [];
        $this->setupFilterPipelines = $existing?->filter_config['pipelines']  ?? [];
        $this->setupModalOpen       = true;

        $this->loadAvailableTargetFields($type, $existing);
    }

    private function loadAvailableTargetFields(string $type, ?Integration $integration): void
    {
        try {
            $connector = IntegrationRegistry::make($type, $integration ? $integration->getConfig() : []);
            $fields    = $connector->getAvailableFields();
            $this->availableTargetFields = $fields;
        } catch (\Throwable) {
            $this->availableTargetFields = [];
        }
    }

    public function saveSetup(): void
    {
        $tenantId = Auth::user()?->tenant_id;

        $integration = new Integration();
        $integration->setConfig($this->setupConfig);
        $encryptedConfig = $integration->config;

        $payload = [
            'tenant_id'     => $tenantId,
            'type'          => $this->setupType,
            'name'          => $this->setupName ?: IntegrationRegistry::getLabel($this->setupType),
            'enabled'       => $this->setupEnabled,
            'config'        => $encryptedConfig,
            'field_mapping' => $this->setupFieldMapping ?: null,
            'filter_config' => array_filter([
                'sources'   => $this->setupFilterSources,
                'tags'      => $this->setupFilterTags,
                'pipelines' => $this->setupFilterPipelines,
            ]) ?: null,
        ];

        if ($this->editingId) {
            Integration::where('tenant_id', $tenantId)->where('id', $this->editingId)->update($payload);
        } else {
            Integration::create($payload);
        }

        $this->setupModalOpen = false;
        $this->loadIntegrations();

        Notification::make()->success()->title(__('filament/integrations.notify_saved'))->send();
    }

    public function toggleEnabled(int $id): void
    {
        $tenantId    = Auth::user()?->tenant_id;
        $integration = Integration::where('tenant_id', $tenantId)->findOrFail($id);
        $integration->update(['enabled' => ! $integration->enabled]);
        $this->loadIntegrations();
        Notification::make()->success()->title($integration->enabled ? __('filament/integrations.notify_disabled') : __('filament/integrations.notify_enabled'))->send();
    }

    public function testConnection(int $id): void
    {
        $tenantId    = Auth::user()?->tenant_id;
        $integration = Integration::where('tenant_id', $tenantId)->findOrFail($id);

        try {
            $connector = IntegrationRegistry::make($integration->type, $integration->getConfig());
            $ok        = $connector->testConnection();
            $integration->update(['status' => $ok ? Integration::STATUS_CONNECTED : Integration::STATUS_ERROR]);
            $this->loadIntegrations();

            if ($ok) {
                Notification::make()->success()->title(__('filament/integrations.connection_successful'))->send();
            } else {
                Notification::make()->danger()->title(__('filament/integrations.connection_failed'))->send();
            }
        } catch (\Throwable $e) {
            Notification::make()->danger()->title(__('filament/integrations.error_prefix') . $e->getMessage())->send();
        }
    }

    public function deleteIntegration(int $id): void
    {
        $tenantId = Auth::user()?->tenant_id;
        Integration::where('tenant_id', $tenantId)->where('id', $id)->delete();
        $this->loadIntegrations();
        Notification::make()->success()->title(__('filament/integrations.notify_removed'))->send();
    }

    public function closeModal(): void
    {
        $this->setupModalOpen = false;
    }

    public function getConfigSchema(): array
    {
        return IntegrationRegistry::getConfigFields($this->setupType);
    }

    protected function getHeaderActions(): array
    {
        return [];
    }
}
