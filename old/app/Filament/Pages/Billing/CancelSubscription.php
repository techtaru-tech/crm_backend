<?php

declare(strict_types=1);

namespace App\Filament\Pages\Billing;

use App\Models\Tenant;
use App\Services\TenantCancellationService;
use Filament\Forms\Components\Radio;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Concerns\InteractsWithForms;
use Filament\Forms\Contracts\HasForms;
use Filament\Notifications\Notification;
use Filament\Pages\Page;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Schema;
use Illuminate\Contracts\Support\Htmlable;
use UnitEnum;

/**
 * Multi-section self-cancellation flow at /admin/billing/cancel.
 *
 * Three logical zones, all on one page (radios reveal the next zone
 * inline rather than a wizard, because the abandonment rate of multi-
 * page wizards is brutal — every transition loses 8-15% of users):
 *
 *   1. Reason picker (radios) + free-text feedback (textarea)
 *   2. Retention offer banner (revealed if reason ∈
 *      {too_expensive, not_using, missing_feature})
 *   3. Confirm cancellation button (red, separate from "Keep"
 *      outbound link)
 *
 * Audit log row gets the verbatim reason + feedback for the
 * SA-side retention dashboard.  TenantCancellationService preserves
 * paid-through access — user keeps the workspace alive until
 * subscription_ends_at, then it flips to expired by the lifecycle
 * cron.
 */
class CancelSubscription extends Page implements HasForms
{
    use InteractsWithForms;

    protected static bool $shouldRegisterNavigation = false;
    protected static ?string $slug = 'billing/cancel';
    protected string $view = 'filament.pages.billing.cancel-subscription';

    public ?array $data = [];

    public function getTitle(): string|Htmlable
    {
        return __('filament/cancel_subscription.title');
    }

    public function getHeading(): string
    {
        return __('filament/cancel_subscription.heading');
    }

    public function getSubheading(): ?string
    {
        return __('filament/cancel_subscription.subheading');
    }

    public static function canAccess(): bool
    {
        $user = auth()->user();
        if (! $user) return false;
        if ($user->is_super_admin ?? false) return true;
        $tenant = $user->tenant ?? null;
        if (! $tenant) return false;
        if ((int) $tenant->owner_id === (int) $user->id) return true;
        return method_exists($user, 'hasRole') && $user->hasRole('admin');
    }

    public function mount(): void
    {
        $this->form->fill([
            'reason'   => null,
            'feedback' => null,
        ]);
    }

    public function form(Schema $form): Schema
    {
        return $form
            ->schema([
                Section::make(__('sections.why_are_you_cancelling'))
                    ->description(__('filament/cancel_subscription.why_section_description'))
                    ->schema([
                        Radio::make('reason')
                            ->label(__('filament/cancel_subscription.field_reason_label'))
                            ->options(TenantCancellationService::reasons())
                            ->required()
                            ->live()
                            ->columns(1),

                        Textarea::make('feedback')
                            ->label(__('filament/cancel_subscription.feedback_label'))
                            ->rows(3)
                            ->maxLength(1000)
                            ->placeholder(__('filament/cancel_subscription.feedback_placeholder')),
                    ]),
            ])
            ->statePath('data');
    }

    /**
     * Whether to surface the retention offer based on the picked
     * reason.  Used by the blade to decide if the offer banner
     * renders — drives the actual conversion lever (15-30% of
     * cancellations get saved by a well-timed offer).
     */
    public function showsRetentionOffer(): bool
    {
        $reason = $this->data['reason'] ?? null;
        return in_array($reason, ['too_expensive', 'not_using', 'missing_feature'], true);
    }

    public function getRetentionOfferCopy(): array
    {
        return match ($this->data['reason'] ?? null) {
            'too_expensive' => [
                'title' => __('filament/cancel_subscription.retention_too_expensive_title'),
                'body'  => __('filament/cancel_subscription.retention_too_expensive_body'),
            ],
            'not_using' => [
                'title' => __('filament/cancel_subscription.retention_not_using_title'),
                'body'  => __('filament/cancel_subscription.retention_not_using_body'),
            ],
            'missing_feature' => [
                'title' => __('filament/cancel_subscription.retention_missing_feature_title'),
                'body'  => __('filament/cancel_subscription.retention_missing_feature_body'),
            ],
            default => [
                'title' => __('filament/cancel_subscription.retention_default_title'),
                'body'  => __('filament/cancel_subscription.retention_default_body'),
            ],
        };
    }

    /**
     * Submit the cancellation.  Routes through TenantCancellationService
     * which handles the audit, event dispatch, and access-cutoff logic.
     */
    public function cancel(): void
    {
        $state = $this->form->getState();

        $tenant = auth()->user()?->tenant;
        if (! $tenant instanceof Tenant) {
            Notification::make()->danger()->title(__('filament/cancel_subscription.no_workspace_title'))->send();
            return;
        }

        $cutoff = app(TenantCancellationService::class)->cancel(
            tenant: $tenant,
            reason: $state['reason'] ?? null,
            feedback: $state['feedback'] ?? null,
            actor: auth()->user(),
        );

        Notification::make()
            ->success()
            ->title(__('filament/cancel_subscription.cancelled_title'))
            ->body(__('filament/cancel_subscription.cancelled_body', ['date' => $cutoff->translatedFormat('M j, Y')]))
            ->persistent()
            ->send();

        $this->redirect('/admin/billing');
    }
}
