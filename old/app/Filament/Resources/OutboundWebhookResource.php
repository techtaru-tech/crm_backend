<?php

namespace App\Filament\Resources;

use App\Filament\Concerns\HasRolePermissions;
use App\Filament\Resources\OutboundWebhookResource\Pages;
use App\Models\OutboundWebhook;
use Filament\Forms\Components\CheckboxList;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Toggle;
use Filament\Notifications\Notification;
use Filament\Resources\Resource;
use Filament\Schemas\Schema;
use Filament\Actions\Action;
use Filament\Actions\DeleteAction;
use Filament\Actions\EditAction;
use Filament\Tables\Columns\IconColumn;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Str;
use UnitEnum;

class OutboundWebhookResource extends Resource
{
    use HasRolePermissions;
    protected static string $permissionPrefix = 'integrations';
    protected static ?string $model = OutboundWebhook::class;
    protected static string|\BackedEnum|null $navigationIcon = 'heroicon-o-arrow-up-on-square';
    protected static string|UnitEnum|null $navigationGroup = 'Settings';
    protected static ?int $navigationSort = 11;

    public static function getNavigationLabel(): string
    {
        return __('filament/outbound_webhooks.nav_label');
    }

    public static function getModelLabel(): string
    {
        return __('filament/outbound_webhooks.model_label');
    }

    public static function getPluralModelLabel(): string
    {
        return __('filament/outbound_webhooks.plural_model_label');
    }

    public static function form(Schema $schema): Schema
    {
        return $schema->components([
            TextInput::make('name')
                ->label(__('filament/outbound_webhooks.webhook_name'))
                ->required()
                ->maxLength(100)
                ->placeholder(__('filament/outbound_webhooks.webhook_name_placeholder')),

            TextInput::make('url')
                ->label(__('filament/outbound_webhooks.endpoint_url'))
                ->required()
                ->url()
                ->maxLength(2048)
                ->placeholder(__('filament/outbound_webhooks.endpoint_url_placeholder')),

            CheckboxList::make('events')
                ->label(__('filament/outbound_webhooks.trigger_events'))
                ->options(OutboundWebhook::eventLabels())
                ->required()
                ->columns(2),

            TextInput::make('secret')
                ->label(__('filament/outbound_webhooks.signing_secret'))
                ->password()
                ->revealable()
                ->helperText(__('filament/outbound_webhooks.signing_secret_helper'))
                ->maxLength(64)
                ->default(Str::random(40)),

            Textarea::make('filters')
                ->label(__('filament/outbound_webhooks.payload_filters'))
                ->rows(4)
                ->helperText(__('filament/outbound_webhooks.payload_filters_helper'))
                ->placeholder(__('filament/outbound_webhooks.payload_filters_placeholder'))
                ->dehydrateStateUsing(function (?string $state): ?array {
                    if (blank($state)) return null;
                    $decoded = json_decode($state, true);
                    return is_array($decoded) ? $decoded : null;
                })
                ->formatStateUsing(function ($state): string {
                    if (empty($state)) return '';
                    return json_encode($state, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE);
                }),

            Toggle::make('enabled')
                ->label(__('filament/outbound_webhooks.enabled'))
                ->default(true),
        ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->modifyQueryUsing(fn(Builder $query) => $query->where('tenant_id', \App\Support\TenantContext::currentId()))
            ->columns([
                TextColumn::make('name')
                    ->label(__('filament/outbound_webhooks.col_name'))
                    ->searchable()
                    ->sortable(),

                TextColumn::make('url')
                    ->label(__('filament/outbound_webhooks.col_url'))
                    ->limit(40)
                    ->tooltip(fn($state) => $state),

                TextColumn::make('events')
                    ->label(__('filament/outbound_webhooks.col_events'))
                    ->formatStateUsing(fn($state) => implode(', ', (array) $state))
                    ->wrap(),

                TextColumn::make('deliveries_count')
                    ->label(__('filament/outbound_webhooks.deliveries'))
                    ->counts('deliveries')
                    ->sortable(),

                IconColumn::make('enabled')
                    ->label(__('filament/outbound_webhooks.col_enabled'))
                    ->boolean()
                    ->trueColor('success')
                    ->falseColor('gray'),

                TextColumn::make('created_at')
                    ->label(__('filament/outbound_webhooks.col_created'))
                    ->since()
                    ->sortable(),
            ])
            ->actions([
                Action::make('test')
                    ->label(__('filament/outbound_webhooks.action_send_test'))
                    ->icon('heroicon-o-paper-airplane')
                    ->color('info')
                    ->action(function ($record) {
                        $payloadData = [
                            'event'      => 'test',
                            'tenant_id'  => $record->tenant_id,
                            'webhook_id' => $record->id,
                            'timestamp'  => now()->toIso8601String(),
                            'payload'    => ['message' => __('filament/outbound_webhooks.test_delivery_body')],
                        ];
                        $payloadJson = json_encode($payloadData);
                        $signed      = $record->signWithTimestamp($payloadJson);

                        $delivery = \App\Models\WebhookDelivery::create([
                            'webhook_id' => $record->id,
                            'tenant_id'  => $record->tenant_id,
                            'event'      => 'test',
                            'payload'    => $payloadData,
                            'status'     => 'pending',
                            'attempts'   => 1,
                        ]);

                        $start = microtime(true);
                        try {
                            $response = Http::timeout(10)
                                ->withHeaders([
                                    'Content-Type'        => 'application/json',
                                    'X-LeadHub-Event'     => 'test',
                                    // Replay-safe Stripe-style signature.
                                    'X-LeadHub-Signature' => $signed['signature'],
                                    'X-LeadHub-Timestamp' => $signed['timestamp'],
                                    'X-LeadHub-Webhook'   => (string) $record->id,
                                ])
                                ->post($record->url, $payloadData);

                            $latency = (int) ((microtime(true) - $start) * 1000);
                            $success = $response->successful();

                            $delivery->update([
                                'status'        => $success ? \App\Models\WebhookDelivery::STATUS_SUCCESS : \App\Models\WebhookDelivery::STATUS_FAILED,
                                'response_code' => $response->status(),
                                'response_body' => substr($response->body(), 0, 4000),
                                'latency_ms'    => $latency,
                            ]);

                            if ($success) {
                                Notification::make()->title(__('notifications.test_delivered_http', ['status' => $response->status()]))->success()->send();
                            } else {
                                Notification::make()->title(__('notifications.test_failed_http', ['status' => $response->status()]))->danger()->send();
                            }
                        } catch (\Throwable $e) {
                            $latency = (int) ((microtime(true) - $start) * 1000);
                            $delivery->update([
                                'status'        => \App\Models\WebhookDelivery::STATUS_FAILED,
                                'response_body' => $e->getMessage(),
                                'latency_ms'    => $latency,
                            ]);
                            Notification::make()->title(__('notifications.test_failed_error', ['error' => $e->getMessage()]))->danger()->send();
                        }
                    }),

                Action::make('deliveries')
                    ->label(__('filament/outbound_webhooks.action_view_deliveries'))
                    ->icon('heroicon-o-clock')
                    ->url(fn($record) => OutboundWebhookResource::getUrl('deliveries', ['record' => $record])),

                EditAction::make(),
                DeleteAction::make(),
            ])
            ->defaultSort('created_at', 'desc')
            ->emptyStateHeading(__('filament/outbound_webhooks.empty_heading'))
            ->emptyStateDescription(__('filament/outbound_webhooks.empty_description'));
    }

    public static function getRelationManagers(): array
    {
        return [];
    }

    public static function getPages(): array
    {
        return [
            'index'      => Pages\ListOutboundWebhooks::route('/'),
            'create'     => Pages\CreateOutboundWebhook::route('/create'),
            'edit'       => Pages\EditOutboundWebhook::route('/{record}/edit'),
            'deliveries' => Pages\WebhookDeliveryLog::route('/{record}/deliveries'),
        ];
    }
}
