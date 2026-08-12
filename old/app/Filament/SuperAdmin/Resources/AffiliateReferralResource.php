<?php

declare(strict_types=1);

namespace App\Filament\SuperAdmin\Resources;

use App\Filament\SuperAdmin\Resources\AffiliateReferralResource\Pages;
use App\Models\AffiliateReferral;
use App\Support\DemoMode;
use Filament\Actions\Action;
use Filament\Actions\BulkAction;
use Filament\Actions\BulkActionGroup;
use Filament\Notifications\Notification;
use Filament\Resources\Resource;
use Filament\Schemas\Schema;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Filters\SelectFilter;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Collection;
use UnitEnum;

/**
 * Super Admin payout console for the affiliate / referral program.
 *
 * Commission rows are booked automatically by the payment gateways
 * on each recurring payment of a referred tenant (see
 * AbstractGateway + the per-gateway commission blocks) — this
 * resource never creates them.  It exists so the operator can:
 *
 *   - see every booked commission, filtered by status
 *   - approve pending commissions (pending → approved)
 *   - record payouts (approved → paid, stamps paid_at)
 *   - reverse a commission when the underlying revenue is refunded
 *
 * The commission rate itself is set on the Billing Settings page
 * (BillingSettings::affiliate_commission_percent).
 */
class AffiliateReferralResource extends Resource
{
    protected static ?string $model = AffiliateReferral::class;
    protected static string|\BackedEnum|null $navigationIcon = 'heroicon-o-user-plus';
    protected static string|UnitEnum|null $navigationGroup = 'Billing';
    protected static ?int $navigationSort = 35;

    public static function getNavigationLabel(): string
    {
        return __('filament/sa_affiliate_referrals.nav_label');
    }

    public static function getModelLabel(): string
    {
        return __('filament/sa_affiliate_referrals.model_label');
    }

    public static function getPluralModelLabel(): string
    {
        return __('filament/sa_affiliate_referrals.plural_model_label');
    }

    /**
     * Commission rows are gateway-booked, never hand-created — hide
     * the Create button entirely.
     */
    public static function canCreate(): bool
    {
        return false;
    }

    public static function getEloquentQuery(): Builder
    {
        return parent::getEloquentQuery()->with(['referrer', 'referred']);
    }

    /**
     * Read-only resource: there is no create/edit form.  The only
     * mutations are the status row/bulk actions on the table.  An
     * empty schema is returned so the abstract contract is satisfied.
     */
    public static function form(Schema $schema): Schema
    {
        return $schema->components([]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->defaultSort('created_at', 'desc')
            ->columns([
                TextColumn::make('referrer.name')
                    ->label(__('filament/sa_affiliate_referrals.col_affiliate'))
                    ->searchable()
                    ->placeholder('—'),

                TextColumn::make('referred.name')
                    ->label(__('filament/sa_affiliate_referrals.col_referred'))
                    ->searchable()
                    ->placeholder('—'),

                TextColumn::make('plan_key')
                    ->label(__('filament/sa_affiliate_referrals.col_plan'))
                    ->badge(),

                TextColumn::make('commission_amount')
                    ->label(__('filament/sa_affiliate_referrals.col_commission'))
                    ->sortable()
                    ->weight('bold')
                    ->formatStateUsing(fn ($state, AffiliateReferral $record): string =>
                        number_format((float) $state, 2) . ' ' . strtoupper((string) $record->currency)),

                TextColumn::make('commission_percent')
                    ->label(__('filament/sa_affiliate_referrals.col_rate'))
                    ->formatStateUsing(fn ($state): string => number_format((float) $state, 1) . '%'),

                TextColumn::make('commission_status')
                    ->label(__('filament/sa_affiliate_referrals.col_status'))
                    ->badge()
                    ->formatStateUsing(fn (?string $state): string =>
                        AffiliateReferral::statusLabels()[$state] ?? (string) $state)
                    ->color(fn (?string $state): string => match ($state) {
                        AffiliateReferral::STATUS_PENDING  => 'warning',
                        AffiliateReferral::STATUS_APPROVED => 'info',
                        AffiliateReferral::STATUS_PAID     => 'success',
                        AffiliateReferral::STATUS_REVERSED => 'gray',
                        default                            => 'gray',
                    }),

                TextColumn::make('created_at')
                    ->label(__('filament/sa_affiliate_referrals.col_booked'))
                    ->dateTime()
                    ->sortable(),

                TextColumn::make('paid_at')
                    ->label(__('filament/sa_affiliate_referrals.col_paid_at'))
                    ->dateTime()
                    ->placeholder('—')
                    ->sortable(),
            ])
            ->filters([
                SelectFilter::make('commission_status')
                    ->label(__('filament/sa_affiliate_referrals.filter_status'))
                    ->options(AffiliateReferral::statusLabels()),
            ])
            ->actions([
                Action::make('approve')
                    ->label(__('filament/sa_affiliate_referrals.action_approve'))
                    ->icon('heroicon-o-check-circle')
                    ->color('info')
                    ->requiresConfirmation()
                    ->visible(fn (AffiliateReferral $record): bool =>
                        $record->commission_status === AffiliateReferral::STATUS_PENDING)
                    ->before(fn () => DemoMode::guard())
                    ->action(function (AffiliateReferral $record): void {
                        $record->update(['commission_status' => AffiliateReferral::STATUS_APPROVED]);
                        Notification::make()
                            ->title(__('filament/sa_affiliate_referrals.notify_approved'))
                            ->success()
                            ->send();
                    }),

                Action::make('markPaid')
                    ->label(__('filament/sa_affiliate_referrals.action_mark_paid'))
                    ->icon('heroicon-o-banknotes')
                    ->color('success')
                    ->requiresConfirmation()
                    ->visible(fn (AffiliateReferral $record): bool =>
                        $record->commission_status === AffiliateReferral::STATUS_APPROVED)
                    ->before(fn () => DemoMode::guard())
                    ->action(function (AffiliateReferral $record): void {
                        $record->update([
                            'commission_status' => AffiliateReferral::STATUS_PAID,
                            'paid_at'           => now(),
                        ]);
                        Notification::make()
                            ->title(__('filament/sa_affiliate_referrals.notify_paid'))
                            ->success()
                            ->send();
                    }),

                Action::make('reverse')
                    ->label(__('filament/sa_affiliate_referrals.action_reverse'))
                    ->icon('heroicon-o-arrow-uturn-left')
                    ->color('danger')
                    ->requiresConfirmation()
                    ->visible(fn (AffiliateReferral $record): bool => in_array(
                        $record->commission_status,
                        [AffiliateReferral::STATUS_PENDING, AffiliateReferral::STATUS_APPROVED],
                        true,
                    ))
                    ->before(fn () => DemoMode::guard())
                    ->action(function (AffiliateReferral $record): void {
                        $record->update(['commission_status' => AffiliateReferral::STATUS_REVERSED]);
                        Notification::make()
                            ->title(__('filament/sa_affiliate_referrals.notify_reversed'))
                            ->warning()
                            ->send();
                    }),
            ])
            ->bulkActions([
                BulkActionGroup::make([
                    BulkAction::make('approveSelected')
                        ->label(__('filament/sa_affiliate_referrals.bulk_approve'))
                        ->icon('heroicon-o-check-circle')
                        ->requiresConfirmation()
                        ->before(fn () => DemoMode::guard())
                        ->action(function (Collection $records): void {
                            $count = 0;
                            foreach ($records as $record) {
                                if ($record->commission_status === AffiliateReferral::STATUS_PENDING) {
                                    $record->update(['commission_status' => AffiliateReferral::STATUS_APPROVED]);
                                    $count++;
                                }
                            }
                            Notification::make()
                                ->title(__('filament/sa_affiliate_referrals.notify_bulk_approved', ['count' => $count]))
                                ->success()
                                ->send();
                        })
                        ->deselectRecordsAfterCompletion(),

                    BulkAction::make('markPaidSelected')
                        ->label(__('filament/sa_affiliate_referrals.bulk_mark_paid'))
                        ->icon('heroicon-o-banknotes')
                        ->requiresConfirmation()
                        ->before(fn () => DemoMode::guard())
                        ->action(function (Collection $records): void {
                            $count = 0;
                            foreach ($records as $record) {
                                if ($record->commission_status === AffiliateReferral::STATUS_APPROVED) {
                                    $record->update([
                                        'commission_status' => AffiliateReferral::STATUS_PAID,
                                        'paid_at'           => now(),
                                    ]);
                                    $count++;
                                }
                            }
                            Notification::make()
                                ->title(__('filament/sa_affiliate_referrals.notify_bulk_paid', ['count' => $count]))
                                ->success()
                                ->send();
                        })
                        ->deselectRecordsAfterCompletion(),
                ]),
            ])
            ->emptyStateHeading(__('filament/sa_affiliate_referrals.empty_heading'))
            ->emptyStateDescription(__('filament/sa_affiliate_referrals.empty_description'))
            ->emptyStateIcon('heroicon-o-user-plus');
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListAffiliateReferrals::route('/'),
        ];
    }
}
