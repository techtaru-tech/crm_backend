<?php

namespace App\Filament\Resources\CompanyResource\RelationManagers;

use App\Enums\LeadSource;
use App\Filament\Resources\LeadResource;
use App\Models\User;
use Filament\Actions\Action;
use Filament\Actions\CreateAction;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Resources\RelationManagers\RelationManager;
use Filament\Schemas\Schema;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Filters\SelectFilter;
use Filament\Tables\Table;

class LeadsRelationManager extends RelationManager
{
    protected static string $relationship = 'leads';

    protected static ?string $title = null;

    protected static string|\BackedEnum|null $icon = 'heroicon-o-users';

    protected static ?string $recordTitleAttribute = 'email';

    public static function getTitle(\Illuminate\Database\Eloquent\Model $ownerRecord, string $pageClass): string
    {
        return __('filament/companies.relation_title');
    }

    public function form(Schema $schema): Schema
    {
        $tenantId = fn () => $this->ownerRecord->tenant_id
            ?? AppSupportTenantContext::currentId();

        return $schema->components([
            TextInput::make('first_name')
                ->label(__('filament/companies.field_first_name_label'))
                ->maxLength(100),
            TextInput::make('last_name')
                ->label(__('filament/companies.field_last_name_label'))
                ->maxLength(100),
            TextInput::make('email')
                ->label(__('filament/companies.field_email_label'))
                ->email()
                ->maxLength(150),
            TextInput::make('phone')
                ->label(__('filament/companies.field_lead_phone_label'))
                ->tel()
                ->maxLength(30),
            Select::make('source')
                ->label(__('filament/companies.field_source_label'))
                ->options(LeadSource::options())
                ->required(),
            Select::make('status')
                ->label(__('filament/companies.field_status_label'))
                ->options([
                    'new'       => __('filament/companies.lead_status_new'),
                    'contacted' => __('filament/companies.lead_status_contacted'),
                    'qualified' => __('filament/companies.lead_status_qualified'),
                    'converted' => __('filament/companies.lead_status_converted'),
                    'lost'      => __('filament/companies.lead_status_lost'),
                ])
                ->default('new')
                ->required(),
            Select::make('assigned_user_id')
                ->label(__('filament/companies.assigned_to'))
                ->options(fn () => User::where('tenant_id', $tenantId())->pluck('name', 'id'))
                ->searchable()
                ->nullable(),
        ]);
    }

    public function table(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('full_name')
                    ->label(__('filament/companies.name'))
                    ->searchable(['first_name', 'last_name'])
                    ->sortable()
                    ->weight('medium')
                    ->description(fn ($record) => $record->email ?? ''),
                TextColumn::make('email')
                    ->label(__('filament/companies.col_lead_email'))
                    ->searchable()
                    ->copyable()
                    ->placeholder('—')
                    ->toggleable(),
                TextColumn::make('phone')
                    ->label(__('filament/companies.col_lead_phone'))
                    ->placeholder('—')
                    ->toggleable(),
                TextColumn::make('status')
                    ->label(__('filament/companies.col_lead_status'))
                    ->badge()
                    ->formatStateUsing(fn ($state) => $state instanceof \App\Enums\LeadStatus
                        ? $state->label()
                        : __('filament/leads.status_' . $state))
                    ->color(fn ($state) => match ($state instanceof \App\Enums\LeadStatus ? $state->value : $state) {
                        'new'       => 'warning',
                        'contacted' => 'info',
                        'qualified' => 'success',
                        'won'       => 'success',
                        'converted' => 'success',
                        'lost'      => 'danger',
                        default     => 'gray',
                    }),
                TextColumn::make('pipelineStage.name')
                    ->label(__('filament/companies.stage'))
                    ->badge()
                    ->color('purple')
                    ->placeholder('—')
                    ->toggleable(),
                TextColumn::make('deal_value')
                    ->label(__('filament/companies.deal_value'))
                    ->money(fn ($record) => $record->deal_currency ?: 'USD')
                    ->sortable()
                    ->placeholder('—'),
                TextColumn::make('assignedUser.name')
                    ->label(__('filament/companies.owner'))
                    ->placeholder('—')
                    ->toggleable(),
                TextColumn::make('created_at')
                    ->label(__('filament/companies.col_lead_created_at'))
                    ->since()
                    ->sortable()
                    ->toggleable(),
            ])
            ->filters([
                SelectFilter::make('status')
                    ->label(__('filament/companies.filter_label_status'))
                    ->options([
                        'new'       => __('filament/companies.lead_status_new'),
                        'contacted' => __('filament/companies.lead_status_contacted'),
                        'qualified' => __('filament/companies.lead_status_qualified'),
                        'converted' => __('filament/companies.lead_status_converted'),
                        'lost'      => __('filament/companies.lead_status_lost'),
                    ]),
            ])
            ->headerActions([
                CreateAction::make()
                    ->label(__('filament/companies.action_add_contact'))
                    ->icon('heroicon-o-user-plus')
                    ->mutateDataUsing(function (array $data): array {
                        $data['tenant_id']  = $this->ownerRecord->tenant_id;
                        $data['company_id'] = $this->ownerRecord->id;
                        return $data;
                    }),
            ])
            ->actions([
                Action::make('view_lead')
                    ->label(__('filament/companies.action_view'))
                    ->icon('heroicon-o-eye')
                    ->color('gray')
                    ->url(fn ($record) => LeadResource::getUrl('view', ['record' => $record]))
                    ->openUrlInNewTab(false),
            ])
            ->defaultSort('created_at', 'desc');
    }
}
