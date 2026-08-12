<?php

namespace App\Filament\Widgets;

use App\Models\Automation;
use App\Models\Form;
use App\Models\Integration;
use App\Models\Lead;
use App\Models\LeadSourceConnection;
use App\Models\Pipeline;
use App\Models\User;
use Filament\Widgets\Widget;
use Illuminate\Support\Facades\Auth;

/**
 * Getting Started Checklist.
 *
 * Guides a fresh tenant through the "6 moves" that turn LeadHub from an
 * empty shell into a working lead engine.  Auto-hides once every item is
 * checked so it doesn't clutter the dashboard for seasoned users.
 */
class GettingStartedChecklist extends Widget
{
    protected string $view = 'filament.widgets.getting-started-checklist';

    protected int|string|array $columnSpan = 'full';

    public static function canView(): bool
    {
        // Super admins don't have a tenant — the checklist is a tenant-level concern.
        if (Auth::user()?->is_super_admin) {
            return false;
        }

        $data = static::computeItems();

        // Hide once everything's done.
        return collect($data)->contains(fn ($i) => ! $i['done']);
    }

    protected function getViewData(): array
    {
        $items = static::computeItems();
        $done  = collect($items)->where('done', true)->count();

        return [
            'items'    => $items,
            'done'     => $done,
            'total'    => count($items),
            'progress' => count($items) === 0 ? 0 : (int) round(($done / count($items)) * 100),
        ];
    }

    /**
     * @return array<int, array{label:string,description:string,url:string,icon:string,done:bool}>
     */
    protected static function computeItems(): array
    {
        $tenantId = \App\Support\TenantContext::currentId();

        if (! $tenantId) {
            return [];
        }

        // Request-scoped cache: this widget is hit twice per render
        // (canView() + getViewData()) and each item triggers a COUNT
        // or EXISTS — memoise so we don't do 12 queries on one page.
        $cacheKey = 'getting_started_checklist:' . $tenantId;
        if (app()->bound($cacheKey)) {
            return app($cacheKey);
        }

        $items = [
            [
                'label'       => __('filament/getting_started.step_invite_team_label'),
                'description' => __('filament/getting_started.step_invite_team_description'),
                'url'         => '/admin/users',
                'icon'        => 'heroicon-o-users',
                'done'        => User::where('tenant_id', $tenantId)->count() > 1,
            ],
            [
                'label'       => __('filament/getting_started.step_create_pipeline_label'),
                'description' => __('filament/getting_started.step_create_pipeline_description'),
                'url'         => '/admin/pipelines',
                'icon'        => 'heroicon-o-rectangle-stack',
                'done'        => Pipeline::where('tenant_id', $tenantId)->exists(),
            ],
            [
                'label'       => __('filament/getting_started.step_connect_source_label'),
                'description' => __('filament/getting_started.step_connect_source_description'),
                'url'         => '/admin/lead-source-connections',
                'icon'        => 'heroicon-o-link',
                'done'        => LeadSourceConnection::where('tenant_id', $tenantId)->where('active', true)->exists(),
            ],
            [
                'label'       => __('filament/getting_started.step_first_lead_label'),
                'description' => __('filament/getting_started.step_first_lead_description'),
                'url'         => '/admin/leads',
                'icon'        => 'heroicon-o-user-plus',
                'done'        => Lead::where('tenant_id', $tenantId)->exists(),
            ],
            [
                'label'       => __('filament/getting_started.step_install_automation_label'),
                'description' => __('filament/getting_started.step_install_automation_description'),
                'url'         => '/admin/automation-templates',
                'icon'        => 'heroicon-o-bolt',
                'done'        => Automation::where('tenant_id', $tenantId)->where('enabled', true)->exists(),
            ],
            [
                'label'       => __('filament/getting_started.step_connect_stack_label'),
                'description' => __('filament/getting_started.step_connect_stack_description'),
                'url'         => '/admin/integrations',
                'icon'        => 'heroicon-o-puzzle-piece',
                'done'        => Integration::where('tenant_id', $tenantId)->where('enabled', true)->exists()
                    || Form::where('tenant_id', $tenantId)->exists(),
            ],
        ];

        app()->instance($cacheKey, $items);

        return $items;
    }
}
