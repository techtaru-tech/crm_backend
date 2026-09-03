<?php

declare(strict_types=1);

namespace App\Filament\Pages;

use App\Filament\Concerns\PageRequiresPermission;

use App\Models\SharedTemplate;
use App\Models\Tenant;
use App\Services\MarketplaceInstaller;
use Filament\Notifications\Notification;
use Filament\Pages\Page;
use Illuminate\Contracts\Support\Htmlable;
use UnitEnum;

/**
 * Tenant-side marketplace browse at /admin/marketplace.
 *
 * Shows published templates (is_public=true) from the shared_templates
 * table — automations, funnels, email sequences, forms built by other
 * tenants and listed for install.
 *
 * Filters: type + category + featured-only.  Search filters by
 * name/description.  Featured templates surface first; rest sort by
 * downloads desc.
 *
 * Install flow (UI follow-up): tenant clicks "Install", system clones
 * the payload into the tenant's own automations/forms/etc.  Paid
 * templates route through the existing billing infrastructure first.
 */
class MarketplacePage extends Page
{
    use PageRequiresPermission;

    protected static string $requiredPermission = 'settings.manage';

    protected static string|\BackedEnum|null $navigationIcon = 'heroicon-o-shopping-bag';
    protected static string|UnitEnum|null $navigationGroup = 'Templates';
    protected static ?int $navigationSort = 90;
    protected static ?string $slug = 'marketplace';
    protected string $view = 'filament.pages.marketplace';

    public string $search = '';
    public ?string $typeFilter = null;
    public ?string $categoryFilter = null;
    public bool $featuredOnly = false;

    public static function getNavigationLabel(): string
    {
        return __('filament/marketplace.nav_label');
    }

    public function getTitle(): string|Htmlable
    {
        return __('filament/marketplace.title');
    }

    public function getHeading(): string
    {
        return __('filament/marketplace.heading');
    }

    public function getSubheading(): ?string
    {
        return __('filament/marketplace.subheading');
    }

    public function getViewData(): array
    {
        $query = SharedTemplate::query()
            ->published()
            ->with('owner:id,name');

        if ($this->typeFilter) {
            $query->where('type', $this->typeFilter);
        }

        if ($this->categoryFilter) {
            $query->where('category', $this->categoryFilter);
        }

        if ($this->featuredOnly) {
            $query->where('is_featured', true);
        }

        if ($this->search !== '') {
            $needle = mb_strtolower($this->search);
            $query->where(function ($q) use ($needle) {
                $q->whereRaw('LOWER(name) like ?', ["%{$needle}%"])
                  ->orWhereRaw('LOWER(description) like ?', ["%{$needle}%"]);
            });
        }

        $templates = $query
            ->orderByDesc('is_featured')
            ->orderByDesc('downloads')
            ->orderBy('name')
            ->limit(60)
            ->get();

        $categories = SharedTemplate::query()
            ->published()
            ->whereNotNull('category')
            ->distinct()
            ->orderBy('category')
            ->pluck('category')
            ->all();

        return [
            'templates'      => $templates,
            'types'          => SharedTemplate::typeLabels(),
            'categories'     => $categories,
            'search'         => $this->search,
            'typeFilter'     => $this->typeFilter,
            'categoryFilter' => $this->categoryFilter,
            'featuredOnly'   => $this->featuredOnly,
            'total'          => $templates->count(),
        ];
    }

    /**
     * Wired from the marketplace card button — clones the picked
     * template's payload into the current tenant via
     * MarketplaceInstaller.  Surfaces a success or error toast.
     */
    public function install(int $templateId): void
    {
        $template = SharedTemplate::query()->published()->find($templateId);
        if (! $template) {
            Notification::make()->danger()->title(__('filament/marketplace.notif_not_found_title'))->send();
            return;
        }

        $tenant = auth()->user()?->tenant;
        if (! $tenant instanceof Tenant) {
            Notification::make()->danger()->title(__('filament/marketplace.notif_no_workspace_title'))->send();
            return;
        }

        if (! $template->isFree()) {
            Notification::make()
                ->warning()
                ->title(__('filament/marketplace.notif_paid_title'))
                ->body(__('filament/marketplace.notif_paid_body', ['price' => $template->price, 'currency' => $template->currency]))
                ->send();
            return;
        }

        $result = app(MarketplaceInstaller::class)->install($template, $tenant);

        if ($result['ok']) {
            Notification::make()
                ->success()
                ->title(__('filament/marketplace.notif_installed_title'))
                ->body(__('filament/marketplace.notif_installed_body', ['count' => $result['created'], 'type' => $result['type']]))
                ->persistent()
                ->send();
        } else {
            Notification::make()
                ->danger()
                ->title(__('filament/marketplace.notif_install_failed_title'))
                ->body($result['error'] ?? __('filament/marketplace.notif_install_unknown_error'))
                ->send();
        }
    }
}
