<?php

namespace App\Filament\Resources;

use App\Filament\Resources\UserResource\Pages;
use App\Models\User;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Toggle;
use Filament\Resources\Resource;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Schema;
use Filament\Actions\Action;
use Filament\Actions\DeleteAction;
use Filament\Actions\EditAction;
use App\Models\MeetingType;
use Illuminate\Support\HtmlString;
use Filament\Tables\Columns\IconColumn;
use Filament\Tables\Columns\ImageColumn;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use UnitEnum;

class UserResource extends Resource
{
    protected static ?string $model = User::class;
    protected static string|\BackedEnum|null $navigationIcon = 'heroicon-o-users';
    protected static string|UnitEnum|null $navigationGroup = 'Settings';
    protected static ?int $navigationSort = 10;

    public static function getNavigationLabel(): string
    {
        return __('filament/users.nav_label');
    }

    /**
     * Method-form model-label overrides.  Replace the static $modelLabel
     * property so the locale resolves at request time (the translator
     * is not yet bound at class-load time for the active session locale).
     */
    public static function getModelLabel(): string
    {
        return __('filament/users.model_label');
    }

    public static function getPluralModelLabel(): string
    {
        return __('filament/users.plural_model_label');
    }

    // Nav entry hidden — Team Members live under the consolidated
    // Team & Seats page (app/Filament/Pages/Settings/TeamSettingsPage).
    // The Resource routes stay registered so Filament can still
    // open /admin/users/create for the invite form and
    // /admin/users/{id}/edit for deep edits, but nothing in the
    // sidebar points at /admin/users directly.
    protected static bool $shouldRegisterNavigation = false;

    public static function form(Schema $schema): Schema
    {
        return $schema->components([
            Section::make(__('sections.user_details'))->schema([
                // Name + 2FA + password are edit-only fields.  On
                // create we delegate to the invitation flow (see
                // CreateUser::create) — the invitee chooses their
                // own display name and password when they accept
                // the email invite, matching the onboarding-wizard
                // team-invite flow.  Admins only need the invitee's
                // email and the role(s) they should hold.
                TextInput::make('name')
                    ->label(__('filament/users.name'))
                    ->required()
                    ->maxLength(100)
                    ->hiddenOn('create'),

                TextInput::make('email')
                    ->label(__('filament/users.email'))
                    ->email()
                    ->required()
                    ->unique(ignoreRecord: true),

                TextInput::make('password')
                    ->label(__('filament/users.password'))
                    ->password()
                    ->dehydrateStateUsing(fn ($state) => $state ? bcrypt($state) : null)
                    ->dehydrated(fn ($state) => filled($state))
                    ->helperText(__('filament/users.password_helper'))
                    ->hiddenOn('create'),

                Select::make('roles')
                    ->label(__('filament/users.role'))
                    // Expose only the two-tier team vocabulary
                    // (Manager / Member) to tenant admins.  Legacy
                    // roles (agent, viewer) stay in the DB for
                    // existing tenants that already assigned them,
                    // but new assignments funnel through these two.
                    // super_admin + admin are platform-level and
                    // cannot be granted from the tenant /admin panel.
                    ->relationship(
                        'roles',
                        'name',
                        modifyQueryUsing: fn ($query) => $query->whereIn('name', ['manager', 'member']),
                    )
                    ->multiple()
                    ->preload(),

                Toggle::make('two_factor_enabled')
                    ->label(__('filament/users.two_factor_enabled'))
                    ->disabled()
                    ->hiddenOn('create'),
            ])->columns(2),
        ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                ImageColumn::make('avatar_url')
                    ->label(__('filament/users.avatar'))
                    ->circular()
                    ->width(40)
                    ->height(40),

                TextColumn::make('name')
                    ->label(__('filament/users.name'))
                    ->searchable()
                    ->sortable(),

                TextColumn::make('email')
                    ->label(__('filament/users.email'))
                    ->searchable()
                    ->sortable(),

                TextColumn::make('roles.name')
                    ->label(__('filament/users.role'))
                    ->badge()
                    ->color(fn (string $state) => match ($state) {
                        'super_admin' => 'danger',
                        'admin'       => 'warning',
                        'manager'     => 'info',
                        'member'      => 'success',
                        default       => 'gray',
                    }),

                IconColumn::make('two_factor_enabled')
                    ->label(__('filament/users.two_factor_short'))
                    ->boolean(),

                TextColumn::make('suspended_at')
                    ->label(__('filament/users.status'))
                    ->badge()
                    ->formatStateUsing(fn ($state) => $state ? __('filament/users.status_suspended') : __('filament/users.status_active'))
                    ->color(fn ($state) => $state ? 'danger' : 'success')
                    ->getStateUsing(fn (User $record) => $record->suspended_at),

                TextColumn::make('created_at')
                    ->label(__('filament/users.created_at'))
                    ->dateTime()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
            ])
            ->filters([])
            ->actions([
                Action::make('meeting_link')
                    ->label(__('filament/users.action_meeting_link'))
                    ->icon('heroicon-o-calendar-days')
                    ->color('gray')
                    // Tenant-scoped: only show meeting types that belong
                    // to the same tenant as the user being viewed.
                    ->visible(fn(User $record) => MeetingType::query()
                        ->where('tenant_id', $record->tenant_id)
                        ->where('user_id', $record->id)
                        ->where('is_active', true)
                        ->exists())
                    ->modalHeading(fn(User $record) => $record->name . __('filament/users.booking_links_suffix'))
                    ->modalSubmitAction(false)
                    ->modalCancelActionLabel(__('filament/users.modal_close'))
                    ->modalContent(function (User $record): HtmlString {
                        $types = MeetingType::query()
                            ->where('tenant_id', $record->tenant_id)
                            ->where('user_id', $record->id)
                            ->where('is_active', true)
                            ->get();
                        $minutesSuffix = e(__('filament/users.booking_links_minutes_suffix'));
                        $html = '<ul class="space-y-2">';
                        foreach ($types as $type) {
                            $html .= '<li><strong>' . e($type->name) . '</strong> &middot; ' . (int) $type->duration_minutes . ' ' . $minutesSuffix . '<br>
                                <a class="text-primary-600 underline break-all" href="' . e($type->booking_url) . '" target="_blank">' . e($type->booking_url) . '</a></li>';
                        }
                        $html .= '</ul>';
                        return new HtmlString($html);
                    }),
                // Trigger a password-reset email using the same
                // plumbing Filament's "Forgot password" link uses.
                // This goes through User::sendPasswordResetNotification
                // which we overrode to use Mail::send (synchronous),
                // so the message actually leaves the server on
                // shared hosting with no queue worker.
                Action::make('reset_password')
                    ->label(__('filament/users.action_send_password_reset'))
                    ->icon('heroicon-o-key')
                    ->color('gray')
                    ->requiresConfirmation()
                    ->modalHeading(fn (User $record) => __('filament/users.reset_modal_heading_prefix') . $record->email . __('filament/users.reset_modal_heading_suffix'))
                    ->modalDescription(__('filament/users.reset_modal_description'))
                    ->visible(fn () => static::currentUserIsAtLeastManager())
                    ->action(function (User $record) {
                        try {
                            $status = \Illuminate\Support\Facades\Password::broker()
                                ->sendResetLink(['email' => $record->email]);
                            if ($status !== \Illuminate\Support\Facades\Password::RESET_LINK_SENT) {
                                throw new \RuntimeException(__('services/invitation.password_broker_rejected', ['status' => $status]));
                            }
                            \Filament\Notifications\Notification::make()
                                ->title(__('filament/users.reset_sent_title'))
                                ->body(__('filament/users.reset_sent_body_prefix') . $record->email . __('filament/users.reset_sent_body_suffix'))
                                ->success()
                                ->send();
                        } catch (\Throwable $e) {
                            \Filament\Notifications\Notification::make()
                                ->title(__('filament/users.reset_failed_title'))
                                // Defense-in-depth: e() on exception message.
                                ->body(e($e->getMessage()))
                                ->danger()
                                ->send();
                        }
                    }),

                // Suspend — stamp suspended_at.  The login flow
                // rejects suspended users with a branded message
                // before even checking the password.
                Action::make('suspend')
                    ->label(__('filament/users.action_suspend'))
                    ->icon('heroicon-o-no-symbol')
                    ->color('warning')
                    ->requiresConfirmation()
                    ->modalHeading(fn (User $record) => __('filament/users.suspend_modal_heading_prefix') . $record->name . __('filament/users.suspend_modal_heading_suffix'))
                    ->modalDescription(__('filament/users.suspend_modal_description'))
                    ->visible(fn (User $record) => static::currentUserIsAtLeastManager() && ! $record->isSuspended())
                    ->action(function (User $record) {
                        $record->forceFill(['suspended_at' => now()])->save();
                        // Kill any in-flight sessions so the
                        // suspension is immediate, not whenever
                        // their cookie next expires.
                        $record->invalidateSessions();
                        \Filament\Notifications\Notification::make()
                            ->title(__('filament/users.suspend_notification_title'))
                            ->body($record->name . __('filament/users.suspend_notification_body_suffix'))
                            ->success()
                            ->send();
                    }),

                Action::make('unsuspend')
                    ->label(__('filament/users.action_unsuspend'))
                    ->icon('heroicon-o-check-badge')
                    ->color('success')
                    ->visible(fn (User $record) => static::currentUserIsAtLeastManager() && $record->isSuspended())
                    ->action(function (User $record) {
                        $record->forceFill(['suspended_at' => null])->save();
                        \Filament\Notifications\Notification::make()
                            ->title(__('filament/users.unsuspend_notification_title'))
                            ->body($record->name . __('filament/users.unsuspend_notification_body_suffix'))
                            ->success()
                            ->send();
                    }),

                EditAction::make(),
                DeleteAction::make(),
            ]);
    }

    public static function getEloquentQuery(): Builder
    {
        return parent::getEloquentQuery()
            ->where('tenant_id', auth()->user()?->tenant_id)
            ->excludingSuperAdmins();
    }

    /*
    |--------------------------------------------------------------------------
    | Filament permission gates
    |--------------------------------------------------------------------------
    | Each gate checks, in order: super-admin bypass, Spatie role/
    | permission, user_tenants pivot role fallback.  The pivot
    | fallback matters because tenants created before the SA-panel
    | fix that added $user->syncRoles(['admin']) have an owner whose
    | Spatie role set is empty — only the user_tenants.role='admin'
    | pivot says they're the owner.  Without this fallback every
    | existing tenant owner would get a 403 on /admin/users/create.
    */
    protected static function currentUserIsAdmin(): bool
    {
        $u = auth()->user();
        if (! $u) return false;
        if ($u->is_super_admin) return true;
        if ($u->hasRole('admin')) return true;
        return static::pivotRoleFor($u) === 'admin';
    }

    protected static function currentUserIsAtLeastManager(): bool
    {
        $u = auth()->user();
        if (! $u) return false;
        if ($u->is_super_admin) return true;
        if ($u->hasRole('admin') || $u->hasRole('manager')) return true;
        return in_array(static::pivotRoleFor($u), ['admin', 'manager']);
    }

    protected static function pivotRoleFor(\App\Models\User $u): ?string
    {
        if (! $u->tenant_id) return null;
        try {
            $row = $u->tenants()->where('tenants.id', $u->tenant_id)->first();
            return $row?->pivot?->role;
        } catch (\Throwable) {
            return null;
        }
    }

    public static function canViewAny(): bool
    {
        return static::currentUserIsAtLeastManager();
    }

    public static function canCreate(): bool
    {
        return static::currentUserIsAtLeastManager();
    }

    public static function canEdit($record): bool
    {
        return static::currentUserIsAtLeastManager();
    }

    public static function canDelete($record): bool
    {
        $u = auth()->user();
        if (! $u) return false;

        // Never let a non-admin delete an admin user.  Guard by
        // role AND by pivot so a compromised manager token can't
        // wipe the workspace owner even if the role wasn't synced.
        if ($record) {
            $targetIsAdmin = $record->hasRole('admin') || $record->tenants()
                ->where('tenants.id', $record->tenant_id)
                ->where('tenant_users.role', 'admin')
                ->exists();
            if ($targetIsAdmin && ! static::currentUserIsAdmin()) {
                return false;
            }
        }

        return static::currentUserIsAtLeastManager();
    }

    public static function getPages(): array
    {
        return [
            'index'  => Pages\ListUsers::route('/'),
            'create' => Pages\CreateUser::route('/create'),
            'edit'   => Pages\EditUser::route('/{record}/edit'),
        ];
    }
}
