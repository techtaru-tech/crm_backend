<?php

namespace App\Filament\Resources\OutboundWebhookResource\Pages;

use App\Filament\Resources\OutboundWebhookResource;
use App\Jobs\DispatchOutboundWebhook;
use App\Models\OutboundWebhook;
use App\Models\WebhookDelivery;
use Filament\Actions\Action;
use Filament\Notifications\Notification;
use Filament\Resources\Pages\Page;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Concerns\InteractsWithTable;
use Filament\Tables\Contracts\HasTable;
use Filament\Tables\Table;
use Illuminate\Support\Facades\Http;

class WebhookDeliveryLog extends Page implements HasTable
{
    use InteractsWithTable;

    protected static string $resource = OutboundWebhookResource::class;
    protected string $view = 'filament.resources.outbound-webhook-resource.pages.webhook-delivery-log';

    public OutboundWebhook $record;

    public function mount(OutboundWebhook $record): void
    {
        $this->record = $record;
    }

    public function getTitle(): string
    {
        return __('filament/outbound_webhooks.delivery_log_title_prefix') . $this->record->name;
    }

    protected function getHeaderActions(): array
    {
        return [
            Action::make('test')
                ->label(__('filament/outbound_webhooks.action_send_test'))
                ->icon('heroicon-o-paper-airplane')
                ->color('info')
                ->action(function () {
                    $payloadData = [
                        'event'      => 'test',
                        'tenant_id'  => $this->record->tenant_id,
                        'webhook_id' => $this->record->id,
                        'timestamp'  => now()->toIso8601String(),
                        'payload'    => ['message' => __('filament/outbound_webhooks.test_delivery_body')],
                    ];
                    $payloadJson = json_encode($payloadData);
                    $signed      = $this->record->signWithTimestamp($payloadJson);

                    $delivery = WebhookDelivery::create([
                        'webhook_id' => $this->record->id,
                        'tenant_id'  => $this->record->tenant_id,
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
                                'X-LeadHub-Webhook'   => (string) $this->record->id,
                            ])
                            ->post($this->record->url, $payloadData);

                        $latency = (int) ((microtime(true) - $start) * 1000);
                        $success = $response->successful();

                        $delivery->update([
                            'status'        => $success ? WebhookDelivery::STATUS_SUCCESS : WebhookDelivery::STATUS_FAILED,
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
                            'status'        => WebhookDelivery::STATUS_FAILED,
                            'response_body' => $e->getMessage(),
                            'latency_ms'    => $latency,
                        ]);
                        Notification::make()->title(__('notifications.test_failed_error', ['error' => $e->getMessage()]))->danger()->send();
                    }
                }),

            Action::make('back')
                ->label(__('filament/outbound_webhooks.action_back_to_webhooks'))
                ->icon('heroicon-o-arrow-left')
                ->color('gray')
                ->url(OutboundWebhookResource::getUrl('index')),
        ];
    }

    public function table(Table $table): Table
    {
        return $table
            ->query(WebhookDelivery::query()->where('webhook_id', $this->record->id)->orderByDesc('created_at'))
            ->columns([
                TextColumn::make('event')
                    ->label(__('filament/outbound_webhooks.col_event'))
                    ->badge()
                    ->color(fn($state) => match(true) {
                        $state === 'test'                  => 'gray',
                        str_contains($state, 'lead')       => 'primary',
                        str_contains($state, 'form')       => 'info',
                        str_contains($state, 'automation') => 'warning',
                        default                            => 'gray',
                    })
                    ->formatStateUsing(fn (?string $state): string => match ($state) {
                        'test'                 => __('filament/outbound_webhooks.event_test'),
                        'lead.created'         => __('filament/outbound_webhooks.event_lead_created'),
                        'lead.updated'         => __('filament/outbound_webhooks.event_lead_updated'),
                        'lead.deleted'         => __('filament/outbound_webhooks.event_lead_deleted'),
                        'lead.stage_changed'   => __('filament/outbound_webhooks.event_lead_stage_changed'),
                        'form.submitted'       => __('filament/outbound_webhooks.event_form_submitted'),
                        'automation.triggered' => __('filament/outbound_webhooks.event_automation_triggered'),
                        default                => (string) $state,
                    }),

                TextColumn::make('status')
                    ->label(__('filament/outbound_webhooks.col_status'))
                    ->badge()
                    ->color(fn($state) => match($state) {
                        'success'  => 'success',
                        'failed'   => 'danger',
                        'retrying' => 'warning',
                        default    => 'gray',
                    })
                    ->formatStateUsing(fn (?string $state): string => match ($state) {
                        'success'  => __('filament/outbound_webhooks.status_success'),
                        'failed'   => __('filament/outbound_webhooks.status_failed'),
                        'retrying' => __('filament/outbound_webhooks.status_retrying'),
                        default    => (string) $state,
                    }),

                TextColumn::make('response_code')
                    ->label(__('filament/outbound_webhooks.col_http'))
                    ->badge()
                    ->color(fn($state) => match(true) {
                        $state >= 200 && $state < 300 => 'success',
                        $state >= 400                 => 'danger',
                        default                       => 'gray',
                    }),

                TextColumn::make('latency_ms')
                    ->label(__('filament/outbound_webhooks.col_latency'))
                    ->suffix(__('filament/outbound_webhooks.latency_ms_suffix'))
                    ->placeholder('—'),

                TextColumn::make('attempts')
                    ->label(__('filament/outbound_webhooks.col_attempts')),

                TextColumn::make('created_at')
                    ->label(__('filament/outbound_webhooks.col_sent'))
                    ->formatStateUsing(fn ($state) => $state?->translatedFormat('M d, Y H:i:s'))
                    ->sortable(),
            ])
            ->actions([
                Action::make('view_payload')
                    ->label(__('filament/outbound_webhooks.action_payload'))
                    ->icon('heroicon-o-document-text')
                    ->color('gray')
                    ->modalHeading(fn($record) => __('filament/outbound_webhooks.sent_payload_modal_prefix') . $record->event)
                    ->modalContent(function ($record) {
                        $json = json_encode($record->payload, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES);
                        return view('filament.modals.code-block', ['code' => $json, 'language' => 'json']);
                    })
                    ->modalSubmitAction(false)
                    ->modalCancelActionLabel(__('filament/outbound_webhooks.modal_close')),

                Action::make('view_response')
                    ->label(__('filament/outbound_webhooks.action_response'))
                    ->icon('heroicon-o-eye')
                    ->color('info')
                    ->modalHeading(fn($record) => __('filament/outbound_webhooks.response_body_modal_prefix') . $record->response_code)
                    ->modalContent(function ($record) {
                        $body = $record->response_body ?? __('filament/outbound_webhooks.no_response_body');
                        $decoded = json_decode($body, true);
                        if ($decoded !== null) {
                            $body = json_encode($decoded, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES);
                        }
                        return view('filament.modals.code-block', ['code' => $body, 'language' => 'json']);
                    })
                    ->modalSubmitAction(false)
                    ->modalCancelActionLabel(__('filament/outbound_webhooks.modal_close')),

                Action::make('retry')
                    ->label(__('filament/outbound_webhooks.action_retry'))
                    ->icon('heroicon-o-arrow-path')
                    ->color('warning')
                    ->visible(fn($record) => $record->canRetry())
                    ->action(function ($record) {
                        DispatchOutboundWebhook::dispatch(
                            $record->tenant_id,
                            $record->event,
                            (array) $record->payload,
                            $record->webhook_id,
                        )->onQueue('webhooks');
                        Notification::make()->title(__('notifications.delivery_requeued'))->warning()->send();
                    }),
            ])
            ->emptyStateHeading(__('filament/outbound_webhooks.log_empty_heading'))
            ->emptyStateDescription(__('filament/outbound_webhooks.log_empty_description'));
    }
}
