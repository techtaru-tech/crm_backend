<?php

namespace App\Filament\Resources\LeadResource\RelationManagers;

use App\Models\LeadTask;
use App\Models\User;
use Filament\Actions\Action;
use Filament\Actions\CreateAction;
use Filament\Actions\DeleteAction;
use Filament\Actions\EditAction;
use Filament\Forms\Components\DateTimePicker;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Filament\Resources\RelationManagers\RelationManager;
use Filament\Schemas\Schema;
use Filament\Tables\Columns\IconColumn;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;

class TasksRelationManager extends RelationManager
{
    protected static string $relationship = 'tasks';

    protected static string|\BackedEnum|null $icon = 'heroicon-o-clipboard-document-check';

    // The English $title property is intentionally omitted; the locale-aware
    // override below resolves the tab title at request time via __().
    public static function getTitle(\Illuminate\Database\Eloquent\Model $ownerRecord, string $pageClass): string
    {
        return __('filament/leads.tasks_title');
    }

    public function form(Schema $schema): Schema
    {
        $tenantId = fn () => $this->ownerRecord->tenant_id
            ?? AppSupportTenantContext::currentId();

        return $schema->components([
            TextInput::make('title')
                ->label(__('filament/leads.task_title'))
                ->required()
                ->maxLength(255)
                ->columnSpanFull(),
            Textarea::make('description')
                ->label(__('filament/leads.description'))
                ->rows(3)
                ->nullable()
                ->columnSpanFull(),
            Select::make('assigned_user_id')
                ->label(__('filament/leads.assigned_to'))
                ->options(fn () => User::where('tenant_id', $tenantId())->pluck('name', 'id'))
                ->default(fn () => auth()->id())
                ->searchable()
                ->required(),
            DateTimePicker::make('due_at')
                ->label(__('filament/leads.due_at'))
                ->required()
                ->seconds(false)
                ->live()
                ->default(now()->addDay()->setTime(9, 0)),
            Select::make('priority')
                ->label(__('filament/leads.priority'))
                ->options(LeadTask::priorityLabels())
                ->default(LeadTask::PRIORITY_NORMAL)
                ->required(),
            DateTimePicker::make('reminder_at')
                ->label(__('filament/leads.reminder_at'))
                ->seconds(false)
                ->nullable()
                ->helperText(__('filament/leads.reminder_help_short'))
                ->default(fn ($get) => $get('due_at')
                    ? \Carbon\Carbon::parse($get('due_at'))->subHour()
                    : now()->addDay()->setTime(8, 0)),
        ]);
    }

    public function table(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('title')
                    ->label(__('filament/leads.task_title'))
                    ->searchable()
                    ->weight('medium')
                    ->wrap(),
                TextColumn::make('priority')
                    ->label(__('filament/leads.priority'))
                    ->badge()
                    ->formatStateUsing(fn ($state) => __('filament/leads.task_priority_' . $state))
                    ->color(fn ($state) => match ($state) {
                        LeadTask::PRIORITY_URGENT => 'danger',
                        LeadTask::PRIORITY_HIGH   => 'warning',
                        LeadTask::PRIORITY_NORMAL => 'indigo',
                        LeadTask::PRIORITY_LOW    => 'gray',
                        default                   => 'gray',
                    }),
                TextColumn::make('assignedUser.name')
                    ->label(__('filament/leads.assigned'))
                    ->placeholder('—')
                    ->toggleable(),
                TextColumn::make('due_at')
                    ->label(__('filament/leads.due'))
                    ->since()
                    ->sortable(),
                IconColumn::make('completed')
                    ->boolean()
                    ->label(__('filament/leads.done')),
            ])
            ->headerActions([
                CreateAction::make()
                    ->icon('heroicon-o-plus-circle')
                    ->mutateDataUsing(function (array $data): array {
                        $data['tenant_id'] = $this->ownerRecord->tenant_id;
                        $data['lead_id']   = $this->ownerRecord->id;
                        return $data;
                    }),
            ])
            ->actions([
                Action::make('mark_completed')
                    ->label(fn ($record) => $record->completed ? __('filament/leads.mark_incomplete') : __('filament/leads.mark_complete'))
                    ->icon(fn ($record) => $record->completed ? 'heroicon-o-arrow-uturn-left' : 'heroicon-o-check-circle')
                    ->color(fn ($record) => $record->completed ? 'gray' : 'success')
                    ->action(function ($record) {
                        $nowCompleted = ! $record->completed;
                        $record->update([
                            'completed'    => $nowCompleted,
                            'completed_at' => $nowCompleted ? now() : null,
                        ]);
                    }),
                EditAction::make(),
                DeleteAction::make()->requiresConfirmation(),
            ])
            ->defaultSort('due_at', 'asc');
    }
}
