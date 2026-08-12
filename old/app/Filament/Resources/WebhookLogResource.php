<?php

namespace App\Filament\Resources;

use App\Enums\LeadSource;
use App\Filament\Resources\WebhookLogResource\Pages;
use App\Jobs\ProcessInboundWebhook;
use App\Models\WebhookLog;
use Filament\Forms\Components\Textarea;
use Filament\Notifications\Notification;
use Filament\Resources\Resource;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Schema;
use Filament\Actions\Action;
use Filament\Actions\DeleteAction;
use Filament\Actions\ViewAction;
use Filament\Tables\Columns\BadgeColumn;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Filters\SelectFilter;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use UnitEnum;

class WebhookLogResource extends Resource
{
    protected static ?string $model = WebhookLog::class;
    protected static string|\BackedEnum|null $navigationIcon = 'heroicon-o-queue-list';
    protected static string|UnitEnum|null $navigationGroup = 'Integrations';
    protected static ?int $navigationSort = 10;

    public static function getNavigationLabel(): string
    {
        return __('filament/webhook_logs.nav_label');
    }

    /**
     * Method-form model-label overrides.  Replace the static $modelLabel
     * property so the locale resolves at request time (the translator
     * is not yet bound at class-load time for the active session locale).
     */
    public static function getModelLabel(): string
    {
        return __('filament/webhook_logs.model_label');
    }

    public static function getPluralModelLabel(): string
    {
        return __('filament/webhook_logs.plural_model_label');
    }

    public static function form(Schema $schema): Schema
    {
        return $schema->components([
            Section::make(__('sections.webhook_log_details'))->schema([
                Textarea::make('payload')
                    ->label(__('filament/webhook_logs.raw_payload'))
                    ->rows(15)
                    ->disabled()
                    ->columnSpanFull()
                    ->formatStateUsing(fn ($state) => json_encode(json_decode($state), JSON_PRETTY_PRINT)),
                Textarea::make('error_message')
                    ->label(__('filament/webhook_logs.error_message'))
                    ->disabled()
                    ->columnSpanFull(),
            ]),
        ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('source')
                    ->label(__('filament/webhook_logs.col_source'))
                    ->formatStateUsing(fn (string $state) => LeadSource::tryFrom($state)?->label() ?? ucfirst($state))
                    ->badge()
                    ->color('primary'),
                TextColumn::make('status')
                    ->label(__('filament/webhook_logs.col_status'))
                    ->badge()
                    ->color(fn (string $state) => match($state) {
                        'success'          => 'success',
                        'failed'           => 'danger',
                        'pending'          => 'warning',
                        'invalid_signature' => 'danger',
                        default            => 'gray',
                    }),
                TextColumn::make('leads_created')
                    ->label(__('filament/webhook_logs.leads'))
                    ->numeric()
                    ->sortable(),
                TextColumn::make('ip_address')
                    ->label(__('filament/webhook_logs.ip')),
                TextColumn::make('error_message')
                    ->label(__('filament/webhook_logs.error'))
                    ->limit(50)
                    ->tooltip(fn ($record) => $record->error_message),
                TextColumn::make('created_at')
                    ->label(__('filament/webhook_logs.received_at'))
                    ->dateTime()
                    ->sortable()
                    ->since(),
                TextColumn::make('processed_at')
                    ->label(__('filament/webhook_logs.processed_at'))
                    ->dateTime()
                    ->sortable()
                    ->since(),
            ])
            ->filters([
                SelectFilter::make('source')
                    ->label(__('filament/webhook_logs.filter_label_source'))
                    ->options(LeadSource::options()),
                SelectFilter::make('status')
                    ->label(__('filament/webhook_logs.filter_label_status'))
                    ->options([
                        'success'  => __('filament/webhook_logs.status_success'),
                        'failed'   => __('filament/webhook_logs.status_failed'),
                        'pending'  => __('filament/webhook_logs.status_pending'),
                        'invalid_signature' => __('filament/webhook_logs.status_invalid_signature'),
                    ]),
            ])
            ->actions([
                Action::make('retry')
                    ->label(__('filament/webhook_logs.action_retry'))
                    ->icon('heroicon-o-arrow-path')
                    ->visible(fn (WebhookLog $record) => $record->isFailed())
                    ->action(function (WebhookLog $record) {
                        if (! $record->source_connection_id) {
                            Notification::make()->title(__('notifications.webhook_source_missing'))->danger()->send();
                            return;
                        }

                        $record->update(['status' => 'pending', 'error_message' => null]);
                        ProcessInboundWebhook::dispatch($record->id, $record->source_connection_id, $record->tenant_id);
                        Notification::make()->title(__('notifications.webhook_requeued'))->success()->send();
                    })
                    ->requiresConfirmation(),
                ViewAction::make(),
                DeleteAction::make(),
            ])
            ->defaultSort('created_at', 'desc')
            ->poll('15s');
    }

    public static function getEloquentQuery(): Builder
    {
        $tenantId = \App\Support\TenantContext::currentId();

        return parent::getEloquentQuery()
            ->when($tenantId, fn ($q) => $q->where('tenant_id', $tenantId));
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListWebhookLogs::route('/'),
            'view'  => Pages\ViewWebhookLog::route('/{record}'),
        ];
    }
}
