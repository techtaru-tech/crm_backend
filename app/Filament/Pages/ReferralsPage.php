<?php

namespace App\Filament\Pages;

use App\Filament\Concerns\PageRequiresPermission;

use App\Models\Tenant;
use Filament\Pages\Page;

class ReferralsPage extends Page
{
    use PageRequiresPermission;

    protected static string $requiredPermission = 'settings.manage';

    protected static string|\BackedEnum|null $navigationIcon  = 'heroicon-o-share';
    protected static string|\UnitEnum|null $navigationGroup = 'Settings';
    protected static ?int    $navigationSort  = 28;
    protected string $view = 'filament.pages.referrals-page';

    public static function getNavigationLabel(): string
    {
        return __('filament/referrals.nav_label');
    }

    public function getTitle(): string
    {
        return __('filament/referrals.title');
    }

    protected function tenant(): ?Tenant
    {
        return auth()->user()?->tenant;
    }

    public function getReferralLink(): string
    {
        $tenant = $this->tenant();
        if (! $tenant) {
            return '';
        }

        // Auto-heal: tenants created before the referral_code boot hook
        // was added can lack a code. Generate one the first time this
        // page renders so the feature "just works" without requiring
        // operator intervention.
        if (empty($tenant->referral_code)) {
            $code = strtoupper(\Illuminate\Support\Str::random(10));
            // Paranoia: retry if the random code collides.
            while (Tenant::where('referral_code', $code)->exists()) {
                $code = strtoupper(\Illuminate\Support\Str::random(10));
            }
            $tenant->forceFill(['referral_code' => $code])->saveQuietly();
        }

        return rtrim(config('app.url'), '/') . '/register?ref=' . $tenant->referral_code;
    }

    public function getStats(): array
    {
        $tenant = $this->tenant();
        if (! $tenant) {
            return [
                'referred_count' => 0,
                'trial_count'    => 0,
                'paying_count'   => 0,
            ];
        }

        $base = Tenant::where('referred_by_tenant_id', $tenant->id);

        return [
            'referred_count' => (clone $base)->count(),
            'trial_count'    => (clone $base)->where('subscription_status', 'trial')->count(),
            'paying_count'   => (clone $base)->where('subscription_status', 'active')->count(),
        ];
    }

    public function getReferredTenants()
    {
        $tenant = $this->tenant();
        if (! $tenant) {
            return collect();
        }

        return Tenant::where('referred_by_tenant_id', $tenant->id)
            ->orderByDesc('created_at')
            ->get();
    }
}
