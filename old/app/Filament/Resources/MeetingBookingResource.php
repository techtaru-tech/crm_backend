<?php

namespace App\Filament\Resources;

use App\Filament\Concerns\HasRolePermissions;
use App\Filament\Resources\MeetingBookingResource\Pages;
use App\Mail\MeetingCancelledMail;
use App\Models\MeetingBooking;
use App\Models\MeetingType;
use Filament\Forms\Components\Textarea;
use Filament\Notifications\Notification;
use Filament\Resources\Resource;
use Filament\Actions\Action;
use Filament\Actions\ViewAction;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Filters\Filter;
use Filament\Tables\Filters\SelectFilter;
use Filament\Tables\Table;
use Filament\Forms\Components\DatePicker;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Facades\Mail;
use UnitEnum;

class MeetingBookingResource extends Resource
{
    use HasRolePermissions;
    protected static string $permissionPrefix = 'leads';
    protected static ?string $model = MeetingBooking::class;
    protected static string|\BackedEnum|null $navigationIcon = 'heroicon-o-calendar';
    protected static string|UnitEnum|null $navigationGroup = 'Tools';
    protected static ?int $navigationSort = 16;

    public static function getNavigationLabel(): string
    {
        return __('filament/meeting_bookings.nav_label');
    }

    /**
     * Method-form model-label overrides.  Replace the static $modelLabel
     * property so the locale resolves at request time (the translator
     * is not yet bound at class-load time for the active session locale).
     */
    public static function getModelLabel(): string
    {
        return __('filament/meeting_bookings.model_label');
    }

    public static function getPluralModelLabel(): string
    {
        return __('filament/meeting_bookings.plural_model_label');
    }

    public static function getEloquentQuery(): Builder
    {
        $tenantId = \App\Support\TenantContext::currentId();
        return parent::getEloquentQuery()->where('tenant_id', $tenantId);
    }

    public static function table(Table $table): Table
    {
        $tenantId = \App\Support\TenantContext::currentId();

        return $table
            ->columns([
                TextColumn::make('guest_name')->label(__('filament/meeting_bookings.guest_name'))->searchable()->sortable(),
                TextColumn::make('guest_email')->label(__('filament/meeting_bookings.guest_email'))->searchable()->copyable(),
                TextColumn::make('meetingType.name')->label(__('filament/meeting_bookings.meeting_type'))->sortable(),
                TextColumn::make('user.name')->label(__('filament/meeting_bookings.host'))->sortable(),
                TextColumn::make('starts_at')
                    ->label(__('filament/meeting_bookings.starts'))
                    ->formatStateUsing(fn ($state) => $state?->translatedFormat('M j, Y g:i A'))
                    ->sortable(),
                TextColumn::make('status')
                    ->label(__('filament/meeting_bookings.status'))
                    ->badge()
                    ->colors([
                        'success' => 'confirmed',
                        'danger'  => 'cancelled',
                        'info'    => 'completed',
                        'gray'    => 'no_show',
                    ])
                    ->formatStateUsing(fn (?string $state): string => match ($state) {
                        'confirmed' => __('filament/meeting_bookings.status_confirmed'),
                        'cancelled' => __('filament/meeting_bookings.status_cancelled'),
                        'completed' => __('filament/meeting_bookings.status_completed'),
                        'no_show'   => __('filament/meeting_bookings.status_no_show'),
                        default     => (string) $state,
                    }),
                TextColumn::make('lead.full_name')
                    ->label(__('filament/meeting_bookings.lead'))
                    ->default('—')
                    ->url(fn($record) => $record->lead_id ? route('filament.admin.resources.leads.edit', $record->lead_id) : null),
            ])
            ->filters([
                SelectFilter::make('status')
                    ->label(__('filament/meeting_bookings.filter_label_status'))
                    ->options([
                        'confirmed' => __('filament/meeting_bookings.status_confirmed'),
                        'cancelled' => __('filament/meeting_bookings.status_cancelled'),
                        'completed' => __('filament/meeting_bookings.status_completed'),
                        'no_show'   => __('filament/meeting_bookings.status_no_show'),
                    ]),
                SelectFilter::make('meeting_type_id')
                    ->label(__('filament/meeting_bookings.meeting_type'))
                    ->options(fn() => MeetingType::where('tenant_id', $tenantId)->pluck('name', 'id')),
                Filter::make('starts_at')
                    ->schema([
                        DatePicker::make('from')->label(__('filament/meeting_bookings.filter_from')),
                        DatePicker::make('until')->label(__('filament/meeting_bookings.filter_until')),
                    ])
                    ->query(function (Builder $query, array $data): Builder {
                        return $query
                            ->when($data['from'] ?? null, fn($q, $d) => $q->whereDate('starts_at', '>=', $d))
                            ->when($data['until'] ?? null, fn($q, $d) => $q->whereDate('starts_at', '<=', $d));
                    }),
            ])
            ->actions([
                ViewAction::make(),
                Action::make('cancel')
                    ->label(__('filament/meeting_bookings.action_cancel'))
                    ->icon('heroicon-o-x-circle')
                    ->color('danger')
                    ->requiresConfirmation()
                    ->visible(fn($record) => $record->status === 'confirmed')
                    ->schema([
                        Textarea::make('cancellation_reason')->label(__('filament/meeting_bookings.cancellation_reason'))->rows(2),
                    ])
                    ->action(function (MeetingBooking $record, array $data) {
                        $record->update([
                            'status'              => 'cancelled',
                            'cancelled_at'        => now(),
                            'cancellation_reason' => $data['cancellation_reason'] ?? null,
                        ]);
                        try {
                            Mail::to($record->guest_email)->send(new MeetingCancelledMail($record));
                        } catch (\Throwable $e) {
                            report($e);
                        }
                        Notification::make()->title(__('notifications.booking_cancelled'))->success()->send();
                    }),
                Action::make('mark_completed')
                    ->label(__('filament/meeting_bookings.action_mark_completed'))
                    ->icon('heroicon-o-check-circle')
                    ->color('success')
                    ->visible(fn($record) => $record->status === 'confirmed')
                    ->action(function (MeetingBooking $record) {
                        $record->update(['status' => 'completed']);
                        Notification::make()->title(__('notifications.booking_completed'))->success()->send();
                    }),
                Action::make('mark_no_show')
                    ->label(__('filament/meeting_bookings.action_mark_no_show'))
                    ->icon('heroicon-o-no-symbol')
                    ->color('warning')
                    ->visible(fn($record) => $record->status === 'confirmed')
                    ->action(function (MeetingBooking $record) {
                        $record->update(['status' => 'no_show']);
                        Notification::make()->title(__('notifications.booking_no_show'))->success()->send();
                    }),
            ])
            ->defaultSort('starts_at', 'desc');
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListMeetingBookings::route('/'),
            'view'  => Pages\ViewMeetingBooking::route('/{record}'),
        ];
    }
}
