<?php

namespace App\Filament\Resources\LeadResource\Pages;

use App\Filament\Resources\LeadResource;
use App\Jobs\EnrichLead;
use App\Jobs\SendLeadEmail;
use App\Jobs\SendLeadMessage;
use App\Models\DealItem;
use App\Models\Lead;
use App\Models\LeadActivity;
use App\Models\LeadAttachment;
use App\Models\LeadNote;
use App\Models\LeadTask;
use App\Models\PipelineStage;
use App\Models\Product;
use App\Models\Tag;
use App\Services\AiEmailComposerService;
use App\Services\LeadActivityService;
use App\Services\LeadMergeService;
use App\Services\LeadNextActionService;
use Filament\Actions\Action;
use Filament\Actions\ActionGroup;
use Filament\Actions\EditAction;
use Filament\Forms\Components\DateTimePicker;
use Filament\Forms\Components\Hidden;
use Filament\Forms\Components\RichEditor;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use App\Support\Currency;
use Filament\Notifications\Notification;
use Filament\Resources\Pages\ViewRecord;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;
use Livewire\WithFileUploads;

class ViewLead extends ViewRecord
{
    use WithFileUploads;

    protected static string $resource = LeadResource::class;

    // NOTE: This page renders a custom blade template (`filament.resources.leads.view`)
    // instead of the default relation-manager tabs layout. Relation managers are
    // registered via LeadResource::getRelations() so they are wired into the Filament
    // panel (and available on standard pages / authorization), but they are NOT
    // rendered automatically here — the custom blade is responsible for surfacing
    // emails / messages / tasks / deal items / sequence enrollments / page views.
    public function getView(): string
    {
        return 'filament.resources.leads.view';
    }

    public string $noteBody = '';

    /** @var array<\Livewire\Features\SupportFileUploads\TemporaryUploadedFile>|null */
    public $newAttachments = null;

    public function getHeaderActions(): array
    {
        return [
            EditAction::make(),

            Action::make('add_line_item')
                ->label(__('filament/leads.add_line_item'))
                ->icon('heroicon-o-plus-circle')
                ->color('primary')
                ->form(function () {
                    $tenantId = \App\Support\TenantContext::currentId();
                    return [
                        Select::make('product_id')
                            ->label(__('filament/leads.product'))
                            ->options(fn() => Product::where('tenant_id', $tenantId)
                                ->where('is_active', true)
                                ->orderBy('name')
                                ->pluck('name', 'id'))
                            ->searchable()
                            ->reactive()
                            ->nullable()
                            ->afterStateUpdated(function ($state, callable $set) {
                                if ($state) {
                                    $product = Product::find($state);
                                    if ($product) {
                                        $set('name', $product->name);
                                        $set('unit_price', (float) $product->price);
                                    }
                                }
                            }),
                        TextInput::make('name')
                            ->label(__('filament/leads.item_name'))
                            ->required()
                            ->maxLength(150),
                        TextInput::make('unit_price')
                            ->label(__('filament/leads.unit_price'))
                            ->numeric()
                            ->prefix(fn () => Currency::defaultSymbol())
                            ->minValue(0)
                            ->required(),
                        TextInput::make('quantity')
                            ->label(__('filament/leads.quantity'))
                            ->numeric()
                            ->integer()
                            ->minValue(1)
                            ->default(1)
                            ->required(),
                        TextInput::make('discount_percent')
                            ->label(__('filament/leads.discount_percent'))
                            ->numeric()
                            ->minValue(0)
                            ->maxValue(100)
                            ->default(0),
                    ];
                })
                ->action(function (array $data) {
                    $tenantId = \App\Support\TenantContext::currentId();
                    DealItem::create([
                        'tenant_id'        => $tenantId,
                        'lead_id'          => $this->record->id,
                        'product_id'       => $data['product_id'] ?? null,
                        'name'             => $data['name'],
                        'unit_price'       => $data['unit_price'],
                        'quantity'         => $data['quantity'] ?? 1,
                        'discount_percent' => $data['discount_percent'] ?? 0,
                        'total'            => 0,
                    ]);
                    $this->record->refresh();
                    Notification::make()->success()->title(__('filament/leads.line_item_added'))->send();
                }),

            Action::make('create_task')
                ->label(__('filament/leads.create_task'))
                ->icon('heroicon-o-clipboard-document-check')
                ->color('primary')
                ->form([
                    TextInput::make('title')
                        ->label(__('filament/leads.task_title'))
                        ->required()
                        ->maxLength(255),
                    Textarea::make('description')
                        ->label(__('filament/leads.description'))
                        ->rows(3)
                        ->nullable(),
                    Select::make('assigned_user_id')
                        ->label(__('filament/leads.assigned_to'))
                        ->options(function () {
                            $tenantId = \App\Support\TenantContext::currentId();
                            return \App\Models\User::where('tenant_id', $tenantId)->pluck('name', 'id');
                        })
                        ->default(fn () => auth()->id())
                        ->searchable()
                        ->required(),
                    DateTimePicker::make('due_at')
                        ->label(__('filament/leads.due_at'))
                        ->required()
                        ->live()
                        ->seconds(false)
                        ->default(now()->addDay()->setTime(9, 0)),
                    Select::make('priority')
                        ->label(__('filament/leads.priority'))
                        ->options(LeadTask::priorityLabels())
                        ->default(LeadTask::PRIORITY_NORMAL)
                        ->required(),
                    DateTimePicker::make('reminder_at')
                        ->label(__('filament/leads.reminder_at'))
                        ->seconds(false)
                        ->helperText(__('filament/leads.reminder_help'))
                        ->default(fn ($get) => $get('due_at')
                            ? \Carbon\Carbon::parse($get('due_at'))->subHour()
                            : now()->addDay()->setTime(8, 0))
                        ->nullable(),
                ])
                ->action(function (array $data) {
                    $tenantId = \App\Support\TenantContext::currentId();

                    LeadTask::create([
                        'tenant_id'        => $tenantId,
                        'lead_id'          => $this->record->id,
                        'assigned_user_id' => $data['assigned_user_id'],
                        'title'            => $data['title'],
                        'description'      => $data['description'] ?? null,
                        'due_at'           => $data['due_at'],
                        'priority'         => $data['priority'] ?? LeadTask::PRIORITY_NORMAL,
                        'reminder_at'      => $data['reminder_at'] ?? null,
                    ]);

                    Notification::make()->success()->title(__('filament/leads.task_created'))->send();
                    $this->refreshFormData(['id']);
                }),

            Action::make('send_email')
                ->label(__('filament/leads.send_email'))
                ->icon('heroicon-o-envelope')
                ->color('info')
                ->form([
                    // "Load from template" helper — fills subject + body
                    // from one of the tenant's saved EmailTemplates so the
                    // operator can reuse / tweak it.  dehydrated(false):
                    // the Select itself is never passed to ->action();
                    // only the resulting subject/body are sent.  Closes
                    // the customer report "can't … choose [a template]
                    // for sending email to leads".
                    Select::make('load_template')
                        ->label(__('filament/leads.load_template'))
                        ->options(function () {
                            $tenantId = \App\Support\TenantContext::currentId();
                            return \App\Models\EmailTemplate::where('tenant_id', $tenantId)
                                ->orderBy('name')
                                ->pluck('name', 'id');
                        })
                        ->searchable()
                        ->dehydrated(false)
                        ->reactive()
                        ->columnSpanFull()
                        ->helperText(__('filament/leads.load_template_help'))
                        ->afterStateUpdated(function ($state, callable $set) {
                            if (! $state) {
                                return;
                            }
                            $tpl = \App\Models\EmailTemplate::find($state);
                            if ($tpl) {
                                $set('subject', $tpl->subject);
                                $set('body', $tpl->body_html);
                            }
                        }),
                    TextInput::make('subject')->label(__('filament/leads.subject'))->required()->columnSpanFull(),
                    RichEditor::make('body')
                        ->label(__('filament/leads.body'))
                        ->required()
                        ->columnSpanFull()
                        ->toolbarButtons(['bold', 'italic', 'link', 'bulletList', 'orderedList']),
                    \Filament\Forms\Components\FileUpload::make('attachments')
                        ->label(__('filament/leads.attachments'))
                        ->multiple()
                        ->disk('local')
                        // Tenant-prefix so attachments from one
                        // workspace can't collide with another's on
                        // the local disk; aligns with the
                        // tenant/{id}/... convention used elsewhere.
                        ->directory(fn () => 'email-attachments/' . (auth()->user()?->tenant_id ?? 'unscoped'))
                        ->columnSpanFull()
                        ->nullable(),
                ])
                ->action(function (array $data) {
                    $lead = $this->record;
                    if (! $lead->email) {
                        Notification::make()->danger()->title(__('filament/leads.no_email_address'))->send();
                        return;
                    }

                    $attachmentPaths = [];
                    foreach ($data['attachments'] ?? [] as $path) {
                        $full = \Illuminate\Support\Facades\Storage::path($path);
                        if (file_exists($full)) {
                            $attachmentPaths[] = $full;
                        }
                    }

                    SendLeadEmail::dispatch(
                        $lead->id,
                        $data['subject'],
                        $data['body'],
                        $lead->email,
                        auth()->id(),
                        $attachmentPaths,
                        (int) $lead->tenant_id,
                    );

                    // If outbound mail is in log mode AND this tenant has
                    // not configured a custom SMTP host, the message will
                    // be written to storage/logs/laravel.log and never
                    // leave the box.  Without an explicit warning the
                    // operator sees the green "queued" toast and assumes
                    // delivery happened.  Surface a clear warning instead
                    // of the success toast in that scenario.
                    $tenantSmtp     = (array) ($lead->tenant?->getSetting('smtp', []));
                    $globalIsLog    = config('mail.default') === 'log';
                    $tenantHasSmtp  = ! empty($tenantSmtp['host']);

                    if ($globalIsLog && ! $tenantHasSmtp) {
                        Notification::make()
                            ->warning()
                            ->title(__('filament/leads.email_log_mode_title'))
                            ->body(__('filament/leads.email_log_mode_body'))
                            ->persistent()
                            ->send();
                    } else {
                        Notification::make()->success()->title(__('filament/leads.email_queued'))->send();
                    }
                }),

            Action::make('call_lead')
                ->label(__('filament/leads.call_lead'))
                ->icon('heroicon-o-phone')
                ->color('success')
                ->visible(function () {
                    if (empty($this->record->phone)) {
                        return false;
                    }
                    $tenant = $this->record->tenant ?? auth()->user()?->tenant;
                    $voice  = (array) (($tenant?->getSetting('messaging') ?? [])['voice'] ?? []);
                    return (bool) ($voice['voice_enabled'] ?? false);
                })
                ->requiresConfirmation()
                ->modalHeading(__('filament/leads.call_modal_heading'))
                ->modalDescription(fn () => __('filament/leads.call_modal_description', ['phone' => $this->record->phone]))
                ->modalSubmitActionLabel(__('filament/leads.call_now'))
                ->action(function () {
                    $user = auth()->user();
                    if (! $user || empty($user->phone)) {
                        Notification::make()->danger()->title(__('filament/leads.call_no_phone'))->send();
                        return;
                    }

                    try {
                        $call = app(\App\Services\Voice\TwilioVoiceService::class)
                            ->initiateCall($this->record, $user);

                        if ($call->status === 'failed') {
                            Notification::make()->danger()->title(__('filament/leads.call_failed_to_start'))->send();
                            return;
                        }

                        Notification::make()->success()->title(__('filament/leads.call_initiated'))->send();
                        $this->refreshFormData(['id']);
                    } catch (\Throwable $e) {
                        Notification::make()->danger()->title(__('filament/leads.call_failed_prefix') . $e->getMessage())->send();
                    }
                }),

            Action::make('send_message')
                ->label(__('filament/leads.send_message'))
                ->icon('heroicon-o-chat-bubble-left-right')
                ->color('success')
                ->form(function () {
                    $lead = $this->record;
                    $channels = [];
                    if ($lead->phone) {
                        $channels['whatsapp'] = __('filament/leads.channel_whatsapp');
                        $channels['sms']      = __('filament/leads.channel_sms');
                        $channels['viber']    = __('filament/leads.channel_viber');
                    }
                    if (data_get($lead->custom_fields, 'telegram_chat_id')) {
                        $channels['telegram'] = __('filament/leads.channel_telegram');
                    }

                    return [
                        Select::make('channel')
                            ->label(__('filament/leads.channel'))
                            ->options($channels)
                            ->required()
                            ->default(array_key_first($channels) ?: 'whatsapp'),
                        Textarea::make('body')
                            ->label(__('filament/leads.message'))
                            ->required()
                            ->rows(4)
                            ->columnSpanFull(),
                    ];
                })
                ->action(function (array $data) {
                    $lead = $this->record;

                    if ($data['channel'] !== 'telegram' && ! $lead->phone) {
                        Notification::make()->danger()->title(__('filament/leads.no_phone_number'))->send();
                        return;
                    }

                    SendLeadMessage::dispatch(
                        $lead->id,
                        $data['channel'],
                        $data['body'],
                        auth()->id()
                    );

                    Notification::make()->success()->title(__('filament/leads.message_queued'))->send();
                }),

            Action::make('open_conversation')
                // Use the loaded relation when available (single query) and
                // fall back to the relation count call only when the record
                // was hydrated without its messages.
                ->label(function () {
                    $count = $this->record->relationLoaded('messages')
                        ? $this->record->messages->count()
                        : $this->record->messages()->count();
                    return __('filament/leads.conversation_count', ['count' => $count]);
                })
                ->icon('heroicon-o-inbox')
                ->color('gray')
                ->url(fn () => '/admin/conversations?lead=' . $this->record->id)
                ->visible(function () {
                    return $this->record->relationLoaded('messages')
                        ? $this->record->messages->isNotEmpty()
                        : $this->record->messages()->exists();
                }),

            Action::make('log_call')
                ->label(__('filament/leads.log_call'))
                ->icon('heroicon-o-phone')
                ->color('warning')
                ->form([
                    Select::make('direction')
                        ->label(__('filament/leads.direction'))
                        ->options(['inbound' => __('filament/leads.inbound'), 'outbound' => __('filament/leads.outbound')])
                        ->required(),
                    TextInput::make('duration')
                        ->label(__('filament/leads.duration'))
                        ->numeric()
                        ->suffix(__('filament/leads.duration_minutes_suffix'))
                        ->required(),
                    Select::make('outcome')
                        ->label(__('filament/leads.outcome'))
                        ->options([
                            'connected'       => __('filament/leads.outcome_connected'),
                            'voicemail'       => __('filament/leads.outcome_voicemail'),
                            'no_answer'       => __('filament/leads.outcome_no_answer'),
                            'not_interested'  => __('filament/leads.outcome_not_interested'),
                            'callback'        => __('filament/leads.outcome_callback'),
                        ])
                        ->required(),
                    Textarea::make('notes')->label(__('filament/leads.lead_notes'))->rows(2),
                ])
                ->action(function (array $data) {
                    app(LeadActivityService::class)->logCallLogged(
                        $this->record,
                        $data['direction'],
                        (int) $data['duration'],
                        $data['outcome'],
                        $data['notes'] ?? null
                    );
                    Notification::make()->success()->title(__('filament/leads.call_logged'))->send();
                    $this->refreshFormData(['id']);
                }),

            Action::make('add_note')
                ->label(__('filament/leads.add_note'))
                ->icon('heroicon-o-chat-bubble-left')
                ->form([
                    \Filament\Forms\Components\TextInput::make('mention_search')
                        ->label(__('filament/leads.mention_label'))
                        ->placeholder(__('filament/leads.mention_placeholder'))
                        ->helperText(__('filament/leads.mention_help'))
                        ->nullable(),
                    Textarea::make('body')
                        ->label(__('filament/leads.note'))
                        ->required()
                        ->rows(5)
                        ->helperText(__('filament/leads.note_body_help'))
                        ->columnSpanFull(),
                ])
                ->action(function (array $data) {
                    $tenantId = \App\Support\TenantContext::currentId();

                    $mentions = [];
                    if (! empty($data['mention_search'])) {
                        $parts = preg_split('/[\s,@]+/', $data['mention_search'], -1, PREG_SPLIT_NO_EMPTY);
                        $mentions = \App\Models\User::where('tenant_id', $tenantId)
                            ->where(function ($q) use ($parts) {
                                foreach ($parts as $part) {
                                    $q->orWhere('name', 'like', "%{$part}%");
                                }
                            })
                            ->pluck('id')
                            ->toArray();
                    }

                    preg_match_all('/@(\w+)/', $data['body'], $bodyMatches);
                    if (! empty($bodyMatches[1])) {
                        $bodyMentioned = \App\Models\User::where('tenant_id', $tenantId)
                            ->where(function ($q) use ($bodyMatches) {
                                foreach ($bodyMatches[1] as $name) {
                                    $q->orWhere('name', 'like', "%{$name}%");
                                }
                            })
                            ->pluck('id')
                            ->toArray();
                        $mentions = array_unique(array_merge($mentions, $bodyMentioned));
                    }

                    LeadNote::create([
                        'lead_id'   => $this->record->id,
                        'tenant_id' => $tenantId,
                        'user_id'   => auth()->id(),
                        'body'      => $data['body'],
                        'mentions'  => $mentions ?: null,
                    ]);
                    app(LeadActivityService::class)->logNoteAdded($this->record, $data['body']);
                    Notification::make()->success()->title(__('filament/leads.note_added'))->send();
                    $this->refreshFormData(['id']);
                }),

            Action::make('move_stage')
                ->label(__('filament/leads.move_stage'))
                ->icon('heroicon-o-arrows-right-left')
                ->form([
                    Select::make('pipeline_id')
                        ->label(__('filament/leads.pipeline'))
                        ->options(function () {
                            $tenantId = \App\Support\TenantContext::currentId();
                            return \App\Models\Pipeline::where('tenant_id', $tenantId)->pluck('name', 'id');
                        })
                        ->reactive(),
                    Select::make('pipeline_stage_id')
                        ->label(__('filament/leads.stage'))
                        ->options(function ($get) {
                            $pid = $get('pipeline_id');
                            return $pid ? PipelineStage::where('pipeline_id', $pid)->pluck('name', 'id') : [];
                        })
                        ->required(),
                ])
                ->action(function (array $data) {
                    $this->record->update([
                        'pipeline_id'       => $data['pipeline_id'],
                        'pipeline_stage_id' => $data['pipeline_stage_id'],
                    ]);
                    Notification::make()->success()->title(__('filament/leads.lead_moved_to_stage'))->send();
                    $this->refreshFormData(['pipeline_stage_id']);
                }),

            Action::make('assign')
                ->label(__('filament/leads.assign'))
                ->icon('heroicon-o-user-plus')
                ->form([
                    Select::make('assigned_user_id')
                        ->label(__('filament/leads.bulk_assign_to'))
                        ->options(function () {
                            $tenantId = \App\Support\TenantContext::currentId();
                            return \App\Models\User::where('tenant_id', $tenantId)->pluck('name', 'id');
                        })
                        ->required(),
                ])
                ->action(function (array $data) {
                    $this->record->update(['assigned_user_id' => $data['assigned_user_id']]);
                    Notification::make()->success()->title(__('filament/leads.lead_assigned'))->send();
                }),

            Action::make('enroll_sequence')
                ->label(__('filament/leads.enroll_in_sequence'))
                ->icon('heroicon-o-envelope-open')
                ->color('info')
                ->form([
                    Select::make('sequence_id')
                        ->label(__('filament/leads.sequence'))
                        ->options(function () {
                            $tenantId = \App\Support\TenantContext::currentId();
                            return \App\Models\EmailSequence::where('tenant_id', $tenantId)
                                ->where('status', 'active')
                                ->pluck('name', 'id');
                        })
                        ->required()
                        ->searchable(),
                ])
                ->action(function (array $data) {
                    $tenantId   = \App\Support\TenantContext::currentId();
                    $sequenceId = (int) $data['sequence_id'];

                    // Tenant-scoped existence check — never fall back to
                    // cross-tenant detection (see commit 9c1ff8a0).
                    $already = \App\Models\EmailSequenceEnrollment::query()
                        ->where('tenant_id', $tenantId)
                        ->where('sequence_id', $sequenceId)
                        ->where('lead_id', $this->record->id)
                        ->exists();
                    if ($already) {
                        Notification::make()->warning()->title(__('filament/leads.already_enrolled'))->send();
                        return;
                    }

                    \App\Models\EmailSequenceEnrollment::create([
                        'tenant_id'    => $tenantId,
                        'sequence_id'  => $sequenceId,
                        'lead_id'      => $this->record->id,
                        'current_step' => 0,
                        'status'       => 'active',
                        'enrolled_at'  => now(),
                        'next_send_at' => now(),
                    ]);
                    Notification::make()->success()->title(__('filament/leads.lead_enrolled'))->send();
                }),

            Action::make('enroll_ai_sdr')
                ->label(__('filament/ai_sdr.enroll_action'))
                ->icon('heroicon-o-cpu-chip')
                ->color('info')
                ->visible(fn () => \App\Models\AiSdrAgent::where('tenant_id', \App\Support\TenantContext::currentId())
                    ->where('active', true)
                    ->exists())
                ->form([
                    Select::make('agent_id')
                        ->label(__('filament/ai_sdr.enroll_agent'))
                        ->options(function () {
                            $tenantId = \App\Support\TenantContext::currentId();
                            return \App\Models\AiSdrAgent::where('tenant_id', $tenantId)
                                ->where('active', true)
                                ->pluck('name', 'id');
                        })
                        ->required()
                        ->searchable(),
                ])
                ->action(function (array $data) {
                    $lead     = $this->record;
                    $tenantId = \App\Support\TenantContext::currentId();

                    // Guardrails enforced here too — the cron runner is not the
                    // only gate (a rep could enroll an opted-out / unreachable
                    // lead by hand otherwise).
                    if ($lead->ai_sdr_opted_out_at !== null) {
                        Notification::make()->warning()->title(__('filament/ai_sdr.enroll_opted_out'))->send();
                        return;
                    }

                    $agentId = (int) $data['agent_id'];

                    // Tenant-scoped agent lookup — never cross tenants.
                    $agent = \App\Models\AiSdrAgent::where('tenant_id', $tenantId)
                        ->whereKey($agentId)
                        ->first();
                    if (! $agent) {
                        Notification::make()->danger()->title(__('filament/ai_sdr.enroll_no_agents'))->send();
                        return;
                    }

                    // The lead must be reachable on the agent's channel (email
                    // needs an address; SMS/WhatsApp need a phone number).
                    if (! $agent->canReach($lead)) {
                        Notification::make()->danger()->title(__('filament/ai_sdr.enroll_unreachable'))->send();
                        return;
                    }

                    // Idempotent: never double-enroll into the same agent.
                    $already = \App\Models\AiSdrEnrollment::query()
                        ->where('tenant_id', $tenantId)
                        ->where('agent_id', $agentId)
                        ->where('lead_id', $lead->id)
                        ->exists();
                    if ($already) {
                        Notification::make()->warning()->title(__('filament/ai_sdr.enroll_already'))->send();
                        return;
                    }

                    \App\Models\AiSdrEnrollment::create([
                        'tenant_id'           => $tenantId,
                        'agent_id'            => $agentId,
                        'lead_id'             => $lead->id,
                        'state'               => \App\Models\AiSdrEnrollment::STATE_ACTIVE,
                        'touches_sent'        => 0,
                        'enrolled_by_user_id' => auth()->id(),
                        'enrolled_at'         => now(),
                        'next_action_at'      => now(),
                    ]);
                    Notification::make()->success()->title(__('filament/ai_sdr.enrolled_success'))->send();
                }),

            Action::make('apply_tags')
                ->label(__('filament/leads.apply_tags'))
                ->icon('heroicon-o-tag')
                ->form([
                    Select::make('tag_ids')
                        ->label(__('filament/leads.tags'))
                        ->multiple()
                        ->options(function () {
                            $tenantId = \App\Support\TenantContext::currentId();
                            return Tag::where('tenant_id', $tenantId)->pluck('name', 'id');
                        })
                        ->default(fn() => $this->record->tags->pluck('id')->toArray()),
                ])
                ->action(function (array $data) {
                    $before = $this->record->tags->pluck('id')->toArray();
                    $this->record->tags()->sync($data['tag_ids'] ?? []);
                    $after = $data['tag_ids'] ?? [];
                    $added   = collect($after)->diff($before);
                    $removed = collect($before)->diff($after);
                    $tagNames = Tag::whereIn('id', array_merge($added->toArray(), $removed->toArray()))->pluck('name', 'id');
                    foreach ($added as $id) {
                        app(LeadActivityService::class)->logTagApplied($this->record, $tagNames[$id] ?? '');
                    }
                    foreach ($removed as $id) {
                        app(LeadActivityService::class)->logTagRemoved($this->record, $tagNames[$id] ?? '');
                    }
                    Notification::make()->success()->title(__('filament/leads.tags_updated'))->send();
                }),

            Action::make('star')
                ->label(fn() => $this->record->is_starred ? __('filament/leads.unstar') : __('filament/leads.star'))
                ->icon(fn() => $this->record->is_starred ? 'heroicon-s-star' : 'heroicon-o-star')
                ->color(fn() => $this->record->is_starred ? 'warning' : 'gray')
                ->action(function () {
                    $this->record->update(['is_starred' => ! $this->record->is_starred]);
                }),

            // Everything below this line is collapsed into a single
            // "More" dropdown to keep the header toolbar readable.
            // The strict review flagged 17 header actions as overflow.
            ActionGroup::make([
            Action::make('create_quote')
                ->label(__('filament/leads.create_quote'))
                ->icon('heroicon-o-document-text')
                ->color('primary')
                ->url(fn () => \App\Filament\Resources\QuoteResource::getUrl('create') . '?lead_id=' . $this->record->id),

            Action::make('create_invoice')
                ->label(__('filament/leads.create_invoice'))
                ->icon('heroicon-o-banknotes')
                ->color('success')
                ->url(fn () => \App\Filament\Resources\InvoiceResource::getUrl('create') . '?lead_id=' . $this->record->id),

            Action::make('enrich_ai')
                ->label(fn() => $this->record->enriched_at ? __('filament/leads.re_enrich_with_ai') : __('filament/leads.enrich_with_ai'))
                ->icon('heroicon-o-sparkles')
                ->color('success')
                ->requiresConfirmation(fn() => (bool) $this->record->enriched_at)
                ->modalHeading(__('filament/leads.re_enrich_modal_heading'))
                ->modalDescription(__('filament/leads.re_enrich_modal_description'))
                ->action(function () {
                    if (! $this->record->email) {
                        Notification::make()->danger()->title(__('filament/leads.enrich_no_email'))->send();
                        return;
                    }
                    EnrichLead::dispatch($this->record, $this->record->tenant_id);
                    Notification::make()->success()->title(__('filament/leads.enrich_queued'))->send();
                }),

            Action::make('ai_draft_email')
                ->label(__('filament/leads.ai_draft_email'))
                ->icon('heroicon-o-cpu-chip')
                ->color('info')
                ->form([
                    \Filament\Forms\Components\Select::make('intent')
                        ->label(__('filament/leads.email_intent'))
                        ->options([
                            'introduction' => __('filament/leads.intent_introduction'),
                            'follow_up'    => __('filament/leads.intent_follow_up'),
                            'proposal'     => __('filament/leads.intent_proposal'),
                            're_engage'    => __('filament/leads.intent_re_engage'),
                            'closing'      => __('filament/leads.intent_closing'),
                        ])
                        ->default('follow_up')
                        ->required(),
                    \Filament\Forms\Components\Textarea::make('context')
                        ->label(__('filament/leads.additional_context'))
                        ->placeholder(__('filament/leads.additional_context_placeholder'))
                        ->rows(2)
                        ->nullable(),
                ])
                ->action(function (array $data) {
                    $draft = app(AiEmailComposerService::class)->draft(
                        $this->record,
                        $data['intent'],
                        $data['context'] ?? null
                    );

                    if (!$draft) {
                        Notification::make()->danger()->title(__('filament/leads.ai_draft_failed'))->send();
                        return;
                    }

                    Notification::make()
                        ->title(__('filament/leads.ai_draft_generated'))
                        ->body("**" . __('filament/leads.subject_label') . ":** {$draft['subject']}\n\n{$draft['body']}")
                        ->info()
                        ->persistent()
                        ->send();
                }),

            Action::make('merge_lead')
                ->label(__('filament/leads.merge_lead'))
                ->icon('heroicon-o-arrows-pointing-in')
                ->color('warning')
                ->visible(fn() => \App\Models\LeadDuplicate::where(function ($q) {
                    $q->where('original_lead_id', $this->record->id)
                      ->orWhere('duplicate_lead_id', $this->record->id);
                })->exists())
                ->form(function () {
                    $tenantId   = \App\Support\TenantContext::currentId();
                    $duplicates = \App\Models\LeadDuplicate::where(function ($q) {
                        $q->where('original_lead_id', $this->record->id)
                          ->orWhere('duplicate_lead_id', $this->record->id);
                    })->get();

                    $options = [];
                    foreach ($duplicates as $dup) {
                        $otherId = $dup->original_lead_id === $this->record->id
                            ? $dup->duplicate_lead_id
                            : $dup->original_lead_id;
                        $other   = Lead::find($otherId);
                        if ($other) {
                            $options[$otherId] = __('filament/leads.merge_option_format', [
                                'name'    => $other->full_name,
                                'email'   => $other->email ?? __('filament/leads.no_email'),
                                'field'   => $dup->match_field,
                            ]);
                        }
                    }

                    return [
                        \Filament\Forms\Components\Select::make('merge_into_id')
                            ->label(__('filament/leads.merge_into_label'))
                            ->options($options)
                            ->required()
                            ->helperText(__('filament/leads.merge_into_help')),
                    ];
                })
                ->action(function (array $data) {
                    $primary   = Lead::find($data['merge_into_id']);
                    $duplicate = $this->record;

                    if (!$primary) {
                        Notification::make()->danger()->title(__('filament/leads.merge_primary_not_found'))->send();
                        return;
                    }

                    app(LeadMergeService::class)->merge($primary, $duplicate);
                    Notification::make()->success()->title(__('filament/leads.merge_success'))->send();

                    $this->redirect(LeadResource::getUrl('view', ['record' => $primary->id]));
                }),

            // ── GDPR Actions ────────────────────────────────────

            Action::make('export_data')
                ->label(__('filament/leads.export_data'))
                ->icon('heroicon-o-arrow-down-tray')
                ->color('gray')
                ->action(function () {
                    $lead = $this->record->load([
                        'activities',
                        'notes',
                        'tasks',
                        'tags',
                        'attachments',
                        'formSubmissions.values',
                        'duplicates',
                        'duplicateOf',
                        'emails',
                        'messages',
                        'pageViews',
                        'dealItems.product',
                        'sequenceEnrollments.sequence',
                    ]);

                    $export = [
                        'exported_at' => now()->toIso8601String(),
                        'lead' => $lead->only([
                            'id', 'first_name', 'last_name', 'email', 'phone',
                            'source', 'status', 'lead_score', 'is_starred',
                            'company', 'job_title', 'industry', 'company_size',
                            'country', 'linkedin_url', 'notes', 'raw_data',
                            'custom_fields', 'consent_text', 'consented_at',
                            'contacted_at', 'deal_value', 'deal_currency',
                            'expected_close_date', 'won_value', 'won_at', 'lost_reason', 'lost_at',
                            'utm_source', 'utm_medium', 'utm_campaign', 'utm_content', 'utm_term',
                            'landing_page', 'referrer_url',
                            'created_at', 'updated_at',
                        ]),
                        'activities' => $lead->activities->map(fn ($a) => $a->only([
                            'id', 'type', 'description', 'metadata', 'created_at',
                        ]))->toArray(),
                        'notes' => $lead->notes->map(fn ($n) => $n->only([
                            'id', 'body', 'mentions', 'created_at',
                        ]))->toArray(),
                        'tasks' => $lead->tasks->map(fn ($t) => $t->only([
                            'id', 'title', 'description', 'due_at', 'completed', 'completed_at', 'created_at',
                        ]))->toArray(),
                        'tags' => $lead->tags->pluck('name')->toArray(),
                        'attachments' => $lead->attachments->map(fn ($a) => $a->only([
                            'id', 'original_name', 'mime_type', 'size', 'created_at',
                        ]))->toArray(),
                        'emails' => $lead->emails->map(fn ($e) => $e->only([
                            'id', 'direction', 'subject', 'from_address', 'from_name',
                            'to_addresses', 'cc_addresses', 'body_text',
                            'sent_at', 'opened_at', 'clicked_at', 'bounced_at', 'created_at',
                        ]))->toArray(),
                        'messages' => $lead->messages->map(fn ($m) => $m->only([
                            'id', 'channel', 'direction', 'from_identifier', 'to_identifier',
                            'body', 'media_url', 'media_type', 'status', 'created_at',
                        ]))->toArray(),
                        'page_views' => $lead->pageViews->map(fn ($p) => $p->only([
                            'id', 'url', 'path', 'title', 'referrer',
                            'utm_source', 'utm_medium', 'utm_campaign',
                            'viewed_at', 'duration_seconds',
                        ]))->toArray(),
                        'deal_items' => $lead->dealItems->map(fn ($d) => [
                            'id'          => $d->id,
                            'product'     => $d->product?->name,
                            'name'        => $d->name,
                            'quantity'    => $d->quantity,
                            'unit_price'  => $d->unit_price,
                            'discount_pct'=> $d->discount_percent,
                            'total'       => $d->total,
                            'created_at'  => $d->created_at,
                        ])->toArray(),
                        'email_sequence_enrollments' => $lead->sequenceEnrollments->map(fn ($s) => [
                            'id'           => $s->id,
                            'sequence'     => $s->sequence?->name,
                            'status'       => $s->status,
                            'current_step' => $s->current_step,
                            'enrolled_at'  => $s->enrolled_at,
                            'completed_at' => $s->completed_at,
                        ])->toArray(),
                        'form_submissions' => $lead->formSubmissions->map(function ($s) {
                            return [
                                'id'         => $s->id,
                                'form_id'    => $s->form_id,
                                'ip_address' => $s->ip_address,
                                'created_at' => $s->created_at,
                                'values'     => $s->values->map(fn ($v) => $v->only([
                                    'field_id', 'value',
                                ]))->toArray(),
                            ];
                        })->toArray(),
                    ];

                    $json     = json_encode($export, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE);
                    $filename = 'lead-' . $lead->id . '-export-' . now()->format('Y-m-d-His') . '.json';

                    return response()->streamDownload(function () use ($json) {
                        echo $json;
                    }, $filename, [
                        'Content-Type' => 'application/json',
                    ]);
                }),

            Action::make('send_portal_link')
                ->label(__('filament/leads.send_portal_link'))
                ->icon('heroicon-o-key')
                ->color('gray')
                ->visible(fn () => ! empty($this->record->email))
                ->requiresConfirmation()
                ->modalHeading(__('filament/leads.portal_link_heading'))
                ->modalDescription(__('filament/leads.portal_link_description'))
                ->action(function () {
                    $lead = $this->record;
                    if (! $lead->email) {
                        Notification::make()->danger()->title(__('filament/leads.no_email_address'))->send();
                        return;
                    }
                    try {
                        $token = \App\Models\PortalAccessToken::create([
                            'lead_id'    => $lead->id,
                            'token'      => \Illuminate\Support\Str::random(64),
                            'expires_at' => now()->addMinutes(30),
                            'ip_address' => request()->ip(),
                        ]);
                        \Illuminate\Support\Facades\Mail::to($lead->email)
                            ->send(new \App\Mail\PortalMagicLinkMail($lead, $token->token));
                        Notification::make()->success()->title(__('filament/leads.portal_link_sent_prefix') . $lead->email)->send();
                    } catch (\Throwable $e) {
                        Notification::make()->danger()->title(__('filament/leads.portal_link_failed_prefix') . $e->getMessage())->send();
                    }
                }),

            Action::make('gdpr_anonymize')
                ->before(fn () => \App\Support\DemoMode::guard(__('filament/demo_mode.gdpr_anonymize_blocked')))
                ->label(__('filament/leads.gdpr_anonymize'))
                ->icon('heroicon-o-user-minus')
                ->color('warning')
                ->requiresConfirmation()
                ->modalHeading(__('filament/leads.gdpr_anonymize_heading'))
                ->modalDescription(__('filament/leads.gdpr_anonymize_description'))
                ->modalSubmitActionLabel(__('filament/leads.gdpr_anonymize_confirm'))
                ->action(function () {
                    $lead = $this->record;
                    $leadId = $lead->id;

                    \Illuminate\Support\Facades\DB::transaction(function () use ($lead, $leadId) {
                        // 1. Delete PII-carrying child rows (attachments, emails,
                        //    messages, notes, page views, meeting bookings, tasks,
                        //    portal tokens/sessions).
                        foreach ($lead->attachments as $attachment) {
                            \Illuminate\Support\Facades\Storage::disk($attachment->disk)
                                ->delete($attachment->path);
                        }
                        $lead->attachments()->delete();
                        $lead->emails()->delete();
                        $lead->messages()->delete();
                        $lead->notes()->forceDelete();
                        $lead->pageViews()->delete();

                        // Tasks' title/description frequently name the lead.
                        $lead->tasks()->update([
                            'title'       => __('filament/leads.gdpr_task_label', ['id' => $leadId]),
                            'description' => null,
                        ]);

                        // Meeting bookings carry guest_name / email / phone / IP.
                        // Explicit tenant_id filter protects against ID
                        // collisions and lets this run safely during
                        // impersonation/queue contexts where the global
                        // scope resolver may not have a tenant.
                        \App\Models\MeetingBooking::withoutGlobalScope('tenant')
                            ->where('tenant_id', $lead->tenant_id)
                            ->where('lead_id', $leadId)
                            ->update([
                                'guest_name'  => __('filament/leads.gdpr_anonymous'),
                                'guest_email' => 'anon-' . $leadId . '@example.invalid',
                                'guest_phone' => null,
                                'notes'       => null,
                                'metadata'    => null,
                            ]);

                        // Portal access tokens + sessions carry IP + UA.
                        \App\Models\PortalAccessToken::where('lead_id', $leadId)->delete();
                        \App\Models\PortalSession::where('lead_id', $leadId)->delete();

                        // Keep activity rows but strip identifying metadata.
                        $lead->activities()->update([
                            'description' => null,
                            'metadata'    => null,
                        ]);

                        // Audit logs referencing this lead carry the original
                        // first/last/email in new_values + old_values.
                        // Explicit tenant_id filter — see note above.
                        \App\Models\AuditLog::withoutGlobalScope('tenant')
                            ->where('tenant_id', $lead->tenant_id)
                            ->where('auditable_type', \App\Models\Lead::class)
                            ->where('auditable_id', $leadId)
                            ->update([
                                'old_values' => null,
                                'new_values' => null,
                                'user_agent' => null,
                                'ip_address' => null,
                                'url'        => null,
                            ]);

                        // 2. Overwrite the lead's own PII columns.
                        $lead->update([
                            'first_name'       => __('filament/leads.gdpr_anonymous'),
                            'last_name'        => '[' . $leadId . ']',
                            'email'            => 'anon-' . $leadId . '@example.invalid',
                            'phone'            => null,
                            'phone_normalized' => null,
                            'linkedin_url'     => null,
                            'custom_fields'    => null,
                            'notes'            => null,
                            'raw_data'         => null,
                            'enrichment_data'  => null,
                            'landing_page'     => null,
                            'referrer_url'     => null,
                        ]);
                    });

                    Notification::make()
                        ->success()
                        ->title(__('filament/leads.gdpr_anonymized_success'))
                        ->send();
                }),

            Action::make('gdpr_erase')
                ->before(fn () => \App\Support\DemoMode::guard(__('filament/demo_mode.gdpr_erase_blocked')))
                ->label(__('filament/leads.gdpr_erase'))
                ->icon('heroicon-o-trash')
                ->color('danger')
                ->requiresConfirmation()
                ->modalHeading(__('filament/leads.gdpr_erase_heading'))
                ->modalDescription(__('filament/leads.gdpr_erase_description'))
                ->modalSubmitActionLabel(__('filament/leads.gdpr_erase_confirm'))
                ->action(function () {
                    $lead = $this->record;

                    // Delete attachment files from disk
                    foreach ($lead->attachments as $attachment) {
                        Storage::disk($attachment->disk)->delete($attachment->path);
                    }

                    // Delete all related records (cascading through every
                    // new entity added today — the strict review flagged
                    // emails/messages/pageViews/dealItems/enrollments as a
                    // GDPR regression).
                    $lead->activities()->delete();
                    $lead->notes()->forceDelete();
                    $lead->tasks()->delete();
                    $lead->attachments()->delete();
                    $lead->tags()->detach();
                    $lead->emails()->delete();
                    $lead->messages()->delete();
                    $lead->pageViews()->delete();
                    $lead->dealItems()->delete();
                    $lead->sequenceEnrollments()->delete();

                    // Delete duplicates referencing this lead
                    \App\Models\LeadDuplicate::where(function ($q) use ($lead) {
                        $q->where('original_lead_id', $lead->id)
                          ->orWhere('duplicate_lead_id', $lead->id);
                    })->delete();

                    // Delete form submissions and their values
                    foreach ($lead->formSubmissions as $submission) {
                        $submission->values()->delete();
                        $submission->delete();
                    }

                    // Force-delete the lead (bypass soft delete)
                    $lead->forceDelete();

                    Notification::make()
                        ->success()
                        ->title(__('filament/leads.gdpr_erase_success'))
                        ->send();

                    $this->redirect(LeadResource::getUrl('index'));
                }),
            ])
            ->label(__('filament/leads.more'))
            ->icon('heroicon-m-ellipsis-vertical')
            ->color('gray')
            ->button(),
        ];
    }

    // ── Attachment methods ────────────────────────────────────

    public function uploadAttachments(): void
    {
        $this->validate([
            'newAttachments.*' => 'file|max:20480', // 20 MB per file
        ]);

        $tenantId = \App\Support\TenantContext::currentId();
        $lead     = $this->record;

        foreach ($this->newAttachments as $file) {
            $filename = Str::uuid() . '.' . $file->getClientOriginalExtension();
            $directory = "lead-attachments/{$tenantId}/{$lead->id}";
            $path = $file->storeAs($directory, $filename, 'local');

            LeadAttachment::create([
                'tenant_id'     => $tenantId,
                'lead_id'       => $lead->id,
                'uploaded_by'   => auth()->id(),
                'filename'      => $filename,
                'original_name' => $file->getClientOriginalName(),
                'mime_type'     => $file->getMimeType(),
                'size'          => $file->getSize(),
                'disk'          => 'local',
                'path'          => $path,
            ]);
        }

        $this->newAttachments = null;
        $this->record->refresh();

        Notification::make()->success()->title(__('filament/leads.attachment_uploaded'))->send();
    }

    public function downloadAttachment(int $attachmentId): mixed
    {
        $attachment = LeadAttachment::findOrFail($attachmentId);

        if ($attachment->lead_id !== $this->record->id) {
            abort(403);
        }

        $disk = $this->safeAttachmentDisk($attachment->disk);

        return response()->streamDownload(function () use ($disk, $attachment) {
            echo Storage::disk($disk)->get($attachment->path);
        }, $attachment->original_name, [
            'Content-Type' => $attachment->mime_type ?? 'application/octet-stream',
        ]);
    }

    public function deleteAttachment(int $attachmentId): void
    {
        $attachment = LeadAttachment::findOrFail($attachmentId);

        if ($attachment->lead_id !== $this->record->id) {
            abort(403);
        }

        $disk = $this->safeAttachmentDisk($attachment->disk);
        Storage::disk($disk)->delete($attachment->path);
        $attachment->delete();
        $this->record->refresh();

        Notification::make()->success()->title(__('filament/leads.attachment_deleted'))->send();
    }

    /**
     * Allowlist guard for the filesystem driver name read off a
     * LeadAttachment row.  Today every write hardcodes 'local', but
     * if a future code path ever lets `disk` be set freely (an import,
     * a third-party plugin, a misconfigured S3 migration), this stops
     * a tenant attacker from passing it through to Storage::disk()
     * and reading from a privileged driver they shouldn't see.
     */
    private function safeAttachmentDisk(?string $disk): string
    {
        $candidate = $disk !== null && $disk !== '' ? $disk : 'local';
        if (! in_array($candidate, ['local', 'public', 's3'], true)) {
            abort(403, __('messages.attachment_disk_not_allowed'));
        }
        return $candidate;
    }

    // ── Deal items ──────────────────────────────────────────

    public function removeDealItem(int $itemId): void
    {
        $item = DealItem::where('lead_id', $this->record->id)->find($itemId);
        if (! $item) {
            return;
        }

        $item->delete();
        $this->record->refresh();

        Notification::make()->success()->title(__('filament/leads.line_item_removed'))->send();
    }

    // ── View data ───────────────────────────────────────────

    protected function getViewData(): array
    {
        $lead        = $this->record->load([
            'activities.user',
            'notes.user',
            'tags',
            'assignedUser',
            'pipelineStage.pipeline',
            'tasks',
            'attachments.uploader',
            'dealItems.product',
            'companyEntity',
            'quotes',
            'invoices',
            'messages' => fn ($q) => $q->latest()->limit(50),
        ]);
        $nextActions = app(LeadNextActionService::class)->recommend($lead);

        return [
            'lead'        => $lead,
            'activities'  => $lead->activities()->latest('created_at')->get(),
            'notes'       => $lead->notes()->latest('created_at')->get(),
            'nextActions' => $nextActions,
            'dealItems'   => $lead->dealItems,
            'quotes'      => $lead->quotes,
            'invoices'    => $lead->invoices,
            'pageViews'   => $lead->pageViews()->latest('viewed_at')->limit(20)->get(),
            'emails'      => $lead->emails()->latest('created_at')->get(),
            'messages'    => $lead->messages,
            'calls'       => $lead->calls()->with('user')->latest('created_at')->limit(10)->get(),
        ];
    }
}
