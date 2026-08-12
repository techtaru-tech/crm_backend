<?php

namespace App\Filament\SuperAdmin\Resources;

use App\Filament\SuperAdmin\Resources\UserResource\Pages;
use App\Models\User;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Toggle;
use Filament\Resources\Resource;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Schema;
use Filament\Actions\Action;
use Filament\Actions\EditAction;
use Filament\Tables\Columns\IconColumn;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Filters\SelectFilter;
use Filament\Tables\Table;
use UnitEnum;

class UserResource extends Resource
{
    protected static ?string $model = User::class;
    protected static string|\BackedEnum|null $navigationIcon = 'heroicon-o-users';
    protected static string|UnitEnum|null $navigationGroup = 'Users';

    public static function getModelLabel(): string
    {
        return __('filament/sa_users.model_label');
    }

    public static function getPluralModelLabel(): string
    {
        return __('filament/sa_users.plural_model_label');
    }

    public static function form(Schema $schema): Schema
    {
        return $schema->components([
            Section::make(__('sections.user_details'))->schema([
                TextInput::make('name')->required()->maxLength(255),
                TextInput::make('email')->email()->required()->unique(ignoreRecord: true)->maxLength(255),
                Select::make('tenant_id')
                    ->label(__('filament/sa_users.primary_workspace'))
                    ->relationship('tenant', 'name')
                    ->searchable()
                    ->preload()
                    ->nullable(),
                Toggle::make('is_super_admin')
                    ->label(__('filament/sa_users.super_admin'))
                    ->helperText(__('filament/sa_users.super_admin_helper')),
            ])->columns(2),
        ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('name')->label(__('filament/sa_users.name'))->searchable()->sortable(),
                TextColumn::make('email')->label(__('filament/sa_users.email'))->searchable()->sortable(),
                TextColumn::make('tenant.name')->label(__('filament/sa_users.workspace'))->searchable()->sortable(),
                IconColumn::make('is_super_admin')->label(__('filament/sa_users.sa'))->boolean(),
                TextColumn::make('suspended_at')
                    ->label(__('filament/sa_users.status'))
                    ->badge()
                    ->formatStateUsing(fn ($state) => $state ? __('filament/sa_users.status_suspended') : __('filament/sa_users.status_active'))
                    ->color(fn ($state) => $state ? 'danger' : 'success')
                    ->getStateUsing(fn (User $record) => $record->suspended_at),
                TextColumn::make('created_at')->label(__('filament/sa_users.created_at'))->dateTime()->sortable(),
            ])
            ->filters([
                SelectFilter::make('tenant_id')
                    ->label(__('filament/sa_users.workspace'))
                    ->relationship('tenant', 'name')
                    ->searchable()
                    ->preload(),
                SelectFilter::make('is_super_admin')
                    ->label(__('filament/sa_users.filter_role'))
                    ->options([
                        '1' => __('filament/sa_users.filter_role_super_admin'),
                        '0' => __('filament/sa_users.filter_role_regular_user'),
                    ]),
            ])
            ->actions([
                // Password reset — same flow as the tenant-admin
                // panel.  Goes through Password::broker()->
                // sendResetLink → notify() → our sync variant.
                Action::make('reset_password')
                    ->before(fn () => \App\Support\DemoMode::guard(__('filament/sa_users.reset_password_demo_guard')))
                    ->label(__('filament/sa_users.reset_password'))
                    ->icon('heroicon-o-key')
                    ->color('gray')
                    ->requiresConfirmation()
                    ->modalHeading(fn (User $record) => __('filament/sa_users.reset_password_modal_heading', ['email' => $record->email]))
                    ->modalDescription(__('filament/sa_users.reset_password_modal_description'))
                    ->action(function (User $record) {
                        try {
                            $status = \Illuminate\Support\Facades\Password::broker()
                                ->sendResetLink(['email' => $record->email]);
                            if ($status !== \Illuminate\Support\Facades\Password::RESET_LINK_SENT) {
                                throw new \RuntimeException(__('filament/sa_users.password_broker_rejected', ['status' => $status]));
                            }
                            \Filament\Notifications\Notification::make()
                                ->title(__('filament/sa_users.reset_link_sent_title'))
                                ->body(__('filament/sa_users.reset_link_sent_body', ['email' => $record->email]))
                                ->success()
                                ->send();
                        } catch (\Throwable $e) {
                            \Filament\Notifications\Notification::make()
                                ->title(__('filament/sa_users.reset_link_failed_title'))
                                // Defense-in-depth: e() on exception message.
                                ->body(e($e->getMessage()))
                                ->danger()
                                ->send();
                        }
                    }),

                // Suspend — SA can suspend ANY user, including
                // tenant admins and other super-admins (but not
                // themselves — guard on auth()->id()).
                Action::make('suspend')
                    ->before(fn () => \App\Support\DemoMode::guard(__('filament/sa_users.suspend_demo_guard')))
                    ->label(__('filament/sa_users.suspend'))
                    ->icon('heroicon-o-no-symbol')
                    ->color('warning')
                    ->requiresConfirmation()
                    ->modalHeading(fn (User $record) => __('filament/sa_users.suspend_modal_heading', ['name' => $record->name]))
                    ->modalDescription(__('filament/sa_users.suspend_modal_description'))
                    ->visible(fn (User $record) => ! $record->isSuspended() && $record->id !== auth()->id())
                    ->action(function (User $record) {
                        $record->forceFill(['suspended_at' => now()])->save();
                        $record->invalidateSessions();
                        \Filament\Notifications\Notification::make()
                            ->title(__('filament/sa_users.user_suspended_title'))
                            ->body(__('filament/sa_users.user_suspended_body', ['email' => $record->email]))
                            ->success()
                            ->send();
                    }),

                Action::make('unsuspend')
                    ->before(fn () => \App\Support\DemoMode::guard(__('filament/sa_users.unsuspend_demo_guard')))
                    ->label(__('filament/sa_users.unsuspend'))
                    ->icon('heroicon-o-check-badge')
                    ->color('success')
                    ->visible(fn (User $record) => $record->isSuspended())
                    ->action(function (User $record) {
                        $record->forceFill(['suspended_at' => null])->save();
                        \Filament\Notifications\Notification::make()
                            ->title(__('filament/sa_users.user_reactivated_title'))
                            ->body(__('filament/sa_users.user_reactivated_body', ['email' => $record->email]))
                            ->success()
                            ->send();
                    }),

                EditAction::make(),
            ]);
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListUsers::route('/'),
            'edit'  => Pages\EditUser::route('/{record}/edit'),
        ];
    }
}
