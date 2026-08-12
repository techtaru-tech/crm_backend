<?php

namespace App\Filament\Resources;

use App\Filament\Concerns\HasRolePermissions;
use App\Filament\Resources\LeadImportResource\Pages;
use App\Models\LeadImport;
use Filament\Resources\Resource;
use Filament\Schemas\Schema;
use Filament\Tables\Columns\BadgeColumn;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use UnitEnum;

class LeadImportResource extends Resource
{
    use HasRolePermissions;
    protected static string $permissionPrefix = 'leads';
    protected static ?string $model = LeadImport::class;
    protected static string|\BackedEnum|null $navigationIcon = 'heroicon-o-arrow-up-tray';
    protected static string|UnitEnum|null $navigationGroup = 'Leads';
    protected static ?int $navigationSort = 5;

    public static function getNavigationLabel(): string
    {
        return __('filament/lead_imports.nav_label');
    }

    /**
     * Method-form model-label overrides.  Replace the static $modelLabel
     * property so the locale resolves at request time (the translator
     * is not yet bound at class-load time for the active session locale).
     */
    public static function getModelLabel(): string
    {
        return __('filament/lead_imports.model_label');
    }

    public static function getPluralModelLabel(): string
    {
        return __('filament/lead_imports.plural_model_label');
    }

    public static function getNavigationBadge(): ?string
    {
        // Safe-wrapped so a missing/locked table never 500s the panel.
        return \App\Support\NavBadge::safe(function (): ?string {
            $tenantId = auth()->user()?->tenant_id;
            if (! $tenantId) return null;

            // 30 s cache per tenant. Shorter than other badges because
            // imports flip from `processing` to `completed` quickly when
            // a tenant uploads a file - they expect the badge to clear.
            $count = \App\Support\TenantCache::remember(
                $tenantId,
                "nav-badge:lead-imports-processing:tenant:{$tenantId}:v1",
                30,
                fn () => static::getEloquentQuery()->where('status', 'processing')->count(),
            );

            return $count > 0 ? (string) $count : null;
        });
    }

    public static function getNavigationBadgeColor(): string|array|null
    {
        return 'warning';
    }

    public static function form(Schema $schema): Schema
    {
        return $schema->components([]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('original_filename')->searchable()->label(__('filament/lead_imports.file')),
                TextColumn::make('status')
                    ->label(__('filament/lead_imports.status'))
                    ->badge()
                    ->color(fn($state) => match($state) {
                        'completed'  => 'success',
                        'processing' => 'warning',
                        'failed'     => 'danger',
                        default      => 'gray',
                    }),
                TextColumn::make('total_rows')->label(__('filament/lead_imports.total')),
                TextColumn::make('imported_count')->label(__('filament/lead_imports.imported'))->color('success'),
                TextColumn::make('duplicate_count')->label(__('filament/lead_imports.dupes'))->color('warning'),
                TextColumn::make('error_count')->label(__('filament/lead_imports.errors'))->color('danger'),
                TextColumn::make('user.name')->label(__('filament/lead_imports.imported_by')),
                TextColumn::make('created_at')->label(__('filament/lead_imports.created_at'))->dateTime()->sortable()->since(),
            ])
            ->defaultSort('created_at', 'desc')
            ->poll('8s');

    }

    public static function getEloquentQuery(): Builder
    {
        $tenantId = \App\Support\TenantContext::currentId();
        return parent::getEloquentQuery()
            ->when($tenantId, fn($q) => $q->where('tenant_id', $tenantId))
            ->with(['user']);
    }

    public static function getPages(): array
    {
        return [
            'index'  => Pages\ListLeadImports::route('/'),
            'create' => Pages\CreateLeadImport::route('/create'),
        ];
    }
}
