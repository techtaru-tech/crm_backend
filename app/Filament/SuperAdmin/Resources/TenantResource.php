<?php

declare(strict_types=1);

namespace App\Filament\SuperAdmin\Resources;

use App\Filament\SuperAdmin\Resources\TenantResource\Pages;
use App\Mail\WorkspaceSuspendedMail;
use App\Models\AuditLog;
use App\Models\Tenant;
use App\Services\PlanService;
use App\Services\TenantSubscriptionState;
use Filament\Actions\Action;
use Filament\Actions\EditAction;
use Filament\Forms\Components\DateTimePicker;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Textarea;
use Filament\Notifications\Notification;
use Filament\Resources\Resource;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Components\Utilities\Get;
use Filament\Schemas\Components\Utilities\Set;
use Filament\Schemas\Schema;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Filters\SelectFilter;
use Filament\Tables\Table;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Mail;
use UnitEnum;

class TenantResource extends Resource
{
    protected static ?string $model = Tenant::class;
    protected static string|\BackedEnum|null $navigationIcon = 'heroicon-o-building-office-2';
    protected static string|UnitEnum|null $navigationGroup = 'Tenants';
    public static function getLabel(): string
    {
        return __('filament/sa_tenants.workspace');
    }

    public static function getPluralLabel(): string
    {
        return __('filament/sa_tenants.workspaces');
    }

    public static function getModelLabel(): string
    {
        return __('filament/sa_tenants.model_label');
    }

    public static function getPluralModelLabel(): string
    {
        return __('filament/sa_tenants.plural_model_label');
    }

    public static function form(Schema $schema): Schema
    {
        // 12-column grid at the schema root (not wrapped in a nested
        // Grid::make).  Reason: a nested Grid ends up rendered as a
        // fi-grid-col inside the root schema, which Filament 4 gives
        // `--col-span-default: span 1 / span 1`.  Without an lg
        // override of its own, the nested Grid is capped at whatever
        // column count its parent exposes.  Declaring columns directly
        // on the schema root makes the Sections siblings of the grid,
        // so their columnSpan values actually drive layout.
        return $schema->columns([
            'default' => 1,
            'lg'      => 12,
        ])->components([
                Section::make(__('sections.workspace_details'))
                    ->columnSpan(['default' => 'full', 'lg' => 8])
                    ->schema([
                        TextInput::make('name')
                            ->required()
                            ->maxLength(255)
                            ->live(onBlur: true)
                            ->afterStateUpdated(function (?string $state, Set $set, Get $get) {
                                // Auto-fill slug + subdomain from name.
                                // Existing values preserved so re-editing
                                // the name won't overwrite a hand-tuned slug.
                                if (! $state) return;
                                if (empty($get('slug'))) {
                                    $set('slug', \Illuminate\Support\Str::slug($state));
                                }
                                if (empty($get('subdomain'))) {
                                    $set('subdomain', \Illuminate\Support\Str::slug($state));
                                }
                            })
                            ->columnSpanFull(),

                        TextInput::make('slug')
                            ->label(__('filament/sa_tenants.slug'))
                            ->required()
                            ->alphaDash()
                            ->unique(ignoreRecord: true)
                            ->rules([
                                function () {
                                    return function (string $attribute, $value, \Closure $fail) {
                                        if (\App\Support\ReservedSlugs::isReserved((string) $value)) {
                                            $fail(__('filament/sa_tenants.reserved_slug_error', ['value' => (string) $value]));
                                        }
                                    };
                                },
                            ])
                            ->prefix(rtrim(config('app.url'), '/') . '/')
                            ->helperText(__('filament/sa_tenants.workspace_url_helper', ['url' => rtrim(config('app.url'), '/') . '/{slug}']))
                            ->columnSpanFull(),

                        TextInput::make('max_seats')
                            ->label(__('filament/sa_tenants.max_seats'))
                            ->numeric()
                            ->default(5)
                            ->minValue(1)
                            ->maxValue(10000)
                            ->helperText(__('filament/sa_tenants.max_seats_helper')),

                        // Plan select drives trial_ends_at on create via
                        // the reactive afterStateUpdated callback.
                        // PlanService::resolveTrialDays handles the per-
                        // plan → BillingSettings → config → 14-day
                        // fallback chain.
                        Select::make('plan')
                            ->label(__('filament/sa_tenants.plan'))
                            ->options(fn () => \App\Models\Plan::query()
                                ->orderBy('sort_order')
                                ->pluck('name', 'key')
                                ->all())
                            ->searchable()
                            ->default('trial')
                            ->required()
                            ->live()
                            ->afterStateUpdated(function (?string $state, Set $set, Get $get) {
                                // Only auto-fill trial_ends_at when status
                                // is trial AND the date is empty.  Don't
                                // clobber a manually-set date or an
                                // existing tenant's trial when SA changes
                                // the plan post-creation.
                                if (! $state) return;
                                if ($get('subscription_status') !== 'trial') return;
                                if (! empty($get('trial_ends_at'))) return;

                                $days = app(PlanService::class)->resolveTrialDays($state);
                                $set('trial_ends_at', now()->addDays($days)->toDateTimeString());
                            }),

                        Select::make('subscription_status')
                            ->label(__('filament/sa_tenants.subscription_status'))
                            ->options([
                                'trial'         => __('filament/sa_tenants.status_trial'),
                                'trial_expired' => __('filament/sa_tenants.status_trial_expired'),
                                'active'        => __('filament/sa_tenants.status_active_paid'),
                                'cancelled'     => __('filament/sa_tenants.status_cancelled'),
                                'expired'       => __('filament/sa_tenants.status_expired'),
                            ])
                            ->default('trial')
                            ->required()
                            ->live()
                            ->helperText(__('filament/sa_tenants.subscription_status_helper')),

                        DateTimePicker::make('trial_ends_at')
                            ->label(__('filament/sa_tenants.trial_ends_at_label'))
                            ->seconds(false)
                            ->visible(fn (Get $get) => in_array(
                                $get('subscription_status'),
                                ['trial', 'trial_expired'],
                                true,
                            ))
                            ->helperText(__('filament/sa_tenants.trial_ends_at_helper')),

                        DateTimePicker::make('subscription_ends_at')
                            ->label(__('filament/sa_tenants.subscription_ends_at_label'))
                            ->seconds(false)
                            ->visible(fn (Get $get) => in_array(
                                $get('subscription_status'),
                                ['active', 'cancelled', 'expired'],
                                true,
                            ))
                            ->helperText(__('filament/sa_tenants.subscription_ends_at_helper')),
                    ])->columns(2),

                // SA-only: visible during the create-tenant flow so
                // the operator can name the workspace owner in one
                // step.  On edit, suspend/reactivate/impersonate
                // happen via the page header actions, not this form.
                Section::make(__('sections.tenant_admin'))
                    ->description(__('filament/sa_tenants.tenant_admin_description'))
                    ->columnSpan(['default' => 'full', 'lg' => 4])
                    ->schema([
                        TextInput::make('admin_name')
                            ->label(__('filament/sa_tenants.admin_name'))
                            ->required()
                            ->maxLength(255),

                        TextInput::make('admin_email')
                            ->label(__('filament/sa_tenants.admin_email'))
                            ->email()
                            ->required()
                            ->maxLength(255),

                        Select::make('admin_password_mode')
                            ->label(__('filament/sa_tenants.admin_password_mode'))
                            ->options([
                                'email_setup_link' => __('filament/sa_tenants.admin_password_mode_email_link'),
                                'generate'         => __('filament/sa_tenants.admin_password_mode_generate'),
                                'manual'           => __('filament/sa_tenants.admin_password_mode_manual'),
                            ])
                            ->default('email_setup_link')
                            ->required()
                            ->live(),

                        TextInput::make('admin_password')
                            ->label(__('filament/sa_tenants.admin_password'))
                            ->password()
                            ->revealable()
                            ->minLength(10)
                            // dehydrated() stays at its default (true).
                            // Previously had ->dehydrated(false), which made
                            // Filament STRIP the typed password out of $data
                            // before submit. CreateTenant::mutateFormDataBeforeCreate
                            // then saw $data['admin_password'] as missing and
                            // silently fell back to Str::password(16) - so the
                            // operator typed one password but a random one got
                            // hashed and stored. Login failed; impersonation
                            // worked (it bypasses the password verifier).
                            // The mutator already unsets admin_password from
                            // $data before Tenant::create, so the value never
                            // reaches the tenants table - no leak risk.
                            ->visible(fn (Get $get): bool => $get('admin_password_mode') === 'manual')
                            ->required(fn (Get $get): bool => $get('admin_password_mode') === 'manual')
                            ->helperText(__('filament/sa_tenants.admin_password_helper')),
                    ])
                    ->visibleOn('create'),
        ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('name')->label(__('filament/sa_tenants.name'))->searchable()->sortable(),
                TextColumn::make('slug')->label(__('filament/sa_tenants.slug'))->searchable()->toggleable(),
                TextColumn::make('owner.name')->label(__('filament/sa_tenants.owner'))->searchable(),
                TextColumn::make('owner.email')->label(__('filament/sa_tenants.owner_email'))->searchable()
                    ->toggleable(isToggledHiddenByDefault: true),

                // Single derived status column.  Replaces the previous
                // raw `subscription_status` badge + boolean `active`
                // icon, which together couldn't represent suspended
                // tenants (active=false but status=trial would still
                // show a green Trial pill).  TenantSubscriptionState
                // is the single source of truth — label + colour
                // both route through it.
                TextColumn::make('status')
                    ->label(__('filament/sa_tenants.status_column'))
                    ->badge()
                    ->state(fn (Tenant $r): string => TenantSubscriptionState::of($r)->state())
                    ->formatStateUsing(fn (string $state): string => match ($state) {
                        TenantSubscriptionState::SUSPENDED     => __('filament/sa_tenants.status_suspended'),
                        TenantSubscriptionState::ON_TRIAL      => __('filament/sa_tenants.status_trial'),
                        TenantSubscriptionState::TRIAL_EXPIRED => __('filament/sa_tenants.status_trial_expired'),
                        TenantSubscriptionState::ACTIVE_PAID   => __('filament/sa_tenants.status_active'),
                        TenantSubscriptionState::CANCELLED     => __('filament/sa_tenants.status_cancelled'),
                        TenantSubscriptionState::EXPIRED       => __('filament/sa_tenants.status_expired'),
                        default                                => __('filament/sa_tenants.status_unknown'),
                    })
                    ->color(fn (string $state): string => match ($state) {
                        TenantSubscriptionState::ACTIVE_PAID   => 'success',
                        TenantSubscriptionState::ON_TRIAL      => 'warning',
                        TenantSubscriptionState::SUSPENDED     => 'danger',
                        TenantSubscriptionState::TRIAL_EXPIRED => 'danger',
                        TenantSubscriptionState::EXPIRED       => 'gray',
                        TenantSubscriptionState::CANCELLED     => 'gray',
                        default                                => 'gray',
                    }),

                TextColumn::make('plan')->label(__('filament/sa_tenants.plan'))->badge()->toggleable(),
                TextColumn::make('seat_count')->label(__('filament/sa_tenants.seats'))
                    ->formatStateUsing(fn ($record) => "{$record->seat_count}/{$record->max_seats}"),
                TextColumn::make('trial_ends_at')->label(__('filament/sa_tenants.trial_ends'))->date()->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
                TextColumn::make('subscription_ends_at')->label(__('filament/sa_tenants.sub_ends'))->date()->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
                TextColumn::make('created_at')->label(__('filament/sa_tenants.created_at'))->dateTime()->sortable(),
            ])
            ->filters([
                SelectFilter::make('subscription_status')
                    ->label(__('filament/sa_tenants.status_column'))
                    ->options([
                        'trial'         => __('filament/sa_tenants.status_trial'),
                        'trial_expired' => __('filament/sa_tenants.status_trial_expired'),
                        'active'        => __('filament/sa_tenants.status_active'),
                        'cancelled'     => __('filament/sa_tenants.status_cancelled'),
                        'expired'       => __('filament/sa_tenants.status_expired'),
                    ]),
                SelectFilter::make('active')
                    ->label(__('filament/sa_tenants.filter_suspension'))
                    ->options(['1' => __('filament/sa_tenants.status_active'), '0' => __('filament/sa_tenants.status_suspended')]),
            ])
            ->actions([
                static::impersonateAction(),
                static::suspendAction(),
                static::reactivateAction(),
                EditAction::make(),
            ]);
    }

    public static function getPages(): array
    {
        return [
            'index'  => Pages\ListTenants::route('/'),
            'create' => Pages\CreateTenant::route('/create'),
            'edit'   => Pages\EditTenant::route('/{record}/edit'),
        ];
    }

    /*
    |--------------------------------------------------------------------------
    | Custom row + page actions
    |--------------------------------------------------------------------------
    | Suspend and Reactivate are exposed BOTH on the table row AND on
    | the EditTenant header (see EditTenant::getHeaderActions) so the
    | SA can lock or unlock a workspace without opening the edit form.
    |
    | Suspension semantics: setting Tenant.active=false makes
    | EnforceSubscription bounce every tenant-side request to
    | /admin/subscription-required on the next hit.  The user stays
    | logged in (cookies remain valid) but every authenticated route
    | sees the suspension and redirects.  Reactivation reverses this
    | atomically.  No session destruction needed — the next request
    | is enough.
    */

    public static function suspendAction(): Action
    {
        return Action::make('suspend')
            ->label(__('filament/sa_tenants.suspend'))
            ->icon('heroicon-o-no-symbol')
            ->color('danger')
            ->visible(fn (Tenant $record): bool => (bool) $record->active)
            ->requiresConfirmation()
            ->modalIcon('heroicon-o-exclamation-triangle')
            ->modalHeading(__('filament/sa_tenants.suspend_modal_heading'))
            ->modalDescription(fn (Tenant $record) => __('filament/sa_tenants.suspend_modal_description', ['name' => $record->name]))
            ->form([
                Textarea::make('reason')
                    ->label(__('filament/sa_tenants.suspend_reason_label'))
                    ->rows(2)
                    ->maxLength(500),
            ])
            ->before(fn () => \App\Support\DemoMode::guard(__('filament/sa_tenants.suspend_demo_guard')))
            ->action(function (Tenant $record, array $data): void {
                $record->forceFill(['active' => false])->save();

                // Notify the workspace owner.  Mirrors what
                // ProcessSubscriptionLifecycle does for auto-suspend
                // — same WorkspaceSuspendedMail, same try/catch
                // semantics — so manual and automatic suspension
                // produce identical inbox UX.  Orphan tenants (no
                // owner) skip the email but still get suspended.
                $emailSent = false;
                $ownerEmail = $record->owner?->email;
                if ($ownerEmail) {
                    try {
                        Mail::to($ownerEmail)->send(new WorkspaceSuspendedMail($record));
                        $emailSent = true;
                    } catch (\Throwable $e) {
                        Log::error('Failed to send manual suspend email', [
                            'tenant_id' => $record->id,
                            'owner'     => $ownerEmail,
                            'error'     => $e->getMessage(),
                        ]);
                    }
                }

                AuditLog::record(
                    'tenant.suspended',
                    $record,
                    [],
                    [
                        'tenant_name' => $record->name,
                        'reason'      => trim((string) ($data['reason'] ?? '')) ?: null,
                        'by_user_id'  => Auth::id(),
                        'email_sent'  => $emailSent,
                    ],
                    'tenant-management',
                );

                Log::info('Tenant suspended', [
                    'tenant'     => $record->name,
                    'by'         => Auth::user()?->email,
                    'reason'     => $data['reason'] ?? null,
                    'email_sent' => $emailSent,
                ]);

                $body = __('filament/sa_tenants.suspend_notification_body_base');
                if ($emailSent) {
                    $body .= __('filament/sa_tenants.suspend_notification_body_owner_notified', ['email' => $ownerEmail]);
                } elseif ($ownerEmail) {
                    $body .= __('filament/sa_tenants.suspend_notification_body_owner_failed');
                } else {
                    $body .= __('filament/sa_tenants.suspend_notification_body_no_owner');
                }

                Notification::make()
                    ->title(__('filament/sa_tenants.suspend_notification_title', ['name' => $record->name]))
                    ->body($body)
                    ->warning()
                    ->send();
            });
    }

    public static function reactivateAction(): Action
    {
        return Action::make('reactivate')
            ->label(__('filament/sa_tenants.reactivate'))
            ->icon('heroicon-o-check-circle')
            ->color('success')
            ->visible(fn (Tenant $record): bool => ! $record->active)
            ->requiresConfirmation()
            ->modalHeading(__('filament/sa_tenants.reactivate_modal_heading'))
            ->modalDescription(fn (Tenant $record) => __('filament/sa_tenants.reactivate_modal_description', ['name' => $record->name]))
            ->before(fn () => \App\Support\DemoMode::guard(__('filament/sa_tenants.reactivate_demo_guard')))
            ->action(function (Tenant $record): void {
                $record->forceFill(['active' => true])->save();

                AuditLog::record(
                    'tenant.reactivated',
                    $record,
                    [],
                    [
                        'tenant_name' => $record->name,
                        'by_user_id'  => Auth::id(),
                    ],
                    'tenant-management',
                );

                Log::info('Tenant reactivated', [
                    'tenant' => $record->name,
                    'by'     => Auth::user()?->email,
                ]);

                Notification::make()
                    ->title(__('filament/sa_tenants.reactivate_notification_title', ['name' => $record->name]))
                    ->success()
                    ->send();
            });
    }

    /**
     * Existing impersonation action — extracted from the inline
     * definition so the table action stack stays readable now that
     * suspend / reactivate live alongside it.
     */
    public static function impersonateAction(): Action
    {
        return Action::make('impersonate')
            ->label(__('filament/sa_tenants.impersonate'))
            ->icon('heroicon-o-finger-print')
            ->color('warning')
            ->requiresConfirmation()
            ->modalIcon('heroicon-o-exclamation-triangle')
            ->modalHeading(__('filament/sa_tenants.impersonate_modal_heading'))
            ->modalDescription(fn (Tenant $record) => __('filament/sa_tenants.impersonate_modal_description', [
                'owner_name'  => (string) ($record->owner?->name ?? ''),
                'owner_email' => (string) ($record->owner?->email ?? ''),
                'tenant_name' => $record->name,
            ]))
            ->visible(fn (Tenant $record) => $record->owner_id !== null)
            // Impersonation is intentionally NOT demo-gated: stepping
            // into a tenant is a headline feature buyers come to the
            // public demo to evaluate.  The ->action() below still
            // verifies isSuperAdmin(), and DemoMode keeps guarding
            // every destructive endpoint inside the impersonated
            // session, so nothing harmful is exposed.
            ->action(function (Tenant $record) {
                $superAdmin = Auth::user();

                if (! $superAdmin || ! $superAdmin->isSuperAdmin()) {
                    Notification::make()->title(__('notifications.permission_denied'))->danger()->send();
                    return;
                }

                if (! $record->owner) {
                    Notification::make()->title(__('notifications.no_owner_for_workspace'))->danger()->send();
                    return;
                }

                if (session()->has('impersonating_from')) {
                    Notification::make()->title(__('notifications.already_impersonating'))->warning()->send();
                    return;
                }

                AuditLog::record(
                    'impersonation.started',
                    $record,
                    [],
                    [
                        'super_admin_id'    => $superAdmin->id,
                        'super_admin_email' => $superAdmin->email,
                        'target_user_id'    => $record->owner->id,
                        'target_user_email' => $record->owner->email,
                    ],
                    'impersonation',
                );

                Log::info('Impersonation started', [
                    'super_admin' => $superAdmin->email,
                    'tenant'      => $record->name,
                    'target'      => $record->owner->email,
                ]);

                $impersonationData = [
                    'impersonating_from'       => $superAdmin->id,
                    'impersonating_tenant_id'  => $record->id,
                    'impersonating_started_at' => now()->toIso8601String(),
                ];

                request()->session()->invalidate();
                request()->session()->regenerateToken();

                Auth::login($record->owner);

                foreach ($impersonationData as $key => $value) {
                    session()->put($key, $value);
                }

                redirect('/admin');
            });
    }
}
