<?php

namespace App\Filament\Pages;

use Filament\Pages\Page;

class SubscriptionRequired extends Page
{
    protected string $view = 'filament.pages.subscription-required';
    protected static bool $shouldRegisterNavigation = false;
    protected static ?string $slug = 'subscription-required';

    public ?string $reason = null;
    public ?array $plans = null;
    public ?string $currentPlan = null;
    public ?string $statusLabel = null;

    public function mount(): void
    {
        $this->reason      = session('reason', 'inactive');
        $this->plans       = app(\App\Services\PlanService::class)->getAllPlans(publicOnly: true);
        $this->currentPlan = auth()->user()?->tenant?->plan;
        $this->statusLabel = auth()->user()?->tenant?->getStatusLabel();
    }

    public function getTitle(): string
    {
        return __('filament/subscription_required.title');
    }

    public function getHeading(): string
    {
        return match ($this->reason) {
            'trial_expired' => __('filament/subscription_required.heading_trial_expired'),
            'cancelled'     => __('filament/subscription_required.heading_cancelled'),
            'expired'       => __('filament/subscription_required.heading_expired'),
            'suspended'     => __('filament/subscription_required.heading_suspended'),
            default         => __('filament/subscription_required.heading_default'),
        };
    }

    public function getSubheading(): ?string
    {
        return match ($this->reason) {
            'trial_expired' => __('filament/subscription_required.subheading_trial_expired'),
            'cancelled'     => __('filament/subscription_required.subheading_cancelled'),
            'expired'       => __('filament/subscription_required.subheading_expired'),
            'suspended'     => __('filament/subscription_required.subheading_suspended'),
            default         => __('filament/subscription_required.subheading_default'),
        };
    }
}
