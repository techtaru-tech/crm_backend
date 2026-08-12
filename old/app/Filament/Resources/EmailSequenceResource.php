<?php

namespace App\Filament\Resources;

use App\Filament\Concerns\HasRolePermissions;
use App\Filament\Resources\EmailSequenceResource\Pages;
use App\Filament\Resources\EmailSequenceResource\RelationManagers;
use App\Models\EmailSequence;
use Filament\Forms\Components\Repeater;
use Filament\Forms\Components\RichEditor;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Toggle;
use Filament\Notifications\Notification;
use Filament\Resources\Resource;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Schema;
use Filament\Actions\Action;
use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteAction;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\EditAction;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Filters\SelectFilter;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use UnitEnum;

class EmailSequenceResource extends Resource
{
    use HasRolePermissions;
    protected static string $permissionPrefix = 'automations';
    protected static ?string $model = EmailSequence::class;
    protected static string|\BackedEnum|null $navigationIcon  = 'heroicon-o-envelope-open';
    protected static string|UnitEnum|null    $navigationGroup = 'Leads';
    protected static ?int    $navigationSort  = 8;

    public static function getNavigationLabel(): string
    {
        return __('filament/email_sequences.nav_label');
    }

    /**
     * Method-form model-label overrides.  Replace the static $modelLabel
     * property so the locale resolves at request time (the translator
     * is not yet bound at class-load time for the active session locale).
     */
    public static function getModelLabel(): string
    {
        return __('filament/email_sequences.model_label');
    }

    public static function getPluralModelLabel(): string
    {
        return __('filament/email_sequences.plural_model_label');
    }

    public static function getEloquentQuery(): Builder
    {
        $tenantId = \App\Support\TenantContext::currentId();
        return parent::getEloquentQuery()->where('tenant_id', $tenantId);
    }

    public static function form(Schema $schema): Schema
    {
        return $schema->components([
            Section::make(__('sections.sequence_info'))
                ->schema([
                    TextInput::make('name')
                        ->label(__('filament/email_sequences.sequence_name'))
                        ->required()
                        ->maxLength(255),
                    Select::make('status')
                        ->label(__('filament/email_sequences.status'))
                        ->options([
                            'draft'  => __('filament/email_sequences.option_status_draft'),
                            'active' => __('filament/email_sequences.option_status_active'),
                            'paused' => __('filament/email_sequences.option_status_paused'),
                        ])
                        ->default('draft')
                        ->required(),
                    Textarea::make('description')
                        ->label(__('filament/email_sequences.description'))
                        ->rows(2)
                        ->nullable()
                        ->columnSpanFull(),
                ])->columns(2),

            Section::make(__('sections.behavior'))
                ->schema([
                    Toggle::make('stop_on_reply')
                        ->label(__('filament/email_sequences.stop_on_reply'))
                        ->default(true)
                        ->helperText(__('filament/email_sequences.stop_on_reply_help')),
                    Toggle::make('stop_on_won')
                        ->label(__('filament/email_sequences.stop_on_won'))
                        ->default(true)
                        ->helperText(__('filament/email_sequences.stop_on_won_help')),
                ])->columns(2),

            Section::make(__('sections.steps'))
                ->description(__('filament/email_sequences.steps_description'))
                ->columnSpanFull()
                ->schema([
                    Repeater::make('steps')
                        ->relationship('steps')
                        ->label('')
                        ->reorderable('sort_order')
                        ->orderColumn('sort_order')
                        ->addActionLabel(__('filament/email_sequences.add_step'))
                        ->schema([
                            TextInput::make('delay_days')
                                ->label(__('filament/email_sequences.delay_days'))
                                ->numeric()
                                ->default(0)
                                ->minValue(0)
                                ->required(),
                            TextInput::make('delay_hours')
                                ->label(__('filament/email_sequences.delay_hours'))
                                ->numeric()
                                ->default(0)
                                ->minValue(0)
                                ->maxValue(23)
                                ->required(),
                            // "Load from template" helper — picks one of
                            // the tenant's saved EmailTemplates and copies
                            // its subject + body into the fields below so
                            // the operator can reuse / tweak it.  Pure UI
                            // helper: dehydrated(false) means it is NOT
                            // persisted on the step (the snapshotted
                            // subject/body_html are what the sequence
                            // sends).  Closes the customer report
                            // "can't … choose [a template] for … email".
                            Select::make('load_template')
                                ->label(__('filament/email_sequences.load_template'))
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
                                ->helperText(__('filament/email_sequences.load_template_help'))
                                ->afterStateUpdated(function ($state, callable $set) {
                                    if (! $state) {
                                        return;
                                    }
                                    $tpl = \App\Models\EmailTemplate::find($state);
                                    if ($tpl) {
                                        $set('subject', $tpl->subject);
                                        $set('body_html', $tpl->body_html);
                                    }
                                }),
                            TextInput::make('subject')
                                ->label(__('filament/email_sequences.subject'))
                                ->required()
                                ->columnSpanFull()
                                ->helperText(__('filament/email_sequences.subject_help')),
                            RichEditor::make('body_html')
                                ->label(__('filament/email_sequences.body'))
                                ->required()
                                ->columnSpanFull()
                                ->helperText(__('filament/email_sequences.body_help')),
                        ])
                        ->columns(2)
                        ->defaultItems(0)
                        ->itemLabel(fn(array $state): ?string =>
                            __('filament/email_sequences.item_label_step_prefix')
                            . (int) ($state['delay_days'] ?? 0) . __('filament/email_sequences.item_label_day_short') . ' '
                            . (int) ($state['delay_hours'] ?? 0) . __('filament/email_sequences.item_label_hour_short') . ' — '
                            . ($state['subject'] ?? __('filament/email_sequences.item_label_no_subject'))
                        ),
                ]),
        ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('name')->label(__('filament/email_sequences.col_name'))->searchable()->sortable(),
                TextColumn::make('status')
                    ->label(__('filament/email_sequences.col_status'))
                    ->badge()
                    ->colors([
                        'gray'    => 'draft',
                        'success' => 'active',
                        'warning' => 'paused',
                    ])
                    ->formatStateUsing(fn (?string $state): string => match ($state) {
                        'draft'  => __('filament/email_sequences.status_draft'),
                        'active' => __('filament/email_sequences.status_active'),
                        'paused' => __('filament/email_sequences.status_paused'),
                        default  => (string) $state,
                    }),
                TextColumn::make('steps_count')
                    ->label(__('filament/email_sequences.col_steps'))
                    ->counts('steps'),
                TextColumn::make('enrolled_count')
                    ->label(__('filament/email_sequences.col_active_enroll'))
                    ->counts([
                        'enrollments' => fn($q) => $q->where('status', 'active'),
                    ]),
                TextColumn::make('completed_count')
                    ->label(__('filament/email_sequences.col_completed'))
                    ->counts([
                        'enrollments as completed_count' => fn($q) => $q->where('status', 'completed'),
                    ]),
                TextColumn::make('created_at')->label(__('filament/email_sequences.col_created'))->date()->sortable(),
            ])
            ->filters([
                SelectFilter::make('status')
                    ->label(__('filament/email_sequences.filter_label_status'))
                    ->options([
                        'draft'  => __('filament/email_sequences.option_status_draft'),
                        'active' => __('filament/email_sequences.option_status_active'),
                        'paused' => __('filament/email_sequences.option_status_paused'),
                    ]),
            ])
            ->actions([
                EditAction::make(),
                Action::make('preview')
                    ->label(__('filament/email_sequences.preview'))
                    ->icon('heroicon-o-eye')
                    ->color('primary')
                    ->modalHeading(fn (EmailSequence $record) => __('filament/email_sequences.preview_modal_heading', ['name' => $record->name]))
                    ->modalDescription(__('filament/email_sequences.preview_description'))
                    ->modalSubmitAction(false)
                    ->modalCancelActionLabel(__('filament/email_sequences.preview_close'))
                    ->modalContent(function (EmailSequence $record) {
                        $sample = [
                            '{first_name}' => __('filament/email_sequences.preview_sample_first_name'),
                            '{last_name}'  => __('filament/email_sequences.preview_sample_last_name'),
                            '{company}'    => __('filament/email_sequences.preview_sample_company_name'),
                            '{email}'      => __('filament/email_sequences.preview_sample_email'),
                        ];
                        $steps = $record->steps()->orderBy('sort_order')->get()->map(function ($step) use ($sample) {
                            return [
                                'step'    => $step->sort_order + 1,
                                'delay'   => trim(($step->delay_days ? $step->delay_days . __('filament/email_sequences.delay_days_short') . ' ' : '') . ($step->delay_hours ? $step->delay_hours . __('filament/email_sequences.delay_hours_short') : '')) ?: __('filament/email_sequences.preview_delay_immediate'),
                                'subject' => strtr($step->subject, $sample),
                                'body'    => strtr($step->body_html, $sample),
                            ];
                        });
                        return view('filament.pages.email-sequence-preview', ['steps' => $steps]);
                    }),
                Action::make('test_send')
                    ->label(__('filament/email_sequences.send_test'))
                    ->icon('heroicon-o-paper-airplane')
                    ->color('warning')
                    ->form([
                        TextInput::make('test_email')
                            ->label(__('filament/email_sequences.send_test_to'))
                            ->email()
                            ->default(fn () => auth()->user()?->email)
                            ->required(),
                        Select::make('step_index')
                            ->label(__('filament/email_sequences.which_step'))
                            ->options(fn (EmailSequence $record) =>
                                $record->steps()->orderBy('sort_order')->get()
                                    ->mapWithKeys(fn ($s) => [$s->sort_order => __('filament/email_sequences.test_send_step_option_label', ['step' => $s->sort_order + 1]) . \Illuminate\Support\Str::limit($s->subject, 40)])
                                    ->all()
                            )
                            ->required(),
                    ])
                    ->action(function (EmailSequence $record, array $data) {
                        $step = $record->steps()->where('sort_order', (int) $data['step_index'])->first();
                        if (! $step) {
                            Notification::make()->danger()->title(__('filament/email_sequences.notif_step_not_found'))->send();
                            return;
                        }

                        $tenant   = $record->tenant;
                        $renderer = app(\App\Services\BrandedEmailRenderer::class);

                        $sampleFirst = __('filament/email_sequences.preview_sample_first_name');
                        $sampleLast  = __('filament/email_sequences.preview_sample_last_name');
                        $ctx = [
                            'first_name' => $sampleFirst,
                            'last_name'  => $sampleLast,
                            'full_name'  => trim($sampleFirst . ' ' . $sampleLast),
                            'company'    => __('filament/email_sequences.preview_sample_company_name'),
                            'email'      => $data['test_email'],
                        ];

                        $subject = $renderer->interpolate($step->subject, $ctx);
                        $body    = $renderer->interpolate($step->body_html, $ctx);
                        // Wrap in the tenant's branded template header/footer.
                        $html    = $renderer->wrap($tenant, $body, $subject);

                        try {
                            if ($tenant) {
                                app(\App\Services\TenantSmtpManager::class)->applyForTenant($tenant);
                            }
                            \Illuminate\Support\Facades\Mail::html($html, function ($m) use ($data, $subject) {
                                $m->to($data['test_email'])->subject(__('filament/email_sequences.test_subject_prefix', ['subject' => $subject]));
                            });
                            Notification::make()->success()->title(__('filament/email_sequences.notif_test_email_sent', ['email' => $data['test_email']]))->send();
                        } catch (\Throwable $e) {
                            Notification::make()->danger()->title(__('filament/email_sequences.notif_test_email_failed', ['error' => $e->getMessage()]))->send();
                        }
                    }),
                Action::make('duplicate')
                    ->label(__('filament/email_sequences.duplicate'))
                    ->icon('heroicon-o-document-duplicate')
                    ->color('gray')
                    // Wrapped in a transaction so a partial copy (e.g.
                    // parent saved, step save blew up) can't leave an
                    // orphan sequence behind. Errors are caught and
                    // shown to the operator instead of 500-ing the
                    // whole list page.
                    ->action(function (EmailSequence $record) {
                        try {
                            \Illuminate\Support\Facades\DB::transaction(function () use ($record) {
                                // Re-fetch WITHOUT the withCount('steps')
                                // virtual column — otherwise replicate()
                                // copies the aggregate "steps_count" as
                                // if it were a real column and the
                                // INSERT errors: "Unknown column
                                // steps_count in INSERT INTO".
                                $fresh = \App\Models\EmailSequence::query()
                                    ->where('tenant_id', $record->tenant_id)
                                    ->where('id', $record->id)
                                    ->firstOrFail();

                                $copy = $fresh->replicate(['created_at', 'updated_at']);
                                $copy->name      = \Illuminate\Support\Str::limit(
                                    $fresh->name . ' ' . __('filament/email_sequences.duplicate_copy_suffix'), 250, ''
                                );
                                $copy->status    = 'draft';
                                $copy->tenant_id = $fresh->tenant_id;

                                // Defensive: strip any eager-loaded
                                // aggregate attributes that Eloquent
                                // treats as columns at save() time.
                                foreach (['steps_count', 'enrollments_count'] as $virt) {
                                    if ($copy->offsetExists($virt)) {
                                        unset($copy->{$virt});
                                    }
                                }
                                $copy->save();
                                $record = $fresh;

                                // Eager-load steps with explicit
                                // tenant_id filter; replicate each with
                                // the new sequence_id + original tenant.
                                $steps = \App\Models\EmailSequenceStep::query()
                                    ->where('tenant_id', $record->tenant_id)
                                    ->where('sequence_id', $record->id)
                                    ->orderBy('sort_order')
                                    ->get();

                                foreach ($steps as $step) {
                                    $stepCopy = $step->replicate(['created_at', 'updated_at']);
                                    $stepCopy->sequence_id = $copy->id;
                                    $stepCopy->tenant_id   = $record->tenant_id;
                                    $stepCopy->save();
                                }
                            });

                            Notification::make()->success()
                                ->title(__('filament/email_sequences.notif_sequence_duplicated'))
                                ->send();

                            // Return to the list so the operator sees the
                            // new "(Copy)" row in context — previously we
                            // stayed silently on whichever row they clicked.
                            return redirect(EmailSequenceResource::getUrl('index'));
                        } catch (\Throwable $e) {
                            logger()->warning('EmailSequence duplicate failed', [
                                'sequence_id' => $record->id,
                                'tenant_id'   => $record->tenant_id,
                                'error'       => $e->getMessage(),
                            ]);
                            Notification::make()->danger()
                                ->title(__('filament/email_sequences.notif_duplicate_failed'))
                                ->body(\Illuminate\Support\Str::limit($e->getMessage(), 200))
                                ->persistent()
                                ->send();
                        }
                    }),
                DeleteAction::make(),
            ])
            ->bulkActions([
                BulkActionGroup::make([
                    DeleteBulkAction::make(),
                ]),
            ])
            ->defaultSort('created_at', 'desc');
    }

    public static function getRelations(): array
    {
        return [
            RelationManagers\EnrollmentsRelationManager::class,
        ];
    }

    public static function getPages(): array
    {
        return [
            'index'  => Pages\ListEmailSequences::route('/'),
            'create' => Pages\CreateEmailSequence::route('/create'),
            'view'   => Pages\ViewEmailSequence::route('/{record}'),
            'edit'   => Pages\EditEmailSequence::route('/{record}/edit'),
        ];
    }
}
