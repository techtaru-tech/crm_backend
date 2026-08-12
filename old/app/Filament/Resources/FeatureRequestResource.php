<?php

namespace App\Filament\Resources;

use App\Filament\Resources\FeatureRequestResource\Pages;
use App\Models\FeatureRequest;
use App\Models\FeatureRequestVote;
use Filament\Actions\Action;
use Filament\Actions\DeleteAction;
use Filament\Actions\EditAction;
use Filament\Forms\Components\Hidden;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Filament\Notifications\Notification;
use Filament\Resources\Resource;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Schema;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Filters\SelectFilter;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Facades\DB;
use UnitEnum;

class FeatureRequestResource extends Resource
{
    protected static ?string $model = FeatureRequest::class;
    protected static string|\BackedEnum|null $navigationIcon = 'heroicon-o-light-bulb';
    protected static string|UnitEnum|null $navigationGroup = 'Tools';
    protected static ?int $navigationSort = 25;

    public static function getNavigationLabel(): string
    {
        return __('filament/feature_requests.nav_label');
    }

    /**
     * Method-form model-label overrides.  Replace the static $modelLabel
     * property so the locale resolves at request time (the translator
     * is not yet bound at class-load time for the active session locale).
     */
    public static function getModelLabel(): string
    {
        return __('filament/feature_requests.model_label');
    }

    public static function getPluralModelLabel(): string
    {
        return __('filament/feature_requests.plural_model_label');
    }

    public static function form(Schema $schema): Schema
    {
        $isAdmin = (bool) (auth()->user()?->is_super_admin
            || auth()->user()?->tenants?->where('pivot.role', 'admin')->isNotEmpty());

        return $schema->components([
            Section::make(__('sections.request_details'))->schema([
                TextInput::make('title')
                    ->label(__('filament/feature_requests.title'))
                    ->required()
                    ->maxLength(200),
                Textarea::make('description')
                    ->label(__('filament/feature_requests.description'))
                    ->rows(5)
                    ->nullable(),
                $isAdmin
                    ? Select::make('status')
                        ->label(__('filament/feature_requests.status'))
                        ->options(FeatureRequest::statusLabels())
                        ->default('open')
                        ->required()
                    : Hidden::make('status')->default('open'),
            ])->columns(1),
        ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->defaultSort('votes_count', 'desc')
            ->columns([
                TextColumn::make('title')
                    ->label(__('filament/feature_requests.col_title'))
                    ->searchable()
                    ->sortable()
                    ->limit(60),
                TextColumn::make('status')
                    ->label(__('filament/feature_requests.col_status'))
                    ->badge()
                    ->formatStateUsing(fn ($state) => FeatureRequest::statusLabels()[$state] ?? ucfirst((string) $state))
                    ->color(fn ($state) => match ($state) {
                        'open'        => 'gray',
                        'planned'     => 'info',
                        'in_progress' => 'warning',
                        'shipped'     => 'success',
                        'closed'      => 'danger',
                        default       => 'gray',
                    }),
                TextColumn::make('votes_count')
                    ->label(__('filament/feature_requests.votes'))
                    ->badge()
                    ->color('primary')
                    ->sortable(),
                TextColumn::make('user.name')->label(__('filament/feature_requests.submitted_by'))->toggleable(),
                TextColumn::make('created_at')->since()->label(__('filament/feature_requests.when'))->sortable(),
            ])
            ->filters([
                SelectFilter::make('status')
                    ->label(__('filament/feature_requests.filter_label_status'))
                    ->options(FeatureRequest::statusLabels()),
            ])
            ->actions([
                Action::make('upvote')
                    ->label(fn (FeatureRequest $r) => $r->hasVoted(auth()->user()) ? __('filament/feature_requests.action_remove_vote') : __('filament/feature_requests.action_upvote'))
                    ->icon(fn (FeatureRequest $r) => $r->hasVoted(auth()->user()) ? 'heroicon-s-hand-thumb-up' : 'heroicon-o-hand-thumb-up')
                    ->color(fn (FeatureRequest $r) => $r->hasVoted(auth()->user()) ? 'primary' : 'gray')
                    ->action(function (FeatureRequest $r) {
                        $userId = auth()->id();
                        DB::transaction(function () use ($r, $userId) {
                            $existing = FeatureRequestVote::where('feature_request_id', $r->id)
                                ->where('user_id', $userId)
                                ->first();

                            if ($existing) {
                                $existing->delete();
                                $r->decrement('votes_count');
                            } else {
                                FeatureRequestVote::create([
                                    'feature_request_id' => $r->id,
                                    'user_id'            => $userId,
                                ]);
                                $r->increment('votes_count');
                            }
                        });
                        Notification::make()->success()->title(__('filament/feature_requests.vote_updated'))->send();
                    }),
                EditAction::make(),
                DeleteAction::make(),
            ]);
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
            'index'  => Pages\ListFeatureRequests::route('/'),
            'create' => Pages\CreateFeatureRequest::route('/create'),
            'edit'   => Pages\EditFeatureRequest::route('/{record}/edit'),
        ];
    }
}
