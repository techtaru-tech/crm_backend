<?php

declare(strict_types=1);

namespace App\Filament\SuperAdmin\Pages;

use App\Filament\SuperAdmin\Widgets\LeadSourceMixChart;
use App\Filament\SuperAdmin\Widgets\MrrTrendChart;
use App\Filament\SuperAdmin\Widgets\SaasStatsOverview;
use App\Filament\SuperAdmin\Widgets\SubscriptionStatusChart;
use App\Filament\SuperAdmin\Widgets\SuperAdminSetupChecklist;
use App\Filament\SuperAdmin\Widgets\TenantSignupsChart;
use App\Models\Lead;
use App\Models\Tenant;
use App\Models\TenantBillingReceipt;
use App\Models\User;
use Filament\Pages\Page;
use Illuminate\Contracts\Support\Htmlable;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Support\Facades\Cache;

class Dashboard extends Page
{
    protected static string|\BackedEnum|null $navigationIcon = 'heroicon-o-home';

    protected string $view = 'filament.super-admin.pages.dashboard';

    protected static ?int $navigationSort = 0;

    public function getTitle(): string|Htmlable
    {
        return __('filament/sa_dashboard.title');
    }

    /**
     * NOTE: we deliberately do NOT override Filament's
     * getHeaderWidgets() / getFooterWidgets() — those would cause
     * <x-filament-panels::page> to render the widgets automatically
     * AND the blade then renders them again via <x-filament-widgets
     * ::widgets>, producing a duplicated dashboard.
     *
     * Instead the blade calls the methods below directly so each
     * widget renders exactly once at a layout position we control.
     */

    /**
     * Top of the page: KPI cards + 12-month trend charts.
     *
     * @return array<int, class-string>
     */
    public function headerWidgetsList(): array
    {
        return [
            // First-login setup checklist (H16) — prepended so it's the
            // first thing a fresh-install operator sees.  The widget's
            // canView() auto-hides once 4 of 5 items are done, so it
            // doesn't follow the operator forever.
            SuperAdminSetupChecklist::class,
            SaasStatsOverview::class,
            MrrTrendChart::class,
            TenantSignupsChart::class,
        ];
    }

    public function headerWidgetsColumns(): int|array
    {
        return 2;
    }

    /**
     * Bottom of the page: distribution donut + connector mix.
     *
     * @return array<int, class-string>
     */
    public function footerWidgetsList(): array
    {
        return [
            SubscriptionStatusChart::class,
            LeadSourceMixChart::class,
        ];
    }

    public function footerWidgetsColumns(): int|array
    {
        return 2;
    }

    /**
     * Lightweight stats still consumed by the blade for the
     * "secondary metrics" strip (leads volume, avg leads/tenant).
     *
     * @return array<string, int>
     */
    public function getStats(): array
    {
        // Cache-key convention for the entire SA dashboard:
        //   `sa-dashboard:<topic>:v<n>`
        // Every cache entry under this prefix is a CROSS-TENANT
        // aggregate — never per-tenant data.  When per-tenant SA
        // drilldowns are added later they MUST be namespaced as
        // `sa-dashboard:tenant:{$id}:<topic>:v<n>` so they cannot
        // collide with the aggregate cache and accidentally render a
        // single tenant's row count under "Total Leads".
        // Cache-bust call sites (app/Console/Commands/RefreshDemoData.php)
        // must mirror these exact strings.
        // Stampede guard — see SaasStatsOverview for rationale.
        return Cache::lock('sa-dashboard:secondary-stats:v1:lock', 10)->block(5, fn () =>
            Cache::remember('sa-dashboard:secondary-stats:v1', 300, function (): array {
            $totalTenants = Tenant::count();
            $totalLeads = Lead::count();

            return [
                'total_tenants' => $totalTenants,
                'active_tenants' => Tenant::where('active', true)->count(),
                'trial_tenants' => Tenant::where('subscription_status', 'trial')->count(),
                'total_users' => User::excludingSuperAdmins()->count(),
                'total_leads' => $totalLeads,
                'leads_this_month' => Lead::where('created_at', '>=', now()->startOfMonth())->count(),
                'new_tenants_30d' => Tenant::where('created_at', '>=', now()->subDays(30))->count(),
                'avg_leads_per_tenant' => $totalTenants > 0 ? (int) round($totalLeads / $totalTenants) : 0,
            ];
        }));
    }

    /**
     * Most-recently-created tenants for the activity feed.  Kept
     * outside the cache because the recency context loses value if
     * the row is even a few minutes stale and the query is cheap.
     *
     * @return Collection<int, Tenant>
     */
    public function getRecentTenants(int $limit = 6): Collection
    {
        return Tenant::with('owner')->latest()->take($limit)->get();
    }

    /**
     * Most-recent SaaS-side payments collected from tenants.
     *
     * @return Collection<int, TenantBillingReceipt>
     */
    public function getRecentReceipts(int $limit = 6): Collection
    {
        return TenantBillingReceipt::with('tenant')
            ->orderByDesc('issued_at')
            ->take($limit)
            ->get();
    }
}
