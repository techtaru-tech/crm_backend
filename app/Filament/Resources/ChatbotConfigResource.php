<?php

declare(strict_types=1);

namespace App\Filament\Resources;

use App\Filament\Concerns\HasRolePermissions;
use App\Filament\Resources\ChatbotConfigResource\Pages;
use App\Models\ChatbotConfig;
use App\Models\MeetingType;
use App\Models\Pipeline;
use App\Models\PipelineStage;
use App\Support\TenantContext;
use Filament\Actions\Action;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\EditAction;
use Filament\Forms\Components\ColorPicker;
use Filament\Forms\Components\Placeholder;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Toggle;
use Filament\Resources\Resource;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Schema;
use Filament\Tables\Columns\IconColumn;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\HtmlString;

/**
 * LeadBot admin — configure the AI website chatbot, copy its one-line
 * embed snippet, and preview it.  Mirrors {@see LeadCaptureWidgetResource}
 * (uuid-keyed public embed) and {@see MeetingTypeResource} (embed-snippet
 * placeholder + share modal).
 *
 * Reuses the `forms` permission prefix — the chatbot is a lead-capture
 * tool that lives alongside forms/widgets, so anyone who can manage forms
 * can manage the bot (no new permission to seed/migrate).
 */
class ChatbotConfigResource extends Resource
{
    use HasRolePermissions;

    protected static string $permissionPrefix = 'forms';
    protected static ?string $model = ChatbotConfig::class;
    protected static string|\BackedEnum|null $navigationIcon = 'heroicon-o-chat-bubble-left-right';
    protected static string|\UnitEnum|null $navigationGroup = 'Tools';
    protected static ?int $navigationSort = 31;

    public static function getNavigationLabel(): string
    {
        return __('filament/chatbot.nav_label');
    }

    public static function getModelLabel(): string
    {
        return __('filament/chatbot.model_label');
    }

    public static function getPluralModelLabel(): string
    {
        return __('filament/chatbot.plural_model_label');
    }

    public static function form(Schema $schema): Schema
    {
        $tenantId = fn () => TenantContext::currentId();

        return $schema->components([

            Section::make(__('filament/chatbot.section_identity'))
                ->columns(2)
                ->schema([
                    TextInput::make('name')
                        ->label(__('filament/chatbot.name'))
                        ->required()
                        ->maxLength(120)
                        ->columnSpanFull(),
                    TextInput::make('greeting')
                        ->label(__('filament/chatbot.greeting'))
                        ->maxLength(255)
                        ->default(__('chatbot.default_greeting'))
                        ->helperText(__('filament/chatbot.greeting_help'))
                        ->columnSpanFull(),
                ]),

            Section::make(__('filament/chatbot.section_brain'))
                ->description(__('filament/chatbot.section_brain_help'))
                ->schema([
                    Textarea::make('persona')
                        ->label(__('filament/chatbot.persona'))
                        ->rows(3)
                        ->maxLength(1500)
                        ->placeholder(__('filament/chatbot.persona_placeholder'))
                        ->helperText(__('filament/chatbot.persona_help')),
                    Textarea::make('knowledge')
                        ->label(__('filament/chatbot.knowledge'))
                        ->rows(10)
                        ->maxLength(6000)
                        ->placeholder(__('filament/chatbot.knowledge_placeholder'))
                        ->helperText(__('filament/chatbot.knowledge_help')),
                ]),

            Section::make(__('filament/chatbot.section_appearance'))
                ->columns(2)
                ->schema([
                    ColorPicker::make('primary_color')
                        ->label(__('filament/chatbot.primary_color'))
                        ->default('#4f46e5'),
                ]),

            Section::make(__('filament/chatbot.section_routing'))
                ->columns(2)
                ->schema([
                    Select::make('pipeline_id')
                        ->label(__('filament/chatbot.route_to_pipeline'))
                        ->options(fn () => Pipeline::where('tenant_id', $tenantId())->pluck('name', 'id'))
                        ->nullable()
                        ->live(),
                    Select::make('pipeline_stage_id')
                        ->label(__('filament/chatbot.initial_stage'))
                        ->options(fn ($get) => $get('pipeline_id')
                            ? PipelineStage::where('pipeline_id', $get('pipeline_id'))->pluck('name', 'id')
                            : [])
                        ->nullable(),
                    Select::make('meeting_type_id')
                        ->label(__('filament/chatbot.booking_meeting_type'))
                        ->helperText(__('filament/chatbot.booking_meeting_type_help'))
                        ->options(fn () => MeetingType::where('tenant_id', $tenantId())
                            ->where('is_active', true)
                            ->pluck('name', 'id'))
                        ->nullable()
                        ->columnSpanFull(),
                ]),

            Section::make(__('filament/chatbot.section_limits'))
                ->columns(2)
                ->schema([
                    Toggle::make('enabled')
                        ->label(__('filament/chatbot.enabled'))
                        ->default(true),
                    TextInput::make('daily_message_cap')
                        ->label(__('filament/chatbot.daily_message_cap'))
                        ->helperText(__('filament/chatbot.daily_message_cap_help'))
                        ->numeric()
                        ->minValue(0)
                        ->default(500),
                ]),

            Section::make(__('filament/chatbot.section_embed'))
                ->schema([
                    Placeholder::make('embed_snippet_display')
                        ->label(__('filament/chatbot.embed_snippet_label'))
                        ->content(function ($record): HtmlString {
                            if (! $record) {
                                return new HtmlString(
                                    '<em class="text-gray-400">'
                                    . e(__('filament/chatbot.save_first_for_embed'))
                                    . '</em>'
                                );
                            }

                            $help = e(__('filament/chatbot.embed_snippet_help'));

                            return new HtmlString(
                                '<pre class="bg-gray-900 text-green-400 rounded-lg p-3 text-xs overflow-x-auto whitespace-pre-wrap break-all font-mono border border-gray-700">'
                                . e($record->embed_snippet)
                                . '</pre>'
                                . '<p class="text-xs text-gray-500 mt-2">' . $help . '</p>'
                            );
                        }),
                ])
                ->hidden(fn ($record) => $record === null)
                ->collapsed(),
        ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('name')
                    ->label(__('filament/chatbot.name'))
                    ->searchable()
                    ->sortable(),
                IconColumn::make('enabled')
                    ->label(__('filament/chatbot.enabled'))
                    ->boolean(),
                TextColumn::make('pipeline.name')
                    ->label(__('filament/chatbot.pipeline'))
                    ->badge()
                    ->color('gray')
                    ->placeholder('—'),
                TextColumn::make('daily_message_cap')
                    ->label(__('filament/chatbot.daily_message_cap'))
                    ->numeric()
                    ->sortable(),
                TextColumn::make('created_at')
                    ->label(__('filament/chatbot.created_at'))
                    ->date()
                    ->sortable()
                    ->toggleable(),
            ])
            ->actions([
                Action::make('preview')
                    ->label(__('filament/chatbot.action_preview'))
                    ->icon('heroicon-o-eye')
                    ->color('gray')
                    ->url(
                        fn (ChatbotConfig $record) => url('/chatbot/' . $record->uuid . '/preview'),
                        shouldOpenInNewTab: true
                    ),
                Action::make('get_snippet')
                    ->label(__('filament/chatbot.action_get_snippet'))
                    ->icon('heroicon-o-code-bracket')
                    ->color('success')
                    ->modalHeading(__('filament/chatbot.snippet_modal_heading'))
                    ->modalDescription(__('filament/chatbot.snippet_modal_description'))
                    ->modalContent(fn (ChatbotConfig $record) => new HtmlString(
                        '<div class="space-y-3">'
                        . '<p class="text-sm text-gray-600">' . e(__('filament/chatbot.snippet_modal_instructions')) . '</p>'
                        . '<pre class="bg-gray-900 text-green-400 rounded-lg p-3 text-xs overflow-x-auto whitespace-pre-wrap break-all font-mono border border-gray-700">'
                        . e($record->embed_snippet)
                        . '</pre>'
                        . '</div>'
                    ))
                    ->modalSubmitAction(false)
                    ->modalCancelActionLabel(__('filament/chatbot.modal_close')),
                EditAction::make(),
            ])
            ->bulkActions([
                DeleteBulkAction::make(),
            ]);
    }

    public static function getPages(): array
    {
        return [
            'index'  => Pages\ListChatbotConfigs::route('/'),
            'create' => Pages\CreateChatbotConfig::route('/create'),
            'edit'   => Pages\EditChatbotConfig::route('/{record}/edit'),
        ];
    }

    public static function getEloquentQuery(): Builder
    {
        $tenantId = TenantContext::currentId();

        return parent::getEloquentQuery()->where('tenant_id', $tenantId);
    }
}
