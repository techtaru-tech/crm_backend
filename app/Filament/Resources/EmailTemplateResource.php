<?php

namespace App\Filament\Resources;

use App\Filament\Concerns\HasRolePermissions;
use App\Filament\Resources\EmailTemplateResource\Pages;
use App\Models\EmailTemplate;
use Filament\Forms\Components\RichEditor;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Filament\Resources\Resource;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Schema;
use Filament\Actions\DeleteAction;
use Filament\Actions\EditAction;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use UnitEnum;

class EmailTemplateResource extends Resource
{
    use HasRolePermissions;
    protected static string $permissionPrefix = 'automations';
    protected static ?string $model = EmailTemplate::class;
    protected static string|\BackedEnum|null $navigationIcon  = 'heroicon-o-envelope';
    protected static string|UnitEnum|null    $navigationGroup = 'Leads';
    protected static ?int    $navigationSort  = 7;

    public static function getNavigationLabel(): string
    {
        return __('filament/email_templates.nav_label');
    }

    public static function getModelLabel(): string
    {
        return __('filament/email_templates.model_label');
    }

    public static function getPluralModelLabel(): string
    {
        return __('filament/email_templates.plural_model_label');
    }

    public static function getEloquentQuery(): Builder
    {
        $tenantId = \App\Support\TenantContext::currentId();
        return parent::getEloquentQuery()->where('tenant_id', $tenantId);
    }

    public static function form(Schema $schema): Schema
    {
        return $schema->components([
            Section::make(__('sections.template'))->schema([
                TextInput::make('name')
                    ->label(__('filament/email_templates.template_name'))
                    ->required()
                    ->maxLength(255),
                TextInput::make('subject')
                    ->label(__('filament/email_templates.email_subject'))
                    ->required()
                    ->helperText(__('filament/email_templates.subject_helper'))
                    ->maxLength(255),
                RichEditor::make('body_html')
                    ->label(__('filament/email_templates.html_body'))
                    ->required()
                    ->helperText(__('filament/email_templates.html_body_helper'))
                    ->columnSpanFull(),
                Textarea::make('body_text')
                    ->label(__('filament/email_templates.plain_text_body'))
                    ->nullable()
                    ->rows(4)
                    ->columnSpanFull(),
            ])->columns(2),
        ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('name')->label(__('filament/email_templates.name'))->searchable()->sortable(),
                TextColumn::make('subject')->label(__('filament/email_templates.subject'))->searchable()->limit(50),
                TextColumn::make('created_at')->label(__('filament/email_templates.created'))->date()->sortable(),
            ])
            ->actions([
                EditAction::make(),
                DeleteAction::make(),
            ])
            ->defaultSort('name');
    }

    public static function getPages(): array
    {
        return [
            'index'  => Pages\ListEmailTemplates::route('/'),
            'create' => Pages\CreateEmailTemplate::route('/create'),
            'edit'   => Pages\EditEmailTemplate::route('/{record}/edit'),
        ];
    }
}
